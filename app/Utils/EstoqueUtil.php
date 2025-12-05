<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\Estoque;
use App\Models\Produto;
use App\Models\Localizacao;
use App\Models\VendiZapConfig;
use App\Models\ProdutoVariacao;
use App\Models\EstoqueAtualProduto;
use App\Models\MovimentacaoProduto;
use Illuminate\Support\Facades\Auth;

class EstoqueUtil
{
    protected $urlVendiZap = "https://app.vendizap.com/api";

    public function incrementaEstoque($produto_id, $quantidade, $produto_variacao_id, $local_id = null)
    {
        if(!$local_id){
            $usuario_id = Auth::user()->id;
            $local = Localizacao::where('usuario_localizacaos.usuario_id', $usuario_id)
            ->select('localizacaos.*')
            ->join('usuario_localizacaos', 'usuario_localizacaos.localizacao_id', '=', 'localizacaos.id')
            ->first();
            if($local){
                $local_id = $local->id;
            }
        }
        $item = Estoque::where('produto_id', $produto_id)
        ->when($produto_variacao_id != null, function ($q) use ($produto_variacao_id) {
            return $q->where('produto_variacao_id', $produto_variacao_id);
        })
        ->where('local_id', $local_id)
        ->first();

        $produto = Produto::findOrFail($produto_id);
        if($produto->combo){

            foreach($produto->itensDoCombo as $c){
                $this->incrementaEstoque($c->item_id, $c->quantidade * $quantidade, $produto_variacao_id, $local_id);
            }
        }else{

            if ($item != null) {
                $item->quantidade += (float)$quantidade;
                $item->save();
            } else {
                $item = Estoque::create([
                    'produto_id' => $produto_id,
                    'quantidade' => $quantidade,
                    'produto_variacao_id' => $produto_variacao_id,
                    'local_id' => $local_id
                ]);
            }
        }

        if($item){
            $atual = EstoqueAtualProduto::where('produto_id', $produto_id)->first();
            if($atual == null){
                EstoqueAtualProduto::create([
                    'quantidade' => $item->quantidade,
                    'produto_id' => $produto_id,
                ]);
            }else{
                $atual->quantidade = $item->quantidade;
                $atual->save();
            }
        }
    }

    public function reduzEstoque($produto_id, $quantidade, $produto_variacao_id, $local_id = null)
    {
        if(!$local_id){
            $usuario_id = Auth::user()->id;
            $local = Localizacao::where('usuario_localizacaos.usuario_id', $usuario_id)
            ->select('localizacaos.*')
            ->join('usuario_localizacaos', 'usuario_localizacaos.localizacao_id', '=', 'localizacaos.id')
            ->first();
            if($local){
                $local_id = $local->id;
            }
        }
        $item = Estoque::where('produto_id', $produto_id)
        ->when($produto_variacao_id != null, function ($q) use ($produto_variacao_id) {
            return $q->where('produto_variacao_id', $produto_variacao_id);
        })
        ->where('local_id', $local_id)
        ->first();

        if ($item != null) {
            $produto = $item->produto;
            if($produto->combo){
                foreach($produto->itensDoCombo as $c){
                    $this->reduzEstoque($c->item_id, $c->quantidade * $quantidade, $produto_variacao_id, $local_id);
                }
            }else{
                $item->quantidade -= $quantidade;
                $item->save();
            }
        }else{
            $produto = Produto::findOrFail($produto_id);
            if($produto->combo){
                foreach($produto->itensDoCombo as $c){
                    $this->reduzEstoque($c->item_id, $c->quantidade * $quantidade, $produto_variacao_id, $local_id);
                }
            }
        }        

        if($produto->vendizap_id){
            $this->SetaEstoqueVendiZap($produto, $quantidade, $produto_variacao_id);
        }

        if($item){
            $atual = EstoqueAtualProduto::where('produto_id', $produto_id)->first();
            if($atual == null){
                EstoqueAtualProduto::create([
                    'quantidade' => $item->quantidade,
                    'produto_id' => $produto_id,
                ]);
            }else{
                $atual->quantidade = $item->quantidade;
                $atual->save();
            }
        }
    }

    private function SetaEstoqueVendiZap($produto, $quantidade, $produto_variacao_id){
        try{
            $estoque = Estoque::where('produto_id', $produto->id)
            ->when($produto_variacao_id != null, function ($q) use ($produto_variacao_id) {
                return $q->where('produto_variacao_id', $produto_variacao_id);
            })->first();
            // dd($estoque);
            $qtdAtual = 0;

            if($estoque){
                $qtdAtual = $estoque->quantidade;
            }
            
            if($produto_variacao_id == null){
                $this->ajustaEstoqueSimples($produto, $qtdAtual);
            }else{
                $this->ajustaEstoqueVariacao($produto, $qtdAtual, $produto_variacao_id);
            }
        }catch(\Exception $e){
            echo $e->getMessage();
            die;
        }

    }

