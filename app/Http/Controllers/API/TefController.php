<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TefMultiPlusCard;
use App\Models\RegistroTef;
use Dompdf\Dompdf;

class TefController extends Controller
{   
    public function __construct(){
        if (!is_dir(public_path('tef_comprovante'))) {
            mkdir(public_path('tef_comprovante'), 0777, true);
        }
    }

    public function verificaAtivo(Request $request){

        $item = TefMultiPlusCard::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->where('usuario_id', $request->usuario_id)
        ->first();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.multipluscard.com.br/api/Servicos/SetVendaTef");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');

        $cnpj = preg_replace('/[^0-9]/', '', $item->cnpj);
        $conteudo = '000-000 = ATV¬001-000 = 1¬999-999 = 0';

        $headers = [
            "Content-Type: application/json",
            "Content-Length: 0",
            "CNPJ: $cnpj",
            "PDV: $item->pdv",
            "TOKEN: $item->token",
            "CONTEUDO: $conteudo"
        ];

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($curl);
        sleep(2);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.multipluscard.com.br/api/Servicos/GetVendasTef");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $headers = [
            "Content-Type: application/json",
            "Content-Length: 0",
            "hash: $result",
            "TOKEN: $item->token",
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $resp = curl_exec($curl);
        $search = "ATV¬001-000";
        if(preg_match("/{$search}/i", $resp)) {
            return response()->json($resp, 200);
        }
        return response()->json($resp, 401);
    }

    public function consulta(Request $request){

        $item = TefMultiPlusCard::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->where('usuario_id', $request->usuario_id)
        ->first();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.multipluscard.com.br/api/Servicos/GetVendasTef");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $headers = [
            "Content-Type: application/json",
            "Content-Length: 0",
            "hash: $request->hash",
            "TOKEN: $item->token",
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $resp = curl_exec($curl);

        // file_put_contents('tef.txt', $resp);
        $cancelada = "Cancelado pelo operador";
        if(preg_match("/{$cancelada}/i", $resp)) {
            return response()->json("Cancelado pelo operador", 401);
        }

        $negada = "Transação negada!";
        if(preg_match("/{$negada}/i", $resp)) {
            return response()->json("Transação negada!", 401);
        }

        $aceita = "Transação Aceita";
        if(preg_match("/{$aceita}/i", $resp)) {

            $dataExplode = explode("¬", $resp);

            $tipo = 'cartao';
            $search = 'VENDA PIX';
            if(preg_match("/{$search}/i", $resp)) {
                $tipo = 'pix';
            }
            $valor = explode("=",$dataExplode[3]);
            $valor = trim($valor[1]);

            $nome_rede = explode("=",$dataExplode[6]);
            $nome_rede = trim($nome_rede[1]);

            $finalizacao = "";
            if (preg_match('/027-000\s*=\s*([^\¬]+)/', $resp, $match)) {
                $finalizacao = trim($match[1]);
            }

            if($tipo == 'pix'){
                $nsu = explode("=",$dataExplode[8]);
                $nsu = trim($nsu[1]);

                $data = explode("=",$dataExplode[14]);
                $data = trim($data[1]);

                $hora = explode("=",$dataExplode[15]);
                $hora = trim($hora[1]);
            }else{
                $nsu = explode("=",$dataExplode[10]);
                $nsu = trim($nsu[1]);

                $data = explode("=",$dataExplode[16]);
                $data = trim($data[1]);

                $hora = explode("=",$dataExplode[17]);
                $hora = trim($hora[1]);
            }

            $dataRegistro = [
                'empresa_id' => $request->empresa_id,
                'valor_total' => $valor,
                'data_transacao' => $data,
                'hora_transacao' => $hora,
                'nsu' => $nsu,
                'nome_rede' => $nome_rede,
                'finalizacao' => $finalizacao,
                'hash' => $request->hash,
                'estado' => 'aprovado',
                'usuario_id' => $request->usuario_id
            ];
            $registro = RegistroTef::create($dataRegistro);
            $this->confirmarVenda($registro);
            return response()->json("Transação Aceita", 200);
        }
        return response()->json($resp, 200);
    }

