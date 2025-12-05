<?php

namespace App\Http\Controllers;

use App\Models\ChaveNfeCte;
use App\Models\Cidade;
use Illuminate\Http\Request;
use App\Models\Cte;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\ComponenteCte;
use App\Models\MedidaCte;
use App\Models\NaturezaOperacao;
use App\Models\Veiculo;
use App\Services\CTeService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use NFePHP\DA\CTe\Dacte;
use NFePHP\DA\CTe\Daevento;
use function Ramsey\Uuid\v1;

class CteController extends Controller
{
    public function __construct()
    {
        if (!is_dir(public_path('xml_cte'))) {
            mkdir(public_path('xml_cte'), 0777, true);
        }
        if (!is_dir(public_path('xml_cte_cancelada'))) {
            mkdir(public_path('xml_cte_cancelada'), 0777, true);
        }
        if (!is_dir(public_path('xml_cte_correcao'))) {
            mkdir(public_path('xml_cte_correcao'), 0777, true);
        }
        if (!is_dir(public_path('dacte_temp'))) {
            mkdir(public_path('dacte_temp'), 0777, true);
        }

        if (!is_dir(public_path('dacte'))) {
            mkdir(public_path('dacte'), 0777, true);
        }

        $this->middleware('permission:cte_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:cte_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:cte_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:cte_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $estado = $request->get('estado');
        $local_id = $request->get('local_id');

        $data = Cte::where('empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($estado != "", function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());

        return view('cte.index', compact('data'));
    }

    public function create()
    {
        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
        if (sizeof($naturezas) == 0) {
            session()->flash("flash_warning", "Primeiro cadastre um natureza de operação!");
            return redirect()->route('natureza-operacao.create');
        }
        $clientes = Cliente::where('empresa_id', request()->empresa_id)->get();
        if (count($clientes) == 0) {
            session()->flash('flash_warning', 'Cadastar um cliente para continuar');
            return redirect()->route('clientes.create');
        }
        $veiculos = Veiculo::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        if (sizeof($veiculos) == 0) {
            session()->flash('flash_warning', 'Cadastar um veículo para continuar');
            return redirect()->route('veiculos.create');
        }
        $unidadesMedida = Cte::unidadesMedida();
        $tiposMedida = Cte::tiposMedida();
        $cidades = Cidade::all();

        $empresa = Empresa::findOrFail(request()->empresa_id);
        $numeroCte = Cte::lastNumero($empresa);

        return view(
            'cte.create',
            compact('naturezas', 'clientes', 'veiculos', 'unidadesMedida', 'tiposMedida', 'cidades', 'numeroCte')
        );
    }

    public function edit($id)
    {
        $item = Cte::findOrFail($id);
        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
        $clientes = Cliente::where('empresa_id', request()->empresa_id)->get();
        $veiculos = Veiculo::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        $unidadesMedida = Cte::unidadesMedida();
        $tiposMedida = Cte::tiposMedida();
        $cidades = Cidade::all();
        return view('cte.edit', compact('item', 'naturezas', 'clientes', 'veiculos', 'unidadesMedida', 'tiposMedida', 'cidades'));
    }

    public function store(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $config = Empresa::find($request->empresa_id);
                $request->merge([
                    'usuario_id' => get_id_user(),
                    'sequencia_cce' => 0,
                    'chave' => '',
                    'tpDoc' => $request->tpDoc ?? '',
                    'estado_emissao' => 'novo',
                    'descOutros' => $request->descOutros ?? '',
                    'nDoc' => $request->nDoc ?? 0,
                    'vDocFisc' => $request->vDocFisc ? __convert_value_bd($request->vDocFisc) : 0,
                    'valor_carga' => __convert_value_bd($request->valor_carga),
                    'valor_transporte' => __convert_value_bd($request->valor_transporte) ?? 0,
                    'valor_receber' => __convert_value_bd($request->valor_receber) ?? 0,
                    'detalhes_retira' => $request->detalhes_retira ?? '',
                    'observacao' => $request->observacao ?? '',
                    'numero_serie' => $config->numero_serie_cte ? $config->numero_serie_cte : 0,
                    'numero' => $request->numero ?? 0,
                    'perc_red_bc' => $request->perc_red_bc ? __convert_value_bd($request->perc_red_bc) : 0,
                    'ambiente' => $config->ambiente
                ]);

                $cte = Cte::create($request->all());
                for ($i = 0; $i < sizeof($request->nome_componente); $i++) {
                    ComponenteCte::create([
                        'nome' => $request->nome_componente[$i],
                        'valor' => __convert_value_bd($request->valor_componente[$i]),
                        'cte_id' => $cte->id
                    ]);
                }

                for ($i = 0; $i < sizeof($request->tipo_medida); $i++) {
                    MedidaCte::create([
                        'cte_id' => $cte->id,
                        'tipo_medida' => $request->tipo_medida[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_carga[$i]),
                        'cod_unidade' => $request->cod_unidade[$i]
                    ]);
                }
                for ($i = 0; $i < sizeof($request->chave_nfe); $i++) {
                    $chave = str_replace(" ", "", $request->chave_nfe[$i]);
                    if (strlen($request->chave_nfe[$i]) > 0) {
                        ChaveNfeCte::create([
                            'cte_id' => $cte->id,
                            'chave' => $chave
                        ]);
                    }
                }
                $descricaoLog = "Número: $cte->numero - Remetente: " . $cte->remetente->info;
                __createLog($request->empresa_id, 'CTe', 'cadastrar', $descricaoLog);
            });
            session()->flash("flash_success", "CTe cadastrada com sucesso!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'CTe', 'erro', $e->getMessage());
            session()->flash("flash_error", "Não foi possível cadastrar a CTe!" . $e->getMessage());
        }
        return redirect()->route('cte.index');
    }

