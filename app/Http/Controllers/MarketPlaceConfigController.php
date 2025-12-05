<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketPlaceConfig;
use App\Utils\UploadUtil;

class MarketPlaceConfigController extends Controller
{
    protected $util;

    public function __construct(UploadUtil $util)
    {
        $this->util = $util;
    }

    public function index(Request $request)
    {
        $item = MarketPlaceConfig::where('empresa_id', $request->empresa_id)
        ->first();

        if($item != null){
            $item->tipos_pagamento = json_decode($item->tipos_pagamento);
            $item->segmento = json_decode($item->segmento);
            $item->tipo_entrega = json_decode($item->tipo_entrega ? $item->tipo_entrega : '[]');
        }

        return view('marketplace_config.index', compact('item'));
    }

    public function store(Request $request)
    {

        $item = MarketPlaceConfig::where('empresa_id', $request->empresa_id)
        ->first();

        $this->__validate($request, $item ? $item->id : null);

        if(!isset($request->tipos_pagamento)){
            $request->tipos_pagamento = [];
        }

        if(!isset($request->segmento)){
            $request->segmento = [];
        }

        if(!isset($request->tipo_entrega)){
            $request->tipo_entrega = [];
        }

        $request->merge([
            'pedido_minimo' => $request->pedido_minimo ? __convert_value_bd($request->pedido_minimo) : null,
            'valor_entrega' => __convert_value_bd($request->valor_entrega),
            'valor_entrega_gratis' => $request->valor_entrega_gratis ? __convert_value_bd($request->valor_entrega_gratis) : null,
            'tipos_pagamento' => json_encode($request->tipos_pagamento),
            'segmento' => json_encode($request->segmento),
            'tipo_entrega' => json_encode($request->tipo_entrega),
        ]);

        if ($item != null) {
            //update
            $file_name_logo = $item->logo;
            $file_name_fav = $item->fav_icon;

            if ($request->hasFile('logo_image')) {
                $this->util->unlinkImage($item, '/logos', 'logo');
                $file_name_logo = $this->util->uploadImage($request, '/logos', 'logo_image');
            }

            if ($request->hasFile('fav_icon_image')) {
                $this->util->unlinkImage($item, '/fav_icons', 'fav_icon');
                $file_name_fav = $this->util->uploadImage($request, '/fav_icons', 'fav_icon_image');
            }
            $request->merge([
                'logo' => $file_name_logo,
                'fav_icon' => $file_name_fav
            ]);

            $item->fill($request->all())->save();
            session()->flash("flash_success", "Configuração atualizada!");
        } else {
            $file_name_logo = '';
            $file_name_fav = '';

            if ($request->hasFile('logo_image')) {
                $file_name_logo = $this->util->uploadImage($request, '/logos', 'logo_image');
            }

            if ($request->hasFile('fav_icon_image')) {
                $file_name_fav = $this->util->uploadImage($request, '/fav_icons', 'fav_icon_image');
            }

            $request->merge([
                'logo' => $file_name_logo,
                'fav_icon' => $file_name_fav
            ]);

            MarketPlaceConfig::create($request->all());
            session()->flash("flash_success", "Configuração cadastrada!");
        }
        return redirect()->back();
    }

    private function __validate(Request $request, $id)
    {
        $rules = [
            'loja_id' => [\Illuminate\Validation\Rule::unique('market_place_configs')->ignore($id)],  
        ];
        $messages = [
            'loja_id.unique' => 'Já existe uma configuração de delivery com este ID.'
        ];
        $this->validate($request, $rules, $messages);
    }

    public function verLoja(Request $request){
        $item = MarketPlaceConfig::where('empresa_id', $request->empresa_id)
        ->first();

        if($item == null){
            session()->flash("flash_warning", "Configure os dados do delivery!");
            return redirect()->route('config-marketplace.index');
        }

        if($item->loja_id == null){
            session()->flash("flash_warning", "Defina o Loja ID!");
            return redirect()->route('config-marketplace.index');
        }

        return redirect()->route('food.index', "link=$item->loja_id");

    }
}
