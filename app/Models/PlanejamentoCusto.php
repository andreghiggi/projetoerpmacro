<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanejamentoCusto extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'cliente_id', 'descricao', 'observacao', 'data_prevista_entrega', 'data_entrega', 'arquivo',
        'usuario_id', 'estado', 'compra_id', 'venda_id', 'numero_sequencial', 'local_id', 'total_custo', 'desconto', 'total_final',

        'codigo_material', 'equipamento', 'desenho', 'material', 'quantidade', 'unidade', 'projeto_id'
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function projeto(){
        return $this->belongsTo(ProjetoCusto::class, 'projeto_id');
    }

    public function produtos()
    {
        return $this->hasMany(ProdutoPlanejamentoCusto::class, 'planejamento_id');
    }

    public function servicos()
    {
        return $this->hasMany(ServicoPlanejamentoCusto::class, 'planejamento_id')->where('terceiro', 0);
    }

    public function servicosTerceiro()
    {
        return $this->hasMany(ServicoPlanejamentoCusto::class, 'planejamento_id')->where('terceiro', 1);
    }

    public function custosAdm()
    {
        return $this->hasMany(CustoAdmPlanejamentoCusto::class, 'planejamento_id');
    }

    public function itensProposta()
    {
        return $this->hasMany(ItemPropostaPlanejamentoCusto::class, 'planejamento_id');
    }

    public function cotacoes()
    {
        return $this->hasMany(Cotacao::class, 'planejamento_id');
    }

    public function logs()
    {
        return $this->hasMany(PlanejamentoCustoLog::class, 'planejamento_id')->orderBy('id', 'desc');
    }

    public static function estados(){
        return [
            // 'novo' => 'Novo',
            'cotacao' => 'Cotação',
            'proposta' => 'Proposta',
            'producao' => 'Produção',
            'cancelado' => 'Cancelado',
            'finalizado' => 'Finalizado',
        ];
    }

    public function proximosEstados(){
        $insere = false;
        $retorno = ['' => 'Selecione'];
        return PlanejamentoCusto::estados();
        foreach(PlanejamentoCusto::estados() as $key => $e){
            if($insere){
                $retorno[$key] = $e;
            }
            if($key == $this->estado){
                $insere = true;
            }
        }
        return $retorno;
    }

    public function _estado(){
        if($this->estado == 'novo'){
            return "<h5 class='badge bg-light text-dark'>NOVO</h5>";
        }else if($this->estado == 'proposta'){
            return "<h5 class='badge bg-primary'>PROPOSTA</h5>";
        }else if($this->estado == 'cotacao'){
            return "<h5 class='badge bg-info'>COTAÇÃO</h5>";
        }else if($this->estado == 'producao'){
            return "<h5 class='badge bg-dark'>PRODUÇÃO</h5>";
        }else if($this->estado == 'cancelado'){
            return "<h5 class='badge bg-danger'>CANCELADO</h5>";
        }else{
            return "<h5 class='badge bg-success'>FINALIZADO</h5>";
        }
    }
}
