<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\PedidoEcommerce;
use App\Models\Cidade;
use App\Models\Transportadora;
use App\Models\NaturezaOperacao;
use App\Models\EcommerceConfig;
use App\Models\Empresa;
use App\Models\Funcionario;
use App\Models\Nfe;

class PedidoEcommerceController extends Controller
{
    public function index(Request $request){

        $pagamentosAlterados = $this->pagamentosCheck($request);
        $estado = $request->estado;
        $cliente_id = $request->cliente_id;
        $cliente = null;

        $data = PedidoEcommerce::
        where('empresa_id', $request->empresa_id)
        ->orderBy('created_at', 'desc')
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->paginate(__itensPagina());

        if($cliente_id){
            $cliente = Cliente::findOrFail($cliente_id);
        }

        return view('pedido_ecommerce.index', compact('data', 'cliente', 'pagamentosAlterados'));
    }

    private function pagamentosCheck($request){
        $data = PedidoEcommerce::
        where('empresa_id', $request->empresa_id)
        ->whereDate('created_at', '>=', date('Y-m-d', strtotime('-7 days')))
        ->get();


        $config = EcommerceConfig::where('empresa_id', $request->empresa_id)
        ->first();

        $alterados = [];
        if($config == null){
            return $alterados;
        }
        try{
            \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);
            foreach($data as $p){
                if($p->transacao_id){
                    $payStatus = \MercadoPago\Payment::find_by_id($p->transacao_id);
                    if($payStatus){
                    // $payStatus->status = 'apprsoved';
                        if($payStatus->status != $p->status_pagamento){
                            array_push($alterados, [
                                'hash_pedido' => $p->hash_pedido,
                                'status' => $payStatus->status
                            ]);

                            $p->status_pagamento = $payStatus->status;
                            $p->save();
                        }
                    }
                }
            }
        }catch(\Exception $e){

        }
        return $alterados;
    }

    public function show($id)
    {
        $item = PedidoEcommerce::findOrFail($id);
        $item->pedido_lido = 1;
        $item->save();
        return view('pedido_ecommerce.show', compact('item'));
    }

    public function alterarEstado($id)
    {
        $item = PedidoEcommerce::findOrFail($id);
        return view('pedido_ecommerce.alterar_estado', compact('item'));
    }

    public function update(Request $request, $id)
    {

        $item = PedidoEcommerce::findOrFail($id);
        $item->fill($request->all())->save();
        session()->flash("flash_success", "Pedido atualizado!");
        return redirect()->route('pedidos-ecommerce.show', $item->id);
    }

    public function destroy($id)
    {
        $item = PedidoEcommerce::findOrFail($id);
        try {
            $item->itens()->delete();
            $item->delete();

            session()->flash("flash_success", "Pedido removido!");
        } catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu Errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function gerarNfe($id)
    {
        $item = PedidoEcommerce::findOrFail($id);

        $cliente = $item->cliente;
        if($cliente->rua == null){

            if($item->rua_entrega != null){
                $cliente->rua = $item->rua_entrega;
                $cliente->numero = $item->numero_entrega;
                $cliente->bairro = $item->bairro_entrega;
                $cliente->cep = $item->cep_entrega;

                $cliente->save();

                $item = PedidoEcommerce::findOrFail($id);
            }
        }

        $cidades = Cidade::all();
        $transportadoras = Transportadora::where('empresa_id', request()->empresa_id)->get();

        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
        if (sizeof($naturezas) == 0) {
            session()->flash("flash_warning", "Primeiro cadastre um natureza de operação!");
            return redirect()->route('natureza-operacao.create');
        } 
        // $produtos = Produto::where('empresa_id', request()->empresa_id)->get();
        $empresa = Empresa::findOrFail(request()->empresa_id);

        $caixa = __isCaixaAberto();
        $empresa = __objetoParaEmissao($empresa, $caixa->local_id);
        
        $numeroNfe = Nfe::lastNumero($empresa);

        $isPedidoEcommerce = 1;
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();

        return view('nfe.create', compact('item', 'cidades', 'transportadoras', 'naturezas', 'isPedidoEcommerce', 
            'numeroNfe', 'caixa', 'funcionarios'));
    }

}
