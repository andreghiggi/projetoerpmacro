<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ComprasIAController extends Controller
{
    public function dashboard()
    {
        $dados = DB::select("
            SELECT 
            p.id,
            p.nome,

            (SELECT SUM(e.quantidade) FROM estoques e WHERE e.produto_id = p.id) AS estoque_atual,
            p.estoque_minimo,

            (
            -- Consumo NFe
            SELECT COALESCE(SUM(ii.quantidade),0)
            FROM item_nves ii
            JOIN nves n ON n.id = ii.nfe_id
            WHERE ii.produto_id = p.id
            AND n.tpNF = 1
            AND n.estado <> 'cancelado'
            AND n.data_emissao >= NOW() - INTERVAL 90 DAY
            +
            -- Consumo NFCe
            COALESCE((
                SELECT SUM(infc.quantidade)
                FROM item_nfces infc
                JOIN nfces c ON c.id = infc.nfce_id
                WHERE infc.produto_id = p.id
                AND c.estado <> 'cancelado'
                AND c.data_emissao >= NOW() - INTERVAL 90 DAY
                ),0)
            ) / 90 AS media_consumo_dia,

            (
                SELECT AVG(DATEDIFF(n.data_entrega, n.data_emissao))
                FROM item_nves ii
                JOIN nves n ON n.id = ii.nfe_id
                WHERE ii.produto_id = p.id
                AND n.tpNF = 0
                AND n.estado <> 'cancelado'
                AND n.data_entrega IS NOT NULL
                ) AS lead_time,

            (
                SELECT ii.valor_unitario
                FROM item_nves ii
                JOIN nves n ON n.id = ii.nfe_id
                WHERE ii.produto_id = p.id
                AND n.tpNF = 0
                AND n.estado <> 'cancelado'
                ORDER BY n.data_emissao DESC
                LIMIT 1
                ) AS ultimo_valor,

            (
                SELECT f.nome_fantasia
                FROM item_nves ii
                JOIN nves n ON n.id = ii.nfe_id
                JOIN fornecedors f ON f.id = n.fornecedor_id
                WHERE ii.produto_id = p.id
                AND n.tpNF = 0
                AND n.estado <> 'cancelado'
                ORDER BY n.data_emissao DESC
                LIMIT 1
                ) AS ultimo_fornecedor

            FROM produtos p
            WHERE p.status = 1
            AND (
                EXISTS (
                    SELECT 1
                    FROM item_nves ii
                    JOIN nves n ON n.id = ii.nfe_id
                    WHERE ii.produto_id = p.id
                    AND n.tpNF = 1
                    AND n.estado <> 'cancelado'
                    AND n.data_emissao >= NOW() - INTERVAL 30 DAY
                    )
                OR
                EXISTS (
                    SELECT 1
                    FROM item_nfces infc
                    JOIN nfces c ON c.id = infc.nfce_id
                    WHERE infc.produto_id = p.id
                    AND c.estado <> 'cancelado'
                    AND c.data_emissao >= NOW() - INTERVAL 30 DAY
                    )
                )
            ORDER BY p.nome ASC
            ");

        return view('compras.ia.dashboard', compact('dados'));
    }

    public function analisar(Request $request)
    {
        $id = $request->produto_id;

        $dados = DB::select("
            SELECT 
            p.id,
            p.nome,

            (SELECT SUM(e.quantidade) FROM estoques e WHERE e.produto_id = p.id) AS estoque_atual,
            p.estoque_minimo,

            (
                SELECT COALESCE(SUM(ii.quantidade),0) / 90
                FROM item_nves ii
                JOIN nves n ON n.id = ii.nfe_id
                WHERE ii.produto_id = p.id
                AND n.estado = 'aprovado'
                AND n.tpNF = 1
                AND n.data_emissao >= NOW() - INTERVAL 90 DAY
                ) AS media_consumo_dia,

            (
                SELECT AVG(DATEDIFF(n.data_entrega, n.data_emissao))
                FROM item_nves ii
                JOIN nves n ON n.id = ii.nfe_id
                WHERE ii.produto_id = p.id
                AND n.estado = 'aprovado'
                AND n.tpNF = 0
                AND n.data_entrega IS NOT NULL
                ) AS lead_time,

            (
                SELECT ii.valor_unitario
                FROM item_nves ii
                JOIN nves n ON n.id = ii.nfe_id
                WHERE ii.produto_id = p.id
                AND n.estado = 'aprovado'
                AND n.tpNF = 0
                ORDER BY n.data_emissao DESC
                LIMIT 1
                ) AS ultimo_valor,

            (
                SELECT f.nome_fantasia
                FROM item_nves ii
                JOIN nves n ON n.id = ii.nfe_id
                JOIN fornecedors f ON f.id = n.fornecedor_id
                WHERE ii.produto_id = p.id
                AND n.estado = 'aprovado'
                AND n.tpNF = 0
                ORDER BY n.data_emissao DESC
                LIMIT 1
                ) AS ultimo_fornecedor

            FROM produtos p
            WHERE p.id = $id
            LIMIT 1
            ");

        $dados = $dados[0];

        $prompt = "
        Produto: {$dados->nome}
        Estoque atual: {$dados->estoque_atual}
        Estoque mínimo: {$dados->estoque_minimo}
        Consumo médio diário: {$dados->media_consumo_dia}
        Lead time médio (dias): {$dados->lead_time}
        Último valor pago: {$dados->ultimo_valor}
        Último fornecedor: {$dados->ultimo_fornecedor}

        Sugira quantidade ideal de compra e explique.
        ";

        $resposta = Http::withHeaders([
            'Authorization' => 'Bearer '.env('GROQ_API_KEY'),
            'Content-Type' => 'application/json'
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'llama-3.1-8b-instant',
            'messages' => [
                ['role' => 'system', 'content' => 'Especialista em compras e estoque.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.2
        ]);


        $ia = $resposta->json()['choices'][0]['message']['content'] ?? 'Erro ao consultar IA';

        return response()->json([
            'dados' => $dados,
            'ia' => $ia
        ]);
    }
}
