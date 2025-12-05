<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\NotaServico;
use App\Models\ContaReceber;
use App\Models\Reserva;
use App\Models\ConfigGeral;
use App\Models\ItemNotaServico;
use Illuminate\Support\Facades\DB;
use CloudDfe\SdkPHP\Nfse;

class NotaServicoController extends Controller
{

    public function __construct()
    {
        if (!is_dir(public_path('xml_nota_servico'))) {
            mkdir(public_path('xml_nota_servico'), 0777, true);
        }
        if (!is_dir(public_path('xml_nota_servico_cancelada'))) {
            mkdir(public_path('xml_nota_servico_cancelada'), 0777, true);
        }

        $this->middleware('permission:nfse_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:nfse_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:nfse_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:nfse_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $tomador = $request->get('tomador');
        $estado = $request->get('estado');

        $empresa = Empresa::findOrFail($request->empresa_id);
        $data = NotaServico::
        where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($tomador), function ($query) use ($tomador) {
            return $query->where('razao_social', 'like', "%$tomador%");
        })
        ->when($estado != "", function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());

        return view('nota_servico.index', compact('data'));

    }

    public function create(){
        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }

        $config = Empresa::find(request()->empresa_id);
        $novoNumeroNFse = $config->numero_ultima_nfse+1;
        return view('nota_servico.create', compact('novoNumeroNFse'));
    }

    public function edit($id){
        $item = NotaServico::findOrFail($id);

        return view('nota_servico.edit', compact('item'));
    }

    public function store(Request $request){
        try{
            DB::transaction(function () use ($request) {

                $config = Empresa::find($request->empresa_id);

                $totalServico = __convert_value_bd($request->valor_servico);
                $nfse = NotaServico::create([
                    'empresa_id' => $request->empresa_id,
                    'valor_total' => $totalServico,
                    'ambiente' => $config->ambiente,
                    'estado' => 'novo',
                    'serie' => '',
                    'codigo_verificacao' => '',
                    'url_xml' => '',
                    'url_pdf_nfse' => '',
                    'url_pdf_rps' => '',
                    'cliente_id' => $request->cliente_id,
                    'natureza_operacao' => $request->natureza_operacao,
                    'documento' => $request->documento,
                    'razao_social' => $request->razao_social,
                    'im' => $request->im ?? '',
                    'ie' => $request->ie ?? '',
                    'cep' => $request->cep ?? '',
                    'rua' => $request->rua,
                    'numero' => $request->numero,
                    'bairro' => $request->bairro,
                    'complemento' => $request->complemento ?? '',
                    'cidade_id' => $request->cidade_id,
                    'email' => $request->email ?? '',
                    'numero_nfse' => $request->numero_nfse ?? 0,
                    'gerar_conta_receber' => $request->gerar_conta_receber,
                    'telefone' => $request->telefone ?? ''
                ]);

                ItemNotaServico::create([
                    'nota_servico_id' => $nfse->id,
                    'discriminacao' => $request->discriminacao,
                    'valor_servico' => __convert_value_bd($request->valor_servico),
                    'servico_id' => $request->servico_id,
                    'codigo_cnae' => $request->codigo_cnae ?? '',
                    'codigo_servico' => $request->codigo_servico ?? '',
                    'codigo_tributacao_municipio' => $request->codigo_tributacao_municipio ?? '',
                    'exigibilidade_iss' => $request->exigibilidade_iss,
                    'iss_retido' => $request->iss_retido,
                    'data_competencia' => $request->data_competencia ?? null,
                    'estado_local_prestacao_servico' => $request->estado_local_prestacao_servico ?? '',
                    'cidade_local_prestacao_servico' => $request->cidade_local_prestacao_servico ?? '',
                    'valor_deducoes' => $request->valor_deducoes ? __convert_value_bd($request->valor_deducoes) : 0,
                    'desconto_incondicional' => $request->desconto_incondicional ? __convert_value_bd($request->desconto_incondicional) : 0,
                    'desconto_condicional' => $request->desconto_condicional ? __convert_value_bd($request->desconto_condicional) : 0,
                    'outras_retencoes' => $request->outras_retencoes ? __convert_value_bd($request->outras_retencoes) : 0,
                    'aliquota_iss' => $request->aliquota_iss ? __convert_value_bd($request->aliquota_iss) : 0,
                    'aliquota_pis' => $request->aliquota_pis ? __convert_value_bd($request->aliquota_pis) : 0,
                    'aliquota_cofins' => $request->aliquota_cofins ? __convert_value_bd($request->aliquota_cofins) : 0,
                    'aliquota_inss' => $request->aliquota_inss ? __convert_value_bd($request->aliquota_inss) : 0,
                    'aliquota_ir' => $request->aliquota_ir ? __convert_value_bd($request->aliquota_ir) : 0,
                    'aliquota_csll' => $request->aliquota_csll ? __convert_value_bd($request->aliquota_csll) : 0,
                    'intermediador' => $request->intermediador ?? 'n',
                    'documento_intermediador' => $request->documento_intermediador ?? '',
                    'nome_intermediador' => $request->nome_intermediador ?? '',
                    'im_intermediador' => $request->im_intermediador ?? '',
                    'responsavel_retencao_iss' => $request->responsavel_retencao_iss ?? 1,

                ]);

                $config->numero_ultima_nfse = $nfse->numero_nfse;
                $config->save();

                if(isset($request->reserva_id)){
                    $reserva = Reserva::findOrFail($request->reserva_id);
                    $reserva->nfse_id = $nfse->id;
                    $reserva->save();
                }

                if($request->data_vencimento){
                    $caixa = __isCaixaAberto();

                    $conta = ContaReceber::create([
                        'empresa_id' => $request->empresa_id,
                        'cliente_id' => $request->cliente_id,
                        'valor_integral' => $totalServico,
                        'data_vencimento' => $request->data_vencimento,
                        'local_id' => $caixa->local_id,
                    ]);

                    $nfse->data_vencimento = $request->data_vencimento;
                    $nfse->conta_receber_id = $conta->id;
                    $nfse->save();
                }
                session()->flash("flash_success", "NFSe criada com sucesso!");

            });
return redirect()->route('nota-servico.index');
}catch (\Exception $e) {
            // echo $e->getMessage();
    session()->flash("flash_error", 'Algo deu errado: ' . $e->getMessage());
    return redirect()->back();
}
}

