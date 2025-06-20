<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailConfig;

class EmailConfigController extends Controller
{
    public function index(Request $request){
        $item = EmailConfig::where('empresa_id', $request->empresa_id)
        ->first();

        return view('email_config.index', compact('item'));
    }

    public function store(Request $request)
    {

        $item = EmailConfig::where('empresa_id', $request->empresa_id)
        ->first();

        if ($item != null) {
            //update

            $item->fill($request->all())->save();
            session()->flash("flash_success", "Configuração atualizada!");
        } else {

            EmailConfig::create($request->all());
            session()->flash("flash_success", "Configuração cadastrada!");
        }
        return redirect()->back();
    }
}
