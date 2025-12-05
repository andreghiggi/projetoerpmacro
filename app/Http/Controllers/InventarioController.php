<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\User;
use App\Models\Produto;
use App\Models\CategoriaProduto;
use App\Models\Empresa;
use App\Models\ItemInventario;
use App\Models\ItemInventarioImpressao;
use App\Utils\EstoqueUtil;
use Dompdf\Dompdf;

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
        ->orderBy('id', 'desc')
        ->paginate(__itensPagina());

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
            $item->itensImpresso()->delete();
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
        if(sizeof($item->itensImpresso) > 0){
            return redirect()->route('inventarios.apontar-impresso', [$id]);
        }
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

    public function renderizar($id)
    {
        $item = Inventario::findOrFail($id);


        $config = Empresa::where('id', request()->empresa_id)->first();

        $p = view('inventarios.imprimir', compact(
            'item',
            'config',
        ));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $domPdf->setPaper("A4");
        $domPdf->render();
        $domPdf->stream("Inventário.pdf", array("Attachment" => false));

    }

    public function imprimir($id)
    {
        $item = Inventario::findOrFail($id);
        $countProdutos = Produto::where('empresa_id', request()->empresa_id)
        ->where('status', 1)->count();

        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->orderBy('nome')
        ->where('status', 1)->get();   
        return view('inventarios.gerar_impressao', compact('item', 'countProdutos', 'categorias'));
    }

    public function addProduto(Request $request)
    {
        if($request->produto_id){
            $produto = Produto::find($request->produto_id);
            $existe = ItemInventarioImpressao::where('inventario_id', $request->inventario_id)
            ->where('produto_id', $produto->id)
            ->exists();

            if (!$existe) {
                ItemInventarioImpressao::create([
                    'inventario_id' => $request->inventario_id,
                    'produto_id' => $produto->id,
                    'quantidade' => null,
                    'observacao' => '',
                    'estado' => null,
                ]);
            }
        }

        if($request->categoria_id){
            $categoria = CategoriaProduto::find($request->categoria_id);
            foreach($categoria->produtos as $p){
                $existe = ItemInventarioImpressao::where('inventario_id', $request->inventario_id)
                ->where('produto_id', $p->id)
                ->exists();

                if (!$existe) {
                    ItemInventarioImpressao::create([
                        'inventario_id' => $request->inventario_id,
                        'produto_id' => $p->id,
                        'quantidade' => null,
                        'observacao' => '',
                        'estado' => null,
                    ]);
                }
            }
        }

        $item = Inventario::findOrFail($request->inventario_id);
        return view('inventarios.partials.linha_impressa', compact('item'));
    }

    public function deleteItem(Request $request)
    {
        $item = ItemInventarioImpressao::find($request->id);

        if (!$item) {
            return response()->json(['status' => false, 'msg' => 'Item não encontrado']);
        }

        $item->delete();

        return response()->json(['status' => true]);
    }

    public function apontarImpresso($id){
        $item = Inventario::findOrFail($id);     

        return view('inventarios.apontar_impresso', compact('item'));
    }

    public function atualizarItem(Request $request)
    {

        $item  = ItemInventarioImpressao::findOrFail($request->id);
        $campo = $request->campo;
        $valor = $request->valor;

        if ($campo === 'quantidade') {
        // converte "1.234,567" para 1234.567
            $valor = __convert_value_bd($valor);
        }

        $item->$campo = $valor;
        $item->save();

        return response()->json([
            'status'  => true,
            'message' => 'Atualizado com sucesso',
            'campo'   => $campo,
            'valor'   => $item->$campo,
        ]);
    }

    public function acerto(Request $request){
        $item = Inventario::findOrFail($request->inventario_id);
        try{
            foreach($item->itensImpresso as $i){
                $produto = $i->produto;
                if($produto->estoque){
                    $estoque = $produto->estoque;
                    $estoque->quantidade = $i->quantidade;
                    $estoque->save();
                }else{
                    $this->util->incrementaEstoque($i->produto_id, $i->quantidade, null);
                }

                ItemInventario::create([
                    'inventario_id' => $i->inventario_id,
                    'produto_id' => $i->produto_id,
                    'quantidade' => $i->quantidade,
                    'observacao' => $i->observacao,
                    'estado' => $i->estado,
                    'usuario_id' => get_id_user()
                ]);
            }

            $item->status = 0;
            $item->save();
            session()->flash('flash_success', 'Inventário acertado com sucesso!');
            return redirect()->route('inventarios.index');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível concluir a ação: ' . $e->getMessage());
            return redirect()->back();
        }

    }

    public function duplicar($id)
    {
        $item = Inventario::findOrFail($id);
        try{
            $last = Inventario::where('empresa_id', request()->empresa_id)
            ->orderBy('numero_sequencial', 'desc')
            ->where('numero_sequencial', '>', 0)->first();

            $numero = $last != null ? $last->numero_sequencial : 0;
            $numero++;

            $inventario = Inventario::create([
                'inicio' => $item->inicio,
                'fim' => $item->fim,
                'observacao' => $item->observacao,
                'tipo' => $item->tipo,
                'status' => 1,
                'empresa_id' => $item->empresa_id,
                'referencia' => $item->referencia,
                'usuario_id' => $item->usuario_id, 
                'numero_sequencial' => $numero
            ]);

            // if(sizeof($item->itens) > 0){

            //     foreach ($item->itens as $i) {
            //         ItemInventarioImpressao::create([
            //             'inventario_id' => $inventario->id,
            //             'produto_id' => $i->produto_id,
            //             'quantidade' => null,
            //             'observacao' => '',
            //             'estado' => null,
            //         ]);
            //     }
            // }else{
            //     $produtos = Produto::where('empresa_id', $item->empresa_id)
            //     ->orderBy('nome')
            //     ->select('nome', 'codigo_barras', 'id')
            //     ->where('status', 1)->get();

            //     foreach($produtos as $produto){
            //         ItemInventarioImpressao::create([
            //             'inventario_id' => $inventario->id,
            //             'produto_id' => $produto->id,
            //             'quantidade' => null,
            //             'observacao' => '',
            //             'estado' => null,
            //         ]);
            //     }
            // }
            session()->flash('flash_success', 'Inventário dulpicado com sucesso!');
            return redirect()->route('inventarios.index');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível concluir a ação: ' . $e->getMessage());
            return redirect()->back();
        }
    }

}