public function update(Request $request, $id){
    try{
        $item = NotaServico::findOrFail($id);
        DB::transaction(function () use ($request, $item) {

            $totalServico = __convert_value_bd($request->valor_servico);
            $config = Empresa::find($request->empresa_id);

            $item->update([
                'empresa_id' => $request->empresa_id,
                'valor_total' => $totalServico,
                'ambiente' => $config->ambiente,
                'serie' => '',
                'codigo_verificacao' => '',
                'url_xml' => '',
                'url_pdf_nfse' => '',
                'url_pdf_rps' => '',
                'cliente_id' => $request->cliente_id,
                'natureza_operacao' => $request->natureza_operacao,
                'documento' => $request->documento,
                'gerar_conta_receber' => $request->gerar_conta_receber,
                'razao_social' => $request->razao_social,
                'im' => $request->im ?? '',
                'ie' => $request->ie ?? '',
                'cep' => $request->cep ?? '',
                'rua' => $request->rua,
                'numero' => $request->numero,
                'numero_nfse' => $request->numero_nfse,
                'bairro' => $request->bairro,
                'complemento' => $request->complemento ?? '',
                'cidade_id' => $request->cidade_id,
                'email' => $request->email ?? '',
                'telefone' => $request->telefone ?? ''
            ]);

            if($item->servico){
                $item->servico->delete();
            }

            ItemNotaServico::create([
                'nota_servico_id' => $item->id,
                'discriminacao' => $request->discriminacao,
                'valor_servico' => __convert_value_bd($request->valor_servico),
                'servico_id' => $request->servico_id,
                'codigo_cnae' => $request->codigo_cnae ?? '',
                'codigo_servico' => $request->codigo_servico ?? '',
                'codigo_tributacao_municipio' => $request->codigo_tributacao_municipio ?? '',
                'exigibilidade_iss' => $request->exigibilidade_iss,
                'iss_retido' => $request->iss_retido,
                'data_competencia' => $request->data_competencia ?? null,
                'estado_local_prestacao_servico' => $request->estado_local_prestacao_servico ?? '',
                'cidade_local_prestacao_servico' => $request->cidade_local_prestacao_servico ?? '',
                'valor_deducoes' => $request->valor_deducoes ? __convert_value_bd($request->valor_deducoes) : 0,
                'desconto_incondicional' => $request->desconto_incondicional ? __convert_value_bd($request->desconto_incondicional) : 0,
                'desconto_condicional' => $request->desconto_condicional ? __convert_value_bd($request->desconto_condicional) : 0,
                'outras_retencoes' => $request->outras_retencoes ? __convert_value_bd($request->outras_retencoes) : 0,
                'aliquota_iss' => $request->aliquota_iss ? __convert_value_bd($request->aliquota_iss) : 0,
                'aliquota_pis' => $request->aliquota_pis ? __convert_value_bd($request->aliquota_pis) : 0,
                'aliquota_cofins' => $request->aliquota_cofins ? __convert_value_bd($request->aliquota_cofins) : 0,
                'aliquota_inss' => $request->aliquota_inss ? __convert_value_bd($request->aliquota_inss) : 0,
                'aliquota_ir' => $request->aliquota_ir ? __convert_value_bd($request->aliquota_ir) : 0,
                'aliquota_csll' => $request->aliquota_csll ? __convert_value_bd($request->aliquota_csll) : 0,
                'intermediador' => $request->intermediador ?? 'n',
                'documento_intermediador' => $request->documento_intermediador ?? '',
                'nome_intermediador' => $request->nome_intermediador ?? '',
                'im_intermediador' => $request->im_intermediador ?? '',
                'responsavel_retencao_iss' => $request->responsavel_retencao_iss ?? 1,

            ]);

            if($request->data_vencimento){

                $caixa = __isCaixaAberto();

                if($item->contaReceber){
                    $item->contaReceber->delete();
                }

                $conta = ContaReceber::create([
                    'empresa_id' => $request->empresa_id,
                    'cliente_id' => $request->cliente_id,
                    'valor_integral' => $totalServico,
                    'data_vencimento' => $request->data_vencimento,
                    'local_id' => $caixa->local_id,
                ]);

                $item->data_vencimento = $request->data_vencimento;
                $item->conta_receber_id = $conta->id;
                $item->save();
            }
            session()->flash("flash_success", "NFSe atualizada com sucesso!");

        });
return redirect()->route('nota-servico.index');
}catch (\Exception $e) {
            // echo $e->getMessage();
    session()->flash("flash_error", 'Algo deu errado: ' . $e->getMessage());
    return redirect()->back();
}
}