    public function store(Request $request){

        $total = $request->total_venda;
        $tipoPagamento = $request->tipo_pagamento;
        $total = number_format($total, 2);
        $total = str_replace(".", "", $total);
        $item = TefMultiPlusCard::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->where('usuario_id', $request->usuario_id)
        ->first();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.multipluscard.com.br/api/Servicos/SetVendaTef");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');

        $registro = RegistroTef::where('empresa_id', $request->empresa_id)
        ->orderBy('id', 'desc')->first();
        $id = 1;
        if($registro != null){
            $id = $registro->id;
        }

        $tipoTransacao = 0;
        if($tipoPagamento == 31){
            $tipoTransacao = 1;
        }else if($tipoPagamento == 32){
            $tipoTransacao = 5;
        }

        $cnpj = preg_replace('/[^0-9]/', '', $item->cnpj);
        $conteudo = '000-000 = CRT¬001-000 = '.$id.'¬003-000 = '.$total.'¬800-001 = '.$tipoTransacao.'¬999-999 = 0';

        $headers = [
            "Content-Type: application/json",
            "Content-Length: 0",
            "CNPJ: $cnpj",
            "PDV: $item->pdv",
            "TOKEN: $item->token",
            "CONTEUDO: $conteudo"
        ];

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($curl);
        return response()->json($result, 200);
    }

    public function cancelar(Request $request)
    {
        try {
            $registro = RegistroTef::findOrFail($request->id);

            $item = TefMultiPlusCard::where('empresa_id', $registro->empresa_id)
            ->where('status', 1)
            ->where('usuario_id', $registro->usuario_id)
            ->first();

            if (!$item) {
                return response()->json(['erro' => 'Configuração TEF não encontrada.'], 400);
            }

            $cnpj = preg_replace('/\D/', '', $item->cnpj);

            $callApi = function ($url, $headers, $method = 'GET', $body = null) {
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_HTTPHEADER => $headers,
                ]);

                if (strtoupper($method) === 'POST') {
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $body ?? '');
                }

