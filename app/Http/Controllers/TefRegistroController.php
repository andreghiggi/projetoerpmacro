<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistroTef;
use App\Models\TefMultiPlusCard;
use App\Models\UsuarioEmpresa;
use App\Models\User;
use Dompdf\Dompdf;

class TefRegistroController extends Controller
{
    public function index(Request $request){
        $data = RegistroTef::where('empresa_id', $request->empresa_id)
        ->when($request->usuario_id, function ($q) use ($request) {
            return $q->where('usuario_id', $request->usuario_id);
        })
        ->where('nfce_id', '!=', null)
        ->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());

        $usuarios = User::where('usuario_empresas.empresa_id', $request->empresa_id)
        ->select('users.*')
        ->join('usuario_empresas', 'users.id', '=', 'usuario_empresas.usuario_id')
        ->get();

        // $str = '2647236 - 000-000 = ADM¬
        // 000-001 = REIMPRESSAO¬
        // 001-000 = 2¬
        // 009-000 = 0¬
        // 011-000 = 00¬
        // 012-000 = 044007¬
        // 022-000 = 06112024¬
        // 028-000 = 27¬
        // 029-001 = "            REIMPRESSAO"¬
        // 029-002 = "VENDA EM AMBIENTE DE HOMOLOGACAO"¬
        // 029-003 = "******NÃO USAR EM CLIENTES******"¬
        // 029-004 = " "¬
        // 029-005 = "                DEBITO"¬
        // 029-006 = "                 REDE                 "¬
        // 029-007 = "           VISA ELECTRON           C"¬
        // 029-008 = "COMPROV: 354197236 VALOR      0,20"¬
        // 029-009 = "MULTIPLUSCARD           06.11.24-18:49"¬
        // 029-010 = "CNPJ/CPF:05.625.872/0001-69           "¬
        // 029-011 = "CIDADE-UF:PRESIDENTE PRU-SP           "¬
        // 029-012 = "CARTAO: XXXXXXXXXXXX1598              "¬
        // 029-013 = "AUTORIZACAO: 636861                   "¬
        // 029-014 = "ARQC:A75B6084259D6C7C"¬
        // 029-015 = "AID: A0000000032010                   "¬
        // 029-016 = "    TRANSACAO AUTORIZADA MEDIANTE     "¬
        // 029-017 = "    USO DE SENHA PESSOAL.             "¬
        // 029-018 = " "¬
        // 029-019 = "                                      "¬
        // 029-020 = "                                  "¬
        // 029-021 = "CONTROLE 11640704002  TEF"¬
        // 029-022 = " "¬
        // 029-023 = "CUPOM FISCAL: 1     PDV: 044"¬
        // 029-024 = "CNPJ EMITENTE: 51.403.129/0001-81"¬
        // 029-025 = "06/11/2024  18:49:22  DEBITO A VISTA"¬
        // 029-026 = "            REIMPRESSAO"¬
        // 029-027 = " "¬
        // 030-000 = Transação Aceita.¬
        // 800-000 = v1.416.229¬
        // 999-999 = 0';

        // $str = explode("029-001 =", $str);
        // $str = $str[1];
        // $str = explode("029-027", $str);
        // $str = $str[0];
        // // $str = str_replace("¬", "", $str);
        // $str = explode("¬", $str);


        // $pdf = "";
        // foreach($str as $s){
        //     $s = explode('"', $s);
        //     if(isset($s[1])){
        //         $pdf .= $s[1] . "<br>";
        //     }
        // }

        // $p = view('tef_registro.pdf', compact('pdf'));
        // $domPdf = new Dompdf(["enable_remote" => true]);
        // $domPdf->loadHtml($p);

        // $pdf = ob_get_clean();

        // $domPdf->set_paper(array(0,0,214,380));
        // $domPdf->render();
        // $domPdf->stream("Comprovante de sangria.pdf", array("Attachment" => false));

        return view('tef_registro.index', compact('data', 'usuarios'));
    }

    public function destroy($id){

        $registro = RegistroTef::findOrFail($id);

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
        $conteudo = '000-000 = CNC¬001-000 = '.$registro->id.'¬003-000 = '.$registro->valor_total.'¬010-000 = '.$registro->nome_rede.'¬012-000 = '.$registro->nsu.'¬022-000 = '.$registro->data_transacao.'¬024-000 = '.$registro->hora_transacao;

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
        if($resp == 'Pendente'){
            session()->flash("flash_warning", "Pendente!");
        }else{
            // dd($resp);
            session()->flash("flash_success", "Evento registrado!");
        }
        return redirect()->back();
    }
}
