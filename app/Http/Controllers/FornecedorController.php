<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fornecedor;
use App\Models\Cidade;
use App\Models\Nfe;
use App\Models\ItemNfe;
use App\Models\Cliente;
use App\Models\ContaPagar;
use App\Imports\ProdutoImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Rules\ValidaDocumentoFornecedor;

class FornecedorController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:fornecedores_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:fornecedores_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:fornecedores_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:fornecedores_delete', ['only' => ['destroy']]);
    }

    private function setNumeroSequencial(){
        $fornecedores = Fornecedor::where('empresa_id', request()->empresa_id)
        ->where('numero_sequencial', null)
        ->get();

        $numero = __getUltimoNumeroSequencial(request()->empresa_id, 'fornecedors');
        // $numero++;

        foreach($fornecedores as $fornecedor){
            $numero++;
            $fornecedor->numero_sequencial = $numero;
            $fornecedor->save();
        }

        __setUltimoNumeroSequencial(request()->empresa_id, 'fornecedors', $numero);
    }

    public function index(Request $request)
    {
        $this->setNumeroSequencial();

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $ordem = $request->get('ordem');

        $data = Fornecedor::where('empresa_id', request()->empresa_id)
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
        return view('fornecedores.index', compact('data'));
    }

    public function create()
    {
        return view('fornecedores.create');
    }

    public function edit($id)
    {
        $item = Fornecedor::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('fornecedores.edit', compact('item'));
    }

    public function store(Request $request)
    {
        $this->__validate($request);
        try {

            $request->merge(['ie' => $request->ie ?? '']);
            Fornecedor::create($request->all());
            __setUltimoNumeroSequencial(request()->empresa_id, 'fornecedors', $request->numero_sequencial);

            if($request->insere_cliente){
                $numero = __getUltimoNumeroSequencial(request()->empresa_id, 'clientes');
                $request->merge([
                    'numero_sequencial' => $numero+1
                ]);
                Cliente::create($request->all());
                __setUltimoNumeroSequencial(request()->empresa_id, 'clientes', $numero+1);
            }
            __createLog($request->empresa_id, 'Fornecedor', 'cadastrar', $request->razao_social);
            session()->flash("flash_success", "Fornecedor cadastrado!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Fornecedor', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('fornecedores.index');
    }

    public function update(Request $request, $id)
    {
        $this->__validate($request, $id);
        $item = Fornecedor::findOrFail($id);
        try {
            $request->merge(['ie' => $request->ie ?? '']);
            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Fornecedor', 'editar', $request->razao_social);
            session()->flash("flash_success", "Fornecedor atualizado!");
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Fornecedor', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('fornecedores.index');
    }

    private function __validate(Request $request, $id = null)
    {
        $rules = [
            'razao_social' => 'required',
            // 'cpf_cnpj' => 'required',
            'cpf_cnpj' => $id == null ? [ 'required', new ValidaDocumentoFornecedor($request->empresa_id) ] : 'required',

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

    public function destroy($id)
    {
        $item = Fornecedor::findOrFail($id);

        if(sizeof($item->compras) > 0){
            session()->flash("flash_warning", "Não é possível remover um fornecedor com compras!");
            return redirect()->back();
        }
        __validaObjetoEmpresa($item);

        try {
            $descricaoLog = $item->razao_social;
            $item->produtoFornecedor()->delete();
            $item->delete();
            __createLog(request()->empresa_id, 'Fornecedor', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Fornecedor removido!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Fornecedor', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function destroySelecet(Request $request)
    {
        $removidos = 0;
        for($i=0; $i<sizeof($request->item_delete); $i++){
            $item = Fornecedor::findOrFail($request->item_delete[$i]);
            $item = Fornecedor::findOrFail($id);
            if(sizeof($item->compras) > 0){
                session()->flash("flash_warning", "Não é possível remover um fornecedor com compras!");
                return redirect()->back();
            }
            try {
                $descricaoLog = $item->razao_social;
                $item->delete();
                $removidos++;
                __createLog(request()->empresa_id, 'Fornecedor', 'excluir', $descricaoLog);
            } catch (\Exception $e) {
                __createLog(request()->empresa_id, 'Fornecedor', 'erro', $e->getMessage());
                session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
                return redirect()->route('fornecedores.index');
            }
        }

        session()->flash("flash_success", "Total de itens removidos: $removidos!");
        return redirect()->route('fornecedores.index');
    }

    public function import(){
        return view('fornecedores.import');
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
                                $item = Fornecedor::create($data);
                                $cont++;
                            }catch(\Exception $e){
                                session()->flash('flash_error', $e->getMessage());
                            }
                        }
                    }
                }
                session()->flash('flash_success', 'Total de fornecedores importados: ' . $cont);
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

    public function historico($id)
    {
        $item = Fornecedor::findOrFail($id);
        __validaObjetoEmpresa($item);

        $data = Nfe::where('fornecedor_id', $id)
        ->orderBy('id', 'desc')
        ->get();

        $produtos = $this->getProdutos($id);
        $faturas = $this->getFaturas($id);

        return view('fornecedores.historico', compact('item', 'data', 'produtos', 'faturas'));
    }

    private function getProdutos($id){

        $data = [];
        $dataIds = [];

        $itens = ItemNfe::select('item_nves.*')
        ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
        ->where('nves.fornecedor_id', $id)
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

        return $data;
    }

    private function getFaturas($id){
        return ContaPagar::where('fornecedor_id', $id)
        ->orderBy('id', 'desc')
        ->get();
    }

}
