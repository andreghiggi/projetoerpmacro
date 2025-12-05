<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\FuncionarioOs;
use App\Models\OrdemServico;
use App\Models\OticaOs;
use App\Models\FaturaOrdemServico;
use App\Models\MedicaoReceitaOs;
use App\Models\Produto;
use App\Models\ProdutoOs;
use App\Models\FormatoArmacaoOtica;
use App\Models\RelatorioOs;
use App\Models\ConfigGeral;
use App\Models\ServicoOs;
use App\Models\Servico;
use App\Models\Funcionario;
use App\Models\MetaResultado;
use App\Models\NaturezaOperacao;
use App\Models\ContaReceber;
use App\Models\Nfe;
use App\Models\TratamentoOtica;
use App\Models\Convenio;
use App\Models\Veiculo;
use App\Models\TipoArmacao;
use App\Models\Transportadora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psy\CodeCleaner\ReturnTypePass;
use Dompdf\Dompdf;
use App\Utils\UploadUtil;
use Illuminate\Support\Str;
use App\Models\Garantia;

class OrdemServicoController extends Controller
{

    protected $util;
    public function __construct(UploadUtil $util)
    {
        $this->util = $util;
        $this->middleware('permission:ordem_servico_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:ordem_servico_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:ordem_servico_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:ordem_servico_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $cliente_id = $request->get('cliente_id');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $codigo = $request->get('codigo');
        $convenio_id = $request->get('convenio_id');
        $situacao_entrega = $request->get('situacao_entrega');
        $adiantamento = $request->get('adiantamento');
        $veiculo_id = $request->get('veiculo_id');
        $estado = $request->get('estado');

        $data = OrdemServico::where('empresa_id', request()->empresa_id)
        ->select('ordem_servicos.*')
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('ordem_servicos.data_inicio', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('ordem_servicos.data_inicio', '<=', $end_date);
        })
        ->when(!empty($codigo), function ($query) use ($codigo) {
            return $query->where('ordem_servicos.codigo_sequencial', $codigo);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('ordem_servicos.estado', $estado);
        })
        ->when(!empty($veiculo_id), function ($query) use ($veiculo_id) {
            return $query->where('ordem_servicos.veiculo_id', $veiculo_id);
        })
        ->when(!empty($convenio_id), function ($query) use ($convenio_id) {
            return $query->join('otica_os', 'otica_os.ordem_servico_id', '=', 'ordem_servicos.id')
            ->where('otica_os.convenio_id', $convenio_id);
        })
        ->when(!empty($situacao_entrega), function ($query) use ($situacao_entrega) {
            if($situacao_entrega == 1){
                return $query->where('ordem_servicos.data_entrega', '!=', '');
            }else{
                return $query->where('ordem_servicos.data_entrega', null);
            }
        })
        ->when(!empty($adiantamento), function ($query) use ($adiantamento) {
            if($adiantamento == 1){
                return $query->where('ordem_servicos.adiantamento', '>', 0);
            }else{
                return $query->where('ordem_servicos.adiantamento', 0);
            }
        })
        ->orderBy('id', 'desc')
        ->paginate(__itensPagina());

        $convenios = Convenio::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        $veiculos = Veiculo::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        $configGeral = ConfigGeral::where('empresa_id', request()->empresa_id)->first();

        return view('ordem_servico.index', compact('data', 'convenios', 'veiculos', 'configGeral'));
    }

    public function create()
    {

        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }
        $hoje = date('d/m/Y');
        $funcionario = Funcionario::where('empresa_id', request()->empresa_id)->first();
        $clientes = Cliente::where('empresa_id', request()->empresa_id)->first();
        $usuario = Auth::user();

        $servicos = Servico::where('empresa_id', request()->empresa_id)->first();

        if ($funcionario == null) {
            session()->flash('flash_warning', 'Cadastrar um funcionario antes de continuar!');
            return redirect()->route('funcionarios.create');
        }
        if ($clientes == null) {
            session()->flash('flash_warning', 'Cadastrar um cliente antes de continuar!');
            return redirect()->route('clientes.create');
        }
        // dd($clientes);
        if ($servicos == null) {
            session()->flash('flash_warning', 'Cadastrar um serviço antes de continuar!');
            return redirect()->route('servicos.create');
        }


