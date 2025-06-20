<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Ciot;
use App\Models\CTeDescarga;
use App\Models\Empresa;
use App\Models\InfoDescarga;
use App\Models\LacreTransporte;
use App\Models\LacreUnidadeCarga;
use App\Models\Mdfe;
use App\Models\MunicipioCarregamento;
use App\Models\Nfe;
use App\Models\NFeDescarga;
use App\Models\Percurso;
use App\Models\UnidadeCarga;
use App\Models\ValePedagio;
use App\Models\Veiculo;
use App\Services\MDFeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use NFePHP\DA\MDFe\Daevento;
use NFePHP\DA\MDFe\Damdfe;
use Symfony\Polyfill\Intl\Idn\Info;

class MdfeController extends Controller
{
    public function __construct()
    {
        if (!is_dir(public_path('xml_mdfe'))) {
            mkdir(public_path('xml_mdfe'), 0777, true);
        }
        if (!is_dir(public_path('xml_mdfe_cancelada'))) {
            mkdir(public_path('xml_mdfe_cancelada'), 0777, true);
        }
        if (!is_dir(public_path('xml_mdfe_correcao'))) {
            mkdir(public_path('xml_mdfe_correcao'), 0777, true);
        }

        $this->middleware('permission:mdfe_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:mdfe_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:mdfe_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:mdfe_delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $estado = $request->get('estado');
        $local_id = $request->get('local_id');

        $data = Mdfe::where('empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($estado != "", function ($query) use ($estado) {
            return $query->where('estado_emissao', $estado);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(env("PAGINACAO"));

        return view('mdfe.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $veiculos = Veiculo::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        $cidades = Cidade::all();
        $empresa = Empresa::findOrFail(request()->empresa_id);

        $numeroMDFe = Mdfe::lastNumero($empresa);

        return view('mdfe.create', compact('veiculos', 'cidades', 'numeroMDFe'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $this->_validate($request);
        try {
            DB::transaction(function () use ($request) {
                $request->merge([
                    'seguradora_nome' => $request->seguradora_nome ?? '',
                    'seguradora_cnpj' => $request->seguradora_cnpj ?? '',
                    'numero_apolice' => $request->numero_apolice ?? '',
                    'numero_averbacao' => $request->numero_averbacao ?? '',
                    'numero_compra' => $request->numero_compra ?? 0,
                    'valor' => $request->valor ?? 0,
                    'encerrado' => false,
                    'estado_emissao' => 'novo',
                    'chave' => '',
                    'seg_cod_barras' => '',
                    'protocolo' => '',
                    'valor_carga' => __convert_value_bd($request->valor_carga),
                    'latitude_carregamento' => $request->latitude_carregamento ?? '',
                    'longitude_carregamento' => $request->longitude_carregamento ?? '',
                    'cep_descarrega' => $request->cep_descarrega ?? '',
                    'latitude_descarregamento' => $request->latitude_descarregamento ?? '',
                    'longitude_descarregamento' => $request->longitude_descarregamento ?? '',
                    'quantidade_rateio' => __convert_value_bd($request->quantidade_rateio),
                    'quantidade_rateio_carga' => __convert_value_bd($request->quantidade_rateio_carga),
                    'quantidade_carga' => $request->quantidade_carga,
                    'produto_pred_nome' => $request->produto_pred_nome ?? '',
                    'produto_pred_ncm' => preg_replace('/[^0-9]/', '', $request->produto_pred_ncm ?? ''),
                    'produto_pred_cod_barras' => $request->produto_pred_cod_barras ?? '',
                    'cep_carrega' => $request->cep_carrega ?? '',
                    'tp_carga' => $request->tp_carga ?? '',
                    'info_complementar' => $request->info_complementar ?? '',
                    'info_adicional_fisco' => $request->info_adicional_fisco ?? '',
                    'filial_id' => $request->filial_id != -1 ? $request->filial_id : null
                ]);

                $mdfe = Mdfe::create($request->all());

                for ($i = 0; $i < sizeof($request->municipiosCarregamento); $i++) {
                    MunicipioCarregamento::create([
                        'mdfe_id' => $mdfe->id,
                        'cidade_id' => $request->municipiosCarregamento[$i]
                    ]);
                }

                for ($i = 0; $i < sizeof($request->codigo_ciot); $i++) {
                    if ($request->codigo_ciot[$i] != null) {
                        Ciot::create([
                            'mdfe_id' => $mdfe->id,
                            'cpf_cnpj' => $request->cpf_cnpj[$i],
                            'codigo' => $request->codigo_ciot[$i]
                        ]);
                    }
                }

                for ($i = 0; $i < sizeof($request->uf); $i++) {
                    if ($request->uf[$i]) {
                        Percurso::create([
                            'uf' => $request->uf[$i],
                            'mdfe_id' => $mdfe->id
                        ]);
                    }
                }

                for ($i = 0; $i < sizeof($request->cnpj_fornecedor); $i++) {
                    if ($request->cnpj_fornecedor[$i] != null) {
                        ValePedagio::create([
                            'mdfe_id' => $mdfe->id,
                            'cnpj_fornecedor' => $request->cnpj_fornecedor[$i],
                            'cnpj_fornecedor_pagador' => $request->cnpj_fornecedor_pagador[$i],
                            'numero_compra' => $request->numero_compra[$i],
                            'valor' => $request->valor_pedagio[$i]
                        ]);
                    }
                }

                for ($i = 0; $i < sizeof($request->tp_und_transp_row); $i++) {
                    $info = InfoDescarga::create([
                        'mdfe_id' => $mdfe->id,
                        'tp_unid_transp' => $request->tp_und_transp_row[$i],
                        'id_unid_transp' => $request->id_und_transp_row[$i],
                        'quantidade_rateio' => __convert_value_bd($request->quantidade_rateio_row[$i]),
                        'cidade_id' => $request->municipio_descarregamento_row[$i]
                    ]);

                    if ($request->chave_cte_row[$i]) {
                        CTeDescarga::create([
                            'info_id' => $info->id,
                            'chave' => $request->chave_cte_row[$i],
                            'seg_cod_barras' => ''
                        ]);
                    }

                    if ($request->chave_nfe_row[$i]) {
                        NFeDescarga::create([
                            'info_id' => $info->id,
                            'chave' =>  $request->chave_nfe_row[$i],
                            'seg_cod_barras' => ''
                        ]);
                    }

                    $lacres = json_decode($request->lacres_transporte_row[$i]);
                    foreach ($lacres as $l) {
                        LacreTransporte::create([
                            'info_id' => $info->id,
                            'numero' => $l
                        ]);
                    }

                    $lacres = json_decode($request->lacres_unidade_row[$i]);
                    foreach ($lacres as $l) {
                        LacreUnidadeCarga::create([
                            'info_id' => $info->id,
                            'numero' => $l
                        ]);
                    }

                    if ($request->quantidade_rateio_carga_row[$i] != "") {
                        UnidadeCarga::create([
                            'info_id' => $info->id,
                            'id_unidade_carga' => $request->id_und_transp_row[$i],
                            'quantidade_rateio' => __convert_value_bd($request->quantidade_rateio_carga_row[$i])
                        ]);
                    }
                }
                $descricaoLog = "Número: $mdfe->mdfe_numero - R$ " . __moeda($mdfe->valor_carga);
                __createLog($request->empresa_id, 'MDFe', 'cadastrar', $descricaoLog);
            });
session()->flash("flash_success", "MDFe adicionada com sucesso!");
} catch (\Exception $e) {
    // echo $e->getMessage() . '<br>' . $e->getLine();
    // die;
    __createLog(request()->empresa_id, 'MDFe', 'erro', $e->getMessage());
    session()->flash("flash_erro", "Algo deu errado: " . $e->getMessage());
}
return redirect()->route('mdfe.index');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = Mdfe::findOrFail($id);

        $veiculos = Veiculo::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        $cidades = Cidade::all();
        return view('mdfe.edit', compact('item', 'veiculos', 'cidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        // $this->_validate($request);
        $item = Mdfe::findOrFail($id);
        try {
            $request->merge([
                'seguradora_nome' => $request->seguradora_nome ?? '',
                'seguradora_cnpj' => $request->seguradora_cnpj ?? '',
                'numero_apolice' => $request->numero_apolice ?? '',
                'numero_averbacao' => $request->numero_averbacao ?? '',
                'numero_compra' => $request->numero_compra ?? 0,
                'valor' => $request->valor ?? 0,
                'encerrado' => false,
                'chave' => '',
                'seg_cod_barras' => '',
                'protocolo' => '',
                'valor_carga' => __convert_value_bd($request->valor_carga),
                'latitude_carregamento' => $request->latitude_carregamento ?? '',
                'longitude_carregamento' => $request->longitude_carregamento ?? '',
                'cep_descarrega' => $request->cep_descarrega ?? '',
                'latitude_descarregamento' => $request->latitude_descarregamento ?? '',
                'longitude_descarregamento' => $request->longitude_descarregamento ?? '',
                'quantidade_rateio' => __convert_value_bd($request->quantidade_rateio),
                'quantidade_rateio_carga' => __convert_value_bd($request->quantidade_rateio_carga),
                'quantidade_carga' => $request->quantidade_carga,
                'produto_pred_nome' => $request->produto_pred_nome ?? '',
                'produto_pred_ncm' => preg_replace('/[^0-9]/', '', $request->produto_pred_ncm ?? ''),
                'produto_pred_cod_barras' => $request->produto_pred_cod_barras ?? '',
                'cep_carrega' => $request->cep_carrega ?? '',
                'tp_carga' => $request->tp_carga ?? '',
                'info_complementar' => $request->info_complementar ?? '',
                'info_adicional_fisco' => $request->info_adicional_fisco ?? '',
                'filial_id' => $request->filial_id != -1 ? $request->filial_id : null
            ]);
            $item->fill($request->all())->save();

            $item->municipiosCarregamento()->delete();
            $item->ciots()->delete();
            $item->percurso()->delete();
            $item->valesPedagio()->delete();
            $item->infoDescarga()->delete();
            $item->infoDescarga()->delete();

            for ($i = 0; $i < sizeof($request->municipiosCarregamento); $i++) {
                MunicipioCarregamento::create([
                    'mdfe_id' => $item->id,
                    'cidade_id' => $request->municipiosCarregamento[$i]
                ]);
            }

            for ($i = 0; $i < sizeof($request->codigo_ciot); $i++) {
                if ($request->codigo_ciot[$i] != null) {
                    Ciot::create([
                        'mdfe_id' => $item->id,
                        'cpf_cnpj' => $request->cpf_cnpj[$i],
                        'codigo' => $request->codigo_ciot[$i]
                    ]);
                }
            }

            if ($request->uf != null) {
                for ($i = 0; $i < sizeof($request->uf); $i++) {
                    if ($request->uf[$i]) {
                        Percurso::create([
                            'uf' => $request->uf[$i],
                            'mdfe_id' => $item->id
                        ]);
                    }
                }
            }

            for ($i = 0; $i < sizeof($request->cnpj_fornecedor); $i++) {
                if ($request->cnpj_fornecedor[$i] != null) {
                    ValePedagio::create([
                        'mdfe_id' => $item->id,
                        'cnpj_fornecedor' => $request->cnpj_fornecedor[$i],
                        'cnpj_fornecedor_pagador' => $request->cnpj_fornecedor_pagador[$i],
                        'numero_compra' => $request->numero_compra[$i],
                        'valor' => __convert_value_bd($request->valor_pedagio[$i])
                    ]);
                }
            }

            for ($i = 0; $i < sizeof($request->tp_und_transp_row); $i++) {

                $info = InfoDescarga::create([
                    'mdfe_id' => $item->id,
                    'tp_unid_transp' => $request->tp_und_transp_row[$i],
                    'id_unid_transp' => $request->id_und_transp_row[$i],
                    'quantidade_rateio' => __convert_value_bd($request->quantidade_rateio_row[$i]),
                    'cidade_id' => $request->municipio_descarregamento_row[$i]
                ]);

                if ($request->chave_cte_row[$i]) {
                    CTeDescarga::create([
                        'info_id' => $info->id,
                        'chave' => $request->chave_cte_row[$i],
                        'seg_cod_barras' => ''
                    ]);
                }

                if ($request->chave_nfe_row[$i]) {
                    NFeDescarga::create([
                        'info_id' => $info->id,
                        'chave' =>  $request->chave_nfe_row[$i],
                        'seg_cod_barras' => ''
                    ]);
                }

                $lacres = json_decode($request->lacres_transporte_row[$i]);
                foreach ($lacres as $l) {
                    LacreTransporte::create([
                        'info_id' => $info->id,
                        'numero' => $l
                    ]);
                }

                $lacres = json_decode($request->lacres_unidade_row[$i]);
                foreach ($lacres as $l) {
                    LacreUnidadeCarga::create([
                        'info_id' => $info->id,
                        'numero' => $l
                    ]);
                }

                if ($request->quantidade_rateio_carga_row[$i] != "") {
                    UnidadeCarga::create([
                        'info_id' => $info->id,
                        'id_unidade_carga' => $request->id_und_transp_row[$i],
                        'quantidade_rateio' => __convert_value_bd($request->quantidade_rateio_carga_row[$i])
                    ]);
                }
            }
            $descricaoLog = "Número: $item->mdfe_numero - R$ " . __moeda($item->valor_carga);
            __createLog($request->empresa_id, 'MDFe', 'editar', $descricaoLog);
            session()->flash("flash_success", "Mdfe atualizada com sucesso!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'MDFe', 'erro', $e->getMessage());
            // echo $e->getMessage() . '<br>' . $e->getLine();
            // die;
            session()->flash("flash_error", "Algo deu errado" . $e->getMessage());
        }
        return redirect()->route('mdfe.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Mdfe::findOrFail($id);
        try {
            $descricaoLog = "Número: $item->mdfe_numero - R$ " . __moeda($item->valor_carga);

            $item->municipiosCarregamento()->delete();
            $item->ciots()->delete();
            $item->percurso()->delete();
            $item->valesPedagio()->delete();
            $item->infoDescarga()->delete();

            $item->delete();
            __createLog(request()->empresa_id, 'MDFe', 'excluir', $descricaoLog);

            session()->flash("flash_success", "MDFe removida!");
        } catch (\Exception $e) {
            // echo $e->getMessage();
            // die;
            __createLog(request()->empresa_id, 'MDFe', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('mdfe.index');
    }

    public function xmlTemp($id)
    {
        $item = Mdfe::findOrFail($id);

        $config = Empresa::where('id', request()->empresa_id)
        ->first();

        $config = __objetoParaEmissao($config, $item->local_id);
        // dd($config);
        $cnpj = preg_replace('/[^0-9]/', '', $config->cpf_cnpj);

        $mdfe_service = new MDFeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$config->ambiente,
            "razaosocial" => $config->nome,
            "siglaUF" => $config->cidade->uf,
            "cnpj" => $cnpj,
            "inscricaomunicipal" => $config->inscricao_municipal,
            "codigomunicipio" => $config->cidade->codigo,
            "schemes" => "PL_MDFe_300a",
            "versao" => '3.00'
        ], $config);
        $mdfe = $mdfe_service->gerar($item);
        if (!isset($mdfe['erros_xml'])) {

            $xml = $mdfe['xml'];
            return response($xml)
            ->header('Content-Type', 'application/xml');
        } else {

            foreach ($mdfe['erros_xml'] as $err) {
                echo $err;
            }
        }
    }

    public function damdfeTemp($id)
    {
        $item = Mdfe::findOrFail($id);

        $config = Empresa::where('id', request()->empresa_id)
        ->first();

        $config = __objetoParaEmissao($config, $item->local_id);
        // dd($config);
        $cnpj = preg_replace('/[^0-9]/', '', $config->cpf_cnpj);

        $mdfe_service = new MDFeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$config->ambiente,
            "razaosocial" => $config->nome,
            "siglaUF" => $config->cidade->uf,
            "cnpj" => $cnpj,
            "inscricaomunicipal" => $config->inscricao_municipal,
            "codigomunicipio" => $config->cidade->codigo,
            "schemes" => "PL_MDFe_300a",
            "versao" => '3.00'
        ], $config);
        $mdfe = $mdfe_service->gerar($item);
        if (!isset($mdfe['erros_xml'])) {

            $xml = $mdfe['xml'];

            $damdfe = new Damdfe($xml);
            $pdf = $damdfe->render();
            return response($pdf)
            ->header('Content-Type', 'application/pdf');
        } else {

            foreach ($mdfe['erros_xml'] as $err) {
                echo $err;
            }
        }
    }

    public function naoEncerrados()
    {
        $config = Empresa::where('id', request()->empresa_id)
        ->first();

        if ($config->arquivo == null) {
            session()->flash("flash_erro", "Configure o certificado!");
            return redirect()->back();
        }

        $cnpj = preg_replace('/[^0-9]/', '', $config->cpf_cnpj);

        $mdfe_service = new MDFeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$config->ambiente,
            "razaosocial" => $config->nome,
            "siglaUF" => $config->cidade->uf,
            "cnpj" => $cnpj,
            "inscricaomunicipal" => $config->inscricao_municipal,
            "codigomunicipio" => $config->cidade->codigo,
            "schemes" => "PL_MDFe_300a",
            "versao" => '3.00'
        ], $config);
        $resultados = $mdfe_service->naoEncerrados();
        $naoEncerrados = [];

        if ($resultados['xMotivo'] != 'Consulta não encerrados não localizou MDF-e nessa situação') {
            if (isset($resultados['infMDFe'])) {
                // if(sizeof($resultados['infMDFe']) == 2){
                if (!isset($resultados['infMDFe'][1])) {
                    $array = [
                        'chave' => $resultados['infMDFe']['chMDFe'],
                        'protocolo' => $resultados['infMDFe']['nProt'],
                        'numero' => 0,
                        'data' => '',
                        'local' => ''
                    ];
                    array_push($naoEncerrados, $array);
                } else {
                    foreach ($resultados['infMDFe'] as $inf) {

                        $array = [
                            'chave' => $inf['chMDFe'],
                            'protocolo' => $inf['nProt'],
                            'numero' => 0,
                            'data' => '',
                            'local' => ''
                        ];
                        array_push($naoEncerrados, $array);
                    }
                }
            }
        }
        $data = $this->percorreDatabaseNaoEncerrados($naoEncerrados);
        return view('mdfe.nao_encerrados', compact('data'));
    }

    private function percorreDatabaseNaoEncerrados($naoEncerrados)
    {
        for ($aux = 0; $aux < count($naoEncerrados); $aux++) {
            $mdfe = Mdfe::where('chave', $naoEncerrados[$aux]['chave'])
            ->where('empresa_id', request()->empresa_id)
            ->first();

            if ($mdfe != null) {

                $naoEncerrados[$aux]['data'] = $mdfe->created_at;
                $naoEncerrados[$aux]['numero'] = $mdfe->mdfe_numero;
                $naoEncerrados[$aux]['local'] = $mdfe->filial ? $mdfe->filial->descricao : 'Matriz';
            }
        }
        return $naoEncerrados;
    }

    public function encerrar(Request $request)
    {
        $config = Empresa::where('id', request()->empresa_id)
        ->first();
        $cnpj = preg_replace('/[^0-9]/', '', $config->cpf_cnpj);
        $mdfe_service = new MDFeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$config->ambiente,
            "razaosocial" => $config->nome,
            "siglaUF" => $config->cidade->uf,
            "cnpj" => $cnpj,
            "inscricaomunicipal" => $config->inscricao_municipal,
            "codigomunicipio" => $config->cidade->codigo,
            "schemes" => "PL_MDFe_300a",
            "versao" => '3.00'
        ], $config);
        $mdfe = Mdfe::where('chave', $request->chave)
        ->where('empresa_id', $request->empresa_id)
        ->first();
        $resp = $mdfe_service->encerrar($request->chave, $request->protocolo);
        if ($resp->infEvento->cStat != 135) {
            session()->flash("flash_error", $resp->infEvento->xMotivo);
            return redirect()->back();
        }
        if ($mdfe != null) {
            $mdfe->encerrado = true;
            $mdfe->save();
        }
        session()->flash("flash_success", $resp->infEvento->xMotivo);
        return redirect()->back();
    }

    public function imprimir($id)
    {
        $item = Mdfe::findOrFail($id);
        $xml = file_get_contents(public_path('xml_mdfe/') . $item->chave . '.xml');

        $config = Empresa::where('id', $item->empresa_id)->first();

        if ($config->logo) {
            $logo = 'data://text/plain;base64,' . base64_encode(file_get_contents(@public_path('uploads/logos/' . $config->logo)));
        } else {
            $logo = null;
        }

        $damdfe = new Damdfe($xml);
        $pdf = $damdfe->render($logo);
        return response($pdf)
        ->header('Content-Type', 'application/pdf');
    }

    public function download($id)
    {
        $item = Mdfe::findOrFail($id);
        $xml = (public_path('xml_mdfe/') . $item->chave . '.xml');
        return response()->download($xml);
    }

    public function createByVendas($ids)
    {
        $ids = explode(",", $ids);
        $nfe = $this->ajustaVendas($ids);

        $empresa = Empresa::where('id', request()->empresa_id)->first();

        $veiculos = Veiculo::where('empresa_id', request()->empresa_id)->where('status', 1)->get();
        if (sizeof($veiculos) == 0) {
            session()->flash("flash_error", "Cadastre um veiculo para criar uma MDFe!");
            return redirect()->route('veiculos.create');
        }

        $cidades = Cidade::all();
        $numeroMDFe = Mdfe::lastNumero($empresa);

        return view('mdfe.importarNfe.create', compact('numeroMDFe', 'veiculos', 'cidades', 'nfe', 'empresa'));
    }

    private function ajustaVendas($ids){
        $empresa = Empresa::where('id', request()->empresa_id)->first();

        $item = [
            'uf_inicio' => $empresa->cidade->uf,
            'uf_fim' => '',
            'cnpj_contratante' => $empresa->cpf_cnpj,
            'quantidade_carga' => 0,
            'valor_carga' => 0,
            'munucipio_carregamento' => $empresa->cidade_id,
            'chave' => '',
            'munucipio_descarregamento' => null
        ];


        foreach($ids as $i){
            $nfe = Nfe::findOrFail($i);
            $item['uf_fim'] = $nfe->cliente->cidade->uf;
            foreach($nfe->itens as $it){
                $item['quantidade_carga'] += $it->quantidade; 
            }

            $item['valor_carga'] += $nfe->total;
            if($nfe->chave){
                $item['chave'] = $nfe->chave;
            }
            $item['munucipio_descarregamento'] = $nfe->cliente->cidade_id;
        }

        return (object)$item;
    }

    public function imprimirCancela($id)
    {
        $item = Mdfe::findOrFail($id);
        $xml = file_get_contents(public_path('xml_mdfe_cancelada/') . $item->chave . '.xml');
        $dadosEmitente = $this->getEmitente($item->empresa);

        try {
            $daevento = new Daevento($xml, $dadosEmitente);
            $daevento->debugMode(true);
            $pdf = $daevento->render();
            header('Content-Type: application/pdf');
            return response($pdf)
            ->header('Content-Type', 'application/pdf');
        } catch (InvalidArgumentException $e) {
            echo "Ocorreu um erro durante o processamento :" . $e->getMessage();
        }
    }

    private function getEmitente($empresa)
    {
        return [
            'razao' => $empresa->nome,
            'logradouro' => $empresa->rua,
            'numero' => $empresa->numero,
            'complemento' => '',
            'bairro' => $empresa->bairro,
            'CEP' => preg_replace('/[^0-9]/', '', $empresa->cep),
            'municipio' => $empresa->cidade->nome,
            'UF' => $empresa->cidade->uf,
            'telefone' => $empresa->telefone,
            'email' => ''
        ];
    }

    public function alterarEstado($id)
    {
        $item = Mdfe::findOrFail($id);
        return view('mdfe.estado_fiscal', compact('item'));
    }

    public function storeEstado(Request $request, $id)
    {
        $item = Mdfe::findOrFail($id);
        try {
            $item->estado_emissao = $request->estado_emissao;
            if ($request->hasFile('file')) {
                $file = $request->file;
                $xml = simplexml_load_file($request->file);

                $chave = substr((string)$xml->MDFe->infMDFe->attributes()->Id, 4, 44);
                $file->move(public_path('xml_mdfe/'), $chave.'.xml');
                $item->chave = $chave;
                $item->mdfe_numero = (string)$xml->MDFe->infMDFe->ide->nMDF;
            }
            $item->save();
            session()->flash("flash_success", "Estado alterado");
        } catch (\Exception $e) {
            echo $e->getMessage();
            die;
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('mdfe.index');
    }
}
