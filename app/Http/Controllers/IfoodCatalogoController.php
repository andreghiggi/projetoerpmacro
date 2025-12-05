<?php

namespace App\Http\Controllers;
use App\Models\IfoodConfig;
use App\Utils\IfoodUtil;
use Illuminate\Http\Request;

class IfoodCatalogoController extends Controller
{

    protected $util;

    public function __construct(IfoodUtil $util)
    {
        $this->util = $util;
    }

    public function index(Request $request){

        $config = IfoodConfig::
        where('empresa_id', $request->empresa_id)
        ->first();

        if($config == null){
            session()->flash("flash_error", "Configure o App");
            return redirect()->route('ifood-config.index');
        }

        $data = $this->util->getCatalogs($config);
        // dd($data);
        if(isset($data->message)){
            session()->flash("flash_error", $data->message);
            return redirect()->route('ifood-config.index');
        }
        return view('ifood.catalogos', compact('data', 'config'));

    }

    public function definir($id){
        $config = IfoodConfig::
        where('empresa_id', request()->empresa_id)
        ->first();

        $config->catalogId = $id;
        $config->save();
        session()->flash("flash_success", "Catálogo definido!");
        return redirect()->back();
    }

}
