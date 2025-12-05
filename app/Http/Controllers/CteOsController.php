<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Cliente;
use App\Models\CteOs;
use App\Models\NaturezaOperacao;
use App\Models\Veiculo;
use App\Models\PercursoCteOs;
use App\Services\CTeOsService;
use App\Services\CTeService;
use Illuminate\Http\Request;
use InvalidArgumentException;
use NFePHP\DA\CTe\DacteOS;
use NFePHP\DA\CTe\Daevento;

use function Ramsey\Uuid\v1;

class CteOsController extends Controller
{
    public function __construct()
    {
        if (!is_dir(public_path('xml_cte_os'))) {
            mkdir(public_path('xml_cte_os'), 0777, true);
        }
        if (!is_dir(public_path('xml_cte_os_cancelada'))) {
            mkdir(public_path('xml_cte_os_cancelada'), 0777, true);
        }
        if (!is_dir(public_path('xml_cte_correcao_os'))) {
            mkdir(public_path('xml_cte_correcao_os'), 0777, true);
        }
        if (!is_dir(public_path('dacte_temp_os'))) {
            mkdir(public_path('dacte_temp_os'), 0777, true);
        }

        if (!is_dir(public_path('dacte_os'))) {
            mkdir(public_path('dacte_os'), 0777, true);
        }

        $this->middleware('permission:cte_os_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:cte_os_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:cte_os_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:cte_os_delete', ['only' => ['destroy']]);
    }


    public function index(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $estado = $request->get('estado');
        $local_id = $request->get('local_id');

        $data = CteOs::where('empresa_id', request()->empresa_id)
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
        ->paginate(__itensPagina());
        return view('cte_os.index', compact('data'));
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
        $veiculos = Veiculo::where('empresa_id', request()->empresa_id)->get();
        if (count($veiculos) == 0) {
            session()->flash('flash_warning', 'Cadastar um veículo para continuar');
            return redirect()->route('veiculos.create');
        }

        $cidades = Cidade::all();

        return view('cte_os.create', compact('naturezas', 'clientes', 'veiculos', 'cidades'));
    }

    public function store(Request $request)
    {
        try {
            $request->merge([
                'usuario_id' => get_id_user(),
                'emitente_id' => $request->remetente_id,
                'tomador_id' => $request->destinatario_id,
                'sequencia_cce' => 0,
                'chave' => '',
                'observacao' => $request->observacao ?? '',
                'quantidade_carga' => __convert_value_bd($request->quantidade_carga),
                'valor_transporte' => __convert_value_bd($request->valor_transporte),
                'valor_receber' => __convert_value_bd($request->valor_receber)

            ]);
            $item = CteOs::create($request->all());

            for ($i = 0; $i < sizeof($request->uf); $i++) {
                if ($request->uf[$i]) {
                    PercursoCteOs::create([
                        'uf' => $request->uf[$i],
                        'cteos_id' => $item->id
                    ]);
                }
            }
            session()->flash('flash_success', 'Cadastrado com sucesso!');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('cte-os.index');
    }

    public function edit($id)
    {
        $item = CteOs::findOrFail($id);
        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
        $clientes = Cliente::where('empresa_id', request()->empresa_id)->get();
        $veiculos = Veiculo::where('empresa_id', request()->empresa_id)->get();
        $cidades = Cidade::all();
        return view('cte_os.edit', compact('item', 'clientes', 'veiculos', 'cidades', 'naturezas'));
    }

    public function update(Request $request, $id)
    {
        $item = CteOs::findOrFail($id);
        try {
            $request->merge([
                'usuario_id' => get_id_user(),
                'emitente_id' => $request->remetente_id,
                'tomador_id' => $request->destinatario_id,
                'sequencia_cce' => 0,
                'chave' => '',
                'quantidade_carga' => __convert_value_bd($request->quantidade_carga),
            ]);
            $item->fill($request->all())->save();
            $item->percurso()->delete();

            for ($i = 0; $i < sizeof($request->uf); $i++) {
                if ($request->uf[$i]) {
                    PercursoCteOs::create([
                        'uf' => $request->uf[$i],
                        'cteos_id' => $item->id
                    ]);
                }
            }

            session()->flash('flash_success', 'Alterado com sucesso!');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('cte-os.index');
    }

    public function xmlTemp($id)
    {
        $item = CteOs::findOrFail($id);

        $empresa = $item->empresa;

        if ($empresa->arquivo == null) {
            session()->flash("flash_error", "Certificado não encontrado para este emitente");
            return redirect()->route('config.index');
        }
        $empresa = __objetoParaEmissao($empresa, $item->local_id);
        
        $cte_service = new CTeOsService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$empresa->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => $empresa->cpf_cnpj,
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
        ], $empresa);

        $doc = $cte_service->gerarCTe($item);
        // dd($doc);
        if (!isset($doc['erros_xml'])) {
            $xml = $doc['xml'];

            return response($xml)
            ->header('Content-Type', 'application/xml');
        } else {
            return response()->json($doc['erros_xml'], 401);
        }
    }

    public function imprimir($id)
    {
        $item = CteOs::findOrFail($id);

        $xml = file_get_contents(public_path('xml_cte_os/') . $item->chave . '.xml');

        $danfe = new DacteOS($xml);
        $pdf = $danfe->render();
        return response($pdf)
        ->header('Content-Type', 'application/pdf');
    }

    public function download($id)
    {
        $item = CteOs::findOrFail($id);

        $xml = (public_path('xml_cte_os/') . $item->chave . '.xml');
        return response()->download($xml);
    }

    public function alterarEstado($id)
    {
        $item = CteOs::findOrFail($id);
        return view('cte_os.estado_fiscal', compact('item'));
    }

    public function storeEstado(Request $request, $id)
    {
        $item = CteOs::findOrFail($id);
        try {
            $item->estado_emissao = $request->estado_emissao;
            if ($request->hasFile('file')) {
                $xml = simplexml_load_file($request->file);
                $chave = substr($xml->infCte->attributes()->Id, 3, 44);
                $file = $request->file;
                $file->move(public_path('xml_cte_os/'), $chave . '.xml');
                $item->chave = $chave;
                $item->numero_emissao = (int)$xml->infCte->ide->nCT;
            }
            $item->save();
            session()->flash("flash_success", "Estado alterado");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Aldo deu errado: " . $e->getMessage());
        }
        return redirect()->route('cte-os.index');
    }

    public function imprimirCancela($id)
    {
        $item = CteOs::findOrFail($id);

        if (file_exists(public_path('xml_cte_os_cancelada/') . $item->chave . '.xml')) {
            $xml = file_get_contents(public_path('xml_cte_os_cancelada/') . $item->chave . '.xml');
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
        }else{
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
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

    public function destroy($id)
    {
        $item = CteOs::findOrFail($id);
        try {

            $item->percurso()->delete();
            $item->delete();

            session()->flash("flash_success", "CTeOs removida!");
        } catch (\Exception $e) {

            session()->flash("flash_error", 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->back();
    }
}
