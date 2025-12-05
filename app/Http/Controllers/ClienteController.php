<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Cidade;
use App\Models\ConfigGeral;
use App\Models\FaturaCliente;
use App\Models\Nfe;
use App\Models\Nfce;
use App\Models\ItemNfe;
use App\Models\ItemNfce;
use App\Models\ContaReceber;
use App\Models\Fornecedor;
use App\Models\CreditoCliente;
use App\Models\TributacaoCliente;
use App\Models\ListaPrecoUsuario;
use Illuminate\Http\Request;
use App\Imports\ProdutoImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Rules\ValidaDocumentoCliente;
use App\Utils\UploadUtil;

class ClienteController extends Controller
{
    protected $util;
    public function __construct(UploadUtil $util)
    {
        $this->util = $util;
        $this->middleware('permission:clientes_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:clientes_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:clientes_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:clientes_delete', ['only' => ['destroy']]);
    }

    private function setNumeroSequencial(){
        $clientes = Cliente::where('empresa_id', request()->empresa_id)
        ->where('numero_sequencial', null)
        ->get();

        $numero = __getUltimoNumeroSequencial(request()->empresa_id, 'clientes');
        // $numero++;

        foreach($clientes as $cliente){
            $numero++;
            $cliente->numero_sequencial = $numero;
            $cliente->save();
        }

        __setUltimoNumeroSequencial(request()->empresa_id, 'clientes', $numero);
    }

    public function modal($id){
        $item = Cliente::findOrFail($id);
        return view('clientes.partials.modal_body', compact('item'))->render();
    }

