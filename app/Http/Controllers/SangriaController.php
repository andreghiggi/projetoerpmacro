<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use App\Models\Nfce;
use App\Models\Nfe;
use App\Models\Empresa;
use App\Models\SangriaCaixa;
use App\Models\SuprimentoCaixa;
use App\Models\ItemContaEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\ContaEmpresaUtil;
use Dompdf\Dompdf;

class SangriaController extends Controller
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
            if (__convert_value_bd($request->valor) <= $this->somaTotalEmCaixa()) {
                $sangria = SangriaCaixa::create([
                    'caixa_id' => $request->caixa_id,
                    'valor' => __convert_value_bd($request->valor),
                    'observacao' => $request->observacao ?? '',
                    'conta_empresa_id' => $request->conta_empresa_sangria_id ?? null
                ]);

                if($request->conta_empresa_sangria_id){
                    $caixa = Caixa::findOrFail($request->caixa_id);
                    $data = [
                        'conta_id' => $caixa->conta_empresa_id,
                        'descricao' => "Sangria de caixa",
                        'tipo_pagamento' => '01',
                        'valor' => __convert_value_bd($request->valor),
                        'caixa_id' => $caixa->id,
                        'tipo' => 'saida'
                    ];
                    $itemContaEmpresa = ItemContaEmpresa::create($data);
                    $this->util->atualizaSaldo($itemContaEmpresa);

                    $data = [
                        'conta_id' => $request->conta_empresa_sangria_id,
                        'descricao' => "Sangria de caixa",
                        'tipo_pagamento' => '01',   
                        'valor' => __convert_value_bd($request->valor),
                        'caixa_id' => $caixa->id,
                        'tipo' => 'entrada'
                    ];
                    $itemContaEmpresa = ItemContaEmpresa::create($data);
                    $this->util->atualizaSaldo($itemContaEmpresa);
                }
                session()->flash("flash_success", "Sangria realizada com sucesso!");


                return redirect()->back()->with(['sangria_id' => $sangria->id]);

            } else {
                session()->flash("flash_warning", "Valor da Sangria ultrapassa o valor disponível!");
                redirect()->back();
            }
        } catch (\Exception $e) {
            // echo $e->getMessage();
            // die;
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
            return redirect()->back();
        }

    }

    public function print($id){
        $sangria = SangriaCaixa::findOrfail($id);
        $empresa = Empresa::findOrFail(request()->empresa_id);
        $p = view('front_box.sangria_print', compact('sangria', 'empresa'));
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->set_paper(array(0,0,214,220));
        $domPdf->render();
        $domPdf->stream("Comprovante de sangria.pdf", array("Attachment" => false));
        exit();
    }

    private function somaTotalEmCaixa()
    {
        $usuario_id = Auth::user()->id;
        $abertura = Caixa::where('empresa_id', request()->empresa_id)->where('status', 1)->where('usuario_id', $usuario_id)
        ->orderBy('id', 'desc')
        ->first();
        // dd($abertura->valor_abertura);
        if ($abertura == null) return 0;
        $soma = 0;
        $soma += $abertura->valor_abertura;
        $nfce = Nfce::selectRaw('sum(total) as valor')->where('empresa_id', request()->empresa_id)->where('caixa_id', $abertura->id)
        ->first();
        if ($nfce != null)
            $soma += $nfce->valor;
        $nfe = Nfe::selectRaw('sum(total) as valor')->where('empresa_id', request()->empresa_id)->where('caixa_id', $abertura->id)
        ->first();
        if ($nfe != null)
            $soma += $nfe->valor;
        $amanha = date('Y-m-d', strtotime('+1 days')) . " 00:00:00";
        $suprimentosSoma = SuprimentoCaixa::selectRaw('sum(valor) as valor')->whereBetween('created_at', [$abertura->created_at, $amanha])
        ->where('caixa_id', $abertura->id)
        ->first();
        if ($suprimentosSoma != null)
            $soma += $suprimentosSoma->valor;
        $sangriasSoma = SangriaCaixa::selectRaw('sum(valor) as valor')->whereBetween('created_at', [$abertura->created_at, $amanha])
        ->where('caixa_id', $abertura->id)
        ->first();
        if ($sangriasSoma != null)
            $soma -= $sangriasSoma->valor;
        return $soma;
    }

    public function destroy($id){
        $item = SangriaCaixa::findOrfail($id);

        try{
            $item->delete();
            session()->flash("flash_success", "Sangria removida!");
        }catch(\Exception $e){
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }
}
