<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BairroDeliveryMaster;
use App\Models\BairroDelivery;
use App\Models\MarketPlaceConfig;

class BairroEmpresaController extends Controller
{

    public function index(Request $request)
    {
        $config = MarketPlaceConfig::where('empresa_id', $request->empresa_id)
        ->first();
        if($config == null){
            session()->flash("flash_warning", 'Primeiro configure o delivery');
            return redirect()->route('config-marketplace.index');
        }

        $data = BairroDelivery::
        when(!empty($request->nome), function ($q) use ($request) {
            return $q->where('nome', 'LIKE', "%$request->nome%");
        })
        ->where('empresa_id', $request->empresa_id)
        ->paginate(__itensPagina());

        return view('bairros.index', compact('config', 'data'));
    }

    public function create()
    {
        return view('bairros.create');
    }

    public function edit($id)
    {
        $item = BairroDelivery::findOrFail($id);
        return view('bairros.edit', compact('item'));
    }

    public function store(Request $request)
    {
        try {
            $request->merge([
                'valor_entrega' => __convert_value_bd($request->valor_entrega)
            ]);
            BairroDelivery::create($request->all());
            session()->flash("flash_success", "Bairro cadastrado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('bairros-empresa.index');
    }

    public function update(Request $request, $id)
    {
        $item = BairroDelivery::findOrFail($id);
        try {
            $request->merge([
                'valor_entrega' => __convert_value_bd($request->valor_entrega)
            ]);
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Bairro alterado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('bairros-empresa.index');
    }

    public function destroy($id)
    {
        $item = BairroDelivery::findOrFail($id);
        try {
            $item->delete();
            session()->flash("flash_success", "Removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('bairros-empresa.index');
    }

    public function super(Request $request){
        $config = MarketPlaceConfig::where('empresa_id', $request->empresa_id)
        ->first();

        $data = BairroDeliveryMaster::where('cidade_id', $config->cidade_id)
        ->get();

        if(sizeof($data) == 0){
            session()->flash("flash_error", 'Nenhum bairro encontrado!');
            return redirect()->back();
        }

        foreach($data as $item){
            $bairro = BairroDelivery::where('empresa_id', $request->empresa_id)
            ->where('bairro_delivery_super', $item->id)
            ->first();
            $item->valor_entrega = $bairro != null ? $bairro->valor_entrega : '';
        }

        return view('bairros.super', compact('config', 'data'));

    }

    public function setBairros(Request $request){
        $cont = 0;
        try {

            for($i=0; $i<sizeof($request->id); $i++){
                if($request->valor_entrega[$i]){
                    $item = BairroDeliveryMaster::findOrfail($request->id[$i]);

                    $bairro = BairroDelivery::where('empresa_id', $request->empresa_id)
                    ->where('bairro_delivery_super', $item->id)
                    ->first();
                    if($bairro == null){
                        BairroDelivery::create([
                            'nome' => $item->nome,
                            'bairro_delivery_super' => $item->id,
                            'valor_entrega' => __convert_value_bd($request->valor_entrega[$i]),
                            'empresa_id' => $request->empresa_id,
                            'status' => 1
                        ]);
                    }else{
                        $bairro->update([
                            'valor_entrega' => __convert_value_bd($request->valor_entrega[$i]),
                        ]);
                    }
                    $cont++;
                }
            }
            if($cont > 0){
                session()->flash("flash_success", "Bairros atribuídos!");
            }else{
                session()->flash("flash_warning", "Informe o valor de entrega para atribuir");
            }
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('bairros-empresa.index');
    }

}
