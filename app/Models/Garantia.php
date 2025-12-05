<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Garantia extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'cliente_id', 'produto_id', 'nfe_id', 'nfce_id', 'data_venda', 'data_solicitacao', 'prazo_garantia',
        'status', 'descricao_problema', 'observacao', 'valor_reparo', 'usuario_id', 'servico_id', 'ordem_servico_id'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class, 'servico_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function dataValidade()
    {
        return $this->data_venda ? Carbon::parse($this->data_venda)->addDays($this->prazo_garantia) : null;
    }

    public function isValida()
    {
        return $this->dataValidade() && now()->lte($this->dataValidade());
    }

    public static function estados()
    {
        return [
            'registrada' => 'Registrada',
            'em análise' => 'Em análise',
            'aprovada' => 'Aprovada',
            'recusada' => 'Recusada',
            'concluída' => 'Concluída',
            'expirada' => 'Expirada',
        ];
    }

    public function statusFormatado()
    {
        switch ($this->status) {
             case 'registrada':
                return '<span class="badge bg-info text-white">Registrada</span>';
            case 'em análise':
                return '<span class="badge bg-warning text-white">Em Análise</span>';
            case 'aprovada':
                return '<span class="badge bg-success text-white">Aprovada</span>';
            case 'recusada':
                return '<span class="badge bg-danger text-white">Recusada</span>';
            case 'concluída':
                return '<span class="badge bg-primary text-white">Concluída</span>';
            case 'expirada':
                return '<span class="badge bg-secondary text-white">Expirada</span>';
            default:
                return '<span class="badge bg-light text-dark">Indefinido</span>';
        }
    }
}