    public function update(Request $request, $id)
    {
        try {
            $result = DB::transaction(function () use ($request, $id) {
                $item = Cte::findOrFail($id);
                $config = Empresa::find($request->empresa_id);

                $request->merge([
                    'descOutros' => $request->descOutros ?? '',
                    'nDoc' => $request->nDoc ?? '',
                    'tpDoc' => $request->tpDoc ?? '',
                    'vDocFisc' => $request->vDocFisc ? __convert_value_bd($request->vDocFisc) : 0,
                    'valor_carga' => __convert_value_bd($request->valor_carga),
                    'ambiente' => $config->ambiente,
                    'valor_transporte' => __convert_value_bd($request->valor_transporte) ?? 0,
                    'valor_receber' => __convert_value_bd($request->valor_receber) ?? 0,
                    'detalhes_retira' => $request->detalhes_retira ?? '',
                    'observacao' => $request->observacao ?? '',
                    'filial_id' => $request->filial_id != -1 ? $request->filial_id : null
                ]);
                $item->fill($request->all())->save();
                $item->componentes()->delete();
                $item->medidas()->delete();
                $item->chaves_nfe()->delete();
                for ($i = 0; $i < sizeof($request->nome_componente); $i++) {
                    ComponenteCte::create([
                        'nome' => $request->nome_componente[$i],
                        'valor' => __convert_value_bd($request->valor_componente[$i]),
                        'cte_id' => $item->id
                    ]);
                }
                for ($i = 0; $i < sizeof($request->tipo_medida); $i++) {
                    MedidaCte::create([
                        'cte_id' => $item->id,
                        'tipo_medida' => $request->tipo_medida[$i],
                        'quantidade' => __convert_value_bd($request->quantidade_carga[$i]),
                        'cod_unidade' => $request->cod_unidade[$i]
                    ]);
                }
                for ($i = 0; $i < sizeof($request->chave_nfe); $i++) {
                    $chave = str_replace(" ", "", $request->chave_nfe[$i]);
                    if (strlen($request->chave_nfe[$i]) > 0) {
                        ChaveNfeCte::create([
                            'cte_id' => $item->id,
                            'chave' => $chave
                        ]);
                    }
                }
                $descricaoLog = "Número: $item->numero - Remetente: " . $item->remetente->info;
                __createLog($request->empresa_id, 'CTe', 'editar', $descricaoLog);
            });
            session()->flash("flash_success", "CTe atualizado!");
        } catch (\Exception $e) {
            // echo $e->getMessage();
            // die;
            __createLog(request()->empresa_id, 'CTe', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('cte.index');
    }

    public function destroy($id)
    {
        $item = Cte::findOrFail($id);
        try {
            $descricaoLog = "Número: $item->numero - Remetente: " . $item->remetente->info;

            $item->componentes()->delete();
            $item->medidas()->delete();
            $item->chaves_nfe()->delete();
            $item->delete();
            __createLog(request()->empresa_id, 'CTe', 'excluir', $descricaoLog);
            session()->flash("flash_success", "CTe removida!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'CTe', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('cte.index');
    }

    public function xmlTemp($id)
    {
        $item = Cte::findOrFail($id);

        $empresa = $item->empresa;

        if ($empresa->arquivo == null) {
            session()->flash("flash_error", "Certificado não encontrado para este emitente");
            return redirect()->route('config.index');
        }

        $empresa = __objetoParaEmissao($empresa, $item->local_id);

        $cte_service = new CTeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$empresa->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => $empresa->cpf_cnpj,
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
        ], $empresa);

        $doc = $cte_service->gerarCTe($item);

        if (!isset($doc['erros_xml'])) {
            $xml = $doc['xml'];

            return response($xml)
            ->header('Content-Type', 'application/xml');
        } else {
            return response()->json($doc['erros_xml'], 401);
        }
    }

    public function dacteTemp($id)
    {
        $item = Cte::findOrFail($id);
        $config = Empresa::where('id', request()->empresa_id)
        ->first();
        $cnpj = preg_replace('/[^0-9]/', '', $config->cnpj);
        $cte_service = new CTeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$config->ambiente,
            "razaosocial" => $config->razao_social,
            "siglaUF" => $config->cidade->uf,
            "cnpj" => $cnpj,
            "schemes" => "PL_CTe_400",
            "versao" => '4.00',
            "proxyConf" => [
                "proxyIp" => "",
                "proxyPort" => "",
                "proxyUser" => "",
                "proxyPass" => ""
            ]
        ], $config);
        $cte = $cte_service->gerarCTe($item);
        if (!isset($cte['erros_xml'])) {
            $xml = $cte['xml'];
            $dacte = new Dacte($xml);
            $dacte->debugMode(true);
            $dacte->setDefaultFont('times');
            $dacte->creditsIntegratorFooter('WEBNFe Sistemas - http://www.webenf.com.br');
            // $dacte->monta();
            $dacte->printParameters('P', 'A4');
            $dacte->setDefaultDecimalPlaces(2);
            $pdf = $dacte->render();
            return response($pdf)
            ->header('Content-Type', 'application/pdf');
        } else {
            foreach ($cte['erros_xml'] as $err) {
                echo $err;
            }
        }
    }

