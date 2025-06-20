<?php

namespace App\Http\Controllers\API\TokenSuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Cidade;

class EmpresaController extends Controller
{
    public function index(Request $request){

        $data = Empresa::all()
        ->each(function($row)
        {
            $row->setHidden(['password', 'token', 'token_nfse', 'senha', 'info', 'empresa_selecionada', 'percentual_comissao', 
                'tipo_contador', 'logo', 'natureza_id_pdv', 'arquivo']);
        });
        return response()->json($data, 200);
    }

    public function find(Request $request, $id){

        $item = Empresa::find($id);
        if($item == null){
            return response()->json("Empresa não encontrado!", 403);
        }
        $item->setHidden(['arquivo', 'token', 'token_nfse', 'senha', 'info', 'empresa_selecionada', 'percentual_comissao', 
            'tipo_contador', 'logo', 'natureza_id_pdv', 'arquivo']);
        return response()->json($item, 200);
    }

    public function store(Request $request){

        try{
            $validaRequest = $this->validaRequest($request);
            if(sizeof($validaRequest) > 0){
                return response()->json($validaRequest, 403);
            }

            $cidade = Cidade::where('nome', $request->cidade)
            ->where('uf', $request->uf)->first();

            if($cidade == null){
                return response()->json("Cidade não encontrada!", 403);
            }
            $request->merge([
                'cidade_id' => $cidade->id
            ]);

            $item = Empresa::create($request->all());
            $item->setHidden(['arquivo', 'token', 'token_nfse', 'senha', 'info', 'empresa_selecionada', 'percentual_comissao', 
                'tipo_contador', 'logo', 'natureza_id_pdv']);
            return response()->json($item, 200);
            
        }catch(\Exception $e){
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }

    public function update(Request $request){

        try{

            $item = Empresa::find($request->id);
            if($item == null){
                return response()->json("Empresa não encontrado!", 403);
            }

            if($request->cidade){
                $cidade = Cidade::where('nome', $request->cidade)
                ->where('uf', $request->uf)->first();

                if($cidade == null){
                    return response()->json("Cidade não encontrada!", 403);
                }

                $request->merge([
                    'cidade_id' => $cidade->id
                ]);
            }

            $item->fill($request->all())->save();
            $item->setHidden(['arquivo', 'token', 'token_nfse', 'senha', 'info', 'empresa_selecionada', 'percentual_comissao', 
                'tipo_contador', 'logo', 'natureza_id_pdv']);
            return response()->json($item, 200);
        }catch(\Exception $e){
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }

    private function validaRequest($request){
        $dataMessage = [];

        if(!isset($request->nome) || $request->nome == ""){
            array_push($dataMessage, "Informe o nome");
        }
        if(!isset($request->cpf_cnpj) || $request->cpf_cnpj == ""){
            array_push($dataMessage, "Informe o CPF/CNPJ, variável cpf_cnpj");
        }

        if(!isset($request->cep) || $request->cep == ""){
            array_push($dataMessage, "Informe o CEP, variável cep");
        }
        if(!isset($request->rua) || $request->rua == ""){
            array_push($dataMessage, "Informe a Rua, variável rua");
        }
        if(!isset($request->numero) || $request->numero == ""){
            array_push($dataMessage, "Informe o Número, variável numero");
        }
        if(!isset($request->bairro) || $request->bairro == ""){
            array_push($dataMessage, "Informe o Bairro, variável bairro");
        }
        if(!isset($request->cidade) || $request->cidade == ""){
            array_push($dataMessage, "Informe a cidade");
        }
        if(!isset($request->uf) || $request->uf == ""){
            array_push($dataMessage, "Informe a uf");
        }

        return $dataMessage;
    }

    public function delete(Request $request){
        
        try{

            $item = Empresa::find($request->id);
            if($item == null){
                return response()->json("Empresa não encontrado!", 403);
            }
            foreach($item->usuarios as $u){
                $u->usuario->acessos()->delete();
            }
            $item->usuarios()->delete();
            // $item->user()->delete();
            $item->plano()->delete();
            $this->deleteRegistros($item->id);
            $item->delete();

            return response()->json($item, 200);

        }catch(\Exception $e){
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }

    private function deleteRegistros($empresa_id){

        \App\Models\ContaPagar::where('empresa_id', $empresa_id)->delete();
        \App\Models\ContaReceber::where('empresa_id', $empresa_id)->delete();
        $prevendas = \App\Models\PreVenda::where('empresa_id', $empresa_id)->get();
        foreach($prevendas as $n){
            $n->itens()->delete();
            $n->fatura()->delete();
            $n->delete();
        }

        $nfe = \App\Models\Nfe::where('empresa_id', $empresa_id)->get();
        foreach($nfe as $n){
            $n->itens()->delete();
            $n->fatura()->delete();
            $n->delete();
        }

        $nfce = \App\Models\Nfce::where('empresa_id', $empresa_id)->get();
        foreach($nfce as $n){
            $n->itens()->delete();
            $n->fatura()->delete();
            $n->delete();
        }

        $carrinhos = \App\Models\Carrinho::where('empresa_id', $empresa_id)->get();
        foreach($carrinhos as $c){
            $c->itens()->delete();
            $c->delete();
        }

        $data = \App\Models\OrdemServico::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->itens()->delete();
            $t->servicos()->delete();
            $t->relatorios()->delete();
            $t->funcionarios()->delete();
            $t->delete();
        }


        \App\Models\DiaSemana::where('empresa_id', $empresa_id)->delete();
        \App\Models\FuncionarioServico::where('empresa_id', $empresa_id)->delete();
        \App\Models\Servico::where('empresa_id', $empresa_id)->delete();

        $funcionarios = \App\Models\Funcionario::where('empresa_id', $empresa_id)->get();
        foreach($funcionarios as $f){
            $f->eventos()->delete();
            $f->funcionamento()->delete();
            $f->interrupcoes()->delete();
            $f->delete();
        }

        $data = \App\Models\Ticket::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->mensagens()->delete();
            $t->delete();
        }

        $data = \App\Models\TransferenciaEstoque::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->itens()->delete();
            $t->delete();
        }
        
        $data = \App\Models\PedidoEcommerce::where('empresa_id', $empresa_id)->get();
        foreach($data as $t){
            $t->itens()->delete();
            $t->delete();
        }
        
        \App\Models\CashBackConfig::where('empresa_id', $empresa_id)->delete();
        \App\Models\CashBackCliente::where('empresa_id', $empresa_id)->delete();

        $data = \App\Models\Cliente::where('empresa_id', $empresa_id)->get();
        foreach($data as $c){
            $c->enderecos()->delete();
            $c->enderecosEcommerce()->delete();
            $c->enderecosDelivery()->delete();
            $c->delete();
        }
        \App\Models\Caixa::where('empresa_id', $empresa_id)->delete();
        \App\Models\MotivoInterrupcao::where('empresa_id', $empresa_id)->delete();
        \App\Models\Notificacao::where('empresa_id', $empresa_id)->delete();
        \App\Models\EcommerceConfig::where('empresa_id', $empresa_id)->delete();
        \App\Models\MarketPlaceConfig::where('empresa_id', $empresa_id)->delete();
        \App\Models\ModeloEtiqueta::where('empresa_id', $empresa_id)->delete();
        $data = \App\Models\VariacaoModelo::where('empresa_id', $empresa_id)->get();
        foreach($data as $c){
            $c->itens()->delete();
            $c->delete();
        }

        $data = \App\Models\VendaSuspensa::where('empresa_id', $empresa_id)->get();
        foreach($data as $c){
            $c->itens()->delete();
            $c->delete();
        }

        $produtos = \App\Models\Produto::where('empresa_id', $empresa_id)->get();
        \App\Models\ProdutoCombo::
        select('produto_combos.*')
        ->join('produtos', 'produtos.id', '=', 'produto_combos.produto_id')
        ->where('produtos.empresa_id', $empresa_id)->delete();

        foreach($produtos as $p){
            $p->movimentacoes()->delete();
            $p->locais()->delete();
            $p->variacoes()->delete();
            $p->fornecedores()->delete();

            if($p->estoque){
                $p->estoque->delete();
            }
            $p->delete();
        }

        \App\Models\ConfigGeral::where('empresa_id', $empresa_id)->delete();
        \App\Models\AcaoLog::where('empresa_id', $empresa_id)->delete();
        \App\Models\PlanoPendente::where('empresa_id', $empresa_id)->delete();
        \App\Models\Marca::where('empresa_id', $empresa_id)->delete();
        
        \App\Models\CategoriaProduto::where('empresa_id', $empresa_id)->delete();

        \App\Models\Fornecedor::where('empresa_id', $empresa_id)->delete();
        \App\Models\NuvemShopConfig::where('empresa_id', $empresa_id)->delete();
        \App\Models\NaturezaOperacao::where('empresa_id', $empresa_id)->delete();
        \App\Models\PadraoTributacaoProduto::where('empresa_id', $empresa_id)->delete();
        \App\Models\Role::where('empresa_id', $empresa_id)->delete();
        \App\Models\FinanceiroPlano::where('empresa_id', $empresa_id)->delete();
        \App\Models\ContadorEmpresa::where('empresa_id', $empresa_id)->delete();
        \App\Models\DiaSemana::where('empresa_id', $empresa_id)->delete();

        $usuarios = \App\Models\UsuarioEmpresa::where('empresa_id', $empresa_id)->get();
        // echo $usuarios;
        // die;
        
        \App\Models\UsuarioLocalizacao::
        select('usuario_localizacaos.*')
        ->join('localizacaos', 'localizacaos.id', '=', 'usuario_localizacaos.localizacao_id')
        ->where('localizacaos.empresa_id', $empresa_id)->delete();

        \App\Models\Localizacao::where('empresa_id', $empresa_id)->delete();
        $planos = \App\Models\PlanoConta::where('empresa_id', $empresa_id)
        ->orderBy('descricao', 'desc')
        ->get();

        foreach($planos as $t){
            $t->delete();
        }

    }
}
