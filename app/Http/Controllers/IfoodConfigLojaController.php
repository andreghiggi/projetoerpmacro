<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IfoodConfig;
use App\Utils\IfoodUtil;

class IfoodConfigLojaController extends Controller
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

        $dataStatus = $this->util->statusMerchant($config);
        $dataInterruptions = $this->util->getInterruptions($config);
        dd($dataStatus);
        if(isset($dataStatus->message)){
            if($dataStatus->message == 'token expired'){
                return redirect()->route('ifood-config.index');
            }

            session()->flash("flash_error", $dataStatus->message);
            return redirect()->route('ifood-config.index');
        }

        if(is_array($dataStatus)){
            $dataMerchant = $dataStatus[0];
        }

        return view('ifood.config_loja', compact('dataMerchant', 'dataInterruptions'));
    }
}
