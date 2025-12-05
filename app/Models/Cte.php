<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cte extends Model
{
    use HasFactory;

    protected $fillable = [
        'remetente_id', 'destinatario_id', 'natureza_id', 'tomador',
        'municipio_envio', 'municipio_inicio', 'municipio_fim', 'logradouro_tomador',
        'numero_tomador', 'bairro_tomador', 'cep_tomador', 'municipio_tomador',
        'valor_transporte', 'valor_receber', 'valor_carga',
        'produto_predominante', 'data_prevista_entrega', 'observacao',
        'sequencia_cce', 'numero', 'chave', 'estado', 'retira', 'detalhes_retira',
        'modal', 'veiculo_id', 'tpDoc', 'descOutros', 'nDoc', 'vDocFisc', 'empresa_id',
        'globalizado', 'cst', 'perc_icms', 'recebedor_id', 'expedidor_id', 'perc_red_bc', 'numero_serie', 'numero', 
        'status_pagamento', 'ambiente', 'cfop', 'api', 'local_id', 'tipo_servico', 'referencia_cte',
        'doc_anterior', 'emitente_anterior', 'tp_doc_anterior', 'serie_anterior', 'n_doc_anterior', 'data_emissao_anterior',
        'uf_anterior', 'ie_anterior', 'cpf_cnpj_tomador', 'ie_tomador', 'razao_social_tomador', 'nome_fantasia_tomador',
        'telefone_tomador', 'email_tomador', 'tipo_cte'
    ];

    public function getTomador()
    {
        if ($this->tomador == 0) return 'Remetente';
        else if ($this->tomador == 1) return 'Expedidor';
        else if ($this->tomador == 2) return 'Recebedor';
        else if ($this->tomador == 3) return 'Destinatário';
    }

    public static function tiposDocumentoAnterior(){
        return [
            '07' => '07 - ATRE',
            '08' => '08 - DTA (Despacho de Transito Aduaneiro)',
            '09' => '09 - Conhecimento Aéreo Internacional',
            '10' => '10 - Conhecimento - Carta de Porte Internacional',
            '11' => '11 - Conhecimento Avulso',
            '12' => '12 - TIF (Transporte Internacional Ferroviário)',
            '13' => '13 - BL (Bill of Lading)',
        ];
    }

    public static function tiposServico(){
        return [
            '0' => 'Normal',
            '1' => 'Subcontratação', 
            '2' => 'Redespacho',
            '3' => 'Redespacho Intermediário',
            '4' => 'Serviço Vinculado a Multimodal'
        ];
    }

    public static function tiposDeCte(){
        return [
            '0' => 'CT-e Normal',
            '1' => 'CT-e de Complemento de Valores', 
            '2' => 'CT-e de Anulação',
            '3' => 'CT-e Substituto',
        ];
    }

    public function getTomadorNome()
    {
        if ($this->tomador == 0) {
            return $this->remetente->razao_social;
        } else if ($this->tomador == 1) {
            return $this->expedidor ? $this->expedidor->razao_social : '--';
        } else if ($this->tomador == 2) {
            return $this->recebedor ? $this->recebedor->razao_social : '--';
        } else if ($this->tomador == 3) {
            return $this->recebedor ? $this->destinatario->razao_social : '--';
        }
    }

    public function getTomadorFull()
    {
        if ($this->tomador == 0) return $this->remetente;
        else if ($this->tomador == 1) return $this->expedidor;
        else if ($this->tomador == 2) return $this->recebedor;
        else if ($this->tomador == 3) return $this->destinatario;
    }

    public function chaves_nfe()
    {
        return $this->hasMany(ChaveNfeCte::class, 'cte_id', 'id');
    }

    public static function lastNumero($empresa)
    {
        if($empresa->ambiente == 2){
            return $empresa->numero_ultima_cte_homologacao+1;
        }else{
            return $empresa->numero_ultima_cte_producao+1;
        }
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'local_id');
    }
    
    public function componentes()
    {
        return $this->hasMany(ComponenteCte::class, 'cte_id');
    }

    public function medidas()
    {
        return $this->hasMany(MedidaCte::class, 'cte_id');
    }

    public function natureza()
    {
        return $this->belongsTo(NaturezaOperacao::class, 'natureza_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(Cliente::class, 'destinatario_id');
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    public function remetente()
    {
        return $this->belongsTo(Cliente::class, 'remetente_id');
    }

    public function recebedor()
    {
        return $this->belongsTo(Cliente::class, 'recebedor_id');
    }

    public function expedidor()
    {
        return $this->belongsTo(Cliente::class, 'expedidor_id');
    }

    public function municipioTomador()
    {
        return $this->belongsTo(Cidade::class, 'municipio_tomador');
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

    public static function unidadesMedida()
    {
        return [
            '00' => 'M3',
            '01' => 'KG',
            '02' => 'TON',
            '03' => 'UNIDADE',
            '04' => 'M2',
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

    public static function tiposMedida()
    {
        return [
            'PESO BRUTO' => 'PESO BRUTO',
            'PESO DECLARADO' => 'PESO DECLARADO',
            'PESO CUBADO' => 'PESO CUBADO',
            'PESO AFORADO' => 'PESO AFORADO',
            'PESO AFERIDO' => 'PESO AFERIDO',
            'LITRAGEM' => 'LITRAGEM',
            'CAIXAS' => 'CAIXAS'
        ];
    }

    public static function tiposTomador()
    {
        return [
            '0' => 'Remetente',
            '1' => 'Expedidor',
            '2' => 'Recebedor',
            '3' => 'Destinatário',
            '4' => 'Outros',
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

    public static function getCsosn()
    {
        return [
            'SN' => 'Simples Nacional',
        ];
    }

    public function estadoEmissao()
    {
        if ($this->estado == 'aprovado') {
            return "<span class='btn btn-sm btn-success'>Aprovado</span>";
        } else if ($this->estado == 'cancelado') {
            return "<span class='btn btn-sm btn-danger'>Cancelado</span>";
        } else if ($this->estado == 'rejeitado') {
            return "<span class='btn btn-sm btn-warning'>Rejeitado</span>";
        }
        return "<span class='btn btn-sm btn-info'>Novo</span>";
    }
}
