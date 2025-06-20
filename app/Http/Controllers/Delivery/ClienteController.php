<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarketPlaceConfig;
use App\Models\CarrinhoDelivery;
use App\Models\EnderecoDelivery;
use App\Models\Cliente;
use App\Models\CategoriaProduto;
use App\Models\BairroDelivery;
use App\Models\FuncionamentoDelivery;

class ClienteController extends Controller
{
    public function __construct(){
        session_start();
    }

    private function getFuncionamento($config){
        $dia = date('w');
        $hora = date('H:i');
        $dia = FuncionamentoDelivery::getDia($dia);

        $funcionamento = FuncionamentoDelivery::where('dia', $dia)
        ->where('empresa_id', $config->empresa_id)->first();

        if($funcionamento != null){

            $atual = strtotime(date('Y-m-d H:i'));
            $dataHoje = date('Y-m-d');
            $inicio = strtotime($dataHoje . " " . $funcionamento->inicio);
            $fim = strtotime($dataHoje . " " . $funcionamento->fim);
            if($atual > $inicio && $atual < $fim){
                $funcionamento->aberto = true;
            }else{
                $funcionamento->aberto = false;
            }
            return $funcionamento;
        }
        return null;
    }

    public function auth(Request $request){
        $carrinho = $this->_getCarrinho();

        $config = MarketPlaceConfig::findOrfail($request->loja_id);
        if($carrinho == []){
            return redirect()->route('food.index', 'link='.$config->loja_id);
        }

        $categorias = CategoriaProduto::where('delivery', 1)
        ->orderBy('nome', 'asc')
        ->where('status', 1)
        ->where('empresa_id', $config->empresa_id)->get();
        $notSearch = true;
        $notInfoHeader = 1;

        $funcionamento = $this->getFuncionamento($config);

        return view('food.auth', compact('carrinho', 'config', 'categorias', 'notSearch', 'notInfoHeader', 'funcionamento'));
    }

    public function conta(Request $request){
        $clienteLogado = $this->_getClienteLogado();
        $config = MarketPlaceConfig::findOrfail($request->loja_id);
        
        if(!$clienteLogado){
            session()->flash("flash_error", "Você não esta identificado!");
            return redirect()->route('food.index', 'link='.$config->loja_id);
        }
        $cliente = Cliente::where('uid', $clienteLogado)->first();
        $notSearch = true;
        $carrinho = $this->_getCarrinho();

        $categorias = CategoriaProduto::where('delivery', 1)
        ->orderBy('nome', 'asc')
        ->where('status', 1)
        ->where('empresa_id', $config->empresa_id)->get();

        $bairros = BairroDelivery::where('empresa_id', $config->empresa_id)
        ->where('status', 1)->get();
        $funcionamento = $this->getFuncionamento($config);
        $notInfoHeader = 1;

        return view('food.conta', compact('cliente', 'config', 'notSearch', 'carrinho', 'categorias', 'bairros', 'funcionamento', 'notInfoHeader'));
    }

    private function _getCarrinho(){
        $data = [];
        if(isset($_SESSION["session_cart_delivery"])){
            $data = CarrinhoDelivery::where('session_cart_delivery', $_SESSION["session_cart_delivery"])
            ->first();
        }
        return $data;
    }

    public function enderecoStore(Request $request){
        $clienteLogado = $this->_getClienteLogado();
        $cli = Cliente::where('uid', $clienteLogado)->first();
        $config = MarketPlaceConfig::findOrfail($request->loja_id);

        try{

            if($cli){

                EnderecoDelivery::where('cliente_id', $cli->id)
                ->update(['padrao' => 0]);
                $padrao = 0;
                if($request->padrao){
                    $padrao = 1; 
                }else{
                    $padrao = sizeof($cli->enderecos) == 0 ? 1 : 0;
                }

                EnderecoDelivery::create([
                    'rua' => $request->rua,
                    'numero' => $request->numero,
                    'bairro' => $request->bairro,
                    'referencia' => $request->referencia ?? '',
                    'bairro_id' => $request->bairro_id,
                    'padrao' => $padrao,
                    'cidade_id' => $config->cidade_id,
                    'tipo' => $request->tipo,
                    'cliente_id' => $cli->id
                ]);
                session()->flash("flash_success", "Endereço cadastrado!");
            }
        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function enderecoUpdate(Request $request){
        $config = MarketPlaceConfig::findOrfail($request->loja_id);
        $clienteLogado = $this->_getClienteLogado();
        $cli = Cliente::where('uid', $clienteLogado)->first();
        // dd($request->all());
        try{

            if($cli){

                EnderecoDelivery::where('cliente_id', $cli->id)
                ->update(['padrao' => 0]);
                $padrao = 0;
                if($request->padrao){
                    $padrao = 1; 
                }else{
                    $padrao = sizeof($cli->enderecos) == 0 ? 1 : 0;
                }
                $endereco = EnderecoDelivery::findOrFail($request->endereco_id);
                $data = [
                    'rua' => $request->rua,
                    'numero' => $request->numero,
                    'bairro' => $request->bairro,
                    'referencia' => $request->referencia ?? '',
                    'bairro_id' => $request->bairro_id,
                    'padrao' => $padrao,
                    'cidade_id' => $config->cidade_id,
                    'tipo' => $request->tipo,
                    'cliente_id' => $cli->id
                ];
                $endereco->fill($data)->save();
                session()->flash("flash_success", "Endereço atualizado!");
            }
        }catch(\Exception $e){
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    private function _getClienteLogado(){
        if(isset($_SESSION['cliente_delivery'])){
            return $_SESSION['cliente_delivery'];
        }
        return null;
    }

    public function logoff(Request $request){
        $config = MarketPlaceConfig::findOrfail($request->loja_id);

        $_SESSION['cliente_delivery'] = null;
        $_SESSION['session_cart_delivery'] = null;
        return redirect()->route('food.index', 'link='.$config->loja_id);
    }

    public function removeEndereco($id){
        $endereco = EnderecoDelivery::findOrFail($id);
        try{
            $endereco->delete();
            session()->flash("flash_success", "Endereço removido!");
        }catch(\Exception $e){
            session()->flash("flash_error", "Erro ao remover endereço, provavelmente já foi utilizado em algum pedido");
        }
        return redirect()->back();
    }

}
