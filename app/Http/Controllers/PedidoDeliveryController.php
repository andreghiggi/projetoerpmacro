<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PedidoDelivery;
use App\Models\TamanhoPizza;
use App\Models\Empresa;
use App\Models\MarketPlaceConfig;
use App\Models\ConfigGeral;
use App\Models\Adicional;
use App\Models\Nfce;
use App\Models\ItemAdicionalDelivery;
use App\Models\ItemPedidoDelivery;
use App\Models\ItemPizzaPedidoDelivery;
use App\Models\Produto;
use App\Models\Cliente;
use App\Models\Marca;
use App\Models\Motoboy;
use App\Models\BairroDelivery;
use App\Models\EnderecoDelivery;
use App\Models\CategoriaProduto;
use App\Models\Caixa;
use App\Models\Funcionario;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use App\Utils\WhatsAppUtil;

class PedidoDeliveryController extends Controller
{
    protected $util;

    public function __construct(WhatsAppUtil $util){
        $this->util = $util;
    }

    private function setNumeroSequencial(){
        $docs = PedidoDelivery::where('empresa_id', request()->empresa_id)
        ->where('numero_sequencial', null)
        ->get();

        $last = PedidoDelivery::where('empresa_id', request()->empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;

        foreach($docs as $d){
            $d->numero_sequencial = $numero;
            $d->save();
            $numero++;
        }
    }

    public function index(Request $request){
        $estado = $request->estado;
        $tipo = $request->tipo;
        $cliente_delivery_id = $request->cliente_delivery_id;
        $cliente = null;
        $this->setNumeroSequencial();

        $data = PedidoDelivery::
        where('empresa_id', $request->empresa_id)
        ->orderBy('created_at', 'desc')
        ->when(!empty($cliente_delivery_id), function ($query) use ($cliente_delivery_id) {
            return $query->where('cliente_id', $cliente_delivery_id);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when(!empty($tipo), function ($query) use ($tipo) {
            if($tipo == 'delivery'){
                return $query->where('inicio_agendamento', null);
            }else{
                return $query->where('inicio_agendamento', '!=', null);
            }
        })
        ->paginate(__itensPagina());

        if($cliente_delivery_id){
            $cliente = Cliente::findOrFail($cliente_delivery_id);
        }

        return view('pedido_delivery.index', compact('data', 'cliente'));
    }

    public function alteraStatus(Request $request){
        $item = PedidoDelivery::findOrFail($request->pedido_id);

        $item->pedido_lido = 1;
        $item->estado = $request->estado;
        $item->horario_leitura = date('H:i');
        $item->save();

        $config = MarketPlaceConfig::where('empresa_id', request()->empresa_id)
        ->first();
        if($request->estado == 'cancelado'){
            session()->flash("flash_error", "Pedido cancelado!");
            $mensagem = "Olá, aqui é da $config->nome seu pedido foi cancelado :(";
        }else{
            session()->flash("flash_success", "Pedido aprovado!");
            $mensagem = "Olá, aqui é da $config->nome seu pedido foi aprovado :)";
        }

        $this->sendMessageWhatsApp($item, $mensagem);
        return redirect()->route('pedidos-delivery.show', [$item->id]);
    }

    private function sendMessageWhatsApp($pedido, $texto){
        $telefone = "55".preg_replace('/[^0-9]/', '', $pedido->cliente->telefone);
        $retorno = $this->util->sendMessage($telefone, $texto, $pedido->empresa_id);
        // dd($retorno);
    }

    public function update(Request $request, $id){
        $item = PedidoDelivery::findOrFail($id);
        try{
            $config = MarketPlaceConfig::where('empresa_id', request()->empresa_id)
            ->first();
            $item->estado = $request->estado;
            if($item->pedido_lido == 0){
                $mensagem = "Olá, aqui é da $config->nome, seu pedido #$item->id foi aprovado :)";
                $this->sendMessageWhatsApp($item, $mensagem);
            }else{
                if($request->estado == 'cancelado'){
                    session()->flash("flash_error", "Pedido cancelado!");
                    $mensagem = "Olá, aqui é da $config->nome, seu pedido #$item->id foi cancelado :(";
                }else{
                    session()->flash("flash_success", "Pedido aprovado!");
                    $mensagem = "Olá, aqui é da $config->nome, seu pedido #$item->id foi aprovado :)";
                }
                $this->sendMessageWhatsApp($item, $mensagem);
            }
            $item->pedido_lido = 1;
            $item->save();
            session()->flash("flash_success", "Estado alterado!");

            
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }
        return redirect()->back();
    }

    private function getLastNumero($empresa_id){
        $last = PedidoDelivery::where('empresa_id', $empresa_id)
        ->orderBy('numero_sequencial', 'desc')
        ->where('numero_sequencial', '>', 0)->first();
        $numero = $last != null ? $last->numero_sequencial : 0;
        $numero++;
        return $numero;
    }

    public function store(Request $request){
        try{
            $cliente = null;
            if($request->cliente_id == null){
                $cliente = Cliente::create([
                    'empresa_id' => $request->empresa_id,
                    'razao_social' => $request->cliente_nome,
                    'telefone' => $request->cliente_fone,
                ]);
            }else{
                $cliente = Cliente::findOrFail($request->cliente_id);
            }
            $pedido = PedidoDelivery::create([
                'empresa_id' => $request->empresa_id,
                'cliente_id' => $cliente->id,
                'valor_total' => 0,
                'pedido_lido' => 1,
                'tipo_pagamento' => '',
                'observacao' => '',
                'telefone' => $request->cliente_fone ?? '',
                'estado' => 'novo',
                'horario_cricao' => date('H:i'),
                'numero_sequencial' => $this->getLastNumero($request->empresa_id)
            ]);
            session()->flash("flash_success", "Pedido criado!");
            return redirect()->route('pedidos-delivery.show', [$pedido->id]);
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
            return redirect()->back();
        }

    }

    public function storeItem(Request $request, $id){
        try {
            DB::transaction(function () use ($request, $id) {

                $adicionais = $request->adicionais;
                $adicionais = explode(",", $adicionais);

                // dd($adicionais);

                $pedido = PedidoDelivery::findOrfail($id);

                $data = [
                    'pedido_id' => $id,
                    'produto_id' => $request->produto_delivery,
                    'observacao' => $request->observacao,
                    'quantidade' => __convert_value_bd($request->quantidade),
                    'valor_unitario' => __convert_value_bd($request->valor_unitario),
                    'sub_total' => __convert_value_bd($request->sub_total),
                    'estado' => $request->estado,
                    'tamanho_id' => $request->tamanho_id
                ];
                $itemPedido = ItemPedidoDelivery::create($data);

                $produto = Produto::findOrFail($request->produto_delivery);
                if($produto != null){
                    if($produto->categoria && $produto->categoria->tipo_pizza){
                        $pizzas = explode(",", $request->pizzas);
                        foreach($pizzas as $pz){
                            ItemPizzaPedidoDelivery::create([
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
                        ItemAdicionalDelivery::create($dataItemAdicional);

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

    public function show($id){
        $item = PedidoDelivery::findOrFail($id);

        $tamanhosPizza = TamanhoPizza::where('empresa_id', request()->empresa_id)
        ->get();
        $config = MarketPlaceConfig::where('empresa_id', request()->empresa_id)
        ->first();
        $cliente = $item->cliente;

        $bairros = BairroDelivery::where('empresa_id', request()->empresa_id)
        ->get();

        $motoboys = Motoboy::where('empresa_id', request()->empresa_id)
        ->where('status', 1)
        ->get();

        return view('pedido_delivery.show', 
            compact('item', 'tamanhosPizza', 'config', 'cliente', 'bairros', 'motoboys'));
    }

    public function print($id){
        $item = PedidoDelivery::findOrFail($id);

        $height = 240;

        $height += $item->countItens()*25;
        $config = Empresa::where('id', $item->empresa_id)->first();

        $p = view('pedido_delivery.imprimir', compact('config', 'item'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $domPdf->setPaper([0,0,204,$height]);
        $domPdf->render();

        $domPdf->stream("Pedido $item->numero_sequencial.pdf", array("Attachment" => false));
    }

    public function setEndereco(Request $request, $id){
        $item = PedidoDelivery::findOrFail($id);
        try{
            $item->endereco_id = $request->endereco_id;
            if($item->endereco){
                $item->valor_entrega = $item->endereco->bairro->valor_entrega;
            }else{
                $item->valor_entrega = 0;
            }

            $item->save();
            $item->sumTotal();

            session()->flash("flash_success", "Endereço alterado!");

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }
        return redirect()->back();
    }

    public function storeEndereco(Request $request, $id){
        $item = PedidoDelivery::findOrFail($id);
        $config = MarketPlaceConfig::where('empresa_id', request()->empresa_id)
        ->first();
        try{

            $data = [
                'rua' => $request->rua,
                'numero'=> $request->numero,
                'bairro_id'=> $request->bairro_id,
                'referencia'=> $request->referencia ?? '',
                'tipo' => $request->tipo,
                'latitude' => '',
                'longitude' => '',
                'cliente_id' => $item->cliente_id,
                'cidade_id' => $config->cidade_id,
                'padrao' => sizeof($item->cliente->enderecos) == 0 ? 1 : 0
            ];
            $endereco = EnderecoDelivery::create($data);
            $item->endereco_id = $endereco->id;

            if($endereco->bairro){
                $item->valor_entrega = $endereco->bairro->valor_entrega;
            }else{
                $item->valor_entrega = 0;
            }
            $item->save();
            session()->flash("flash_success", "Endereço cadastrado!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }
        return redirect()->back();

    }

    public function updateItem(Request $request, $id){
        $item = ItemPedidoDelivery::findOrfail($id);
        $item->estado = $request->estado;
        if(isset($request->tempo_preparo)){
            $item->tempo_preparo = $request->tempo_preparo;
        }
        $item->save();
        session()->flash("flash_success", "Estado do item #$id - ". $item->produto->nome ." alterado para $request->estado!");
        return redirect()->back();
    }

    public function finish(Request $request, $id){
        $pedido = PedidoDelivery::findOrfail($id);
        $motoboy_id = $request->motoboy_id;
        $valor_comissao = __convert_value_bd($request->valor_comissao);

        $pedido->motoboy_id = $motoboy_id;
        $pedido->comissao_motoboy = $valor_comissao;
        $pedido->save();

        if($pedido->status == 1){
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

        $isDelivery = 1;
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

        // if($config != null && $config->modelo == 'compact'){
        //     $view = 'front_box.create2';
        //     $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)
        //     ->where('categoria_id', null)
        //     ->orderBy('nome', 'asc')
        //     ->where('status', 1)
        //     ->where('categoria_id', null)
        //     ->paginate(4);

        //     $marcas = Marca::where('empresa_id', request()->empresa_id)
        //     ->orderBy('nome', 'asc')
        //     ->paginate(4);

        //     $produtos = Produto::select('produtos.*', \DB::raw('sum(quantidade) as quantidade'))
        //     ->where('empresa_id', request()->empresa_id)
        //     ->where('produtos.status', 1)
        //     ->where('status', 1)
        //     ->leftJoin('item_nfces', 'item_nfces.produto_id', '=', 'produtos.id')
        //     ->groupBy('produtos.id')
        //     ->orderBy('quantidade', 'desc')
        //     ->paginate(12);
        // }
        $local_id = $caixa->local_id;

        return view($view, 
            compact('categorias', 'abertura', 'funcionarios', 'pedido', 'itens', 'isDelivery', 'caixa', 
                'config', 'tiposPagamento', 'isVendaSuspensa', 'marcas', 'produtos', 'local_id'));

    }

    public function enviarMensagemWpp($id){
        $pedido = PedidoDelivery::findOrfail($id);
        $texto = $this->montaTextoPedido($pedido);
        $telefone = "55".preg_replace('/[^0-9]/', '', $pedido->cliente->telefone);
        $retorno = $this->util->sendMessage($telefone, $texto, $pedido->empresa_id);

        $config = MarketPlaceConfig::where('empresa_id', $pedido->empresa_id)
        ->first();
        $telefone = "55".preg_replace('/[^0-9]/', '', $config->telefone);
        $retorno = $this->util->sendMessage($telefone, $texto, $pedido->empresa_id);
        try{
            $retorno = json_decode($retorno);
            // dd($retorno);
            if(!$retorno->success){
                session()->flash("flash_error", $retorno->message);
            }else{
                session()->flash("flash_success", "Mensagem enviada!");
            }
        }catch(\Exception $e){
            session()->flash("flash_error", 'Algo deu errado '. $e->getMessage());
        }
        return redirect()->back();
    }

    private function montaTextoPedido($pedido){
        $texto = "Olá, ". $pedido->cliente->razao_social . ", já recebemos seu pedido, muito obrigado!\n\n";

        $texto .= "PEDIDO #$pedido->id\n\n";
        if(sizeof($pedido->itensProdutos) > 0){
            $texto .= "PRODUTOS\n";
            foreach($pedido->itens as $i){
                $texto .= number_format($i->quantidade, 0) . "X ";
                if($i->produto->referencia){
                    $texto .= "#REF-" . $i->produto->referencia;
                    if($i->tamanho){
                        foreach($i->pizzas as $pizza){
                            $texto .= " 1/" . sizeof($i->pizzas) . " " . $pizza->sabor->nome;
                        }
                        $texto .= " - tamanho" . $i->tamanho->nome;

                    }else{
                        $texto .= " " . $i->produto->nome;
                    }

                    if(sizeof($i->adicionais) > 0){
                        $texto .= "\n adicionais: ";
                        foreach($i->adicionais as $key => $a){
                            $texto .= $a->adicional->nome . ($key+1 < sizeof($i->adicionais) ? ", " : "");
                        }
                    }

                    $texto .= " R$ " . __moeda($i->valor_unitario);
                    $texto .= " = R$ " . __moeda($i->sub_total);
                    $texto .= "\n";
                }
            }
        }

        if(sizeof($pedido->itensServico) > 0){
            $texto .= "SERVIÇOS\n";
            foreach($pedido->itens as $i){
                if($i->servico){
                    $texto .= number_format($i->quantidade, 0) . "X ";

                    $texto .= " " . $i->servico->nome;
                    $texto .= " R$ " . __moeda($i->valor_unitario);
                    $texto .= " = R$ " . __moeda($i->sub_total);
                    $texto .= "\n";

                }
            }
        }

        if($pedido->endereco){
            $texto .= "\nEndereço de entrega: " . $pedido->endereco->info;
        }else{
            $texto .= "\nRETIRADA EM BALCÃO";
        }

        $texto .= "\n\nForma de pagamento: " . $pedido->tipo_pagamento;
        if($pedido->troco_para > 0){
            $texto .= " - troco para: " . __moeda($pedido->troco_para);
        }

        $texto .= "\nSubtotal: " . __moeda($pedido->itens->sum('sub_total'));
        $texto .= "\nDesconto: " . __moeda($pedido->valor_desconto);
        $texto .= "\nValor entraga: " . __moeda($pedido->valor_entrega);
        $texto .= "\nTotal: " . __moeda($pedido->valor_total);

        return $texto;
    }

    public function destroy($id){
        $pedido = PedidoDelivery::findOrFail($id);
        try {
            foreach($pedido->itens as $item){
                $item->adicionais()->delete();
                $item->pizzas()->delete();
                $item->delete();

                if($item->agendamento){
                    $item->agendamento()->delete();
                }
            }
            $pedido->delete();
            session()->flash("flash_success", "Pedido removido!");
            return redirect()->route('pedidos-delivery.index');

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado '. $e->getMessage());
            return redirect()->back();
        }
    }

    public function destroyItem($id){
        $item = ItemPedidoDelivery::findOrFail($id);
        try {
            $pedido = $item->pedido;
            $item->adicionais()->delete();
            $item->pizzas()->delete();
            $item->delete();

            if($item->agendamento){
                $item->agendamento()->delete();
            }
            $pedido->sumTotal();
            
            session()->flash("flash_success", "Item removido!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado '. $e->getMessage());
        }
        return redirect()->back();
    }

}
