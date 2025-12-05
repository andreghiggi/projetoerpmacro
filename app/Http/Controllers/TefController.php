<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nfce;
use App\Models\TefMultiPlusCard;
use Dompdf\Dompdf;

class TefController extends Controller
{

    public function imprimir($id){
        $nfce = Nfce::findOrFail($id);

        $registro = $nfce->registroTef;
        if(!$registro){
            echo "Não existe comprovante de TEF!";
            die;
        }

        $item = TefMultiPlusCard::where('empresa_id', $registro->empresa_id)
        ->where('status', 1)
        ->where('usuario_id', $registro->usuario_id)
        ->first();

        if (!$item) {
            echo "Configuração TEF não encontrada";
            die;
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
            echo "Pinpad não está ativo ou falha de comunicação.";
            die;
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
            echo "Tempo limite atingido aguardando retorno TEF";
            die;
        }

        if (!preg_match('/029-001\s*=\s*(.*?)¬029-027/', $resp, $match)) {
            echo "Não foi possível localizar o comprovante na resposta.";
            die;
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
        // file_put_contents(public_path('tef_comprovante/comprovante.pdf'), $domPdf->output());
        return $domPdf->stream('documento.pdf', ['Attachment' => false]);

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

    }

}
