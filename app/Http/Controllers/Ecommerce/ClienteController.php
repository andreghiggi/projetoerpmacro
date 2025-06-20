<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Carrinho;
use App\Models\Cliente;
use App\Models\EcommerceConfig;
use App\Models\EnderecoEcommerce;
use App\Models\CategoriaProduto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClienteController extends Controller
{
    public function __construct(){
        session_start();
    }

    public function cadastro(Request $request){
        $config = EcommerceConfig::findOrfail($request->loja_id);
        $carrinho = $this->_getCarrinho();
        $categorias = CategoriaProduto::where('ecommerce', 1)->where('status', 1)
        ->where('empresa_id', $config->empresa_id)->get();
        return view('loja.cadastro', compact('carrinho', 'config', 'categorias'));
    }

    public function update(Request $request, $id){
        $item = Cliente::findOrFail($id);
        try{
            $item->razao_social = $request->nome;
            $item->email = $request->email;
            if($request->senha){
                if(strlen($request->senha) < 6){
                    session()->flash("flash_error", 'Senha deve ter no mínimo 6 caracteres!');
                    return redirect()->back();
                }
                $item->senha = md5($request->senha);
            }
            $item->save();
            session()->flash("flash_success", "Dados atualizados!");
        }catch(\Exception $e){
            session()->flash("flash_error", 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->back();
    }

    private function _getCarrinho(){
        $data = [];
        if(isset($_SESSION["session_cart"])){
            $data = Carrinho::where('session_cart', $_SESSION["session_cart"])
            ->first();
        }
        return $data;
    }

    public function cadastroStore(Request $request){
        $this->_validate($request);
        $config = EcommerceConfig::findOrfail($request->loja_id);

        try {

            $nfe = DB::transaction(function () use ($request, $config) {

                $code = rand() % 9000 + 999;
                $telefone = preg_replace('/[^0-9]/', '', $request->telefone);

                $request->merge([
                    'senha' => md5($request->senha),
                    'telefone' => $telefone,
                    'uid' => Str::random(30),
                    'token' => $code,
                    'status' => 1,
                    'razao_social' => $request->nome,
                    'empresa_id' => $config->empresa_id
                ]);
                $cli = Cliente::create($request->all());

                EnderecoEcommerce::create([
                    'cliente_id' => $cli->id,
                    'cidade_id' => $request->cidade_id,
                    'rua' => $request->rua,
                    'numero' => $request->numero,
                    'referencia' => $request->referencia,
                    'cep' => $request->cep,
                    'padrao' => 1,
                    'bairro' => $request->bairro
                ]);

                $_SESSION['cliente_ecommerce'] = $cli->id;
                return $cli;
            });
            session()->flash("flash_success", "Bem vindo " . $request->nome);

        } catch (\Exception $e) {
            // echo $e->getMessage() . '<br>' . $e->getLine();
            // die;
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
            return redirect()->back();
        }
        return redirect()->route('loja.carrinho', 'link='.$config->loja_id);

    }

    private function _validate(Request $request){
        $doc = $request->cpf_cnpj;

        $rules = [
            'senha' => 'required|min:6|same:repita_senha',
            'email' => 'required',
            'nome' => 'required',
            'termos' => 'required',
        ];

        $messages = [
            'senha.same' => 'Senhas não coincidem',
            'senha.min' => 'Mínimo de 6 caracteres',
            'senha.required' => 'Campo obrigatório',
            'nome.required' => 'Campo obrigatório',
            'email.required' => 'Campo obrigatório',
            'termos.required' => 'Aceite os termos e condições',
        ];

        $this->validate($request, $rules, $messages);
    }

    private function _getClienteLogado(){
        if(isset($_SESSION['cliente_ecommerce'])){
            return $_SESSION['cliente_ecommerce'];
        }
        return null;
    }

    public function minhaConta(Request $request){
        $clienteLogado = $this->_getClienteLogado();
        $config = EcommerceConfig::findOrfail($request->loja_id);

        if($clienteLogado == null){
            return redirect()->route('loja.cadastro', 'link='.$config->loja_id);
        }

        $cliente = Cliente::findOrFail($clienteLogado);
        $categorias = CategoriaProduto::where('ecommerce', 1)->where('status', 1)
        ->where('empresa_id', $config->empresa_id)->get();
        $carrinho = $this->_getCarrinho();

        \MercadoPago\SDK::setAccessToken($config->mercadopago_access_token);
        foreach($cliente->pedidosEcommerce as $p){
            if($p->transacao_id && $p->status_pagamento != 'approved'){
                $payStatus = \MercadoPago\Payment::find_by_id($p->transacao_id);
                if($payStatus){
                    $p->status_pagamento = $payStatus->status;
                    $p->save();
                }
            }
        }

        return view('loja.minha_conta', compact('cliente', 'config', 'categorias', 'carrinho'));
    }

    public function storeEndereco(Request $request){
        $clienteLogado = $this->_getClienteLogado();
        $request->merge([
            'cliente_id' => $clienteLogado,
            'padrao' => $request->padrao ? 1 : 0,
        ]);

        if($request->padrao){
            EnderecoEcommerce::where('cliente_id', $clienteLogado)->update(['padrao' => 0]);
        }
        if($request->endereco_id){
            $endereco = EnderecoEcommerce::findOrfail($request->endereco_id);

            $endereco->fill($request->all())->save();
            session()->flash("flash_success", "Endereço atualizado!");

        }else{
            EnderecoEcommerce::create($request->all());
            session()->flash("flash_success", "Endereço cadastrado!");
        }
        return redirect()->back();
    }

    public function logoff(){
        $_SESSION['cliente_ecommerce'] = null;
        return redirect()->back();
    }

    public function login(Request $request){
        $clienteLogado = $this->_getClienteLogado();
        $config = EcommerceConfig::findOrfail($request->loja_id);
        
        if($clienteLogado){
            return redirect()->route('loja.minha-conta', 'link='.$config->loja_id);
        }
        $categorias = CategoriaProduto::where('ecommerce', 1)->where('status', 1)
        ->where('empresa_id', $config->empresa_id)->get();
        return view('loja.login', compact('config', 'categorias'));
        
    }

    public function auth(Request $request){
        $config = EcommerceConfig::findOrfail($request->loja_id);
        
        $email = $request->email;
        $senha = $request->senha;
        $cliente = Cliente::where('email', $email)
        ->where('empresa_id', $config->empresa_id)
        ->where('senha', md5($senha))
        ->first();

        if($cliente == null){
            session()->flash("flash_error", "Credenciais incorretas!");
            return redirect()->back();
        }
        
        $_SESSION['cliente_ecommerce'] = $cliente->id;
        return redirect()->route('loja.index', 'link='.$config->loja_id);

    }
}