    public function index(Request $request)
    {
        $this->setNumeroSequencial();

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $ordem = $request->get('ordem');

        $data = Cliente::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->razao_social), function ($q) use ($request) {
            return  $q->where(function ($quer) use ($request) {
                return $quer->where('razao_social', 'LIKE', "%$request->razao_social%");
            });
        })
        ->when(!empty($request->cpf_cnpj), function ($q) use ($request) {
            return  $q->where(function ($quer) use ($request) {
                return $quer->where('cpf_cnpj', 'LIKE', "%$request->cpf_cnpj%");
            });
        })
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!$ordem, function ($query) {
            return $query->orderBy('razao_social');
        })
        ->when($ordem, function ($query) use ($ordem) {
            return $query->orderBy($ordem, $ordem == 'created_at' ? 'desc' : 'asc');
        })
        ->paginate(__itensPagina());

        $configGeral = ConfigGeral::where('empresa_id', $request->empresa_id)
        ->first();
        $tipoExibe = $configGeral && $configGeral->clientes_exibe_tabela == 0 
        ? 'card' 
        : 'tabela';
        
        return view('clientes.index', compact('data', 'tipoExibe'));
    }

    public function create()
    {
        $listasPreco = ListaPrecoUsuario::select('lista_precos.*')
        ->join('lista_precos', 'lista_precos.id', '=', 'lista_preco_usuarios.lista_preco_id')
        ->where('lista_preco_usuarios.usuario_id', get_id_user())
        ->get();

        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();
        $tiposPagamento = Nfce::tiposPagamento();
        if($config != null){
            $config->tipos_pagamento_pdv = $config != null && $config->tipos_pagamento_pdv ? json_decode($config->tipos_pagamento_pdv) : [];
            $temp = [];
            if(sizeof($config->tipos_pagamento_pdv) > 0){
                foreach($tiposPagamento as $key => $t){
                    if(in_array($t, $config->tipos_pagamento_pdv)){
                        $temp[$key] = $t;
                    }
                }
                $tiposPagamento = $temp;
            }
        }

        return view('clientes.create', compact('listasPreco', 'tiposPagamento'));
    }

    public function edit($id)
    {
        $item = Cliente::findOrFail($id);
        __validaObjetoEmpresa($item);

        $listasPreco = ListaPrecoUsuario::select('lista_precos.*')
        ->join('lista_precos', 'lista_precos.id', '=', 'lista_preco_usuarios.lista_preco_id')
        ->where('lista_preco_usuarios.usuario_id', get_id_user())
        ->get();

        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();
        $tiposPagamento = Nfce::tiposPagamento();
        if($config != null){
            $config->tipos_pagamento_pdv = $config != null && $config->tipos_pagamento_pdv ? json_decode($config->tipos_pagamento_pdv) : [];
            $temp = [];
            if(sizeof($config->tipos_pagamento_pdv) > 0){
                foreach($tiposPagamento as $key => $t){
                    if(in_array($t, $config->tipos_pagamento_pdv)){
                        $temp[$key] = $t;
                    }
                }
                $tiposPagamento = $temp;
            }
        }

        return view('clientes.edit', compact('item', 'listasPreco', 'tiposPagamento'));
    }

    public function store(Request $request)
    {
        // $this->__validate($request);
        try {

            $file_name = '';
            if ($request->hasFile('image')) {
                $file_name = $this->util->uploadImage($request, '/clientes');
            }

            $request->merge([
                'ie' => $request->ie ?? '',
                'nome_fantasia' => $request->nome_fantasia ?? '',
                'valor_cashback' => $request->valor_cashback ? __convert_value_bd($request->valor_cashback) : 0,
                'valor_credito' => $request->valor_credito ? __convert_value_bd($request->valor_credito) : 0,
                'imagem' => $file_name,
            ]);

            $cliente = Cliente::where('empresa_id', $request->empresa_id)
            ->where('cpf_cnpj', $request->cpf_cnpj)
            ->first();

            // if($cliente){
            //     session()->flash("flash_error", "Cliente já esta cadastrado");
            // }
            $cliente = Cliente::create($request->all());
            __setUltimoNumeroSequencial(request()->empresa_id, 'clientes', $request->numero_sequencial);

            $this->cadastraTributacao($cliente, $request);

            if($request->insere_fornecedor){
                $numero = __getUltimoNumeroSequencial(request()->empresa_id, 'fornecedors');
                $request->merge([
                    'numero_sequencial' => $numero+1
                ]);
                Fornecedor::create($request->all());
                __setUltimoNumeroSequencial(request()->empresa_id, 'fornecedors', $numero+1);
            }

            if($request->dias_vencimento[0] != ''){
                for($i=0; $i<sizeof($request->dias_vencimento); $i++){
                    FaturaCliente::create([
                        'cliente_id' => $cliente->id,
                        'tipo_pagamento' => $request->tipo_pagamento[$i] ?? null,
                        'dias_vencimento' => $request->dias_vencimento[$i]
                    ]);
                }
            }

            __createLog($request->empresa_id, 'Cliente', 'cadastrar', $request->razao_social);
            session()->flash("flash_success", "Cliente cadastrado!");
        } catch (\Exception $e) {
            // echo $e->getMessage();
            // die;
            __createLog($request->empresa_id, 'Cliente', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('clientes.index');
    }

    private function cadastraTributacao($cliente, Request $request){
        if($cliente->tributacao){
            $cliente->tributacao()->delete();
        }

        $request->merge([
            'cliente_id' => $cliente->id
        ]);

        TributacaoCliente::create($request->all());
    }

    public function removeImagem($id){
        $item = Cliente::findOrFail($id);
        try{
            $this->util->unlinkImage($item, '/clientes');
            $item->imagem = '';
            $item->save();
            session()->flash("flash_success", "Imagem removida");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $this->__validate($request, $id);
        $item = Cliente::findOrFail($id);
        try {

            $file_name = $item->imagem;

            if ($request->hasFile('image')) {
                $this->util->unlinkImage($item, '/clientes');
                $file_name = $this->util->uploadImage($request, '/clientes');
            }
            $request->merge([
                'ie' => $request->ie ?? '',
                'valor_cashback' => $request->valor_cashback ? __convert_value_bd($request->valor_cashback) : 0,
                'valor_credito' => $request->valor_credito ? __convert_value_bd($request->valor_credito) : 0,
                'imagem' => $file_name,
            ]);
            $item->fill($request->all())->save();

            $this->cadastraTributacao($item, $request);


            if($request->dias_vencimento[0] != ''){
                $item->fatura()->delete();
                for($i=0; $i<sizeof($request->dias_vencimento); $i++){
                    FaturaCliente::create([
                        'cliente_id' => $item->id,
                        'tipo_pagamento' => $request->tipo_pagamento[$i] ?? null,
                        'dias_vencimento' => $request->dias_vencimento[$i]
                    ]);
                }
            }

            __createLog($request->empresa_id, 'Cliente', 'editar', $request->razao_social);
            session()->flash("flash_success", "Cliente atualizado!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Cliente', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('clientes.index');
    }

    private function __validate(Request $request, $id = null)
    {
        $rules = [
            'razao_social' => 'required',
            'cpf_cnpj' => $id == null ? [ 'required', new ValidaDocumentoCliente($request->empresa_id) ] : 'required',
            // 'ie' => 'required',
            'telefone' => 'required',
            'cidade_id' => 'required',
            'rua' => 'required',
            'cep' => 'required',
            'numero' => 'required',
            'bairro' => 'required',
        ];

        $messages = [
            'razao_social.required' => 'Campo Obrigatório',
            'cpf_cnpj.required' => 'Campo Obrigatório',
            'ie.required' => 'Campo Obrigatório',
            'telefone.required' => 'Campo Obrigatório',
            'cidade_id.required' => 'Campo Obrigatório',
            'rua.required' => 'Campo Obrigatório',
            'cep.required' => 'Campo Obrigatório',
            'numero.required' => 'Campo Obrigatório',
            'bairro.required' => 'Campo Obrigatório',
        ];
        $this->validate($request, $rules, $messages);
    }

    public function historico($id)
    {
        $item = Cliente::findOrFail($id);
        __validaObjetoEmpresa($item);

        $nves = Nfe::where('cliente_id', $id)->get();
        $nfces = Nfce::where('cliente_id', $id)->get();

        $data = [];
        foreach($nves as $n){
            $n->tipo = 'nfe';
            array_push($data, $n);
        }

        foreach($nfces as $n){
            $n->tipo = 'nfce';
            array_push($data, $n);
        }

        usort($data, function($a, $b){
            return $a->created_at < $b->created_at ? 1 : -1;
        });

        $produtos = $this->getProdutos($id);
        $faturas = $this->getFaturas($id);

        return view('clientes.historico', compact('item', 'data', 'produtos', 'faturas'));
    }

    private function getProdutos($id){

        $data = [];
        $dataIds = [];

        $itens = ItemNfe::select('item_nves.*')
        ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
        ->where('nves.cliente_id', $id)
        ->get();

        foreach($itens as $i){
            if(!in_array($i->produto_id, $dataIds)){
                $data[] = $i;
                $dataIds[] = $i->produto_id;
            }else{
                for($j=0; $j<sizeof($data); $j++){
                    if($data[$j]['produto_id'] == $i->produto_id){
                        $data[$j]['quantidade'] += $i->quantidade;
                    }
                }
            }
        }

        $itens = ItemNfce::select('item_nfces.*')
        ->join('nfces', 'nfces.id', '=', 'item_nfces.nfce_id')
        ->where('nfces.cliente_id', $id)
        ->get();

        foreach($itens as $i){
            if(!in_array($i->produto_id, $dataIds)){
                $data[] = $i;
                $dataIds[] = $i->produto_id;
            }else{
                for($j=0; $j<sizeof($data); $j++){
                    if($data[$j]['produto_id'] == $i->produto_id){
                        $data[$j]['quantidade'] += $i->quantidade;
                    }
                }
            }
        }
        // dd($data);
        return $data;
    }

    private function getFaturas($id){
        return ContaReceber::where('cliente_id', $id)
        ->orderBy('id', 'desc')
        ->get();
    }

    public function destroy($id)
    {
        $item = Cliente::findOrFail($id);

        if(sizeof($item->vendas) > 0){
            session()->flash("flash_warning", "Não é possível remover um cliente com vendas!");
            return redirect()->back();
        }
        __validaObjetoEmpresa($item);
        
        $item->tributacao()->delete();
        try {
            $descricaoLog = $item->razao_social;

            $item->delete();
            __createLog(request()->empresa_id, 'Cliente', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Cliente removido!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Cliente', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function import(){
        return view('clientes.import');
    }

    public function downloadModelo(){
        return response()->download(public_path('files/') . 'import_clients_csv_template.xlsx');
    }

    public function storeModelo(Request $request){
        if ($request->hasFile('file')) {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            $rows = Excel::toArray(new ProdutoImport, $request->file);
            $retornoErro = $this->validaArquivo($rows);
            $cont = 0;
            if($retornoErro == ""){
                foreach($rows as $row){
                    foreach($row as $key => $r){

                        if($r[0] != 'RAZÃO SOCIAL' && isset($r[0])){
                            try{
                                $data = $this->preparaObjeto($r, $request->empresa_id);
                                $item = Cliente::create($data);
                                $cont++;
                            }catch(\Exception $e){
                                session()->flash('flash_error', $e->getMessage());
                            }
                        }
                    }
                }

                session()->flash('flash_success', 'Total de clientes importados: ' . $cont);
                return redirect()->back();
            }else{
                session()->flash('flash_error', $retornoErro);
                return redirect()->back();
            }

        }else{
            session()->flash('flash_error', 'Nenhum Arquivo!!');
            return redirect()->back();
        }
    }

    private function preparaObjeto($linha, $empresa_id){
        $cpf_cnpj = trim((string)$linha[2]);
        $mask = '##.###.###/####-##';

        if(strlen($cpf_cnpj) == 11){
            $mask = '###.###.###.##';
        }
        if(!str_contains($cpf_cnpj, ".")){
            $cpf_cnpj = __mask($cpf_cnpj, $mask);
        }

        $cidade = Cidade::where('nome', $linha[7])
        ->where('uf', $linha[8])->first();
        $data = [
            'empresa_id' => $empresa_id,
            'razao_social' => $linha[0],
            'nome_fantasia' => $linha[1] != '' ? $linha[1] : '',
            'cpf_cnpj' => $cpf_cnpj,
            'ie' => $linha[3] != '' ? $linha[3] : '',
            'contribuinte' => $linha[13] != '' ? $linha[13] : 0,
            'consumidor_final' => $linha[14] != '' ? $linha[14] : 0,
            'email' => $linha[10] != '' ? $linha[10] : '',
            'telefone' => $linha[9] != '' ? $linha[9] : '',
            'cidade_id' => $cidade != null ? $cidade->id : 1,
            'rua' => $linha[4],
            'cep' => $linha[11],
            'numero' => $linha[5],
            'bairro' => $linha[6],
            'complemento' => $linha[12] != '' ? $linha[12] : ''
        ];

        return $data;
    }

    private function validaArquivo($rows){
        $cont = 1;
        $msgErro = "";
        foreach($rows as $row){
            foreach($row as $key => $r){
                if(isset($r[0])){
                    $razaoSocial = $r[0];
                    $cpfCnpj = $r[2];
                    $rua = $r[4];
                    $numero = $r[5];
                    $bairro = $r[6];
                    $cidade = $r[7];
                    $uf = $r[8];
                    $cep = $r[11];

                    if(strlen($razaoSocial) == 0){
                        $msgErro .= "Coluna razão social em branco na linha: $cont | "; 
                    }

                    if(strlen($cpfCnpj) == 0){
                        $msgErro .= "Coluna CPF/CNPJ em branco na linha: $cont | "; 
                    }

                    if(strlen($rua) == 0){
                        $msgErro .= "Coluna rua em branco na linha: $cont | "; 
                    }

                    if(strlen($numero) == 0){
                        $msgErro .= "Coluna numero em branco na linha: $cont | "; 
                    }
                    if(strlen($bairro) == 0){
                        $msgErro .= "Coluna bairro em branco na linha: $cont | "; 
                    }
                    if(strlen($cidade) == 0){
                        $msgErro .= "Coluna cidade em branco na linha: $cont | "; 
                    }
                    if(strlen($cep) == 0){
                        $msgErro .= "Coluna CEP em branco na linha: $cont | "; 
                    }

                    if($msgErro != ""){
                        return $msgErro;
                    }
                    $cont++;
                }
            }
        }

        return $msgErro;
    }

    public function alterarStatusCredito(Request $request)
    {
        $credito = CreditoCliente::find($request->id);

        if (!$credito) {
            return response()->json(['status' => false, 'msg' => 'Crédito não encontrado']);
        }

        $credito->status = false;
        $credito->save();

        return response()->json($credito, 200);
    }


    public function cashBack($id){
        $item = Cliente::findOrFail($id);
        return view('clientes.cash_back', compact('item'));
    }

    public function destroySelecet(Request $request)
    {
        $removidos = 0;
        for($i=0; $i<sizeof($request->item_delete); $i++){
            $item = Cliente::findOrFail($request->item_delete[$i]);
            if(sizeof($item->vendas) > 0){
                session()->flash("flash_warning", "Não é possível remover um cliente com vendas!");
                return redirect()->back();
            }
            try {
                $descricaoLog = $item->razao_social;
                $item->enderecosEcommerce()->delete();
                $item->delete();
                $removidos++;
                __createLog(request()->empresa_id, 'Cliente', 'excluir', $descricaoLog);
            } catch (\Exception $e) {
                __createLog(request()->empresa_id, 'Cliente', 'erro', $e->getMessage());
                session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
                return redirect()->route('clientes.index');
            }
        }

        session()->flash("flash_success", "Total de itens removidos: $removidos!");
        return redirect()->back();
    }

}