    public function imprimir($id)
    {
        $item = Cte::findOrFail($id);

        $xml = file_get_contents(public_path('xml_cte/') . $item->chave . '.xml');

        $danfe = new Dacte($xml, $item->estado);
        $pdf = $danfe->render();
        return response($pdf)
        ->header('Content-Type', 'application/pdf');
    }

    public function imprimirCancela($id)
    {
        $item = Cte::findOrFail($id);

        if (file_exists(public_path('xml_cte_cancelada/') . $item->chave . '.xml')) {
            $xml = file_get_contents(public_path('xml_cte_cancelada/') . $item->chave . '.xml');
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
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }

    public function imprimirCorrecao($id)
    {
        $item = Cte::findOrFail($id);

        $xml = file_get_contents(public_path('xml_cte_correcao/') . $item->chave . '.xml');
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

    public function download($id)
    {
        $item = Cte::findOrFail($id);

        $xml = (public_path('xml_cte/') . $item->chave . '.xml');
        return response()->download($xml);
    }

    public function alterarEstado($id)
    {
        $item = Cte::findOrFail($id);
        return view('cte.estado_fiscal', compact('item'));
    }

    public function storeEstado(Request $request, $id)
    {
        $item = Cte::findOrFail($id);
        try {
            $item->estado = $request->estado_emissao;
            if ($request->hasFile('file')) {
                $xml = simplexml_load_file($request->file);

                $chave = substr($xml->CTe->infCte->attributes()->Id, 3, 44);
                $file = $request->file;
                $file->move(public_path('xml_cte/'), $chave.'.xml');
                $item->chave = $chave;
                $item->numero = $xml->CTe->infCte->ide->nCT;
            }
            $item->save();
            session()->flash("flash_success", "Estado alterado");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Aldo deu errado: " . $e->getMessage());
        }
        return redirect()->route('cte.index');
    }
}
