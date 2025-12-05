<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListaPreco;
use App\Models\User;
use App\Models\Produto;
use App\Models\ItemListaPreco;
use App\Models\ListaPrecoUsuario;
use Illuminate\Support\Facades\DB;

class ListaPrecoController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:lista_preco_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:lista_preco_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:lista_preco_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:lista_preco_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request){
        $nome = $request->get('nome');
        $tipo_pagamento = $request->get('tipo_pagamento');
        $funcionario_id = $request->get('funcionario_id');

        $data = ListaPreco::where('empresa_id', $request->empresa_id)
        ->orderBy('nome', 'desc')
        ->when($nome, function ($query) use ($nome) {
            return $query->where('nome', "LIKE", "%$nome%");
        })
        ->when($tipo_pagamento, function ($query) use ($tipo_pagamento) {
            return $query->where('tipo_pagamento', $tipo_pagamento);
        })
        ->when($funcionario_id, function ($query) use ($funcionario_id) {
            return $query->where('funcionario_id', $funcionario_id);
        })
        ->get();

        $totalDeProdutos = Produto::where('empresa_id', $request->empresa_id)->count();
        return view('lista_preco.index', compact('data', 'totalDeProdutos'));
    }

    public function create(){

        $usuarios = User::where('usuario_empresas.empresa_id', request()->empresa_id)
        ->join('usuario_empresas', 'users.id', '=', 'usuario_empresas.usuario_id')
        ->select('users.*')
        ->get();

        return view('lista_preco.create', compact('usuarios'));
    }

    public function edit($id){
        $item = ListaPreco::findOrFail($id);
        __validaObjetoEmpresa($item);

        $usuarios = User::where('usuario_empresas.empresa_id', request()->empresa_id)
        ->join('usuario_empresas', 'users.id', '=', 'usuario_empresas.usuario_id')
        ->select('users.*')
        ->get();

        $users = [];
        foreach($item->usuarios as $u){
            $users[] = $u->usuario_id;
        }
        $item->usuarios = $users;

        return view('lista_preco.edit', compact('item', 'usuarios'));
    }

    public function store(Request $request){
        try{
            $item = DB::transaction(function () use ($request) {
                if(!$request->percentual_alteracao && !$request->valor_alteracao){
                    session()->flash("flash_warning", "Informe o valor ou percentual de alteração!");
                    return redirect()->back();
                }

                $request->merge([
                    'valor_alteracao' => __convert_value_bd($request->valor_alteracao),
                    'percentual_alteracao' => $request->percentual_alteracao ? __convert_value_bd($request->percentual_alteracao) : 0
                ]);

                $item = ListaPreco::create($request->all());

                $produtos = Produto::where('empresa_id', $request->empresa_id)->get();

                foreach($produtos as $p){
                    $valor = 0;
                    if($request->ajuste_sobre == 'valor_venda'){
                        if($request->tipo == 'incremento'){
                            if($request->percentual_alteracao > 0){
                                $valor = $p->valor_unitario + ($p->valor_unitario*($request->percentual_alteracao/100));
                            }else{
                                $valor = $p->valor_unitario + $request->valor_alteracao;
                            }
                        }else{
                            if($request->percentual_alteracao > 0){
                                $valor = $p->valor_unitario - ($p->valor_unitario*($request->percentual_alteracao/100));
                            }else{
                                $valor = $p->valor_unitario - $request->valor_alteracao;
                            }
                        }

                    }else{
                        if($request->tipo == 'incremento'){
                            if($request->percentual_alteracao > 0){
                                $valor = $p->valor_compra + ($p->valor_compra*($request->percentual_alteracao/100));
                            }else{
                                $valor = $p->valor_compra + $request->valor_alteracao;
                            }
                        }else{
                            if($request->percentual_alteracao > 0){
                                $valor = $p->valor_compra - ($p->valor_compra*($request->percentual_alteracao/100));
                            }else{
                                $valor = $p->valor_compra - $request->valor_alteracao;
                            }
                        }
                    }
                    ItemListaPreco::create([
                        'lista_id' => $item->id,
                        'produto_id' => $p->id,
                        'valor' => $valor,
                        'percentual_lucro' => $request->percentual_alteracao
                    ]);
                }

                for($i=0; $i<sizeof($request->usuarios); $i++){
                    ListaPrecoUsuario::create([
                        'lista_preco_id' => $item->id, 
                        'usuario_id' => $request->usuarios[$i]
                    ]);
                }
                __createLog($request->empresa_id, 'Lista de Preços', 'cadastrar', $request->nome);
                return $item;
            });

            session()->flash("flash_success", "Lista cadastrada!");
            return redirect()->route('lista-preco.index');
        }catch(\Exception $e){
            echo $e->getMessage() . '<br>' . $e->getLine();
            die;
            __createLog(request()->empresa_id, 'Lista de Preços', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        $item = ListaPreco::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item = DB::transaction(function () use ($request, $item) {
                $item->fill($request->all())->save();

                // $produtos = Produto::where('empresa_id', $request->empresa_id)->get();
                // $item->itens()->delete();

                // foreach($produtos as $p){
                //     $valor = 0;
                //     if($request->ajuste_sobre == 'valor_venda'){
                //         if($request->tipo == 'incremento'){
                //             if($request->percentual_alteracao > 0){
                //                 $valor = $p->valor_unitario + ($p->valor_unitario*($request->percentual_alteracao/100));
                //             }else{
                //                 $valor = $p->valor_unitario + __convert_value_bd($request->valor_alteracao);
                //             }
                //         }else{
                //             if($request->percentual_alteracao > 0){
                //                 $valor = $p->valor_unitario - ($p->valor_unitario*($request->percentual_alteracao/100));
                //             }else{
                //                 $valor = $p->valor_unitario - __convert_value_bd($request->valor_alteracao);
                //             }
                //         }

                //     }else{
                //         if($request->tipo == 'incremento'){
                //             if($request->percentual_alteracao > 0){
                //                 $valor = $p->valor_compra + ($p->valor_compra*($request->percentual_alteracao/100));
                //             }else{
                //                 $valor = $p->valor_compra + __convert_value_bd($request->valor_alteracao);
                //             }
                //         }else{
                //             if($request->percentual_alteracao > 0){
                //                 $valor = $p->valor_compra - ($p->valor_compra*($request->percentual_alteracao/100));
                //             }else{
                //                 $valor = $p->valor_compra - __convert_value_bd($request->valor_alteracao);
                //             }
                //         }
                //     }
                //     ItemListaPreco::create([
                //         'lista_id' => $item->id,
                //         'produto_id' => $p->id,
                //         'valor' => $valor,
                //         'percentual_lucro' => $request->percentual_alteracao
                //     ]);

                // }

                $item->usuarios()->delete();
                for($i=0; $i<sizeof($request->usuarios); $i++){
                    ListaPrecoUsuario::create([
                        'lista_preco_id' => $item->id, 
                        'usuario_id' => $request->usuarios[$i]
                    ]);
                }

                __createLog($request->empresa_id, 'Lista de Preços', 'editar', $request->nome);
                return $item;
            });
            session()->flash('flash_success', 'Alterado com sucesso');
        } catch (\Exception $e) {
            dd($e->getLine());
            __createLog(request()->empresa_id, 'Lista de Preços', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('lista-preco.index');
    }

    public function destroy($id)
    {
        $item = ListaPreco::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->nome;
            $item->itens()->delete();
            $item->usuarios()->delete();
            $item->delete();
            __createLog(request()->empresa_id, 'Lista de Preços', 'excluir', $descricaoLog);
            session()->flash('flash_success', 'Lista removida com sucesso');
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Lista de Preços', 'erro', $e->getMessage());
            session()->flash('flash_warning', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('lista-preco.index');
    }

    public function destroySelecet(Request $request)
    {
        $removidos = 0;
        for($i=0; $i<sizeof($request->item_delete); $i++){
            $item = ListaPreco::findOrFail($request->item_delete[$i]);
            try {
                $descricaoLog = $item->nome;
                $item->itens()->delete();
                $item->delete();
                $removidos++;
                __createLog(request()->empresa_id, 'Lista de Preços', 'excluir', $descricaoLog);

            } catch (\Exception $e) {
                __createLog(request()->empresa_id, 'Lista de Preços', 'erro', $e->getMessage());
                session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
                return redirect()->route('lista-preco.index');
            }
        }

        session()->flash("flash_success", "Total de itens removidos: $removidos!");
        return redirect()->route('lista-preco.index');
    }

    public function show(Request $request, $id){
        $nome = $request->nome;
        $item = ListaPreco::findOrFail($id);
        __validaObjetoEmpresa($item);
        $produtos = ItemListaPreco::where('lista_id', $id)
        ->select('item_lista_precos.*')
        ->join('produtos', 'produtos.id', '=', 'item_lista_precos.produto_id')
        ->when(!empty($nome), function ($q) use ($nome) {
            return $q->where('produtos.nome', 'LIKE', "%$nome%");
        })
        ->paginate(40);

        return view('lista_preco.show', compact('item', 'produtos'));
    }

    public function updateItem(Request $request){
        $item = ItemListaPreco::findOrFail($request->item_id);
        try{
            $item->valor = __convert_value_bd($request->valor);
            $item->save();
            session()->flash('flash_success', 'Valor alterado do item ' . $item->produto->nome);
        } catch (\Exception $e) {
            session()->flash('flash_warning', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->back();
    }

}
