<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use App\Models\Nfce;
use App\Models\Nfe;
use App\Models\SangriaCaixa;
use App\Models\Empresa;
use App\Models\SuprimentoCaixa;
use App\Models\ItemContaEmpresa;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;
use Svg\Tag\Rect;
use App\Utils\ContaEmpresaUtil;
use Dompdf\Dompdf;

class SuprimentoController extends Controller
{

    protected $util;
    public function __construct(ContaEmpresaUtil $util){
        $this->util = $util;
    }

    public function store(Request $request)
    {
        try {
            if(!$request->valor || __convert_value_bd($request->valor) == 0){
                session()->flash("flash_error", "Informe um valor maior que zero");
                return redirect()->back();
            }
            $suprimento = SuprimentoCaixa::create([
                'caixa_id' => $request->caixa_id,
                'valor' => __convert_value_bd($request->valor),
                'observacao' => $request->observacao ?? '',
                'tipo_pagamento' => $request->tipo_pagamento,
                'conta_empresa_id' => $request->conta_empresa_suprimento_id ?? null
            ]);

            if($request->conta_empresa_sangria_id){
                $caixa = Caixa::findOrFail($request->caixa_id);
                $data = [
                    'conta_id' => $caixa->conta_empresa_id,
                    'descricao' => "Suprimento de caixa",
                    'tipo_pagamento' => $request->tipo_pagamento,
                    'valor' => __convert_value_bd($request->valor),
                    'caixa_id' => $caixa->id,
                    'tipo' => 'entrada'
                ];
                $itemContaEmpresa = ItemContaEmpresa::create($data);
                $this->util->atualizaSaldo($itemContaEmpresa);

                $data = [
                    'conta_id' => $request->conta_empresa_sangria_id,
                    'descricao' => "Suprimento de caixa",
                    'tipo_pagamento' => $request->tipo_pagamento,   
                    'valor' => __convert_value_bd($request->valor),
                    'caixa_id' => $caixa->id,
                    'tipo' => 'saida'
                ];
                $itemContaEmpresa = ItemContaEmpresa::create($data);
                $this->util->atualizaSaldo($itemContaEmpresa);
            }
            session()->flash("flash_success", "Suprimento realizado com sucesso!");
            return redirect()->back()->with(['suprimento_id' => $suprimento->id]);

        } catch (\Exception $e) {
            // echo $e->getMessage();
            // die;
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function print($id){
        $suprimento = SuprimentoCaixa::findOrfail($id);
        $empresa = Empresa::findOrFail(request()->empresa_id);
        $p = view('front_box.suprimento_print', compact('suprimento', 'empresa'));
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->set_paper(array(0,0,214,220));
        $domPdf->render();
        $domPdf->stream("Comprovante de suprimento.pdf", array("Attachment" => false));
        exit();
    }

    public function destroy($id){
        $item = SuprimentoCaixa::findOrfail($id);

        try{
            $item->delete();
            session()->flash("flash_success", "Suprimento removido!");
        }catch(\Exception $e){
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

}
