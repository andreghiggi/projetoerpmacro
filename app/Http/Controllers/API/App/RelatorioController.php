<?php

namespace App\Http\Controllers\API\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\Nfce;
use App\Models\Empresa;
use App\Models\MovimentacaoProduto;
use App\Models\Estoque;
use App\Models\Produto;
use Dompdf\Dompdf;

class RelatorioController extends Controller
{
    public function vendas(Request $request){

        $tipo = $request->tipo;
        $start_date = $request->data_inicial;
        if($start_date){
            $start_date = \Carbon\Carbon::createFromFormat('d/m/Y', $start_date)->format('Y-m-d');
        }
        $end_date = $request->data_final;
        if($end_date){
            $end_date = \Carbon\Carbon::createFromFormat('d/m/Y', $end_date)->format('Y-m-d');
        }
        $local_id = $request->local_id;
        $cliente_id = $request->cliente_id;
        $start_time = $request->horario_inicial;
        $end_time = $request->horario_final;
        $estado = $request->estado;

        if($start_date){
            if($start_time){
                $start_date .= " $start_time:59";
            }else{
                $start_date .= " 00:00:00";
            }
        }

        if($end_date){
            if($end_time){
                $end_date .= " $end_time:59";
            }else{
                $end_date .= " 23:59:59";
            }
        }

        $vendas = Nfe::where('empresa_id', $request->empresa_id)->where('tpNF', 1)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->where('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->where('created_at', '<=', $end_date);
        })
        // ->where('nves.estado', '!=', 'cancelado')
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when(empty($estado), function ($query) use ($estado) {
            return $query->where('estado', '!=', 'cancelado');
        })
        ->limit($total_resultados ?? 1000000)
        // ->when($local_id, function ($query) use ($local_id) {
        //     return $query->where('local_id', $local_id);
        // })
        // ->when(!$local_id, function ($query) use ($locais) {
        //     return $query->whereIn('local_id', $locais);
        // })
        ->when($cliente_id, function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->get();

        $vendasCaixa = Nfce::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->where('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->where('created_at', '<=', $end_date);
        })

        ->where('nfces.empresa_id', $request->empresa_id)
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when(empty($estado), function ($query) use ($estado) {
            return $query->where('estado', '!=', 'cancelado');
        })
        ->limit($total_resultados ?? 1000000)
        // ->when($local_id, function ($query) use ($local_id) {
        //     return $query->where('local_id', $local_id);
        // })
        // ->when(!$local_id, function ($query) use ($locais) {
        //     return $query->whereIn('local_id', $locais);
        // })
        ->when($cliente_id, function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->get();

        $data = $this->uneArrayVendas($vendas, $vendasCaixa);

        usort($data, function($a, $b){
            return $a['data'] > $b['data'] ? 1 : -1;
        });

        $p = view('relatorios.vendas', compact('data', 'tipo'))
        ->with('title', 'Relatório de Vendas');

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        if (!is_dir(public_path('relatorios_app'))) {
            mkdir(public_path('relatorios_app'), 0777, true);
        }

        // limpar pasta

        $files = glob(public_path('relatorios_app/')."/*");
        foreach($files as $file){ 
            if(is_file($file)) unlink($file); 
        }

        $empresa = Empresa::findOrFail($request->empresa_id);
        $doc = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);

        $fileName = "Relatorio_vendas_$doc.pdf";
        file_put_contents(public_path('relatorios_app/') . $fileName , $domPdf->output());

