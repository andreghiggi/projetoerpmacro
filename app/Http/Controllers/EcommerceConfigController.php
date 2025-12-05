<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EcommerceConfig;
use App\Utils\UploadUtil;

class EcommerceConfigController extends Controller
{
    protected $util;

    public function __construct(UploadUtil $util)
    {
        $this->util = $util;
    }

    public function index(Request $request)
    {
        $item = EcommerceConfig::where('empresa_id', $request->empresa_id)
        ->first();

        if($item != null){
            $item->tipos_pagamento = json_decode($item->tipos_pagamento);
        }
        return view('ecommerce_config.index', compact('item'));
    }

    public function store(Request $request)
    {
        $item = EcommerceConfig::where('empresa_id', $request->empresa_id)
        ->first();

        $this->_validate($request, $item ? $item->id : null);

        if(!isset($request->tipos_pagamento)){
            $request->tipos_pagamento = [];
        }

        $request->merge([
            'frete_gratis_valor' => $request->frete_gratis_valor ? __convert_value_bd($request->frete_gratis_valor) : null,
            'tipos_pagamento' => json_encode($request->tipos_pagamento),
            'politica_privacidade' => $request->politica_privacidade ?? '',
            'termos_condicoes' => $request->termos_condicoes ?? '',
            'dados_deposito' => $request->dados_deposito ?? ''
        ]);

        if ($item != null) {
            //update
            $file_name_logo = $item->logo;

            if ($request->hasFile('logo_image')) {
                $this->util->unlinkImage($item, '/logos', 'logo');
                $file_name_logo = $this->util->uploadImage($request, '/logos', 'logo_image');
            }

            $request->merge([
                'logo' => $file_name_logo,
            ]);

            $item->fill($request->all())->save();
            session()->flash("flash_success", "Configuração atualizada!");
        } else {
            $file_name_logo = '';

            if ($request->hasFile('logo_image')) {
                $file_name_logo = $this->util->uploadImage($request, '/logos', 'logo_image');
            }

            $request->merge([
                'logo' => $file_name_logo,
            ]);

            EcommerceConfig::create($request->all());
            session()->flash("flash_success", "Configuração cadastrada!");
        }
        return redirect()->back();
    }

    private function _validate(Request $request, $id){
        $rules = [
            'loja_id' => [\Illuminate\Validation\Rule::unique('ecommerce_configs')->ignore($id)],
        ];

        $messages = [
            'loja_id.unique' => 'Já existe uma configuração de ecommerce com este ID.'
        ];
        $this->validate($request, $rules, $messages);
    }

    public function verSite(Request $request){
        $item = EcommerceConfig::where('empresa_id', $request->empresa_id)
        ->first();

        if($item == null){
            session()->flash("flash_warning", "Configure os dados do ecommerce!");
            return redirect()->route('config-ecommerce.index');
        }

        // return redirect(env("APP_URL") . "/loja/" . $item->loja_id);
        return redirect()->route('loja.index', "link=$item->loja_id");
    }

}