public function destroy($id)
{
    $item = NotaServico::findOrFail($id);
    try {
        if($item->servico){
            $item->servico->delete();
        }
        $item->delete();
        session()->flash("flash_success", "NFSe removida!");
    } catch (\Exception $e) {
            // echo $e->getLine();
            // die;
        session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
    }
    return redirect()->route('nota-servico.index');
}

public function imprimir($id){
    $item = NotaServico::findOrFail($id);
    return redirect($item->url_pdf_nfse);
}

public function preview($id){
    $item = NotaServico::findOrFail($id);
    
    if(!is_dir(public_path('nfse_temp'))){
        mkdir(public_path('nfse_temp'), 0777, true);
    }

    $empresa = Empresa::findOrFail($item->empresa_id);
    $params = [
        'token' => $empresa->token_nfse,
        'ambiente' => Nfse::AMBIENTE_PRODUCAO,
        'timeout' => 60,
        'port' => 443,
        'http_version' => CURL_HTTP_VERSION_NONE,
        'debug' => false,
        'options' => [
            'debug' => false,
            'timeout' => 60,
            'port' => 443,
            'http_version' => CURL_HTTP_VERSION_NONE
        ]
    ];
    $nfse = new Nfse($params);
    $servico = $item->servico;
    try {

        $doc = preg_replace('/[^0-9]/', '', $item->documento);
        $im = preg_replace('/[^0-9]/', '', $item->im);
        $ie = preg_replace('/[^0-9]/', '', $item->ie);

        $config = ConfigGeral::where('empresa_id', $item->empresa_id)
        ->first();

        $regimeTributacao = null;
        if($config != null){
            $regimeTributacao = $config->regime_nfse;
        }

        $valorLiquido = $servico->valor_servico - $servico->desconto_incondicional - $servico->desconto_condicional;

        $payload = [
            "numero" => $item->numero_nfse,
            "serie" => $empresa->numero_serie_nfse,
            "tipo" => "1",
            "status" => "1",
            "data_emissao" => date("Y-m-d\TH:i:sP"),
            "data_competencia" => date("Y-m-d\TH:i:sP"),
            "regime_tributacao" => $regimeTributacao,
            
            "tomador" => [
                "cnpj" => strlen($doc) == 14 ? $doc : null,
                "cpf" => strlen($doc) == 11 ? $doc : null,
                "im" => $im ? $im : null,
                "ie" => $ie ? $ie : null,
                "razao_social" => $item->razao_social,
                "endereco" => [
                    "logradouro" => $this->retiraAcentos($item->rua),
                    "numero" => $this->retiraAcentos($item->numero),
                    "complemento" => $this->retiraAcentos($item->complemento),
                    "bairro" => $this->retiraAcentos($item->bairro),
                    "codigo_municipio" => $item->cidade->codigo,
                    "uf" => $item->cidade->uf,
                    "cep" => preg_replace('/[^0-9]/', '', $item->cep)
                ]
            ],
            "servico" => [
                "codigo" => $servico->codigo_servico,
                "codigo_tributacao_municipio" => $servico->codigo_tributacao_municipio,
                "discriminacao" => $this->retiraAcentos($servico->discriminacao),
                "codigo_municipio" => $empresa->cidade->codigo,
                "valor_servicos" => $servico->valor_servico,
                "valor_pis" => $servico->aliquota_pis,
                "valor_aliquota" => $servico->aliquota_iss,
                // "valor_liquido" => $servico->valor_servico - $servico->desconto_incondicional - $servico->desconto_condicional,
                "valor_liquido" => $valorLiquido - ($servico->iss_retido == 1 ? $servico->valor_servico * ($servico->aliquota_iss/100) : 0),
                "codigo_cnae" => $servico->codigo_cnae,
                "valor_iss" => $servico->iss_retido == 1 ? $servico->valor_servico * ($servico->aliquota_iss/100) : null,
                "valor_iss_retido" => $servico->iss_retido == 1 ? $servico->valor_servico * ($servico->aliquota_iss/100) : null,
                "itens" => [
                    [
                        "codigo" => $servico->codigo_servico,
                        "codigo_tributacao_municipio" => $servico->codigo_tributacao_municipio,
                        "discriminacao" => $this->retiraAcentos($servico->discriminacao),
                        "codigo_municipio" => $empresa->cidade->codigo,
                        "valor_servicos" => $servico->valor_servico,
                        "valor_pis" => $servico->aliquota_pis,
                        "valor_aliquota" => $servico->aliquota_iss,
                        "valor_liquido" => $valorLiquido - ($servico->iss_retido == 1 ? $servico->valor_servico * ($servico->aliquota_iss/100) : 0),
                        "codigo_cnae" => $servico->codigo_cnae,
                        "valor_iss" => $servico->iss_retido == 1 ? $servico->valor_servico * ($servico->aliquota_iss/100) : null,
                        "valor_iss_retido" => $servico->iss_retido == 1 ? $servico->valor_servico * ($servico->aliquota_iss/100) : null,
                    ]
                ]
            ]
        ];
        // dd($payload);
        if($servico->desconto_incondicional > 0){
            $payload['servico']['valor_desconto_incondicionado'] = $servico->desconto_incondicional;
            $payload['servico']['itens'][0]['valor_desconto_incondicionado'] = $servico->desconto_incondicional;
        }

        if($servico->desconto_condicional > 0){
            $payload['servico']['valor_desconto_condicionado'] = $servico->desconto_condicional;
            $payload['servico']['itens'][0]['valor_desconto_condicionado'] = $servico->desconto_condicional;
        }

        if($servico->iss_retido == 1){
            $payload['servico']['iss_retido'] = true;
            $payload['servico']['responsavel_retencao'] = $servico->responsavel_retencao_iss;
        }
        // dd($payload);
        // return response()->json($payload, 404);
        $rute = "nfse_temp/temp.pdf";
        $resp = $nfse->preview($payload);
        $resp = json_decode($resp);
        if(isset($resp->pdf)){
            $pdf_b64 = base64_decode($resp->pdf);

            if(file_put_contents($rute, $pdf_b64)){
                header("Content-type: application/pdf");
                echo $pdf_b64;
            }
        }else{
            dd($resp);
        }

    }catch (\Exception $e) {
        session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        return redirect()->back();
    }

}


private function retiraAcentos($texto){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/", "/(ç)/", "/(&)/"),explode(" ","a A e E i I o O u U n N c e"),$texto);
}

}
