<?php

namespace App\Http\Controllers\API\Comanda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nfce;
use App\Models\ItemNfce;
use App\Models\FaturaNfce;
use App\Models\ConfigGeral;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Funcionario;
use App\Models\Produto;
use App\Models\SuprimentoCaixa;
use App\Models\SangriaCaixa;
use App\Models\Caixa;
use App\Models\ItemPedido;
use App\Models\Mesa;
use App\Models\ContaReceber;
use App\Utils\EstoqueUtil;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;
use Dompdf\Dompdf;
use NFePHP\DA\NFe\Danfce;
use App\Models\VendaSuspensa;

class FrontboxController extends Controller
{

    protected $util;

    public function __construct(EstoqueUtil $util)
    {
        $this->util = $util;
    }

    public function tiposDePagamento(Request $request){

        try{
            $tiposPagamento = Nfce::tiposPagamento();

            $config = ConfigGeral::where('empresa_id', $request->empresa_id)->first();
            $tiposPagamento = Nfce::tiposPagamento();

            if($config != null){
                $config->tipos_pagamento_pdv = $config != null && $config->tipos_pagamento_pdv ? json_decode($config->tipos_pagamento_pdv) : [];
                $temp = [];
                if(sizeof($config->tipos_pagamento_pdv) > 0){
                    foreach($tiposPagamento as $key => $t){
                        if(in_array($t, $config->tipos_pagamento_pdv)){
                            $temp[$key] = $t;
                        }
                    }
                    $tiposPagamento = $temp;
                }
            }
            $data = [];

            array_push($data, [
                'id' => '',
                'nome' => 'Selecione'
            ]);
            foreach($tiposPagamento as $key => $t){
                array_push($data, [
                    'id' => $key,
                    'nome' => $t
                ]);
            }
            return response()->json($data, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(), 401);
        }
    }