        $convenios = Convenio::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        $formatosArmacao = FormatoArmacaoOtica::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        $tiposArmacao = TipoArmacao::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        $tratamentos = TratamentoOtica::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        $veiculos = Veiculo::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        $configGeral = ConfigGeral::where('empresa_id', request()->empresa_id)->first();

        return view('ordem_servico.create', compact('hoje', 'funcionario', 'usuario', 'servicos', 'convenios', 
            'formatosArmacao', 'tiposArmacao', 'tratamentos', 'veiculos', 'configGeral'));
    }

    public function edit($id)
    {
        $funcionario = Funcionario::where('empresa_id', request()->empresa_id)->first();
        $clientes = Cliente::where('empresa_id', request()->empresa_id)->first();
        $usuario = Auth::user();

        $servicos = Servico::where('empresa_id', request()->empresa_id)->first();

        $item = OrdemServico::findOrFail($id);
        __validaObjetoEmpresa($item);
        
        $convenios = Convenio::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        $formatosArmacao = FormatoArmacaoOtica::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        $tiposArmacao = TipoArmacao::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        $tratamentos = TratamentoOtica::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        if($item->oticaOs){
            $item->oticaOs->tratamentos = json_decode($item->oticaOs->tratamentos ? $item->oticaOs->tratamentos : '[]');
        }

        $veiculos = Veiculo::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        $configGeral = ConfigGeral::where('empresa_id', request()->empresa_id)->first();

        return view('ordem_servico.edit', compact('funcionario', 'usuario', 'servicos', 'item', 'convenios', 
            'formatosArmacao', 'tiposArmacao', 'tratamentos', 'veiculos', 'configGeral'));
    }

    public function duplicar($id)
    {
        $funcionario = Funcionario::where('empresa_id', request()->empresa_id)->first();
        $clientes = Cliente::where('empresa_id', request()->empresa_id)->first();
        $usuario = Auth::user();

        $servicos = Servico::where('empresa_id', request()->empresa_id)->first();

        $item = OrdemServico::findOrFail($id);
        __validaObjetoEmpresa($item);
        
        $convenios = Convenio::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        $formatosArmacao = FormatoArmacaoOtica::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        $tiposArmacao = TipoArmacao::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();
        $tratamentos = TratamentoOtica::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        if($item->oticaOs){
            $item->oticaOs->tratamentos = json_decode($item->oticaOs->tratamentos ? $item->oticaOs->tratamentos : '[]');
        }

        $veiculos = Veiculo::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        $configGeral = ConfigGeral::where('empresa_id', request()->empresa_id)->first();

        return view('ordem_servico.duplicar', compact('funcionario', 'usuario', 'servicos', 'item', 'convenios', 
            'formatosArmacao', 'tiposArmacao', 'tratamentos', 'veiculos', 'configGeral'));
    }

    public function store(Request $request)
    {
        $this->_validate($request);
        try {

            $lastItem = OrdemServico::where('empresa_id', $request->empresa_id)
            ->orderBy('codigo_sequencial', 'desc')->first();
            $codigo_sequencial = 1;
            if($lastItem != null){
                $codigo_sequencial = $lastItem->codigo_sequencial+1;
            }
            $ordem = OrdemServico::create([
                'descricao' => $request->descricao ?? '',
                'usuario_id' => get_id_user(),
                'cliente_id' => $request->cliente_id,
                'empresa_id' => $request->empresa_id,
                'codigo_sequencial' => $codigo_sequencial,
                'data_inicio' => $request->data_inicio,
                'data_entrega' => $request->data_entrega,
                'funcionario_id' => $request->funcionario_id,
                'veiculo_id' => $request->veiculo_id ?? null,
                'hash_link' => Str::random(30),
                'tipo_servico' => $request->tipo_servico ?? null,
                'diagnostico_cliente' => $request->diagnostico_cliente ?? '',
                'equipamento' => $request->equipamento ?? null,
                'numero_serie' => $request->numero_serie ?? null,
                'cor' => $request->cor ?? null,
            ]);

            // verifica ótica
            if($request->medico_id){
                $this->insereReceitaOtica($request, $ordem);
            }
            __createLog($request->empresa_id, 'Ordem de Serviço', 'cadastrar', "#$codigo_sequencial - Cliente " . $ordem->cliente->info);
            session()->flash("flash_success", "Ordem de Serviço criada com sucesso");
            return redirect()->route('ordem-servico.show', $ordem->id);
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Ordem de Serviço', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu Errado" . $e->getMessage());
            // dd(__getError($e));
            return redirect()->back();
        }
    }

    private function _validate(Request $request){

        $rules = [
            'data_inicio' => 'required',
            // 'data_entrega' => 'required',
        ];

        $messages = [
            'data_inicio.required' => 'Campo obrigatório',
            'data_entrega.required' => 'Campo obrigatório',
        ];

        $this->validate($request, $rules, $messages);
    }

    private function insereReceitaOtica($request, $ordem){

        $file_name = '';
        if (!is_dir(public_path('files_receita'))) {
            mkdir(public_path('files_receita'), 0777, true);
        }
        if ($request->hasFile('arquivo')) {
            if($ordem->oticaOs && $ordem->oticaOs->arquivo_receita){

                if(file_exists(public_path('files_receita/') . $ordem->oticaOs->arquivo_receita)){
                    unlink(public_path('files_receita/') . $ordem->oticaOs->arquivo_receita);
                }
            }
            $file = $request->arquivo;
            $ext = $file->getClientOriginalExtension();
            $file_name = Str::random(20) . ".$ext";
            $file->move(public_path('files_receita/'), $file_name);
        }
        // dd($request->all());

        OticaOs::where('ordem_servico_id', $ordem->id)->delete();
        $data = [
            'ordem_servico_id' => $ordem->id,
            'medico_id' => $request->medico_id,
            'validade' => $request->validade,
            'arquivo_receita' => $file_name,
            'observacao_receita' => $request->observacao_receita ?? '',
            'convenio_id' => $request->convenio_id,
            'tipo_lente' => $request->tipo_lente,
            'laboratorio_id' => $request->laboratorio_id,
            'material_lente' => $request->material_lente,
            'descricao_lente' => $request->descricao_lente ?? '',
            'coloracao_lente' => $request->coloracao_lente ?? '',
            'armacao_propria' => $request->armacao_propria,
            'armacao_segue' => $request->armacao_segue,
            'formato_armacao_id' => $request->formato_armacao_id,
            'armacao_aro' => $request->armacao_aro,
            'armacao_ponte' => $request->armacao_ponte,
            'tipo_armacao_id' => $request->tipo_armacao_id,
            'armacao_maior_diagonal' => $request->armacao_maior_diagonal,
            'armacao_altura_vertical' => $request->armacao_altura_vertical,
            'armacao_distancia_pupilar' => $request->armacao_distancia_pupilar,
            'armacao_altura_centro_longe_od' => $request->armacao_altura_centro_longe_od,
            'armacao_altura_centro_longe_oe' => $request->armacao_altura_centro_longe_oe,
            'armacao_altura_centro_perto_od' => $request->armacao_altura_centro_perto_od,
            'armacao_altura_centro_perto_oe' => $request->armacao_altura_centro_perto_oe,
            'tratamentos' => $request->tratamentos ? json_encode($request->tratamentos) : '[]'
        ];

        OticaOs::create($data);

        if($request->esferico_longe_od){
            MedicaoReceitaOs::where('ordem_servico_id', $ordem->id)->delete();
            
            $data = [
                'ordem_servico_id' => $ordem->id,
                'esferico_longe_od' => $request->esferico_longe_od,
                'esferico_longe_oe' => $request->esferico_longe_oe,
                'esferico_perto_od' => $request->esferico_perto_od,
                'esferico_perto_oe' => $request->esferico_perto_oe,
                'cilindrico_longe_od' => $request->cilindrico_longe_od,
                'cilindrico_longe_oe' => $request->cilindrico_longe_oe,
                'cilindrico_perto_od' => $request->cilindrico_perto_od,
                'cilindrico_perto_oe' => $request->cilindrico_perto_oe,
                'eixo_longe_od' => $request->eixo_longe_od,
                'eixo_longe_oe' => $request->eixo_longe_oe,
                'eixo_perto_od' => $request->eixo_perto_od,
                'eixo_perto_oe' => $request->eixo_perto_oe,
                'altura_longe_od' => $request->altura_longe_od,
                'altura_longe_oe' => $request->altura_longe_oe,
                'altura_perto_od' => $request->altura_perto_od,
                'altura_perto_oe' => $request->altura_perto_oe,
                'dnp_longe_od' => $request->dnp_longe_od,
                'dnp_longe_oe' => $request->dnp_longe_oe,
                'dnp_perto_od' => $request->dnp_perto_od,
                'dnp_perto_oe' => $request->dnp_perto_oe
            ];

            MedicaoReceitaOs::create($data);
        }
    }

    public function update(Request $request, $id)
    {
        $item = OrdemServico::findOrFail($id);
        try {
            $request->merge([
                'descricao' => $request->input('descricao'),
                'usuario_id' => get_id_user(),
                'cliente_id' => $request->cliente_id,
                'empresa_id' => $request->empresa_id,
                'data_inicio' => $request->data_inicio,
                'data_entrega' => $request->data_entrega,
                'funcionario_id' => $request->funcionario_id
            ]);

            $item->fill($request->all())->save();

            if($request->medico_id){
                $this->insereReceitaOtica($request, $item);
            }
            __createLog($request->empresa_id, 'Ordem de Serviço', 'editar', "#$item->codigo_sequencial - Cliente " . $item->cliente->info);
            session()->flash("flash_success", "Ordem de Serviço alterada com sucesso");
            return redirect()->route('ordem-servico.show', $item->id);
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Ordem de Serviço', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu Errado" . $e->getMessage());
            return redirect()->back();
        }
    }

    public function show($id)
    {
        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }
        $ordem = OrdemServico::findOrFail($id);

        if($ordem->hash_link == null){
            $ordem->hash_link = Str::random(30);
            $ordem->save();
        }
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();
        $servicos = Servico::where('empresa_id', request()->empresa_id)->get();

        $veiculos = Veiculo::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->get();

        $configGeral = ConfigGeral::where('empresa_id', request()->empresa_id)->first();

        return view('ordem_servico.show', compact('funcionarios', 'ordem', 'servicos', 'veiculos', 'configGeral'));
    }

    public function storeServico(Request $request)
    {
        $id = $request->ordem_servico_id;
        $ordem = OrdemServico::findOrFail($id);
        $valor = $ordem->valor + (__convert_value_bd($request->valor) * __convert_value_bd($request->quantidade));
        $ordem->valor = $valor;
        $ordem->save();
        try {
            $servico = ServicoOs::create([
                'servico_id' => $request->servico_id,
                'ordem_servico_id' => $ordem->id,
                'quantidade' => __convert_value_bd($request->quantidade),
                'valor' => __convert_value_bd($request->valor),
                'status' => $request->status,
                'subtotal' => __convert_value_bd($request->quantidade) * __convert_value_bd($request->valor)
            ]);

            $descricaoLog = "#$ordem->codigo_sequencial - Serviço Adicionado: " . $servico->servico->nome;

            __createLog(request()->empresa_id, 'Ordem de Serviço - Serviço', 'cadastrar', $descricaoLog);
            session()->flash("flash_success", "Serviço adicionado!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Ordem de Serviço - Serviço', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado" . $e->getMessage());
        }
        return redirect()->back();
    }

    public function storeProduto(Request $request)
    {
        $id = $request->ordem_servico_id;
        $ordem = OrdemServico::findOrFail($id);
        $valor = $ordem->valor + (__convert_value_bd($request->valor_produto) * __convert_value_bd($request->quantidade_produto));
        $ordem->valor = $valor;
        $ordem->save();
        try {
            $produto = ProdutoOs::create([
                'produto_id' => $request->produto_id,
                'ordem_servico_id' => $ordem->id,
                'quantidade' => __convert_value_bd($request->quantidade_produto),
                'valor' => __convert_value_bd($request->valor_produto),
                'subtotal' => __convert_value_bd($request->quantidade_produto) * __convert_value_bd($request->valor_produto)
            ]);
            $descricaoLog = "#$ordem->codigo_sequencial - Serviço Adicionado: " . $produto->produto->nome;

            __createLog(request()->empresa_id, 'Ordem de Serviço - Produto', 'cadastrar', $descricaoLog);
            session()->flash("flash_success", "Produto adicionado!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Ordem de Serviço - Produto', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function deletarProduto($id)
    {
        $produtoOs = ProdutoOs::where('id', $id)->first();
        $ordem = OrdemServico::where('id', $produtoOs->ordem_servico_id)->first();
        $valor = $ordem->valor - $produtoOs->subtotal;
        $ordem->valor = $valor;
        $ordem->save();
        try {
            $produtoOs->delete();
            $descricaoLog = "#$ordem->codigo_sequencial - Serviço Removido: " . $produtoOs->produto->nome;
            __createLog(request()->empresa_id, 'Ordem de Serviço - Produto', 'excluir', $descricaoLog);

            session()->flash("flash_success", "Produto removido");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Ordem de Serviço - Produto', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function deletarServico($id)
    {
        $produtoOs = ServicoOs::where('id', $id)->first();
        $ordem = OrdemServico::where('id', $produtoOs->ordem_servico_id)->first();
        $valor = $ordem->valor - $produtoOs->subtotal;
        $ordem->valor = $valor;
        $ordem->save();
        try {
            $produtoOs->delete();
            $descricaoLog = "#$ordem->codigo_sequencial - Serviço Removido: " . $produtoOs->servico->nome;
            __createLog(request()->empresa_id, 'Ordem de Serviço - Serviço', 'excluir', $descricaoLog);

            session()->flash("flash_success", "Serviço removido");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Ordem de Serviço - Serviço', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado" . $e->getMessage());
        }
        return redirect()->back();
    }

    public function alterarStatusServico($id)
    {
        $servicoOs = ServicoOs::where('id', $id)->first();
        try {
            $servicoOs->status = !$servicoOs->status;
            $servicoOs->save();
            session()->flash("flash_success", "Status Alterado");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado" . $e->getMessage());
        }
        return redirect()->back();
    }

    public function storeFuncionario(Request $request)
    {
        $id = $request->ordem_servico_id;
        $ordem = OrdemServico::findOrFail($id);
        // $this->_validateFuncionario($request);
        try {
            FuncionarioOs::create([
                'usuario_id' => get_id_user(),
                'funcionario_id' => $request->funcionario_id,
                'ordem_servico_id' => $request->ordem_servico_id,
                'funcao' => $request->funcao
            ]);
            session()->flash("flash_success", "Funcionario Adicionado a Ordem de Serviço");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function addRelatorio($id)
    {
        $ordem = OrdemServico::where('id', $id)->first();
        return view('ordem_servico.add_relatorio', compact('ordem'));
    }

    public function storeRelatorio(Request $request)
    {
        // dd($request->ordem_servico_id);
        try {
            RelatorioOs::create([
                'usuario_id' => get_id_user(),
                'texto' => $request->texto,
                'ordem_servico_id' => $request->ordem_servico_id
            ]);
            session()->flash("flash_success", "Relatório Adicionado");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('ordem-servico.show', $request->ordem_servico_id);
    }

    public function alterarEstado($id)
    {
        $ordem = OrdemServico::where('id', $id)->first();
        return view('ordem_servico.alterar_estado', compact('ordem'));
    }

    public function updateEstado(Request $request, $id)
    {
        $ordem = OrdemServico::findOrFail($id);
        try {
            $ordem->estado = $request->novo_estado;
            $ordem->faturada = $request->faturar;

            if($ordem->estado == 'fz' && $request->faturar){
                $caixa = __isCaixaAberto();
                if($caixa == null){
                    session()->flash("flash_error", "Abra o caixa!");
                    return redirect()->back();
                }
                $ordem->caixa_id = $caixa->id;


                if($request->tipo_pagamento && $request->tipo_pagamento[0]){
                    for($i=0; $i<sizeof($request->tipo_pagamento); $i++){
                        FaturaOrdemServico::create([
                            'ordem_servico_id' => $ordem->id, 
                            'tipo_pagamento' => $request->tipo_pagamento[$i], 
                            'data_vencimento' => $request->data_vencimento[$i], 
                            'valor' => __convert_value_bd($request->valor_fatura[$i])
                        ]);

                        if(strtotime($request->data_vencimento[$i]) >= strtotime(date('Y-m-d'))){
                            ContaReceber::create([
                                'empresa_id' => $ordem->empresa_id,
                                'ordem_servico_id' => $ordem->id,
                                'cliente_id' => $ordem->cliente->id,
                                'valor_integral' => __convert_value_bd($request->valor_fatura[$i]),
                                'tipo_pagamento' => $request->tipo_pagamento[$i],
                                'data_vencimento' => $request->data_vencimento[$i],
                                'local_id' => $caixa->local_id,
                            ]);
                        }
                    }
                }
            }

            if($ordem->estado == 'fz'){
                // apontar as garantias
                foreach($ordem->itens as $produto){
                    if($produto->produto->prazo_garantia > 0){
                        Garantia::create([
                            'empresa_id' => $ordem->empresa_id,
                            'cliente_id' => $ordem->cliente_id,
                            'produto_id' => $produto->produto_id,
                            'ordem_servico_id' => $ordem->id,
                            'usuario_id' => \Auth::user()->id,
                            'prazo_garantia' => $produto->produto->prazo_garantia,
                            'data_venda' => date('Y-m-d')
                        ]);
                    }
                }

                foreach($ordem->servicos as $servico){
                    if($servico->servico->prazo_garantia > 0){
                        Garantia::create([
                            'empresa_id' => $ordem->empresa_id,
                            'cliente_id' => $ordem->cliente_id,
                            'servico_id' => $servico->servico_id,
                            'ordem_servico_id' => $ordem->id,
                            'usuario_id' => \Auth::user()->id,
                            'prazo_garantia' => $servico->servico->prazo_garantia,
                            'data_venda' => date('Y-m-d')
                        ]);
                    }
                }
            }

            $ordem->save();
            session()->flash("flash_success", "Estado alterado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('ordem-servico.show', [$ordem->id]);
    }

    public function imprimir($id)
    {
        $ordem = OrdemServico::findOrFail($id);

        __validaObjetoEmpresa($ordem);
        $config = Empresa::where('id', request()->empresa_id)->first();
        if ($config == null) {
            session()->flash("flash_warning", "Configure o emitente");
            return redirect()->route('config.index');
        }

        $configGeral = ConfigGeral::where('empresa_id', $ordem->empresa_id)->first();

        $p = view('ordem_servico.imprimir', compact('config', 'ordem', 'configGeral'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $domPdf->setPaper("A4");
        $domPdf->render();
        $domPdf->stream("Ordem de Serviço.pdf", array("Attachment" => false));
        // return view('ordem_servico.print', compact('ordem', 'config'));
    }

    public function editRelatorio($id)
    {
        $item = RelatorioOs::findOrFail($id);

        $ordem = OrdemServico::where('id', $item->ordem_servico_id)->first();

        return view('ordem_servico.edit_relatorio', compact('item', 'ordem'));
    }

    public function updateRelatorio(Request $request, $id)
    {
        $ordem = RelatorioOs::findOrFail($id);
        $item = OrdemServico::findOrFail($request->ordem_servico_id);
        try {
            $ordem->texto = $request->texto;
            $ordem->save();
            session()->flash("flash_success", "Reletório Alterado");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado" . $e->getMessage());
        }
        return redirect()->route('ordem-servico.show', $item);
    }

    public function deleteRelatorio(Request $request, $id)
    {
        $relatorioOs = RelatorioOs::where('id', $id)->first();
        try {
            $relatorioOs->delete();
            session()->flash("flash_success", "Relatório Deletado");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function destroy($id)
    {
        $item = OrdemServico::findOrFail($id);
        try {
            $descricaoLog = "#$item->codigo_sequencial - Cliente " . $item->cliente->info;
            $item->servicos()->delete();
            $item->relatorios()->delete();
            $item->fatura()->delete();
            $item->itens()->delete();

            $item->delete();
            __createLog(request()->empresa_id, 'Ordem de Serviço', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Ordem de Serviço removida");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Ordem de Serviço', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }   

    public function gerarNfe($id)
    {
        $item = OrdemServico::findOrFail($id);
        $cidades = Cidade::all();
        $transportadoras = Transportadora::where('empresa_id', request()->empresa_id)->get();

        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
        if (sizeof($naturezas) == 0) {
            session()->flash("flash_warning", "Primeiro cadastre um natureza de operação!");
            return redirect()->route('natureza-operacao.create');
        } 
        // $produtos = Produto::where('empresa_id', request()->empresa_id)->get();
        $empresa = Empresa::findOrFail(request()->empresa_id);
        $numeroNfe = Nfe::lastNumero($empresa);

        $isOrdemServico = 1;
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();

        $naturezaPadrao = NaturezaOperacao::where('empresa_id', request()->empresa_id)
        ->where('padrao', 1)->first();

        return view('nfe.create', compact('item', 'cidades', 'transportadoras', 'naturezas', 'isOrdemServico', 'numeroNfe', 'funcionarios',
            'naturezaPadrao'));
    }

    public function destroySelecet(Request $request)
    {
        $removidos = 0;
        for($i=0; $i<sizeof($request->item_delete); $i++){
            $item = OrdemServico::findOrFail($request->item_delete[$i]);
            try {
                $descricaoLog = "#$item->codigo_sequencial - Cliente " . $item->cliente->info;
                $item->servicos()->delete();
                $item->relatorios()->delete();
                $item->itens()->delete();
                $item->delete();
                $removidos++;
                __createLog(request()->empresa_id, 'Ordem de Serviço', 'excluir', $descricaoLog);
            } catch (\Exception $e) {
                __createLog(request()->empresa_id, 'Ordem de Serviço', 'erro', $e->getMessage());
                session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
                return redirect()->back();
            }
        }

        session()->flash("flash_success", "Total de itens removidos: $removidos!");
        return redirect()->back();
    }

    public function printOtica(Request $request){
        $ordem = OrdemServico::findOrFail($request->ordem_servico_id);
        __validaObjetoEmpresa($ordem);

        $config = Empresa::where('id', request()->empresa_id)->first();
        $config = __objetoParaEmissao($config, $ordem->local_id);

        $viaCliente = $request->via_cliente;
        $viaLaboratorio = $request->via_laboratorio;
        $os = $request->os;

        $p = view('ordem_servico.print_otica', compact('config', 'ordem', 'viaCliente', 'viaLaboratorio', 'os'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $domPdf->setPaper("A4");
        $domPdf->render();
        $domPdf->stream("Impressão Ótica.pdf", array("Attachment" => false));
    }

    public function updateEntrega(Request $request, $id){
        $item = OrdemServico::findOrFail($id);
        try{

            $item->adiantamento = __convert_value_bd($request->adiantamento);
            $item->data_entrega = $request->data_entrega;
            $item->veiculo_id = $request->veiculo_id ?? null;
            $item->save();

            session()->flash("flash_success", "Ordem de Serviço alterada com sucesso");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function updateDescricao(Request $request, $id){
        $item = OrdemServico::findOrFail($id);
        try{

            $item->descricao = $request->descricao ?? '';
            $item->save();

            session()->flash("flash_success", "Ordem de Serviço alterada com sucesso");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function updateDiagnostico(Request $request, $id){
        $item = OrdemServico::findOrFail($id);
        try{

            $item->diagnostico_tecnico = $request->diagnostico_tecnico ?? '';
            $item->save();

            session()->flash("flash_success", "Ordem de Serviço alterada com sucesso");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function downloadArquivo($id){
        $item = OrdemServico::findOrFail($id);

        if ($item->oticaOs->arquivo_receita && file_exists(public_path('files_receita/') . $item->oticaOs->arquivo_receita)) {
            return response()->download(public_path('files_receita/') . $item->oticaOs->arquivo_receita);
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }

    public function metas(Request $request){
        $metas = MetaResultado::where('empresa_id', $request->empresa_id)
        ->where('tabela', 'Ordens de Serviço')
        ->get();

        if(sizeof($metas) == 0){
            session()->flash("flash_warning", "Defina uma meta para ordem de serviço!");
            return redirect()->route('metas.index');
        }

        $totalMeta = $metas->sum('valor');
        $somaOsMes = $this->somaOsMes($request->empresa_id);

        return view('ordem_servico.metas', compact('metas', 'totalMeta', 'somaOsMes'));
    }

    private function somaOsMes($empresa_id){
        $soma = OrdemServico::where('empresa_id', $empresa_id)
        ->where('estado', '!=', 'cancelado')
        ->whereMonth('created_at', date('m'))
        ->sum('valor');
        
        return $soma;
    }
}
