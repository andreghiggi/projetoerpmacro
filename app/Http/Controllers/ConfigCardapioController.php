<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfiguracaoCardapio;
use App\Utils\UploadUtil;

class ConfigCardapioController extends Controller
{
    protected $util;

    public function __construct(UploadUtil $util)
    {
        $this->util = $util;
    }

    public function index(Request $request)
    {
        $item = ConfiguracaoCardapio::where('empresa_id', $request->empresa_id)
        ->first();

        return view('cardapio.config.index', compact('item'));
    }

    public function store(Request $request)
    {
        $this->__validate($request);

        $item = ConfiguracaoCardapio::where('empresa_id', $request->empresa_id)
        ->first();

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
                'fav_icon' => $file_name_fav,
                'percentual_taxa_servico' => $request->percentual_taxa_servico ?? 0,
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
                'fav_icon' => $file_name_fav,
                'percentual_taxa_servico' => $request->percentual_taxa_servico ?? 0,
            ]);

            ConfiguracaoCardapio::create($request->all());
            session()->flash("flash_success", "Configuração cadastrada!");
        }
        return redirect()->back();
    }

    private function __validate(Request $request)
    {
        $rules = [
            'api_token' => 'required'
        ];
        $messages = [
            'api_tokken.required' => 'Campo Obrigatório'
        ];
        $this->validate($request, $rules, $messages);
    }

    public function download(){
        return response()->download(public_path('app.apk'));
    }
    
}
