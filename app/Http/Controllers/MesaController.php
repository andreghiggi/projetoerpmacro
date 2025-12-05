<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesa;
use Illuminate\Support\Str;
use chillerlan\QRCode\QRCode;

class MesaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:mesa_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:mesa_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:mesa_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:mesa_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        
        $data = Mesa::where('empresa_id', $request->empresa_id)
        ->orderBy('nome')
        ->get();

        return view('mesas.index', compact('data'));
    }

    public function create()
    {
        return view('mesas.create');
    }

    public function edit($id)
    {
        $item = Mesa::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('mesas.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {

            $request->merge([
                'hash' => Str::random(30)
            ]);
            Mesa::create($request->all());
            session()->flash('flash_success', 'Mesa cadastrada com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('mesas.index');
    }

    public function update(Request $request, $id)
    {
        $item = Mesa::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {

            $item->fill($request->all())->save();
            session()->flash('flash_success', 'Mesa alterada com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('mesas.index');
    }

    public function destroy($id)
    {
        $item = Mesa::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->delete();
            session()->flash('flash_success', 'Mesa removida com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível deletar: ' . $e->getMessage());
        }
        return redirect()->back();
    }

    public function qrCode($id){
        $item = Mesa::findOrFail($id);
        $link = env("APP_URL") . "/cardapio?link=$item->hash";

        echo $link;
        echo "<br>$item->nome";
        echo '<br><img src="'.(new QRCode)->render($link).'" alt="QR Code" style="width: 300px;" />';
    }

}
