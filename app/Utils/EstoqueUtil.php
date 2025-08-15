<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\Estoque;
use App\Models\Produto;
use App\Models\Localizacao;
use App\Models\MovimentacaoProduto;
use Illuminate\Support\Facades\Auth;

class EstoqueUtil
{
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
                Estoque::create([
                    'produto_id' => $produto_id,
                    'quantidade' => $quantidade,
                    'produto_variacao_id' => $produto_variacao_id,
                    'local_id' => $local_id
                ]);
            }
        }
    }

    public function incrementaEstoqueCron($produto_id, $quantidade, $produto_variacao_id = null, $local_id = 2)
    {
        if ($produto_variacao_id) {
            $variacaoExiste = \DB::table('produto_variacaos')
                ->where('id', $produto_variacao_id)
                ->exists();
            if (!$variacaoExiste) {
                $produto_variacao_id = null;
            }
        }

        if (!$local_id) {
            if (\Auth::check()) {
                $usuario_id = \Auth::id();
            } else {
                $usuario_id = null;
            }

            $local = Localizacao::where('usuario_localizacaos.usuario_id', $usuario_id)
                ->select('localizacaos.*')
                ->join('usuario_localizacaos', 'usuario_localizacaos.localizacao_id', '=', 'localizacaos.id')
                ->first();

            if ($local) {
                $local_id = $local->id;
            }
        }

        // Busca estoque existente
        $item = Estoque::where('produto_id', $produto_id)
            ->when($produto_variacao_id !== null, function ($q) use ($produto_variacao_id) {
                return $q->where('produto_variacao_id', $produto_variacao_id);
            })
            ->where('local_id', $local_id)
            ->first();

        $produto = Produto::find($produto_id);
        if (!$produto) {
            \Log::warning("Produto {$produto_id} não encontrado ao tentar incrementar estoque.");
            return; // ou tratar de outra forma
        }


        if ($produto->combo) {
            foreach ($produto->itensDoCombo as $c) {
                $this->incrementaEstoque(
                    $c->item_id,
                    $c->quantidade * $quantidade,
                    $produto_variacao_id,
                    $local_id
                );
            }
        } else {
            if ($item) {
                $item->quantidade += (float) $quantidade;
                $item->save();
            } else {
                Estoque::create([
                    'produto_id' => $produto_id,
                    'quantidade' => $quantidade,
                    'produto_variacao_id' => $produto_variacao_id, // agora só entra se existir
                    'local_id' => $local_id
                ]);
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
