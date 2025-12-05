<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\IfoodUtil;
use App\Models\IfoodConfig;

class IfoodConfigController extends Controller
{

    protected $util;

    public function __construct(IfoodUtil $util)
    {
        $this->util = $util;
    }
    
    public function index(Request $request){
        $item = IfoodConfig::
        where('empresa_id', $request->empresa_id)
        ->first();

        return view('ifood.config', compact('item'));
    }

    public function store(Request $request){
        $item = IfoodConfig::where('empresa_id', $request->empresa_id)
        ->first();

        $request->merge(['grantType' => 'authorization_code']);

        if ($item != null) {

            $item->fill($request->all())->save();
            session()->flash("flash_success", "Configuração atualizada!");
        } else {
            IfoodConfig::create($request->all());
            session()->flash("flash_success", "Configuração cadastrada!");
        }
        return redirect()->back();
    }

    public function userCode(Request $request){
        $item = IfoodConfig::
        where('empresa_id', $request->empresa_id)
        ->first();

        $userCode = $this->util->getUserCode($item);
        if($userCode){
            session()->flash("flash_success", "Novo código gerado!");
            return redirect()->back();
        }
    }

    public function getToken(Request $request){
        $item = IfoodConfig::
        where('empresa_id', $request->empresa_id)
        ->first();
        $result = $this->util->oAuthToken($item);

        if($result['success'] == 0){
            $result = $this->util->newToken($item);
            // session()->flash("mensagem_erro", "Algo deu errado ao gerar token: " . $result['message']);
        }else{
            session()->flash("flash_success", "Token gerado!");
        }

        return redirect()->back();
    }

}
