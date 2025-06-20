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

        // $resp = '000-000 = CRT¬001-000 = 1¬002-000 = 1¬003-000 = 010¬004-000 = 0¬009-000 = 0¬010-000 = PAGSEGURO¬010-001 = 08561701000101¬010-002 = 999¬011-000 = 20¬012-000 = 038002¬013-000 = VY39PW¬015-000 = 1309204008¬016-000 = 1309204008¬017-000 = 0¬018-000 = 01¬022-000 = 13092024¬023-000 = 204003¬027-000 = 09330208011204008¬028-000 = 27¬029-001 = "VENDA EM AMBIENTE DE HOMOLOGACAO"¬029-002 = "******NÃO USAR EM CLIENTES******"¬029-003 = ""¬029-004 = "               PAGBANK                "¬029-005 = "                 VISA                 "¬029-006 = " "¬029-007 = "CARTAO: 417402XXXXXX0653"¬029-008 = "1A VIA CLIENTE  DATA:13/09/24 20:40:06"¬029-009 = "AUTO: VY39PW"¬029-010 = "CV: 623500000"¬029-011 = " "¬029-012 = "COMPRA DEBITO"¬029-013 = "VALOR TOTAL: R0,10"¬029-014 = " "¬029-015 = "AID: A0000000032010"¬029-016 = "ARQC: 7B7F8F85EAD4BFD1"¬029-017 = "LABEL: VISA DEBITO"¬029-018 = "--------------------------------------"¬029-019 = "MULTIPLUS PAY"¬029-020 = "RUA DOZE DE OUTUBRO 623"¬029-021 = "PRESIDENTE PR - SP"¬029-022 = "CNPJ: 45.314.249/0001-00"¬029-023 = "CONTROLE 09330208011  TEF"¬029-024 = " "¬029-025 = "CUPOM FISCAL: 1     PDV: 038"¬029-026 = "CNPJ EMITENTE: 51.403.129/0001-81"¬029-027 = "13/09/2024  20:40:08  DEBITO A VISTA"¬030-000 = Transação Aceita.¬040-000 = ELECTRON¬170-000 = 000623500000¬200-000 = 000¬200-001 = 000¬740-000 = 417402******0653¬741-000 = PAYWAVE/VISA¬742-000 = 260430¬743-000 = 0¬800-001 = 1¬800-007 = 09330208011¬800-000 = v1.416.216¬999-999 = 0';

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
                'hash' => $request->hash,
                'estado' => 'aprovado',
                'usuario_id' => $request->usuario_id
            ];
            $registro = RegistroTef::create($dataRegistro);
            $this->confirmarVenda($registro);
            // return response()->json($dataExplode, 200);
            return response()->json("Transação Aceita", 200);
        }
        return response()->json($resp, 200);
    }

    public function consultaCancelamento(Request $request){

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

        // $resp = '000-000 = CRT¬001-000 = 1¬003-000 = 010¬009-000 = 255¬010-000 = PAGSEGURO¬012-000 = 038002¬022-000 = 13092024¬024-000 = 204003¬028-000 = 0¬030-000 = Cancelado pelo operador.¬800-000 = v1.416.218¬999-999 = 0;
        $cancelada = "Cancelado pelo operador";
        if(preg_match("/{$cancelada}/i", $resp)) {
            return response()->json("Cancelado pelo operador", 401);
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

    public function cancelar(Request $request){

        $registro = RegistroTef::findOrFail($request->id);

        $item = TefMultiPlusCard::where('empresa_id', $registro->empresa_id)
        ->where('status', 1)
        ->where('usuario_id', $registro->usuario_id)
        ->first();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.multipluscard.com.br/api/Servicos/SetVendaTef");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');

        $cnpj = preg_replace('/[^0-9]/', '', $item->cnpj);
        $conteudo = '000-000 = CNC¬001-000 = '.$registro->id.'¬003-000 = '.$registro->valor_total.'¬010-000 = '.$registro->nome_rede.'¬012-000 = '.$registro->nsu.'¬022-000 = '.$registro->data_transacao.'¬023-000 = '.$registro->hora_transacao.'¬999-999 = 0';

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

    public function imprimir(Request $request){

        $registro = RegistroTef::findOrFail($request->id);

        $item = TefMultiPlusCard::where('empresa_id', $registro->empresa_id)
        ->where('status', 1)
        ->where('usuario_id', $registro->usuario_id)
        ->first();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.multipluscard.com.br/api/Servicos/SetVendaTef");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');

        $cnpj = preg_replace('/[^0-9]/', '', $item->cnpj);
        $conteudo = '000-000 = ADM¬000-001 = REIMPRESSAO¬001-000 = '.$registro->id.'¬012-000 = '.$registro->nsu.'¬022-000 = '.$registro->data_transacao.'¬999-999 = 0';

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
        // $result = "2639647";
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
        if($resp != 'Pendente'){
            $str = explode("029-001 =", $resp);
            $str = $str[1];
            $str = explode("029-027", $str);
            $str = $str[0];
            $str = explode("¬", $str);
            $pdf = "";
            foreach($str as $s){
                $s = explode('"', $s);
                if(isset($s[1])){
                    $pdf .= $s[1] . "<br>";
                }
            }

            $p = view('tef_registro.pdf', compact('pdf'));
            $domPdf = new Dompdf(["enable_remote" => true]);
            $domPdf->loadHtml($p);

            $pdf = ob_get_clean();

            $domPdf->set_paper(array(0,0,214,380));
            $domPdf->render();
            file_put_contents(public_path('tef_comprovante/') . 'comprovante.pdf', $domPdf->output());
            return response()->json($pdf, 200);

        }
        return response()->json($resp, 200);

    }

    private function confirmarVenda($registro){
        try {

            $item = TefMultiPlusCard::where('empresa_id', $registro->empresa_id)
            ->where('status', 1)
            ->where('usuario_id', $registro->usuario_id)
            ->first();

            $conteudo = '000-000 = CRT¬001-000 = '.$registro->id.'¬010-000 = '.$registro->nome_rede.'¬012-000 = '.$registro->nsu.'¬027-000 = ' . $registro->hora_transacao. '¬999-999 = 0';

            $contentLength = strlen($conteudo);

            $headers = [
                "Content-Type: application/json",
                "Content-Length: $contentLength",
                "CNPJ: $cnpj",
                "PDV: $item->pdv",
                "TOKEN: $item->token",
            ];

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL,
                "https://api.multipluscard.com.br/api/Servicos/SetVendaTef");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $conteudo);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($curl);

        }catch(\Exception $e){
            \Log::error('Erro na operação CNF.', ['exception' => $e->getMessage()]);
        }
        
    }
}
