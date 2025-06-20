<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Empresa;
use App\Models\HospedeReserva;

class ReservaClienteController extends Controller
{
    public function index($codigo){
        $item = Reserva::where('codigo_reseva', $codigo)
        ->first();

        if($item == null){
            abort(404);
        }

        $empresa = Empresa::findOrFail($item->empresa_id);

        return view('reservas.checkin_cliente', compact('item', 'empresa'));
    }

    public function checkinStart(Request $request, $id){
        $item = Reserva::findOrFail($id);

        try{

            for($i=0; $i<sizeof($request->nome_completo); $i++){
                $hospede = HospedeReserva::findOrFail($request->hospede_id[$i]);

                $hospede->nome_completo = $request->nome_completo[$i];
                $hospede->cpf = $request->cpf[$i];
                $hospede->cep = $request->cep[$i];
                $hospede->rua = $request->rua[$i];
                $hospede->numero = $request->numero[$i];
                $hospede->bairro = $request->bairro[$i];
                $hospede->cidade_id = $request->cidade_id[$i];
                $hospede->telefone = $request->telefone[$i];
                $hospede->email = $request->email[$i];

                $hospede->save();
            }

            $item->data_checkin_realizado = date('Y-m-d H:i:s');
            $item->estado = 'iniciado';
            $item->save();
            session()->flash("flash_success", "Checkin realizado com sucesso!");
            return redirect()->back();
        } catch (\Exception $e) {
            echo 'Algo deu errado: '. $e->getMessage();
            die;
        }
    }

}