    private function getLastNumero($empresa_id){
        $last = Nfce::where('empresa_id', $empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;
        return $numero;
    }

    public function store(Request $request){
        try{
            $nfce = DB::transaction(function () use ($request) {

                $empresa = Empresa::find($request->empresa_id);

                $numero_nfce = $empresa->numero_ultima_nfce_producao+1;
                if ($empresa->ambiente == 2) {
                    $numero_nfce = $empresa->numero_ultima_nfce_homologacao+1;
                }

                $total = __convert_value_bd(str_replace("R$ ", "", $request->total));
                $desconto = __convert_value_bd(str_replace("R$ ", "", $request->desconto));
                $acrescimo = __convert_value_bd(str_replace("R$ ", "", $request->acrescimo));
                $valor_recebido = __convert_value_bd(str_replace("R$ ", "", $request->valor_recebido));

                $funcionario = Funcionario::where('codigo', $request->codigo_operador)
                ->where('empresa_id', $request->empresa_id)->first();

                if(!$funcionario->usuario_id){
                    return response()->json("Usuário não vinculado ao funcionário!", 401);
                }

                $caixa = Caixa::where('usuario_id', $funcionario->usuario_id)
                ->where('status', 1)
                ->first();

                if($caixa == null){
                    return response()->json("Caixa está fechado!", 401);
                }

                $objetoVenda = [
                    'cliente_id' => $request->cliente,
                    'natureza_id' => $empresa->natureza_id_pdv,
                    'ambiente' => $empresa->ambiente,
                    'total' => $total,
                    'desconto' => $desconto,
                    'acrescimo' => $acrescimo,
                    'estado' => 'novo',
                    'numero' => $numero_nfce,
                    'chave' => '',
                    'observacao' => $request->observacao,
                    'local_id' => $caixa->local_id,
                    'empresa_id' => $request->empresa_id,
                    'numero_serie' => $empresa->numero_serie_nfce ?? 1,
                    'cliente_cpf_cnpj' => $request->cpf_nota,
                    'cliente_nome' => '',
                    'troco' => $valor_recebido > 0 ? ($valor_recebido - $total) : 0,

                    'emissor_nome' => $empresa->nome,
                    'emissor_cpf_cnpj' => $empresa->cpf_cnpj,

                    'caixa_id' => $caixa->id,
                    'local_id' => $caixa->local_id,
                    'user_id' => $funcionario->usuario_id,
                    'numero_sequencial' => $this->getLastNumero($request->empresa_id)
                ];

                $nfce = Nfce::create($objetoVenda);

                foreach($request->itens as $item){
                    $product = Produto::findOrFail($item['produto_id']);

                    $variacao_id = isset($item['variacao_id']) ? $item['variacao_id'] : null;
                    ItemNfce::create([
                        'nfce_id' => $nfce->id,
                        'produto_id' => $product->id,
                        'quantidade' => $item['quantidade'],
                        'valor_unitario' => $product->valor_unitario,
                        'sub_total' => $item['sub_total'],
                        'perc_icms' => __convert_value_bd($product->perc_icms),
                        'perc_pis' => __convert_value_bd($product->perc_pis),
                        'perc_cofins' => __convert_value_bd($product->perc_cofins),
                        'perc_ipi' => __convert_value_bd($product->perc_ipi),
                        'cst_csosn' => $product->cst_csosn,
                        'cst_pis' => $product->cst_pis,
                        'cst_cofins' => $product->cst_cofins,
                        'cst_ipi' => $product->cst_ipi,
                        'cfop' => $product->cfop_estadual,
                        'ncm' => $product->ncm,
                        'variacao_id' => $variacao_id,
                    ]);

                    if ($product->gerenciar_estoque) {
                        $this->util->reduzEstoque($product->id, __convert_value_bd($request['quantidade']), $variacao_id, $caixa->local_id);

                        $tipo = 'reducao';
                        $codigo_transacao = $nfce->id;
                        $tipo_transacao = 'venda_nfce';

                        $this->util->movimentacaoProduto($product->id, __convert_value_bd($request['quantidade']), $tipo, $codigo_transacao, $tipo_transacao, $funcionario->usuario_id, $variacao_id);
                    }
                }

                if(sizeof($request->fatura) > 0){
                    foreach($request->fatura as $i => $fatura){
                        FaturaNfce::create([
                            'nfce_id' => $nfce->id,
                            'tipo_pagamento' => $fatura['tipo_pagamento'],
                            'data_vencimento' => $fatura['vencimento'],
                            'valor' => $fatura['valor']
                        ]);

                        $vencimento = $fatura['vencimento'];
                        $dataAtual = date('Y-m-d');

                        if(strtotime($vencimento) > strtotime($dataAtual)){
                            ContaReceber::create([
                                'nfe_id' => null,
                                'nfce_id' => $nfce->id,
                                'cliente_id' => $request->cliente,
                                'data_vencimento' => $fatura['vencimento'],
                                'data_recebimento' => $fatura['vencimento'],
                                'valor_integral' => $fatura['valor'],
                                'valor_recebido' => 0,
                                'status' => 0,
                                'referencia' => "Parcela $i+1 da venda código $nfce->id",
                                'empresa_id' => $request->empresa_id,
                                'juros' => 0,
                                'multa' => 0,
                                'observacao' => '',
                                'tipo_pagamento' => $fatura['tipo_pagamento'],
                                'local_id' => $caixa->local_id
                            ]);
                        }
                    }
                }else{
                    FaturaNfce::create([
                        'nfce_id' => $nfce->id,
                        'tipo_pagamento' => $request->tipo_pagamento,
                        'data_vencimento' => date('Y-m-d'),
                        'valor' => $nfce->total
                    ]);
                }

                if ($request->pedido_id) {

                    $pedido = Pedido::findOrfail($request->pedido_id);
                    $pedido->status = 0;
                    $pedido->em_atendimento = 0;
                    $pedido->nfce_id = $nfce->id;

                    Mesa::where('id', $pedido->mesa_id)->update(['ocupada' => 0]);

                    ItemPedido::where('pedido_id', $pedido->id)
                    ->update([ 'estado' => 'finalizado' ]);
                    $pedido->save();

                }

                if ($request->venda_suspensa_id) {
                    $vendaSuspensa = VendaSuspensa::findOrfail($request->venda_suspensa_id);
                    $vendaSuspensa->itens()->delete();
                    $vendaSuspensa->delete();
                }

                return $nfce;
            });

return response()->json($nfce, 200);
}catch(\Exception $e){
    return response()->json($e->getMessage(), 401);
}
}

public function caixa(Request $request){
    try{

        $funcionario = Funcionario::where('codigo', $request->codigo_operador)
        ->where('empresa_id', $request->empresa_id)->first();

        if(!$funcionario->usuario_id){
            return response()->json("Usuário não vinculado ao funcionário!", 401);
        }

        $item = Caixa::where('usuario_id', $funcionario->usuario_id)->where('status', 1)
        ->first();

        $vendas = Nfce::where('caixa_id', $item->id)
        ->with(['itens', 'cliente', 'fatura'])
        ->orderBy('id', 'desc')
        ->select('numero_sequencial', 'total', 'cliente_id', 'desconto', 'acrescimo', 'created_at', 'estado', 'id')
        ->get();

        foreach($vendas as $v){
            $v->tipo_pagamento = Nfce::getTipoPagamento($v->tipo_pagamento);
            foreach($v->fatura as $f){
                $f->tipo_pagamento_str = Nfce::getTipoPagamento($f->tipo_pagamento);
            }
        }

        $suprimentos = SuprimentoCaixa::where('caixa_id', $item->id)
        ->get();
        foreach($suprimentos as $v){
            $v->tipo_pagamento = Nfce::getTipoPagamento($v->tipo_pagamento);
        }

        $sangrias = SangriaCaixa::where('caixa_id', $item->id)
        ->get();
            // foreach($sangrias as $v){
            //     $v->tipo_pagamento = Nfce::getTipoPagamento($v->tipo_pagamento);
            // }

        $totalDeVendas = $vendas->sum('total');
        $totalSangrias = $sangrias->sum('valor');
        $totalSuprimentos = $suprimentos->sum('valor');
        $data = [
            'valor_abertura' => $item->valor_abertura,
            'observacao' => $item->observacao,
            'data_abertura' => $item->created_at,
            'vendas' => $vendas,
            'status' => $item->status,
            'suprimentos' => $suprimentos,
            'sangrias' => $sangrias,
            'totalDeVendas' => $totalDeVendas,
            'totalSangrias' => $totalSangrias,
            'totalSuprimentos' => $totalSuprimentos,
        ];

        return response()->json($data, 200);

    }catch(\Exception $e){
        return response()->json($e->getMessage(), 401);
    }
}

public function sangriaStore(Request $request){
    try{

        $funcionario = Funcionario::where('codigo', $request->codigo_operador)
        ->where('empresa_id', $request->empresa_id)->first();

        if(!$funcionario->usuario_id){
            return response()->json("Usuário não vinculado ao funcionário!", 401);
        }

        $caixa = Caixa::where('usuario_id', $funcionario->usuario_id)->where('status', 1)
        ->first();

        $valor = __convert_value_bd(str_replace("R$ ", "", $request->valor));

        $sangria = SangriaCaixa::create([
            'caixa_id' => $caixa->id,
            'valor' => $valor,
            'observacao' => $request->observacao ?? '',
            'conta_empresa_id' => null
        ]);

        return response()->json("ok", 200);
    }catch(\Exception $e){
        return response()->json($e->getMessage(), 401);
    }
}

public function suprimentoStore(Request $request){
    try{

        $funcionario = Funcionario::where('codigo', $request->codigo_operador)
        ->where('empresa_id', $request->empresa_id)->first();

        if(!$funcionario->usuario_id){
            return response()->json("Usuário não vinculado ao funcionário!", 401);
        }

        $caixa = Caixa::where('usuario_id', $funcionario->usuario_id)->where('status', 1)
        ->first();

        $valor = __convert_value_bd(str_replace("R$ ", "", $request->valor));

        $sangria = SuprimentoCaixa::create([
            'caixa_id' => $caixa->id,
            'valor' => $valor,
            'tipo_pagamento' => $request->tipo_pagamento,
            'observacao' => $request->observacao ?? '',
            'conta_empresa_id' => null
        ]);

        return response()->json("ok", 200);
    }catch(\Exception $e){
        return response()->json($e->getMessage(), 401);
    }
}

public function fecharCaixa(Request $request){
    try{

        $funcionario = Funcionario::where('codigo', $request->codigo_operador)
        ->where('empresa_id', $request->empresa_id)->first();

        if(!$funcionario->usuario_id){
            return response()->json("Usuário não vinculado ao funcionário!", 401);
        }

        $item = Caixa::where('usuario_id', $funcionario->usuario_id)->where('status', 1)
        ->first();

        $item->status = 0;
        $item->data_fechamento = date('Y-m-d h:i:s');

        $item->save();

        return response()->json("ok", 200);
    }catch(\Exception $e){
        return response()->json($e->getMessage(), 401);
    }
}

public function abrirCaixa(Request $request){
    try{

        $funcionario = Funcionario::where('codigo', $request->codigo_operador)
        ->where('empresa_id', $request->empresa_id)->first();

        if(!$funcionario->usuario_id){
            return response()->json("Usuário não vinculado ao funcionário!", 401);
        }

        $valor = __convert_value_bd(str_replace("R$ ", "", $request->valor));

        $local_id = null;
        $user = User::findOrFail($funcionario->usuario_id);
        if(!$request->local_id){
            if(sizeof($user->locais) > 0){
                $local_id = $user->locais[0]->localizacao_id;
            }
        }else{
            $local_id = $request->local_id;
        }
        $data = [
            'usuario_id' => $funcionario->usuario_id,
            'valor_abertura' => $valor ?? 0,
            'observacao' => $request->observacao ?? '',
            'conta_empresa_id' => null,
            'local_id' => $local_id,
            'status' => 1,
            'valor_fechamento' => 0,
            'empresa_id' => $request->empresa_id
        ];
        $item = Caixa::create($data);

        return response()->json("ok", 200);
    }catch(\Exception $e){
        return response()->json($e->getMessage(), 401);
    }
}

public function cupomNaoFiscal(Request $request){
    $item = Nfce::findOrFail($request->id);
    try{
        if (!is_dir(public_path('comprovantes_pdv'))) {
            mkdir(public_path('comprovantes_pdv'), 0777, true);
        }else{
            $destino = public_path('comprovantes_pdv');
            $this->clearFolder($destino);
        }
        $config = Empresa::where('id', $item->empresa_id)
        ->first();

        $configGeral = ConfigGeral::where('empresa_id', $item->empresa_id)->first();
        $p = view('front_box.cupom_nao_fiscal', compact('config', 'item', 'configGeral'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $height = 320;

        $height += sizeof($item->itens)*11;

        if($item->observacao != ''){
            $height += 30;
        }

        if($configGeral->mensagem_padrao_impressao_venda != ''){
            $height += 30;
        }
        
        $domPdf->setPaper([0,0,244,$height]);
        $domPdf->render();

        $fileName = "comprovante_$item->id.pdf";
        file_put_contents(public_path('comprovantes_pdv/') . $fileName , $domPdf->output());

        $url = env("APP_URL"). "/comprovantes_pdv/".$fileName;
        return response()->json($url, 200);

    }catch(\Exception $e){
        return response()->json($e->getMessage(), 401);
    }
}

public function cupomFiscal(Request $request){
    $item = Nfce::findOrFail($request->id);
    try{

        if (!is_dir(public_path('comprovantes_pdv'))) {
            mkdir(public_path('comprovantes_pdv'), 0777, true);
        }else{
            $destino = public_path('comprovantes_pdv');
            $this->clearFolder($destino);
        }

        if (file_exists(public_path('xml_nfce/') . $item->chave . '.xml')) {
            $xml = file_get_contents(public_path('xml_nfce/') . $item->chave . '.xml');
            $danfe = new Danfce($xml, $item);
            $empresa = $item->empresa;
            $empresa = __objetoParaEmissao($empresa, $item->local_id);

            if($empresa->logo){
                $logo = 'data://text/plain;base64,'. base64_encode(file_get_contents(public_path('/uploads/logos/') . $empresa->logo));
                $danfe->logoParameters($logo, 'L');
            }
            $pdf = $danfe->render();

            $fileName = "comprovante_$item->id.pdf";
            file_put_contents(public_path('comprovantes_pdv/') . $fileName , $pdf);

            $url = env("APP_URL"). "/comprovantes_pdv/".$fileName;
            
            return response()->json($url, 200);

        } else {
            return response()->json("Arquivo não encontrado", 401);
        }

    }catch(\Exception $e){
        return response()->json($e->getMessage(), 401);
    }
}

private function clearFolder($destino){
    $files = glob($destino."/*");
    foreach($files as $file){ 
        if(is_file($file)) unlink($file); 
    }
}

}
