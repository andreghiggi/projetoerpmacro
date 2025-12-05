<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CteOs extends Model
{
    use HasFactory;

    protected $fillable = [
        'emitente_id', 'tomador_id', 'usuario_id', 'natureza_id', 'tomador',
        'municipio_envio', 'municipio_inicio', 'municipio_fim', 'valor_transporte',
        'valor_receber', 'observacao', 'sequencia_cce', 'numero_emissao', 'chave', 'estado_emissao',
        'empresa_id', 'cst', 'perc_icms', 'descricao_servico', 'quantidade_carga', 'veiculo_id',
        'modal', 'data_viagem', 'horario_viagem', 'cfop', 'local_id'
    ];


    // 0-Remetente; 1-Expedidor; 2-Recebedor; 3-Destinatário

    public function getTomador()
    {
        if ($this->tomador == 0) return 'Remetente';
        else if ($this->tomador == 1) return 'Expedidor';
        else if ($this->tomador == 2) return 'Recebedor';
        else if ($this->tomador == 3) return 'Destinatário';
    }

    public function emitente()
    {
        return $this->belongsTo(Cliente::class, 'emitente_id');
    }

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'local_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function tomador_cli()
    {
        return $this->belongsTo(Cliente::class, 'tomador_id');
    }

    public function municipioEnvio()
    {
        return $this->belongsTo(Cidade::class, 'municipio_envio');
    }

    public function municipioInicio()
    {
        return $this->belongsTo(Cidade::class, 'municipio_inicio');
    }

    public function municipioFim()
    {
        return $this->belongsTo(Cidade::class, 'municipio_fim');
    }

    public function natureza()
    {
        return $this->belongsTo(NaturezaOperacao::class, 'natureza_id');
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(Cliente::class, 'tomador_id');
    }

    public static function lastCTe()
    {
        $value = session('user_logged');
        $empresa_id = $value['empresa'];
        $cte = CteOs::where('numero_emissao', '!=', 0)
            ->where('empresa_id', $empresa_id)
            ->orderBy('numero_emissao', 'desc')
            ->first();

        $config = Empresa::where('id', $empresa_id)
            ->first();
        if ($cte == null) {
            return $config->ultimo_numero_cte ?? 0;
        } else {

            return $config->ultimo_numero_cte > $cte->numero_emissao ? $config->ultimo_numero_cte : $cte->numero_emissao;
        }
    }

    public static function getCsts()
    {
        return [
            '00' => '00 - tributação normal ICMS',
            '20' => '20 - tributação com BC reduzida do ICMS',
            '40' => '40 - ICMS isenção',
            '41' => '41 - ICMS não tributada',
            '51' => '51 - ICMS diferido',
            '60' => '60 - ICMS cobrado por substituição tributária',
            '90' => '90 - ICMS outros',
        ];
    }

    public static function filtroData($dataInicial, $dataFinal, $estado)
    {
        $value = session('user_logged');
        $empresa_id = $value['empresa'];
        $c = CteOs::where('empresa_id', $empresa_id)
            ->whereBetween('created_at', [
                $dataInicial,
                $dataFinal
            ]);

        if ($estado != 'TODOS') $c->where('estado', $estado);

        return $c->get();
    }

    public static function filtroEstado($estado)
    {
        $value = session('user_logged');
        $empresa_id = $value['empresa'];
        $c = CteOs::where('empresa_id', $empresa_id)
            ->where('estado', $estado);

        return $c->get();
    }

    public static function tiposTomador()
    {
        return [
            '0' => 'Remetente',
            '1' => 'Expedidor',
            '2' => 'Recebedor',
            '3' => 'Destinatário'
        ];
    }

    public static function modals()
    {
        return [
            '01' => 'RODOVIARIO',
            '02' => 'AEREO',
            '03' => 'AQUAVIARIO',
            '04' => 'FERROVIARIO',
            '05' => 'DUTOVIARIO',
            '06' => 'MULTIMODAL',
        ];
    }

    public static function gruposCte()
    {
        return [
            'ide',
            'toma03',
            'toma04',
            'enderToma',
            'autXML',
            'compl',
            'ObsCont',
            'ObsFisco',
            'emit',
            'enderEmit',
            'rem',
            'enderReme',
            'infNF',
            'infOutros',
            'infUnidTransp',
            'IacUnidCarga',
            'infUnidCarga',
            'exped',
            'enderExped',
            'receb',
            'enderReceb',
            'dest',
            'enderDest',
            'vPrest',
            'Comp',
            'imp',
            'ICMS',
            'infQ',
            'docAnt'
        ];
    }

    public function percurso()
    {
        return $this->hasMany(PercursoCteOs::class, 'cteos_id');
    }

    public function estadoEmissao()
    {
        if ($this->estado_emissao == 'aprovado') {
            return "<span class='btn btn-sm btn-success'>Aprovado</span>";
        } else if ($this->estado_emissao == 'cancelado') {
            return "<span class='btn btn-sm btn-danger'>Cancelado</span>";
        } else if ($this->estado_emissao == 'rejeitado') {
            return "<span class='btn btn-sm btn-warning'>Rejeitado</span>";
        }
        return "<span class='btn btn-sm btn-info'>Novo</span>";
    }
}
