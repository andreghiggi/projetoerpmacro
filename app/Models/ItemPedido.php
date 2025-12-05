<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id', 'produto_id', 'observacao', 'estado', 'quantidade', 'valor_unitario', 'sub_total', 
        'ponto_carne', 'tamanho_id', 'tempo_preparo', 'funcionario_id', 'impresso', 'nome_cardapio', 'telefone_cardapio',
        'finalizado_pdv'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function funcionario(){
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    public function pedido(){
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function adicionais(){
        return $this->hasMany(ItemAdicional::class, 'item_pedido_id')->with('adicional');
    }

    public function pizzas(){
        return $this->hasMany(ItemPizzaPedido::class, 'item_pedido_id')->with('sabor');
    }

    public function tamanho(){
        return $this->belongsTo(TamanhoPizza::class, 'tamanho_id');
    }

    public function tempoPreparoRestante(){
        $horarioPedido = $this->updated_at;
        $horarioAtual = date('Y-m-d H:i:s');
        $dif = strtotime($horarioAtual) - strtotime($horarioPedido);
        if(!$this->tempo_preparo) return -1;
        $minutos = $this->tempo_preparo - $dif/60;
        return (int)$minutos;
    }

    public function getAdicionaisStr(){
        $adds = "";
        foreach($this->adicionais as $a){
            $adds .= $a->adicional->nome . ", ";
        }
        $adds = substr($adds, 0, strlen($adds)-2);
        return $adds;
    }

}
