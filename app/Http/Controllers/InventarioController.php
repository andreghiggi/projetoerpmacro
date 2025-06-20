<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\User;
use App\Models\ItemInventario;
use App\Utils\EstoqueUtil;

class InventarioController extends Controller
{

    protected $util;

    public function __construct(EstoqueUtil $util)
    {
        $this->util = $util;

        $this->middleware('permission:inventario_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:inventario_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:inventario_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:inventario_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {

        $status = $request->status;
        $usuario_id = $request->usuario_id;
        $data = Inventario::where('empresa_id', $request->empresa_id)
        ->when($status != '', function ($q) use ($status) {
            return $q->where('status', $status);
        })
        ->when($usuario_id != '', function ($q) use ($usuario_id) {
            return $q->where('usuario_id', $usuario_id);
        })
        ->paginate(env("PAGINACAO"));

        $usuarios = User::where('usuario_empresas.empresa_id', request()->empresa_id)
        ->join('usuario_empresas', 'users.id', '=', 'usuario_empresas.usuario_id')
        ->select('users.*')
        ->get();

        return view('inventarios.index', compact('data', 'usuarios'));
    }

    public function create(Request $request)
    {
        $usuarios = User::where('usuario_empresas.empresa_id', request()->empresa_id)
        ->join('usuario_empresas', 'users.id', '=', 'usuario_empresas.usuario_id')
        ->select('users.*')
        ->get();

        return view('inventarios.create', compact('usuarios'));
    }

    public function edit($id)
    {
        $item = Inventario::findOrFail($id);
        __validaObjetoEmpresa($item);
        $usuarios = User::where('usuario_empresas.empresa_id', request()->empresa_id)
        ->join('usuario_empresas', 'users.id', '=', 'usuario_empresas.usuario_id')
        ->select('users.*')
        ->get();

        return view('inventarios.edit', compact('item', 'usuarios'));
    }

    public function store(Request $request)
    {

        try {

            $last = Inventario::where('empresa_id', $request->empresa_id)
            ->orderBy('numero_sequencial', 'desc')
            ->where('numero_sequencial', '>', 0)->first();

            $numero = $last != null ? $last->numero_sequencial : 0;
            $numero++;

            $request->merge([
                'numero_sequencial' => $numero,
                'usuario_id' => get_id_user()
            ]);

            Inventario::create($request->all());
            __createLog($request->empresa_id, 'Inventário', 'cadastrar', $request->referencia);
            session()->flash('flash_success', 'Inventário cadastrado com sucesso!');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Inventário', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível concluir o cadastro' . $e->getMessage());
        }
        
        return redirect()->route('inventarios.index');
    }

    public function update(Request $request, $id)
    {

        try {
            $item = Inventario::findOrFail($id);
            $item->fill($request->all())->save();
            __createLog($request->empresa_id, 'Inventário', 'editar', $request->referencia);
            session()->flash('flash_success', 'Inventário alterado com sucesso!');
        } catch (\Exception $e) {
            __createLog($request->empresa_id, 'Inventário', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível concluir o cadastro' . $e->getMessage());
        }
        
        return redirect()->route('inventarios.index');
    }

    public function destroy($id)
    {
        $item = Inventario::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->referencia;
            $item->itens()->delete();
            $item->delete();
            __createLog(request()->empresa_id, 'Inventário', 'excluir', $descricaoLog);
            session()->flash('flash_success', 'Removido com sucesso!');
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Inventário', 'erro', $e->getMessage());
            session()->flash('flash_error', 'Não foi possível deletar' . $e->getMessage());
        }
        return redirect()->back();
    }

    public function destroyItem($id)
    {
        $item = ItemInventario::findOrFail($id);
        try {
            $item->delete();
            session()->flash('flash_success', 'Item removido com sucesso!');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível deletar' . $e->getMessage());
        }
        return redirect()->back();
    }

    public function apontar($id){
        $item = Inventario::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('inventarios.apontar', compact('item'));
    }

    public function storeItem(Request $request, $id){
        $item = Inventario::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            ItemInventario::create([
                'inventario_id' => $id,
                'produto_id' => $request->produto_id,
                'quantidade' => __convert_value_bd($request->quantidade),
                'observacao' => $request->observacao ?? '',
                'estado' => $request->estado,
                'usuario_id' => get_id_user()
            ]);
            session()->flash('flash_success', 'Item adicionado com sucesso!');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->back();
    }

    public function itens($id){
        $item = Inventario::findOrFail($id);
        return view('inventarios.itens', compact('item'));
    }

    public function compararEstoque($id){
        $item = Inventario::findOrFail($id);

        $data = [];
        $itensDiferentes = 0;
        foreach($item->itens as $key => $i){
            $data[$key]['id'] = $i->id;
            $data[$key]['nome'] = $i->produto->nome;
            $data[$key]['quantidade'] = $i->quantidade;
            $data[$key]['estado'] = $i->estado;
            $data[$key]['estoque'] = $i->produto->estoque ? number_format($i->produto->estoque->quantidade, 0) : '--';
            if($i->produto->estoque){

                $v = $i->quantidade-$i->produto->estoque->quantidade;
                if($i->produto->unidade == 'UN' || $i->produto->unidade == 'UNID'){
                    $data[$key]['diferenca'] = number_format($v, 0);
                }else{
                    $data[$key]['diferenca'] = number_format($v, 2);
                }

                if($i->quantidade != $i->produto->estoque->quantidade){
                    $itensDiferentes++;
                }

            }else{
                $itensDiferentes++;
                if($i->produto->unidade == 'UN' || $i->produto->unidade == 'UNID'){
                    $data[$key]['diferenca'] = number_format($i->quantidade, 0);
                }else{
                    $data[$key]['diferenca'] = number_format($i->quantidade, 2);
                }
            }
        }

        // dd($data);
        return view('inventarios.comparar', compact('item', 'data', 'itensDiferentes'));
    }

    public function definirEstoque($id){
        $item = Inventario::findOrFail($id);
        foreach($item->itens as $key => $i){
            if($i->produto->estoque){

                $estoque = $i->produto->estoque;
                $estoque->quantidade = $i->quantidade;
                $estoque->save();
            }else{
                $this->util->incrementaEstoque($i->produto_id, $i->quantidade, null);
            }
        }

        return redirect()->back();
    }

}
