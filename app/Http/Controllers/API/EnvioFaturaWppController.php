<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\Nfce;
use App\Models\Cliente;
use App\Models\ConfigGeral;
use App\Models\Empresa;
use Illuminate\Support\Str;
use Dompdf\Dompdf;
use NFePHP\DA\NFe\Danfe;
use NFePHP\DA\NFe\Danfce;

class EnvioFaturaWppController extends Controller
{
    public function index(Request $request){
        $id = $request->id;
        $tipo = $request->tipo;

        $item = null;
        if($tipo == 'nfe'){
            $item = Nfe::findOrFail($id);
        }elseif($tipo == 'nfce'){
            $item = Nfce::findOrFail($id);
        }

        $config = ConfigGeral::where('empresa_id', $item->empresa_id)->first();

        if($config){
            $telefone = '';
            if($item->cliente){
                $telefone = $item->cliente->telefone;
            }

            $mensagem = $this->__replaceText($item, $config->mensagem_wpp_link);

            $data = [
                'telefone' => $telefone,
                'mensagem' => $mensagem,
                'cliente_info' => $item->cliente ? $item->cliente->info : '',
                'enviar_danfe_wpp_link' => $config->enviar_danfe_wpp_link,
                'enviar_xml_wpp_link' => $config->enviar_xml_wpp_link,
                'enviar_pedido_a4_wpp_link' => $config->enviar_pedido_a4_wpp_link,
            ];

            return response()->json($data, 200);
        }
    }

    private function __replaceText($venda, $mensagem){
        $texto = str_replace("[valor_total]", __moeda($venda->total), $mensagem);
        $texto = str_replace("[numero_pedido]", $venda->numero_sequencial, $texto);   

        $texto = str_replace("[numero_nfe]", $venda->numero ? $venda->numero : '', $texto);   
        $texto = str_replace("[chave]", $venda->chave ? $venda->chave : '', $texto);   

        $texto = str_replace("[nome_cliente]", $venda->cliente ? $venda->cliente->razao_social : '', $texto);

        return $texto;
    }

    public function createFiles(Request $request){
        $id = $request->id;
        $tipo = $request->tipo;

        $item = null;
        if($tipo == 'nfe'){
            $item = Nfe::findOrFail($id);
        }elseif($tipo == 'nfce'){
            $item = Nfce::findOrFail($id);
        }

        if($item->cliente){
            $cliente = $item->cliente;
            $cliente->telefone = $request->telefone;
            $cliente->save();
        }

        $data = [];

        $mensagem = $request->mensagem . "%0A";
        $mensagem = str_replace('\n', "%0A", $mensagem);

        if($request->enviar_pedido_a4 == true){
            $pedidoA4 = $this->criaPedidoA4($item, $tipo);
            $data['pedido_a4_link'] = $pedidoA4;
            $mensagem .= "*Link do pedido* " . $pedidoA4 . "%0A";
        }

        if($request->enviar_xml == true && $item->estado == 'aprovado'){
            $xml = $this->criaXml($item, $tipo);
            $data['xml_link'] = $xml;
            $mensagem .= "*Link do xml* " . $xml . "%0A";
        }

        if($request->enviar_danfe == true && $item->estado == 'aprovado'){
            $danfe = $this->criaDanfe($item, $tipo);
            $data['danfe'] = $danfe;
            $mensagem .= "*Link da DANFE* " . $danfe . "%0A";
        }

        return response()->json($mensagem, 200);
    }

    private function criaPedidoA4($item, $tipo){
        if($tipo == 'nfe'){
            return $this->imprimirA4Nfe($item);
        }else{
            return $this->imprimirA4Nfce($item);
        }
    }

    private function criaDanfe($item, $tipo){
        if($tipo == 'nfe'){
            return $this->imprimirDanfeNfe($item);
        }else{
            return $this->imprimirDanfceNfce($item);
        }
    }