        $url = env("APP_URL"). "/relatorios_app/".$fileName;
        return response()->json($url, 200);
    }

    private function uneArrayVendas($vendas, $vendasCaixa)
    {
        $adicionados = [];
        $arr = [];
        foreach ($vendas as $v) {
            $temp = [
                'id' => $v->numero_sequencial,
                'data' => $v->created_at,
                'tipo' => 'Pedido',
                'total' => $v->total,
                'cliente' => $v->cliente ? $v->cliente->info : '--',
                'localizacao' => $v->localizacao
                // 'itens' => $v->itens
            ];
            array_push($adicionados, $v->id);
            array_push($arr, $temp);
        }
        foreach ($vendasCaixa as $v) {
            $temp = [
                'id' => $v->numero_sequencial,
                'data' => $v->created_at,
                'tipo' => 'PDV',
                'total' => $v->total,
                'cliente' => $v->cliente ? $v->cliente->info : '--',
                'localizacao' => $v->localizacao
                // 'itens' => $v->itens
            ];
            array_push($adicionados, $v->id);
            array_push($arr, $temp);
        }
        return $arr;
    }

    public function estoque(Request $request){

        $estoque_minimo = $request->estoque_minimo;
        $start_date = $request->data_inicial;
        if($start_date){
            $start_date = \Carbon\Carbon::createFromFormat('d/m/Y', $start_date)->format('Y-m-d');
        }
        $end_date = $request->data_final;
        if($end_date){
            $end_date = \Carbon\Carbon::createFromFormat('d/m/Y', $end_date)->format('Y-m-d');
        }
        $categoria_id = $request->categoria_id;

        $data = [];

        if($estoque_minimo == 1){

            $produtosComEstoqueMinimo = Produto::where('produtos.empresa_id', $request->empresa_id)
            ->select('produtos.*')
            ->when($categoria_id, function ($query) use ($categoria_id) {
                return $query->where('categoria_id', $categoria_id);
            })
            // ->when(!empty($local_id), function ($query) use ($local_id) {
            //     return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            //     ->where('produto_localizacaos.localizacao_id', $local_id);
            // })
            // ->when(!$local_id, function ($query) use ($locais) {
            //     return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            //     ->whereIn('produto_localizacaos.localizacao_id', $locais);
            // })
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('produtos.created_at', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date,) {
                return $query->whereDate('produtos.created_at', '<=', $end_date);
            })
            // ->limit(20)
            ->where('produtos.estoque_minimo', '>', 0)->get();
            foreach($produtosComEstoqueMinimo as $produto){
                $estoque = Estoque::where('produto_id', $produto->id)->first();
                
                if($estoque == null || $estoque->quantidade <= $produto->estoque_minimo){

                    if(sizeof($produto->variacoes) == 0){
                        $qtd = $estoque ? $estoque->quantidade : '0';
                        if(!$local_id){
                            $qtd = $produto->estoqueTotalProduto();
                        }
                        $linha = [
                            'produto' => $produto->nome,
                            'quantidade' => $qtd,
                            'estoque_minimo' => $produto->estoque_minimo,
                            'valor_compra' => $produto->valor_compra,
                            'valor_venda' => $produto->valor_unitario,
                            'categoria' => $produto->categoria ? $produto->categoria->nome : '--',
                            'data_cadastro' => __data_pt($produto->created_at)
                        ];
                        array_push($data, $linha);
                    }else{

                        foreach($produto->variacoes as $v){
                            $linha = [
                                'produto' => $produto->nome . " " . $v->descricao,
                                'quantidade' => $v->estoque ? $v->estoque->quantidade : '',
                                'estoque_minimo' => $produto->estoque_minimo,
                                'valor_compra' => $produto->valor_compra,
                                'valor_venda' => $v->valor,
                                'categoria' => $produto->categoria ? $produto->categoria->nome : '--',
                                'data_cadastro' => __data_pt($produto->created_at)
                            ];
                            array_push($data, $linha);
                        }
                    }

                    
                }
            }
        }else if($start_date || $end_date){
            $movimentacoes = MovimentacaoProduto::
            select('movimentacao_produtos.*')
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('movimentacao_produtos.created_at', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date,) {
                return $query->whereDate('movimentacao_produtos.created_at', '<=', $end_date);
            })
            ->join('produtos', 'produtos.id', '=', 'movimentacao_produtos.produto_id')
            ->when($categoria_id, function ($query) use ($categoria_id) {
                return $query->where('produtos.categoria_id', $categoria_id);
            })
            // ->when(!empty($local_id), function ($query) use ($local_id) {
            //     return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            //     ->where('produto_localizacaos.localizacao_id', $local_id);
            // })
            // ->when(!$local_id, function ($query) use ($locais) {
            //     return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            //     ->whereIn('produto_localizacaos.localizacao_id', $locais);
            // })
            ->where('produtos.empresa_id', $request->empresa_id)
            ->groupBy('produtos.id')
            ->orderBy('movimentacao_produtos.created_at', 'desc')
            ->get();

            foreach($movimentacoes as $m){

                $produto = $m->produto;
                if(sizeof($produto->variacoes) == 0){
                    $linha = [
                        'produto' => $m->produto->nome,
                        'quantidade' => $m->produto->estoqueTotalProduto(),
                        'estoque_minimo' => $m->produto->estoque_minimo,
                        'valor_compra' => $m->produto->valor_compra,
                        'valor_venda' => $m->produto->valor_unitario,
                        'categoria' => $m->produto->categoria ? $m->produto->categoria->nome : '--',
                        'data_cadastro' => __data_pt($m->produto->created_at)
                    ];
                    array_push($data, $linha);
                }else{
                    foreach($produto->variacoes as $v){
                        $linha = [
                            'produto' => $m->produto->nome . " " . $v->descricao,
                            'quantidade' => $v->estoque ? $v->estoque->quantidade : '',
                            'estoque_minimo' => $m->produto->estoque_minimo,
                            'valor_compra' => $m->produto->valor_compra,
                            'valor_venda' => $v->valor,
                            'categoria' => $m->produto->categoria ? $m->produto->categoria->nome : '--',
                            'data_cadastro' => __data_pt($m->produto->created_at)
                        ];
                        array_push($data, $linha);
                    }
                }
            }

        }else{

            $estoque = Estoque::
            select('estoques.*')
            ->join('produtos', 'produtos.id', '=', 'estoques.produto_id')
            ->groupBy('produtos.id')
            ->when(!empty($local_id), function ($query) use ($local_id) {
                return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
                ->where('estoques.local_id', $local_id);
            })
            ->when($categoria_id, function ($query) use ($categoria_id) {
                return $query->where('produtos.categoria_id', $categoria_id);
            })
            ->where('produtos.empresa_id', $request->empresa_id)->get();

            foreach($estoque as $m){
                $produto = $m->produto;
                if(sizeof($produto->variacoes) == 0){
                    $linha = [
                        'produto' => $m->produto->nome,
                        'quantidade' => $m->quantidade,
                        'estoque_minimo' => $m->produto->estoque_minimo,
                        'valor_compra' => $m->produto->valor_compra,
                        'valor_venda' => $m->produto->valor_unitario,
                        'categoria' => $m->produto->categoria ? $m->produto->categoria->nome : '--',
                        'data_cadastro' => __data_pt($m->produto->created_at)
                    ];
                    array_push($data, $linha);
                }else{
                    foreach($produto->variacoes as $v){
                        $linha = [
                            'produto' => $m->produto->nome . " " . $v->descricao,
                            'quantidade' => $v->estoque ? $v->estoque->quantidade : '',
                            'estoque_minimo' => $m->produto->estoque_minimo,
                            'valor_compra' => $m->produto->valor_compra,
                            'valor_venda' => $v->valor,
                            'categoria' => $m->produto->categoria ? $m->produto->categoria->nome : '--',
                            'data_cadastro' => __data_pt($m->produto->created_at)
                        ];
                        array_push($data, $linha);
                    }
                }
            }
        }
        $localizacao = null;
        $p = view('relatorios.estoque', compact('data', 'start_date', 'end_date', 'estoque_minimo', 'localizacao'))
        ->with('title', 'Relatório de Estoque');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        if (!is_dir(public_path('relatorios_app'))) {
            mkdir(public_path('relatorios_app'), 0777, true);
        }

        // limpar pasta

        $files = glob(public_path('relatorios_app/')."/*");
        foreach($files as $file){ 
            if(is_file($file)) unlink($file); 
        }

        $empresa = Empresa::findOrFail($request->empresa_id);
        $doc = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);

        $fileName = "Relatorio_vendas_$doc.pdf";
        file_put_contents(public_path('relatorios_app/') . $fileName , $domPdf->output());

        $url = env("APP_URL"). "/relatorios_app/".$fileName;
        return response()->json($url, 200);
    }
}
