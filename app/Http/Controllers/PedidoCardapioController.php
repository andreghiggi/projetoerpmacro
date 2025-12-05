<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\ItemPedido;
use App\Models\ItemPedidoServico;
use App\Models\Empresa;
use App\Models\Nfce;
use App\Models\ConfigGeral;
use App\Models\Produto;
use App\Models\CarrinhoCardapio;
use App\Models\ItemAdicional;
use App\Models\ItemPizzaPedido;
use App\Models\Adicional;
use App\Models\Marca;
use App\Models\CategoriaProduto;
use App\Models\ConfiguracaoCardapio;
use App\Models\TamanhoPizza;
use App\Models\Caixa;
use App\Models\Funcionario;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use App\Models\ImpressoraPedidoProduto;
use App\Models\Mesa;

class PedidoCardapioController extends Controller
{
    public function index(Request $request){
        $data = Pedido::
        where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->orderBy('created_at', 'desc')
        ->get();

        $mesas = Mesa::where('status', 1)
        ->where('empresa_id', $request->empresa_id)
        ->orderBy('nome')
        ->get();

        return view('pedidos.index', compact('data', 'mesas'));
    }

    public function store(Request $request){
        $cliente_id = $request->cliente_id;
        $comanda = $request->comanda;
        $mesa = $request->mesa;
        $clienteNome = $request->cliente_nome;
        $clienteFone = $request->cliente_fone;
        $item = Pedido::where('status', 1)
        ->where('empresa_id', $request->empresa_id)
        ->where('comanda', $comanda)
        ->first();

        if($item != null){
            session()->flash("flash_error", 'Comanda já está aberta');
            return redirect()->back();
        }

        try{
            $data = [
                'cliente_id' => $cliente_id,
                'cliente_nome' => $clienteNome,
                'cliente_fone' => $clienteFone,
                'comanda' => $comanda,
                'mesa_id' => $request->mesa_id,
                'total' => 0,
                'empresa_id' => $request->empresa_id,
                'local_pedido' => 'PDV'
            ];

            Mesa::where('id', $request->mesa_id)->update(['ocupada' => 1]);

            Pedido::create($data);
            session()->flash("flash_success", "Comanda aberta!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }
        return redirect()->back();
    }

    public function show($id){
        $item = Pedido::findOrfail($id);
        $tamanhosPizza = TamanhoPizza::where('empresa_id', request()->empresa_id)
        ->get();

        $config = ConfiguracaoCardapio::where('empresa_id', request()->empresa_id)
        ->first();

        $configGeral = ConfigGeral::where('empresa_id', request()->empresa_id)
        ->first();
        if($config && $config->percentual_taxa_servico){
            $item->acrescimo = $item->total * ($config->percentual_taxa_servico/100);
            $item->save();
        }

        $clientes = [];
        $push = [];

        $totalClientes = ItemPedido::where('pedido_id', $id)
        ->select('nome_cardapio')->distinct('nome_cardapio')->count();

        if($totalClientes > 1){
            $valorPorCliente = 0;
            if($item->acrescimo > 0){
                $valorPorCliente = (float)number_format($item->acrescimo/$totalClientes,2);
            }

            foreach($item->itens as $i){
                if($i->nome_cardapio && !$i->finalizado_pdv){
                    if(!in_array($i->nome_cardapio, $push)){
                        $push[] = $i->nome_cardapio;
                        $clientes[$i->nome_cardapio] = (float)$i->sub_total;
                    }else{
                        $clientes[$i->nome_cardapio] += $i->sub_total;
                    }
                }
            }
            if($valorPorCliente > 0){
                foreach($clientes as $key => $c){
                    $clientes[$key] += $valorPorCliente;
                }
            }
            // dd($clientes);
        }

        $mesas = Mesa::where('status', 1)
        ->where('empresa_id', request()->empresa_id)
        ->orderBy('nome')
        ->get();

        return view('pedidos.show', compact('item', 'tamanhosPizza', 'config', 'configGeral', 'clientes', 'mesas'));
    }

    public function updateTable(Request $request, $id){
        $pedido = Pedido::findOrfail($id);
        $pedido->mesa_id = $request->mesa_id;

        if($request->comanda){
            $outroPedido = Pedido::where('empresa_id', $request->empresa_id)
            ->where('comanda', $request->comanda)->where('status', 1)->first();

            if($outroPedido && $outroPedido->id != $id){
                session()->flash("flash_warning", "Essa comanda já está aberta");
                return redirect()->back();
            }
            $pedido->comanda = $request->comanda;
        }

        $pedido->save();

        session()->flash("flash_success", "Mesa/comanda alterada!");
        return redirect()->back();
    }

    public function storeServico(Request $request, $id){
        try {
            DB::transaction(function () use ($request, $id) {
                $pedido = Pedido::findOrfail($id);

                $data = [
                    'pedido_id' => $id,
                    'servico_id' => $request->servico_id,
                    'observacao' => $request->observacao,
                    'quantidade' => __convert_value_bd($request->quantidade),
                    'valor_unitario' => __convert_value_bd($request->valor_unitario),
                    'sub_total' => __convert_value_bd($request->sub_total),
                ];
                $itemPedido = ItemPedidoServico::create($data);
                $pedido->sumTotal();

            });
            session()->flash("flash_success", "Serviço adicionado!");

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }

        return redirect()->back();
    }

    private function validaItemImpressao($produto_id){

        $imprime = ImpressoraPedidoProduto::where('produto_id', $produto_id)->first();
        return $imprime != null ? 0 : 1;
    }

    public function storeItem(Request $request, $id){
        try {
            DB::transaction(function () use ($request, $id) {

                $adicionais = $request->adicionais;
                $adicionais = explode(",", $adicionais);

                $pedido = Pedido::findOrfail($id);
                $impresso = $this->validaItemImpressao($request->produto_cardapio);
                $data = [
                    'pedido_id' => $id,
                    'produto_id' => $request->produto_cardapio,
                    'observacao' => $request->observacao,
                    'quantidade' => __convert_value_bd($request->quantidade),
                    'valor_unitario' => __convert_value_bd($request->valor_unitario),
                    'sub_total' => __convert_value_bd($request->sub_total),
                    'estado' => $request->estado,
                    'ponto_carne' => $request->ponto_carne,
                    'tamanho_id' => $request->tamanho_id,
                    'impresso' => $impresso
                ];
                $itemPedido = ItemPedido::create($data);

                $produto = Produto::findOrFail($request->produto_cardapio);
                if($produto != null){
                    if($produto->categoria && $produto->categoria->tipo_pizza){
                        $pizzas = explode(",", $request->pizzas);
                        foreach($pizzas as $pz){
                            ItemPizzaPedido::create([
                                'item_pedido_id' => $itemPedido->id,
                                'produto_id' => $pz
                            ]);
                        }
                    }
                }
                foreach($adicionais as $a){
                    if($a){
                        $adicional = Adicional::findOrFail($a);
                        $dataItemAdicional = [
                            'item_pedido_id' => $itemPedido->id,
                            'adicional_id' => $adicional->id,
                        ];
                        ItemAdicional::create($dataItemAdicional);

                    }
                }

                $pedido->sumTotal();

            });
            session()->flash("flash_success", "Produto adicionado!");

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }

        return redirect()->back();

    }

    public function delete(Request $request){
        $item = Pedido::findOrFail($request->comanda_id);
        $carrinho = CarrinhoCardapio::where('session_cart_cardapio', $item->session_cart_cardapio)->first();
        if($carrinho){
            foreach($carrinho->itens as $it){
                $it->adicionais()->delete();
                $it->pizzas()->delete();
            }
            $carrinho->itens()->delete();
            $carrinho->delete();
        }

        if($item->_mesa){
            $mesa = $item->_mesa;
            $mesa->ocupada = 0;
            $mesa->save();
        }
        try {
            foreach($item->itens as $it){
                $it->adicionais()->delete();
                $it->pizzas()->delete();
                $it->delete();
            }
            $item->notificacoes()->delete();
            $item->delete();

            session()->flash("flash_success", "Comanda removida!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado '. $e->getMessage());
        }

        if(isset(request()->redirect_mesas_pdv)){
            return redirect()->route('frontbox.mesas');
        }
        return redirect()->back();
    }

    public function destroy($id){
        $item = Pedido::findOrFail($id);
        $carrinho = CarrinhoCardapio::where('session_cart_cardapio', $item->session_cart_cardapio)->first();
        if($carrinho){
            foreach($carrinho->itens as $it){
                $it->adicionais()->delete();
                $it->pizzas()->delete();
            }
            $carrinho->itens()->delete();
            $carrinho->delete();
        }

        if($item->_mesa){
            $mesa = $item->_mesa;
            $mesa->ocupada = 0;
            $mesa->save();
        }
        try {
            foreach($item->itens as $it){
                $it->adicionais()->delete();
                $it->pizzas()->delete();
                $it->delete();
            }
            $item->notificacoes()->delete();
            $item->delete();

            session()->flash("flash_success", "Comanda removida!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado '. $e->getMessage());
        }

        if(isset(request()->redirect_mesas_pdv)){
            return redirect()->route('frontbox.mesas');
        }
        return redirect()->back();
    }

    public function destroyItem($id){
        $item = ItemPedido::findOrFail($id);
        try {
            $pedido = $item->pedido;
            $item->adicionais()->delete();
            $item->pizzas()->delete();
            $item->delete();
            $pedido->sumTotal();

            session()->flash("flash_success", "Item removido!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function destroyItemServico($id){
        $item = ItemPedidoServico::findOrFail($id);
        try {
            $pedido = $item->pedido;
            $item->delete();
            $pedido->sumTotal();

            session()->flash("flash_success", "Serviço removido!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function print($id){
        $item = Pedido::findOrFail($id);

        $height = 190;

        $height += $item->countItens()*42;
        $config = Empresa::where('id', $item->empresa_id)->first();

        $p = view('pedidos.imprimir', compact('config', 'item'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $domPdf->setPaper([0,0,204,$height]);
        $pdf = $domPdf->render();

        $domPdf->stream("Pedido $id.pdf", array("Attachment" => false));
    }

    public function printHtml($id){
        $item = Pedido::findOrFail($id);

        $height = 180;
        $height += $item->countItens()*25;
        $config = Empresa::where('id', $item->empresa_id)->first();

        return view('pedidos.imprimir_hmtl', compact('config', 'item'));

    }

    public function finish($id){
        $pedido = Pedido::findOrFail($id);

        if($pedido->status == 0){
            session()->flash("flash_warning", 'Pedido já esta finalizado');
            return redirect()->back();
        }

        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }

        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->where('status', 1)
        ->where('categoria_id', null)
        ->get();

        $abertura = Caixa::where('empresa_id', request()->empresa_id)->where('usuario_id', get_id_user())
        ->where('status', 1)
        ->first();

        $config = Empresa::findOrFail(request()->empresa_id);
        if($config == null){
            session()->flash("flash_warning", "Configure antes de continuar!");
            return redirect()->route('config.index');
        }

        if($config->natureza_id_pdv == null){
            session()->flash("flash_warning", "Configure a natureza de operação padrão para continuar!");
            return redirect()->route('config.index');
        }

        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();

        $itens = $pedido->itens;
        foreach($itens as $i){
            $i->valor_unitario = $i->sub_total/$i->quantidade;
        }
        $caixa = __isCaixaAberto();

        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();
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

        $isVendaSuspensa = 0;

        $view = 'front_box.create';
        $produtos = [];
        $marcas = [];
        $servicos = $pedido->itensServico;
        foreach($servicos as $s){
            $s->valor = $s->valor_unitario;
        }

        if($config != null && $config->modelo == 'compact'){
            $view = 'front_box.create2';
            $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)
            ->where('categoria_id', null)
            ->orderBy('nome', 'asc')
            ->where('status', 1)
            ->where('categoria_id', null)
            ->paginate(4);

            $marcas = Marca::where('empresa_id', request()->empresa_id)
            ->orderBy('nome', 'asc')
            ->paginate(4);

            $produtos = Produto::select('produtos.*', \DB::raw('sum(quantidade) as quantidade'))
            ->where('empresa_id', request()->empresa_id)
            ->where('produtos.status', 1)
            ->where('status', 1)
            ->leftJoin('item_nfces', 'item_nfces.produto_id', '=', 'produtos.id')
            ->groupBy('produtos.id')
            ->orderBy('quantidade', 'desc')
            ->paginate(12);
        }
        $local_id = $caixa->local_id;
        $acrescimo = $pedido->acrescimo;
        return view($view, compact('categorias', 'abertura', 'funcionarios', 'pedido', 'itens', 'caixa', 'config', 
            'tiposPagamento', 'isVendaSuspensa', 'produtos', 'marcas', 'servicos', 'local_id', 'acrescimo'));
    }

    public function liberarMesa($id){
        $item = Pedido::findOrfail($id);
        $item->confirma_mesa = 1;
        $item->save();
        session()->flash("flash_success", "Mesa liberada!");
        return redirect()->back();
    }

    public function finishClient(Request $request){

        $pedido = Pedido::findOrfail($request->pedido_id);

        if($pedido->status == 0){
            session()->flash("flash_warning", 'Pedido já esta finalizado');
            return redirect()->back();
        }

        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }

        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)->where('status', 1)->get();

        $abertura = Caixa::where('empresa_id', request()->empresa_id)->where('usuario_id', get_id_user())
        ->where('status', 1)
        ->first();

        $config = Empresa::findOrFail(request()->empresa_id);
        if($config == null){
            session()->flash("flash_warning", "Configure antes de continuar!");
            return redirect()->route('config.index');
        }

        if($config->natureza_id_pdv == null){
            session()->flash("flash_warning", "Configure a natureza de operação padrão para continuar!");
            return redirect()->route('config.index');
        }

        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();

        $itens = [];
        $pushItensPedido = [];

        $total = 0;
        foreach($pedido->itens as $i){
            if($i->nome_cardapio == $request->nome){
                $itens[] = $i;
                $pushItensPedido[] = $i->id;
                $total = $i->sub_total;
            }
        }

        foreach($itens as $i){
            $i->valor_unitario = $i->sub_total/$i->quantidade;
        }
        $caixa = __isCaixaAberto();

        $config = ConfigGeral::where('empresa_id', request()->empresa_id)->first();
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

        $isVendaSuspensa = 0;

        $view = 'front_box.create';
        $produtos = [];
        $marcas = [];
        $servicos = $pedido->itensServico;
        foreach($servicos as $s){
            $s->valor = $s->valor_unitario;
        }

        if($config != null && $config->modelo == 'compact'){
            $view = 'front_box.create2';
            $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)
            ->where('categoria_id', null)
            ->orderBy('nome', 'asc')
            ->where('status', 1)
            ->paginate(4);

            $marcas = Marca::where('empresa_id', request()->empresa_id)
            ->orderBy('nome', 'asc')
            ->paginate(4);

            $produtos = Produto::select('produtos.*', \DB::raw('sum(quantidade) as quantidade'))
            ->where('empresa_id', request()->empresa_id)
            ->where('produtos.status', 1)
            ->where('status', 1)
            ->leftJoin('item_nfces', 'item_nfces.produto_id', '=', 'produtos.id')
            ->groupBy('produtos.id')
            ->orderBy('quantidade', 'desc')
            ->paginate(12);
        }
        $local_id = $caixa->local_id;
        $acrescimo = $request->valor - $total;
        return view($view, compact('categorias', 'abertura', 'funcionarios', 'pedido', 'itens', 'caixa', 'config', 
            'tiposPagamento', 'isVendaSuspensa', 'produtos', 'marcas', 'servicos', 'local_id', 'acrescimo', 'pushItensPedido'));
    }

    public function historico(Request $request){
        $data = Pedido::
        where('empresa_id', $request->empresa_id)
        ->where('status', 0)
        ->when(!empty($request->comanda), function ($q) use ($request) {
            return $q->where('comanda', $request->comanda);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());

        $mesas = Mesa::where('status', 1)
        ->where('empresa_id', $request->empresa_id)
        ->orderBy('nome')
        ->get();

        return view('pedidos.historico', compact('data', 'mesas'));
    }
}
