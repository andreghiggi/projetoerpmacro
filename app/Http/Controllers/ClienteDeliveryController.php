<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteDeliveryController extends Controller
{
    public function index(Request $request){
        $data = Cliente::where('empresa_id', request()->empresa_id)
        ->when(!empty($request->razao_social), function ($q) use ($request) {
            return $q->where('razao_social', 'LIKE', "%$request->razao_social%");
        })
        ->when(!empty($request->telefone), function ($q) use ($request) {
            return $q->where('telefone', 'LIKE', "%$request->telefone%");
        })
        ->where('uid', '!=', '')
        ->paginate(__itensPagina());
        return view('delivery.clientes.index', compact('data'));
    }

    public function edit($id)
    {
        $item = Cliente::findOrFail($id);
        $n = explode(" ", $item->razao_social);
        $nome = $n[0];
        $sobreNome = '';
        if(isset($n[1])){
            $sobreNome = $n[1];
        }
        return view('delivery.clientes.edit', compact('item', 'nome', 'sobreNome'));
    }

    public function update(Request $request, $id)
    {
        try {

            $item = Cliente::findOrFail($id);
            $request->merge([
                'razao_social' => $request->nome . " " . $request->sobre_nome
            ]);

            if($request->senha){
                $request->merge([
                    'senha' => md5($request->senha)
                ]);
            }
            $item->fill($request->all())->save();

            session()->flash("flash_success", "Cliente atualizado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->route('clientes-delivery.index');
    }

    public function destroy($id)
    {
        $item = Cliente::findOrFail($id);
        try {
            $item->delete();
            session()->flash("flash_success", "Cliente removido!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function show($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('delivery.clientes.pedidos', compact('cliente'));
        
    }

    public function enderecos($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('delivery.clientes.enderecos', compact('cliente'));
        
    }

}
