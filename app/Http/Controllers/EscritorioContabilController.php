<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EscritorioContabil;

class EscritorioContabilController extends Controller
{
    public function index(Request $request){
        $item = EscritorioContabil::where('empresa_id', $request->empresa_id)
        ->first();

        return view('escritorio_contabil.index', compact('item'));
    }

    public function store(Request $request)
    {

        $item = EscritorioContabil::where('empresa_id', $request->empresa_id)
        ->first();

        if ($item != null) {
            //update

            $item->fill($request->all())->save();
            session()->flash("flash_success", "Escritório contabil atualizado!");
        } else {

            EscritorioContabil::create($request->all());
            session()->flash("flash_success", "Escritório contabil cadastrado!");
        }
        return redirect()->back();
    }
}
