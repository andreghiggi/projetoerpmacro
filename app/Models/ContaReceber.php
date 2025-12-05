<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaReceber extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nfe_id', 'nfce_id', 'cliente_id', 'descricao', 'valor_integral', 'valor_recebido', 'data_vencimento',
        'data_recebimento', 'status', 'observacao', 'tipo_pagamento', 'caixa_id', 'local_id', 'arquivo', 'motivo_estorno',
        'categoria_conta_id', 'valor_original', 'observacao2', 'observacao3', 'referencia', 'conta_empresa_id', 'ordem_servico_id'
    ];

    protected $appends = [ 'info' ];

    public function getInfoAttribute()
    {   
        if($this->cliente){
            return "Cliente: " . $this->cliente->info . " - valor: R$ " . __moeda($this->valor_integral) . ", vencimento: " . __data_pt($this->data_vencimento, 0);
        }else{
            return "Valor: R$ " . __moeda($this->valor_integral) . ", vencimento: " . __data_pt($this->data_vencimento, 0);
        }
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaConta::class, 'categoria_conta_id');
    }

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'local_id');
    }

    public function nfce()
    {
        return $this->belongsTo(Nfce::class, 'nfce_id');
    }

    public function contaEmpresa()
    {
        return $this->belongsTo(ContaEmpresa::class, 'conta_empresa_id');
    }

    public function nfe()
    {
        return $this->belongsTo(Nfe::class, 'nfe_id');
    }

    public function ordemServico()
    {
        return $this->belongsTo(OrdemServico::class, 'ordem_servico_id');
    }

    public function contaFatura(){
        $nfe = $this->nfe;
        $total = sizeof($nfe->fatura);

        $posicao = 1;
        foreach($nfe->fatura as $fat){
            if($fat->data_vencimento != $this->data_vencimento){
                $posicao++;
            }else{
                return "$posicao/$total";
            }
        }
        return "$posicao/$total";
    }
    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id')->with('cidade');
    }

    public function boleto()
    {
        return $this->hasOne(Boleto::class, 'conta_receber_id');
    }

    public static function tiposPagamento()
    {
        return [
            '01' => 'Dinheiro',
            '02' => 'Cheque',
            '03' => 'Cartão de Crédito',
            '04' => 'Cartão de Débito',
            '05' => 'Crédito Loja',
            '06' => 'Crediário',
            '10' => 'Vale Alimentação',
            '11' => 'Vale Refeição',
            '12' => 'Vale Presente',
            '13' => 'Vale Combustível',
            '14' => 'Duplicata Mercantil',
            '15' => 'Boleto Bancário',
            '16' => 'Depósito Bancário',
            '17' => 'Pagamento Instantâneo (PIX)',
            '90' => 'Sem Pagamento',
            '99' => 'Outros',
        ];
    }

    public function diasAtraso(){
        $d = date('Y-m-d');
        $d2 = $this->data_vencimento;
        $dif = strtotime($d2) - strtotime($d);
        $dias = floor($dif / (60 * 60 * 24));
        if($dias == 0){
            return "conta vence hoje";
        }

        if($dias > 0){
            return "$dias dia(s) para o vencimento";
        }else{
            return "conta vencida à " . ($dias*-1) . " dia(s)";
        }
    } 
}