    private function ajustaEstoqueVariacao($item, $quantidade, $produto_variacao_id){
        $produtoVariacao = ProdutoVariacao::find($produto_variacao_id);

        if($produtoVariacao){
            $config = VendiZapConfig::where('empresa_id', $item->empresa_id)->first();


            $ch = curl_init();
            $headers = [
                "X-Auth-Id: " . $config->auth_id,
                "X-Auth-Secret: " . $config->auth_secret,
            ];

            curl_setopt($ch, CURLOPT_URL, $this->urlVendiZap . '/produtos/'.$item->vendizap_id);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            $data = json_decode(curl_exec($ch));
            curl_close($ch);

            if(!isset($data->estoque)){
                return;
            }

            // dd($data);
            if(sizeof($data->variacoes) == 1){
                foreach($data->variacoes[0]->variaveis as $variavel){
                    if($variavel->nome == $produtoVariacao->descricao){
                        $data = [
                            'quantidade' => $quantidade,
                            'combinacao' => [
                                $data->variacoes[0]->id => $variavel->id
                            ]
                        ];
                    }
                }
            }else{
                $combinacao = $this->getCombinacao($data, $produtoVariacao);

                $data = [
                    'quantidade' => $quantidade,
                    'combinacao' => $combinacao
                ];
            }
            // dd($data);

            $ch = curl_init();
            $headers = [
                "X-Auth-Id: " . $config->auth_id,
                "X-Auth-Secret: " . $config->auth_secret,
                'Content-Type: application/json'
            ];


            curl_setopt($ch, CURLOPT_URL, $this->urlVendiZap . '/estoque/'.$item->vendizap_id);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

            $data = json_decode(curl_exec($ch));
            curl_close($ch);
            // dd($data);
            return $data;
        }
    }

    private function getCombinacao($data, $produtoVariacao){
        $descricao = $produtoVariacao->descricao;
        $temp = explode(" ", $descricao);
        $ret1 = [];
        $ret2 = [];
        $retorno = [];

        foreach($data->variacoes[0]->variaveis as $va){
            if($va->nome == $temp[0]){
                $ret1 = [
                    'variacao_id' => $data->variacoes[0]->id,
                    'variavel_id' => $va->id
                ];
            }
        }

        foreach($data->variacoes[1]->variaveis as $va){
            if($va->nome == $temp[1]){
                $ret2 = [
                    'variacao_id' => $data->variacoes[1]->id,
                    'variavel_id' => $va->id
                ];
            }
        }
        return [
            $ret1['variacao_id'] => $ret1['variavel_id'],
            $ret2['variacao_id'] => $ret2['variavel_id'],
        ];
        
    }

    private function ajustaEstoqueSimples($item, $quantidade){
        $config = VendiZapConfig::where('empresa_id', $item->empresa_id)->first();

        $ch = curl_init();
        $headers = [
            "X-Auth-Id: " . $config->auth_id,
            "X-Auth-Secret: " . $config->auth_secret,
            'Content-Type: application/json'
        ];

        $data = [
            'quantidade' => $quantidade
        ];

        curl_setopt($ch, CURLOPT_URL, $this->urlVendiZap . '/estoque/'.$item->vendizap_id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

        $data = json_decode(curl_exec($ch));
        curl_close($ch);
        // dd($data);
        return $data;
    }

    public function reduzComposicao($produto_id, $quantidade, $produto_variacao_id = null)
    {
        $produto = Produto::findOrFail($produto_id);
        foreach ($produto->composicao as $item) {
            $this->reduzEstoque($item->ingrediente_id, ($item->quantidade * $quantidade), $produto_variacao_id);
        }
        $this->incrementaEstoque($produto_id, $quantidade, $produto_variacao_id);
    }

    public function verificaEstoqueComposicao($produto_id, $quantidade, $produto_variacao_id = null)
    {
        $produto = Produto::findOrFail($produto_id);
        $mensagem = "";
        foreach ($produto->composicao as $item) {
            $qtd = $item->quantidade * $quantidade;

            if($item->ingrediente->estoque){
                if($qtd > $item->ingrediente->estoque->quantidade){
                    $mensagem .= $item->ingrediente->nome . " com estoque insuficiente | ";
                }
            }else{
                $mensagem .= $item->ingrediente->nome . " sem nenhum estoque cadastrado | ";
            }
        }
        $mensagem = substr($mensagem, 0, strlen($mensagem)-2);
        return $mensagem;

    }

    public function verificaEstoqueCombo($produto, $quantidade)
    {

        $mensagem = "";
        foreach ($produto->itensDoCombo as $item) {
            $qtd = $item->quantidade * $quantidade;
            if($item->produtoDoCombo->gerenciar_estoque){
                if($item->produtoDoCombo->estoque){
                    if($qtd > $item->produtoDoCombo->estoque->quantidade){
                        $mensagem .= $item->produtoDoCombo->nome . " com estoque insuficiente | ";
                    }
                }else{
                    $mensagem .= $item->produtoDoCombo->nome . " sem nenhum estoque cadastrado | ";
                }
            }
        }
        $mensagem = substr($mensagem, 0, strlen($mensagem)-2);
        return $mensagem;

    }

    public function movimentacaoProduto($produto_id, $quantidade, $tipo, $codigo_transacao, $tipo_transacao, $user_id,
        $produto_variacao_id = null){

        $estoque = Estoque::where('produto_id', $produto_id)->first();
        MovimentacaoProduto::create([
            'produto_id' => $produto_id,
            'quantidade' => $quantidade,
            'tipo' => $tipo,
            'codigo_transacao' => $codigo_transacao,
            'tipo_transacao' => $tipo_transacao,
            'produto_variacao_id' => $produto_variacao_id,
            'user_id' => $user_id,
            'estoque_atual' => $estoque ? $estoque->quantidade : 0
        ]);
    }

}
