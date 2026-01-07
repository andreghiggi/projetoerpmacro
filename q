[1mdiff --git a/app/Console/Commands/SyncOrdersConectaVendaCron.php b/app/Console/Commands/SyncOrdersConectaVendaCron.php[m
[1mindex efc4ce8..003f705 100644[m
[1m--- a/app/Console/Commands/SyncOrdersConectaVendaCron.php[m
[1m+++ b/app/Console/Commands/SyncOrdersConectaVendaCron.php[m
[36m@@ -7,6 +7,7 @@[m [muse App\Models\ConectaVendaPedido;[m
 use App\Models\Empresa;[m
 use App\Utils\ConectaVendaSincronizador;[m
 use App\Utils\HttpUtil;[m
[32m+[m[32muse App\Utils\EstoqueUtil;[m
 use Illuminate\Console\Command;[m
 use Illuminate\Support\Facades\DB;[m
 use Illuminate\Support\Facades\Http;[m
[36m@@ -71,12 +72,16 @@[m [mclass SyncOrdersConectaVendaCron extends Command[m
             if($response->status() == 200){[m
                 $conecta_pedidos = json_decode($response);[m
                 foreach($conecta_pedidos->dados as $conecta_pedido){[m
[31m-                    $pedido = ConectaVendaPedido::where('conecta_pedido_id', $conecta_pedido->id)->first();[m
[32m+[m[32m                    /** @var ConectaVendaPedido|null $pedido */[m[41m [m
[32m+[m[32m                    $pedido               = ConectaVendaPedido::where('conecta_pedido_id', $conecta_pedido->id)->first();[m
[32m+[m[41m                    [m
 [m
                     if(!$pedido){[m
                         $pedido = $this->util->createOrder($conecta_pedido, $config);[m
                     }[m
 [m
[32m+[m[32m                    $pedido_situacao_atual = $pedido->situacao;[m
[32m+[m
                     $situacoes_skip_update = [[m
                         "Finalizado"[m
                     ];[m
[36m@@ -88,8 +93,22 @@[m [mclass SyncOrdersConectaVendaCron extends Command[m
                     $pedido->situacao = $conecta_pedido->situacao;[m
                     $pedido->save();[m
 [m
[31m-                    // if($conecta_pedido->situacao == 'Cancelado' || $conecta_pedido->situacao == 'cancelado'){[m
[31m-                    //     $this->util->returnStock($conecta_pedido, $config);[m
[32m+[m[32m                    $situacoes_devolver_estoque = [[m
[32m+[m[32m                        'em aberto',[m
[32m+[m[32m                        'pago',[m
[32m+[m[32m                    ];[m
[32m+[m
[32m+[m[32m                    $pedido_situacao_nova = strtolower($conecta_pedido->situacao);[m
[32m+[m[32m                    $devolver_estoque     = $pedido_situacao_nova == 'cancelado' && in_array( $pedido_situacao_atual, $situacoes_devolver_estoque );[m
[32m+[m
[32m+[m[32m                    if( $devolver_estoque ){[m
[32m+[m
[32m+[m[32m                        $estoque_util = new EstoqueUtil();[m
[32m+[m
[32m+[m[32m                        foreach($pedido->produtos() as $produto) {[m
[32m+[m[32m                            $estoque_util->reduzEstoque( $produto->produto_id, $produto->qtde, $produto->variacao_id );[m
[32m+[m[32m                        }[m
[32m+[m[32m                    }[m
                     // }else {[m
                         [m
                     // }[m
[1mdiff --git a/app/Http/Controllers/ConectaVendaPedidoController.php b/app/Http/Controllers/ConectaVendaPedidoController.php[m
[1mindex a2e5638..28be4e2 100644[m
[1m--- a/app/Http/Controllers/ConectaVendaPedidoController.php[m
[1m+++ b/app/Http/Controllers/ConectaVendaPedidoController.php[m
[36m@@ -113,7 +113,7 @@[m [mclass ConectaVendaPedidoController extends Controller[m
                 DB::commit();[m
                 return redirect()->back()->with(session()->flash('flash_success', 'Pedido Cancelado!'));[m
             }[m
[31m-[m
[32m+[m[41m            [m
             $response = $this->util->updateOrderStatus($config, $item->conecta_pedido_id, "cancelado");[m
             $item->situacao = "Cancelado";[m
             $item->save();[m
[1mdiff --git a/app/Http/Controllers/ProdutoController.php b/app/Http/Controllers/ProdutoController.php[m
[1mindex 8b7ef09..7e85d0d 100644[m
[1m--- a/app/Http/Controllers/ProdutoController.php[m
[1m+++ b/app/Http/Controllers/ProdutoController.php[m
[36m@@ -305,10 +305,17 @@[m [mclass ProdutoController extends Controller[m
         $item = Produto::with('variacoes')->findOrFail($id);[m
         [m
         $variacoesIds = $item->variacoes->pluck('id')->toArray();[m
[31m-        $estoques = Estoque::whereIn('produto_variacao_id', $variacoesIds)->groupBy('produto_variacao_id')->get();[m
[32m+[m[32m        $estoques = Estoque::whereIn('produto_variacao_id', $variacoesIds)->groupBy('produto_variacao_id')[m
[32m+[m[32m        ->get();[m
[32m+[m[41m        [m
[32m+[m[32m        $estoques_table = [];[m
[32m+[m[32m        foreach ($estoques as $estoque) {[m
[32m+[m[32m            $estoques_table[ $estoque->produto_variacao_id ] = $estoque;[m
[32m+[m[32m        }[m
[32m+[m[41m        [m
         foreach ($item->variacoes as $variacao) {[m
[31m-            $estoqueVariacao = $estoques[$variacao->id] ?? collect();[m
[31m-            $variacao->estoque_total = $estoqueVariacao->sum('quantidade');[m
[32m+[m[32m            $estoque_qtde            = intval($estoques_table[$variacao->id]->quantidade);[m
[32m+[m[32m            $variacao->estoque_total = $estoque_qtde;[m
         }[m
         [m
         __validaObjetoEmpresa($item);[m
[36m@@ -595,35 +602,7 @@[m [mclass ProdutoController extends Controller[m
                 if($request->nuvemshop){[m
                     $resp = $this->utilNuvemShop->create($request, $produto);[m
                 }[m
[31m-                [m
[31m-                if($request->conectavenda){[m
[31m-                    $produto->conecta_venda_qtd_minima    = $request->conecta_venda_qtd_minima;[m
[31m-                    $produto->conecta_venda_multiplicador = $request->conecta_venda_multiplicador;[m
[31m-                    $produto->solicita_observacao         = $request->solicita_observacao;[m
[31m-                    $emp                                  = ConectaVendaConfig::where('empresa_id', $request->empresa_id)->first();[m
[31m-                    if(!$emp){[m
[31m-                        session()->flash('flash_error', 'Conecta Venda não configurado!');[m
[31m-                        return $produto;[m
[31m-                    }[m
[31m-                    try {[m
[31m-                        $retornoConecta = $this->utilConectaVenda->create($emp, $produto);[m
[31m-                        if (isset($retornoConecta['produtos_ids'])) {[m
[31m-                            $produto->conecta_venda_id              = $produto->id;[m
[31m-                            $produto->conecta_venda_status          = 1;[m
[31m-                            $produto->conecta_venda_data_publicacao = $request->created_at;[m
[31m-                            $produto->save();[m
[31m-                        } else {[m
[31m-                            \Log::warning('Produto integrado, mas sem ID retornado pelo Conecta Venda.', $retornoConecta);[m
[31m-                            session()->flash('flash_warning', 'Produto integrado ao Conecta Venda, mas não retornou ID.');[m
[31m-                        }[m
[31m-                        [m
[31m-                    } catch (\Exception $e) {[m
[31m-                        die($e);[m
[31m-                        \Log::error('Erro ao integrar com Conecta Venda: ' . $e->getMessage());[m
[31m-                        session()->flash('flash_error', 'Erro ao integrar com Conecta Venda: ' . $e->getMessage());[m
[31m-                    }[m
[31m-                }[m
[31m-                [m
[32m+[m
                 // Produto Imagens[m
                 [m
                 if( $produto_imagens ) {[m
[36m@@ -657,6 +636,36 @@[m [mclass ProdutoController extends Controller[m
                     }[m
                     ProdutoImagens::create_all( $produto_imagens_create );[m
                 }[m
[32m+[m[41m                [m
[32m+[m[32m                if($request->conectavenda){[m
[32m+[m[32m                    $produto->conecta_venda_qtd_minima    = $request->conecta_venda_qtd_minima;[m
[32m+[m[32m                    $produto->conecta_venda_multiplicador = $request->conecta_venda_multiplicador;[m
[32m+[m[32m                    $produto->solicita_observacao         = $request->solicita_observacao;[m
[32m+[m[32m                    $emp                                  = ConectaVendaConfig::where('empresa_id', $request->empresa_id)->first();[m
[32m+[m[32m                    if(!$emp){[m
[32m+[m[32m                        session()->flash('flash_error', 'Conecta Venda não configurado!');[m
[32m+[m[32m                        return $produto;[m
[32m+[m[32m                    }[m
[32m+[m[32m                    try {[m
[32m+[m[32m                        $retornoConecta = $this->utilConectaVenda->create($emp, $produto);[m
[32m+[m[32m                        if (isset($retornoConecta['produtos_ids'])) {[m
[32m+[m[32m                            $produto->conecta_venda_id              = $produto->id;[m
[32m+[m[32m                            $produto->conecta_venda_status          = 1;[m
[32m+[m[32m                            $produto->conecta_venda_data_publicacao = $request->created_at;[m
[32m+[m[32m                            $produto->save();[m
[32m+[m[32m                        } else {[m
[32m+[m[32m                            \Log::warning('Produto integrado, mas sem ID retornado pelo Conecta Venda.', $retornoConecta);[m
[32m+[m[32m                            session()->flash('flash_warning', 'Produto integrado ao Conecta Venda, mas não retornou ID.');[m
[32m+[m[32m                        }[m
[32m+[m[41m                        [m
[32m+[m[32m                    } catch (\Exception $e) {[m
[32m+[m[32m                        die($e);[m
[32m+[m[32m                        \Log::error('Erro ao integrar com Conecta Venda: ' . $e->getMessage());[m
[32m+[m[32m                        session()->flash('flash_error', 'Erro ao integrar com Conecta Venda: ' . $e->getMessage());[m
[32m+[m[32m                    }[m
[32m+[m[32m                }[m
[32m+[m[41m                [m
[32m+[m[41m                [m
 [m
 [m
                 return $produto;[m
[36m@@ -2898,6 +2907,12 @@[m [mpublic function alterarCampo(Request $request)[m
         }[m
     }[m
 [m
[32m+[m[32m    $produto = $produto_variacao->produto;[m
[32m+[m
[32m+[m[32m    if(plano_ativo("Conecta Venda") && $produto->conecta_venda_id){[m
[32m+[m[32m        $conecta_config = ConectaVendaConfig::where('empresa_id', $request->empresa_id)->first();[m
[32m+[m[32m        $this->utilConectaVenda->create( $conecta_config, $produto);[m
[32m+[m[32m    }[m[41m    [m
 [m
     return response()->json(['sucesso' => true]);[m
 }[m
[1mdiff --git a/app/Utils/ConectaVendaSincronizador.php b/app/Utils/ConectaVendaSincronizador.php[m
[1mindex d7c42ff..1d4a055 100644[m
[1m--- a/app/Utils/ConectaVendaSincronizador.php[m
[1m+++ b/app/Utils/ConectaVendaSincronizador.php[m
[36m@@ -34,7 +34,6 @@[m [mclass ConectaVendaSincronizador[m
         $url_completa = true;[m
         $produto_fotos = $produto->imagens( $url_completa );[m
 [m
[31m-[m
         $estoque_sob_encomenda = $produto->gerenciar_estoque == 0;[m
 [m
         $ativo = $desativar ? 0 : 1;[m
[36m@@ -148,7 +147,7 @@[m [mclass ConectaVendaSincronizador[m
 [m
         $response = Http::asJson()->post('https://api.conectavenda.com.br/produtos/criar', $payload);[m
 [m
[31m-        // HttpUtil::dd($response, $payload);[m
[32m+[m[32m        HttpUtil::dd($response, $payload);[m
 [m
         if (!$response->successful()) {[m
             throw new \Exception("Erro ao enviar produto ao Conecta Venda: " . $response->body());[m
[36m@@ -415,6 +414,8 @@[m [mclass ConectaVendaSincronizador[m
             $usuarioId = \Auth::check() ? \Auth::id() : null;[m
 [m
             if ($transacao) {[m
[32m+[m[32m                $estoque_util = new EstoqueUtil();[m
[32m+[m[32m                $estoque_util->reduzEstoque( $produto->produto_id, $produto->qtde, $produto->variacao_id );[m
                 // $this->utilEstoque->movimentacaoProduto([m
                 //     $produto->produto_id,[m
                 //     $qtd,[m
[36m@@ -456,6 +457,21 @@[m [mclass ConectaVendaSincronizador[m
         if (!$response->successful()) {[m
             throw new \Exception("Erro ao atualizar status do pedido no Conecta Venda: " . $response->body());[m
         }[m
[32m+[m
[32m+[m[32m        $conecta_venda = ConectaVendaPedido::where('conecta_pedido_id', $conecta_venda_id)->first();[m
[32m+[m
[32m+[m[32m        if(!$conecta_venda) {[m
[32m+[m[32m            throw new \Exception("Erro ao capturar pedido");[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        $pedido_itens = $conecta_venda->produtos;[m
[32m+[m
[32m+[m[32m        $produtos_ids = $pedido_itens->pluck('produto_id');[m
[32m+[m[32m        // capturar todos Produtos na lista de ids[m
[32m+[m[32m        $produtos = Produto::where('id', $produtos_ids)->get();[m
[32m+[m
[32m+[m[32m        $this->atualizar_estoques($empresa, $produtos);[m
[32m+[m
         return $response->json();[m
     }[m
 [m
[36m@@ -472,6 +488,84 @@[m [mclass ConectaVendaSincronizador[m
         return 'data:image/' . $type . ';base64,' . base64_encode($data);[m
     }[m
 [m
[32m+[m[32m    /**[m
[32m+[m[32m     * Summary of atualizar_estoques[m
[32m+[m[32m     * @param ConectaVendaConfig $empresa[m
[32m+[m[32m     * @param array<Produto> $produtos[m
[32m+[m[32m     * @throws \Exception[m
[32m+[m[32m     */[m
[32m+[m[32m    public function atualizar_estoques(ConectaVendaConfig $empresa, $produtos) {[m
[32m+[m
[32m+[m[32m        $config = ConectaVendaConfig::where('empresa_id', $empresa->empresa_id)->first();[m
[32m+[m
[32m+[m[32m        if (!$config || !$config->client_secret) {[m
[32m+[m[32m            throw new \Exception("Chave de API do Conecta Venda não encontrada para a empresa.");[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        $estoques_request = [];[m
[32m+[m
[32m+[m[32m        foreach($produtos as $produto) {[m
[32m+[m
[32m+[m[32m            if (!$produto->conecta_venda_id) {[m
[32m+[m[32m                throw new \Exception("Produto {$produto->id} não possui conecta_venda_id vinculado.");[m
[32m+[m[32m            }[m
[32m+[m
[32m+[m[32m            if( isset($produto->variacoes) && !$produto->variacoes->isEmpty() ) {[m
[32m+[m[32m                foreach ($produto->variacoes as $i => $variacao) {[m
[32m+[m[32m                    $estoque_sob_encomenda = $produto->gerenciar_estoque == 0;[m
[32m+[m
[32m+[m[32m                    if($estoque_sob_encomenda) {[m
[32m+[m[32m                        continue;[m
[32m+[m[32m                    }[m
[32m+[m
[32m+[m[32m                    $produto_id  = (string)$produto->id;[m
[32m+[m[32m                    $variacao_id = "{$produto->id}.{$variacao->id}";[m
[32m+[m[32m                    $estoque     = (int) ($variacao->estoque()->sum('quantidade'));[m
[32m+[m
[32m+[m[32m                    $estoques_request[] = [[m
[32m+[m[32m                        'produto_id'          => $produto_id,[m
[32m+[m[32m                        'produto_variacao_id' => $variacao_id,[m
[32m+[m[32m                        'estoque'             => $estoque,[m
[32m+[m[32m                    ];[m
[32m+[m[32m                }[m
[32m+[m[32m            } else {[m
[32m+[m[32m                $estoque_sob_encomenda = $produto->gerenciar_estoque == 0;[m
[32m+[m
[32m+[m[32m                if($estoque_sob_encomenda) {[m
[32m+[m[32m                    return;[m
[32m+[m[32m                }[m
[32m+[m
[32m+[m[32m                $produto_id  = (string)$produto->id;[m
[32m+[m[32m                $variacao_id = "{$produto->id}.0";[m
[32m+[m[32m                $estoque     = (int) ($produto->estoque()->sum('quantidade'));[m
[32m+[m
[32m+[m[32m                $estoques_request[] = [[m
[32m+[m[32m                    'produto_id'          => $produto_id,[m
[32m+[m[32m                    'produto_variacao_id' => $variacao_id,[m
[32m+[m[32m                    'estoque'             => $estoque,[m
[32m+[m[32m                ];[m
[32m+[m
[32m+[m[32m            }[m
[32m+[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        $payload = [[m
[32m+[m[32m            'chave' => $config->client_secret,[m
[32m+[m[32m            'dados' => $estoques_request,[m
[32m+[m[32m        ];[m
[32m+[m
[32m+[m[32m        $response = Http::asJson()->post('https://api.conectavenda.com.br/estoques/editar', $payload);[m
[32m+[m
[32m+[m[32m        // HttpUtil::dd($response, $payload);[m
[32m+[m
[32m+[m[32m        if (!$response->successful()) {[m
[32m+[m[32m            throw new \Exception("Erro ao atualizar estoque no Conecta Venda: " . $response->body());[m
[32m+[m[32m        }[m
[32m+[m
[32m+[m[32m        return $response->json();[m
[32m+[m
[32m+[m[32m    }[m
[32m+[m
     public function atualizarEstoque(ConectaVendaConfig $empresa, Produto $produto)[m
     {[m
         $config = ConectaVendaConfig::where('empresa_id', $empresa->empresa_id)->first();[m
