<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\PlanoContaUtil;
use App\Models\PlanoConta;

class PlanoContaController extends Controller
{

    protected $util;
    
    public function __construct(PlanoContaUtil $util){
        $this->util = $util;
    }

    public function index(Request $request){
        $data = PlanoConta::where('empresa_id', $request->empresa_id)
        ->orderBy('descricao')
        ->get();

        return view('plano_contas.index', compact('data'))
        ->with('title', 'Plano de Contas');
    }

    public function start(Request $request){
        try{ 
            $this->util->criaPlanoDeContas($request->empresa_id);
            session()->flash("flash_success", "Plano de contas criado!");
        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function store(Request $request){
        if($request->plano_conta_id){

            $plano = PlanoConta::findOrFail($request->plano_conta_id);
            $grau = $plano->grauItem();
            // echo $plano;
            // die;
            $ultimo = $plano->dependentes->last();
            $descricao = "";

            if($ultimo){
                $temp = explode("-", $ultimo->descricao);
                $temp = trim($temp[0]);

                $temp = explode(".", $temp);
                foreach($temp as $key => $t){
                    if(sizeof($temp)-1 > $key){
                        $descricao .= "$t.";
                    }else{
                        if($grau != 5){
                            $descricao .= (int)$t+1;
                        }else{
                            $descricao .= "0".((int)$t+1);
                        }
                    }
                }
            }else{
                $descricao = explode("-", $plano->descricao);
                $descricao = trim($descricao[0]) . ".01";
            }

            
            $descricao = $descricao . " - $request->descricao";

            PlanoConta::create([
                'empresa_id' => $request->empresa_id,
                'descricao' => $descricao,
                'plano_conta_id' => $request->plano_conta_id
            ]);
            session()->flash("flash_success", "Registro adicionado!");
        }else{
            $plano = PlanoConta::findOrFail($request->edit_id);
            $plano->descricao = $request->descricao;
            $plano->save();
            session()->flash("flash_success", "Registro atualizado!");
        }
        return redirect()->back();
    }

    public function destroy($id){
        $item = PlanoConta::findOrFail($id);
        $item->delete();
        session()->flash("flash_error", "Registro removido");
        return redirect()->back();
    }

}