                $response = curl_exec($ch);
                curl_close($ch);
                return $response;
            };

            $conteudoAtv = '000-000 = ATV¬001-000 = 1¬999-999 = 0';
            $headersAtv = [
                "Content-Type: application/json",
                "CNPJ: $cnpj",
                "PDV: $item->pdv",
                "TOKEN: $item->token",
                "CONTEUDO: $conteudoAtv",
            ];

            $hashAtv = $callApi("https://api.multipluscard.com.br/api/Servicos/SetVendaTef", $headersAtv, 'POST');
            sleep(2);

            $respAtv = $callApi("https://api.multipluscard.com.br/api/Servicos/GetVendasTef", [
                "Content-Type: application/json",
                "hash: $hashAtv",
                "TOKEN: $item->token",
            ]);

            if (!str_contains($respAtv, 'ATV')) {
                return response()->json(['erro' => 'Pinpad não está ativo!'], 400);
            }

            $conteudoCnc = sprintf(
                '000-000 = CNC¬001-000 = %s¬003-000 = %s¬010-000 = %s¬012-000 = %s¬022-000 = %s¬023-000 = %s¬999-999 = 0',
                $registro->id,
                $registro->valor_total,
                $registro->nome_rede,
                $registro->nsu,
                $registro->data_transacao,
                $registro->hora_transacao
            );

            file_put_contents(public_path('logs_tef/cancelamento_cnc.txt'), $conteudoCnc);

            $headersCnc = [
                "Content-Type: application/json",
                "CNPJ: $cnpj",
                "PDV: $item->pdv",
                "TOKEN: $item->token",
                "CONTEUDO: $conteudoCnc",
            ];

            $hashCnc = $callApi("https://api.multipluscard.com.br/api/Servicos/SetVendaTef", $headersCnc, 'POST');
            sleep(2); 

            $respGet = $callApi("https://api.multipluscard.com.br/api/Servicos/GetVendasTef", [
                "Content-Type: application/json",
                "hash: $hashCnc",
                "TOKEN: $item->token",
            ]);
            file_put_contents(public_path('logs_tef/cancelamento_resultado.txt'), $respGet);


            if (str_contains(strtoupper($respGet), 'APROV')) {
                $conteudoCnf = sprintf(
                    '000-000 = CNF¬001-000 = %s¬010-000 = %s¬012-000 = %s¬999-999 = 0',
                    $registro->id,
                    $registro->nome_rede,
                    $registro->nsu
                );

                $headersCnf = [
                    "Content-Type: application/json",
                    "CNPJ: $cnpj",
                    "PDV: $item->pdv",
                    "TOKEN: $item->token",
                    "CONTEUDO: $conteudoCnf",
                ];

                $respCnf = $callApi("https://api.multipluscard.com.br/api/Servicos/SetVendaTef", $headersCnf, 'POST');
                file_put_contents(public_path('logs_tef/cancelamento_cnf.txt'), $respCnf);
            }


            return response()->json([
                'mensagem' => 'Cancelamento enviado. Consulte o log para detalhes.',
                'hash' => $hashCnc,
            ], 200);


        } catch (\Throwable $e) {
            \Log::error('Erro no cancelamento TEF', ['erro' => $e->getMessage()]);
            return response()->json(['erro' => 'Falha no cancelamento TEF: ' . $e->getMessage()], 500);
        }
    }

    public function consultaCancelamento(Request $request){

        $item = TefMultiPlusCard::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->where('usuario_id', $request->usuario_id)
        ->first();

        $registroTef = RegistroTef::findOrFail($request->id);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.multipluscard.com.br/api/Servicos/GetVendasTef");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $headers = [
            "Content-Type: application/json",
            "Content-Length: 0",
            "hash: $request->hash",
            "TOKEN: $item->token",
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $resp = curl_exec($curl);

        $cancelada = "Cancelado pelo operador";
        if(preg_match("/{$cancelada}/i", $resp)) {
            return response()->json("Cancelado pelo operador", 401);
        }

        $aceita = "Transação Aceita";
        if(preg_match("/{$aceita}/i", $resp)) {
            $registroTef->estado = 'cancelado';
            $registroTef->save();
            return response()->json('Cancelado', 200);

        }
        return response()->json($resp, 200);
    }

    public function imprimir(Request $request)
    {
        $registro = RegistroTef::findOrFail($request->id);

        $item = TefMultiPlusCard::where('empresa_id', $registro->empresa_id)
        ->where('status', 1)
        ->where('usuario_id', $registro->usuario_id)
        ->first();

        if (!$item) {
            return response()->json(['erro' => 'Configuração TEF não encontrada'], 400);
        }

        $cnpj = preg_replace('/\D/', '', $item->cnpj);

        $callApi = function ($url, $headers, $method = 'GET', $body = null) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_HTTPHEADER => $headers,
            ]);

            if (strtoupper($method) === 'POST') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body ?? '');
            }

            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        };

        $conteudo = '000-000 = ATV¬001-000 = 1¬999-999 = 0';
        $headers = [
            "Content-Type: application/json",
            "Content-Length: 0",
            "CNPJ: $cnpj",
            "PDV: $item->pdv",
            "TOKEN: $item->token",
            "CONTEUDO: $conteudo",
        ];

        $hash = $callApi("https://api.multipluscard.com.br/api/Servicos/SetVendaTef", $headers, 'POST');

        sleep(2);
        $resp = $callApi("https://api.multipluscard.com.br/api/Servicos/GetVendasTef", [
            "Content-Type: application/json",
            "Content-Length: 0",
            "hash: $hash",
            "TOKEN: $item->token",
        ]);

        if (!preg_match("/ATV¬001-000/i", $resp)) {
            return response()->json(['erro' => 'Pinpad não está ativo ou falha de comunicação.'], 400);
        }

        $conteudo = sprintf(
            '000-000 = ADM¬000-001 = REIMPRESSAO¬012-000 = %s¬022-000 = %s¬999-999 = 0',
            $registro->nsu,
            $registro->data_transacao
        );

        $headers = [
            "Content-Type: application/json",
            "Content-Length: 0",
            "CNPJ: $cnpj",
            "PDV: $item->pdv",
            "TOKEN: $item->token",
            "CONTEUDO: $conteudo",
        ];

        $hash = $callApi("https://api.multipluscard.com.br/api/Servicos/SetVendaTef", $headers, 'POST');

        $tentativas = 0;
        do {
            sleep(1);
            $resp = $callApi("https://api.multipluscard.com.br/api/Servicos/GetVendasTef", [
                "Content-Type: application/json",
                "Content-Length: 0",
                "hash: $hash",
                "TOKEN: $item->token",
            ]);

            $tentativas++;
        } while (in_array($resp, ['Pendente', 'Processando']) && $tentativas < 10);

        // file_put_contents(public_path('logs_tef/reimpressao.txt'), $resp . " t: " . $tentativas);

        if ($resp === 'Pendente') {
            return response()->json(['erro' => 'Tempo limite atingido aguardando retorno TEF.'], 400);
        }

        if (!preg_match('/029-001\s*=\s*(.*?)¬029-027/', $resp, $match)) {
            return response()->json(['erro' => 'Não foi possível localizar o comprovante na resposta.'], 400);
        }

        $textoBruto = $match[1];
        $linhas = explode('¬', $textoBruto);
        $html = '';

        foreach ($linhas as $linha) {
            if (preg_match('/"([^"]+)"/', $linha, $x)) {
                $html .= htmlspecialchars($x[1]) . '<br>';
            }
        }

        $htmlView = view('tef_registro.pdf', ['pdf' => $html])->render();
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($htmlView);
        $domPdf->set_paper([0, 0, 214, 380]);
        $domPdf->render();
        file_put_contents(public_path('tef_comprovante/comprovante.pdf'), $domPdf->output());

        $conteudo = sprintf(
            '000-000 = CNF¬001-000 = %s¬010-000 = %s¬012-000 = %s¬027-000 = %s¬999-999 = 0',
            $registro->id,
            $registro->nome_rede,
            $registro->nsu,
            $registro->finalizacao
        );

        file_put_contents(public_path('logs_tef/reimpressao_cnf_conteudo.txt'), $conteudo);

        $headers = [
            "Content-Type: application/json",
            "Content-Length: 0",
            "CNPJ: $cnpj",
            "PDV: $item->pdv",
            "TOKEN: $item->token",
            "CONTEUDO: $conteudo",
        ];

        $respCnf = $callApi("https://api.multipluscard.com.br/api/Servicos/SetVendaTef", $headers, 'POST');
        file_put_contents(public_path('logs_tef/reimpressao_cnf.txt'), $respCnf);

        return response()->json("ok", 200);
    }


    private function confirmarVenda($registro)
    {
        try {
            $item = TefMultiPlusCard::where('empresa_id', $registro->empresa_id)
            ->where('status', 1)
            ->where('usuario_id', $registro->usuario_id)
            ->first();

            if (!$item) {
                \Log::warning("Confirmação TEF: Configuração não encontrada para empresa {$registro->empresa_id}");
                return;
            }

            $cnpj = preg_replace('/\D/', '', $item->cnpj);

            $conteudo = sprintf(
                '000-000 = CNF¬001-000 = %s¬010-000 = %s¬012-000 = %s¬027-000 = %s¬999-999 = 0',
                $registro->id,
                $registro->nome_rede,
                $registro->nsu,
                $registro->finalizacao
            );

            file_put_contents(public_path('logs_tef/confirmar_venda_conteudo.txt'), $conteudo);

            $headers = [
                "Content-Type: application/json",
                "Content-Length: 0",
                "CNPJ: $cnpj",
                "PDV: $item->pdv",
                "TOKEN: $item->token",
                "CONTEUDO: $conteudo",
            ];

            $curl = curl_init("https://api.multipluscard.com.br/api/Servicos/SetVendaTef");
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => true,
            ]);

            $resp = curl_exec($curl);
            curl_close($curl);

            file_put_contents(public_path('logs_tef/confirmar_venda.txt'), $resp);

            if ($resp === false) {
                \Log::error('Confirmação TEF: Falha na comunicação CURL.');
                return;
            }

            if (str_contains($resp, '[ERRO]')) {
                \Log::error("Confirmação TEF retornou erro: {$resp}");
                return;
            }

            if (preg_match('/CNF/i', $resp)) {
                \Log::info("Confirmação TEF concluída com sucesso. ID: {$registro->id}");
            } else {
                \Log::warning("Confirmação TEF: resposta inesperada: {$resp}");
            }

        } catch (\Throwable $e) {
            \Log::error('Erro na operação CNF.', ['exception' => $e->getMessage()]);
        }
    }

}
