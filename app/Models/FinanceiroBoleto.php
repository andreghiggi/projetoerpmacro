<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceiroBoleto extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'valor', 'vencimento', 'pdf_boleto', '_id',
        'status', 'multa', 'juros', 'valor_recebido', 'arquivo', 'plano_id', 'data_recebimento', 'data_liquidacao'
    ];

    public function categoria(){
        return $this->belongsTo(CategoriaContaPagarSuper::class, 'categoria_id');
    }

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function plano(){
        return $this->belongsTo(Plano::class, 'plano_id');
    }

    public static function tiposPagamento(){
        return [
            'Dinheiro',
            'Cheque',
            'Boleto',
            'Cartão de Crédito',
            'Cartão de Débito',
            'Depósito Bancário',
            'Pagamento Instantâneo (PIX)',
            'Outros'
        ];
    }
}