    public function imprimirDanfceNfce($item)
    {

        $empresa = $item->empresa;
        if (file_exists(public_path('xml_nfce/') . $item->chave . '.xml')) {
            $xml = file_get_contents(public_path('xml_nfce/') . $item->chave . '.xml');

            $danfe = new Danfe($xml);
            if($empresa->logo){
                $logo = 'data://text/plain;base64,'. base64_encode(file_get_contents(public_path('/uploads/logos/') . $empresa->logo));
                $danfe->logoParameters($logo, 'L');
            }
            $pdf = $danfe->render();

            if (!is_dir(public_path('wpp_files'))) {
                mkdir(public_path('wpp_files'), 0777, true);
            }
            $fileName = $item->chave.".pdf";
            file_put_contents(public_path('wpp_files/') . $fileName , $pdf);
            return env("APP_URL"). "/wpp_files/".$fileName;
        } 
    }

    public function imprimirDanfeNfe($item)
    {

        $empresa = $item->empresa;
        if (file_exists(public_path('xml_nfe/') . $item->chave . '.xml')) {
            $xml = file_get_contents(public_path('xml_nfe/') . $item->chave . '.xml');

            $danfe = new Danfe($xml);
            if($empresa->logo){
                $logo = 'data://text/plain;base64,'. base64_encode(file_get_contents(public_path('/uploads/logos/') . 
                    $empresa->logo));
                $danfe->logoParameters($logo, 'L');
            }
            $danfe->exibirTextoFatura = 1;
            $pdf = $danfe->render();

            if (!is_dir(public_path('wpp_files'))) {
                mkdir(public_path('wpp_files'), 0777, true);
            }
            $fileName = $item->chave.".pdf";
            file_put_contents(public_path('wpp_files/') . $fileName , $pdf);
            return env("APP_URL"). "/wpp_files/".$fileName;
        } 
    }

    public function imprimirA4Nfe($item)
    {

        $config = Empresa::where('id', $item->empresa_id)->first();

        $config = __objetoParaEmissao($config, $item->local_id);
        $configGeral = ConfigGeral::where('empresa_id', $item->empresa_id)->first();

        if($configGeral && $configGeral->mensagem_padrao_impressao_venda){
            $item->observacao .= " " . $configGeral->mensagem_padrao_impressao_venda;
        }

        $p = view('nfe.imprimir', compact('config', 'item', 'configGeral'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $domPdf->setPaper("A4");
        $domPdf->render();

        if (!is_dir(public_path('wpp_files'))) {
            mkdir(public_path('wpp_files'), 0777, true);
        }
        $fileName = Str::random(50).".pdf";
        $dir = public_path('wpp_files/') . $fileName;
        file_put_contents($dir, $domPdf->output());
        return env("APP_URL")."/wpp_files/".$fileName;
    }

    public function imprimirA4Nfce($item)
    {

        $config = Empresa::where('id', $item->empresa_id)->first();
        $configGeral = ConfigGeral::where('empresa_id', $item->empresa_id)->first();

        $p = view('front_box.imprimir_a4', compact('config', 'item', 'configGeral'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $domPdf->setPaper("A4");
        $domPdf->render();

        if (!is_dir(public_path('wpp_files'))) {
            mkdir(public_path('wpp_files'), 0777, true);
        }
        $fileName = Str::random(50).".pdf";
        $dir = public_path('wpp_files/') . $fileName;
        file_put_contents($dir, $domPdf->output());
        return env("APP_URL")."/wpp_files/".$fileName;
    }

    private function criaXml($item, $tipo){
        if (!is_dir(public_path('wpp_files'))) {
            mkdir(public_path('wpp_files'), 0777, true);
        }
        if($tipo == 'nfe'){
            if (file_exists(public_path('xml_nfe/') . $item->chave . '.xml')) {
                // $origem = public_path('xml_nfe/') . $item->chave . '.xml';
                // $destinoDir = public_path('wpp_files');
                // $destino = $destinoDir . '/' . $item->chave . '.xml';
                // copy($origem, $destino);
                return env("APP_URL")."/xml-download/". $item->chave;
            }
        }else{
            if (file_exists(public_path('xml_nfce/') . $item->chave . '.xml')) {
                // $origem = public_path('xml_nfce/') . $item->chave . '.xml';
                // $destinoDir = public_path('wpp_files');
                // $destino = $destinoDir . '/' . $item->chave . '.xml';
                // copy($origem, $destino);
                return env("APP_URL")."/xml-download/". $item->chave;
            }
        }
    }
}
