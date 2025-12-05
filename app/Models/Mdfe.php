<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mdfe extends Model
{

	protected $fillable = [
		'uf_inicio', 'uf_fim', 'encerrado', 'data_inicio_viagem', 'carga_posterior',
		'veiculo_tracao_id', 'veiculo_reboque_id', 'veiculo_reboque2_id',
		'veiculo_reboque3_id', 'estado_emissao', 'seguradora_nome',
		'seguradora_cnpj', 'numero_apolice', 'numero_averbacao', 'valor_carga',
		'quantidade_carga', 'info_complementar', 'info_adicional_fisco', 'cnpj_contratante',
		'mdfe_numero', 'condutor_nome', 'condutor_cpf', 'tp_emit', 'tp_transp', 'lac_rodo',
		'chave', 'protocolo', 'empresa_id', 'produto_pred_nome', 'produto_pred_ncm',
		'produto_pred_cod_barras', 'cep_carrega', 'cep_descarrega', 'tp_carga',
		'latitude_carregamento', 'longitude_carregamento', 'latitude_descarregamento',
		'longitude_descarregamento', 'local_id', 'tipo_modal',
		'nome_pagador', 'documento_pagador', 'ind_pag', 'valor_transporte'
	];

	public function componentes(){
		return $this->hasMany(ComponenteMdfe::class, 'mdfe_id');
	}

	public function parcelamento(){
		return $this->hasMany(ParcelamentoMdfe::class, 'mdfe_id');
	}

	public function infosBancaria(){
		return $this->hasMany(InformacaoBancariaMdfe::class, 'mdfe_id');
	}
	
	public function empresa()
	{
		return $this->belongsTo(Empresa::class, 'empresa_id');
	}
	
	public function veiculoTracao()
	{
		return $this->belongsTo(Veiculo::class, 'veiculo_tracao_id');
	}

	public function localizacao()
	{
		return $this->belongsTo(Localizacao::class, 'local_id');
	}

	public function veiculoReboque()
	{
		return $this->belongsTo(Veiculo::class, 'veiculo_reboque_id');
	}

	public function veiculoReboque2()
	{
		return $this->belongsTo(Veiculo::class, 'veiculo_reboque2_id');
	}

	public function veiculoReboque3()
	{
		return $this->belongsTo(Veiculo::class, 'veiculo_reboque3_id');
	}

	public function municipiosCarregamento()
	{
		return $this->hasMany(MunicipioCarregamento::class, 'mdfe_id', 'id');
	}

	public function ciots()
	{
		return $this->hasMany(Ciot::class, 'mdfe_id', 'id');
	}

	public function percurso()
	{
		return $this->hasMany(Percurso::class, 'mdfe_id', 'id');
	}

	public function valesPedagio()
	{
		return $this->hasMany(ValePedagio::class, 'mdfe_id', 'id');
	}

	public function infoDescarga()
	{
		return $this->hasMany(InfoDescarga::class, 'mdfe_id', 'id');
	}

	public static function lastNumero($empresa)
	{
		if($empresa->ambiente == 2){
			return $empresa->numero_ultima_mdfe_homologacao+1;
		}else{
			return $empresa->numero_ultima_mdfe_producao+1;
		}
	}
	

	public static function cUF()
	{
		return [
			'12' => 'AC',
			'27' => 'AL',
			'13' => 'AM',
			'16' => 'AP',
			'29' => 'BA',
			'23' => 'CE',
			'53' => 'DF',
			'32' => 'ES',
			'52' => 'GO',
			'21' => 'MA',
			'31' => 'MG',
			'50' => 'MS',
			'51' => 'MT',
			'15' => 'PA',
			'25' => 'PB',
			'26' => 'PE',
			'22' => 'PI',
			'41' => 'PR',
			'33' => 'RJ',
			'24' => 'RN',
			'11' => 'RO',
			'14' => 'RR',
			'43' => 'RS',
			'42' => 'SC',
			'28' => 'SE',
			'35' => 'SP',
			'17' => 'TO'
		];
	}

	public static function tiposUnidadeTransporte()
	{
		return [
			'1' => 'Rodoviário Tração',
			'2' => 'Rodoviário Reboque',
			'3' => 'Navio',
			'4' => 'Balsa',
			'5' => 'Aeronave',
			'6' => 'Vagão',
			'7' => 'Outros'
		];
	}

	public static function indicadoresDePagamento(){
		return [
			0 => 'A vista',
			1 => 'A prazo',
			2 => 'Outros',
		];
	}

	public static function tiposDeComponentes(){
		return [
			'01' => 'Frete',
			'02' => 'Pedágio',
			'03' => 'Outros',
		];
	}

	public static function tiposCarga()
	{
		return [
			'01' => 'Granel sólido',
			'02' => 'Granel líquido',
			'03' => 'Frigorificada',
			'04' => 'Conteinerizada',
			'05' => 'Carga Geral',
			'06' => 'Neogranel',
			'07' => 'Perigosa (granel sólido)',
			'08' => 'Perigosa (granel líquido)',
			'09' => 'Perigosa (carga frigorificada)',
			'10' => 'Perigosa (conteinerizada)',
			'11' => 'Perigosa (carga geral)'
		];
	}

	public static function tiposModal(){
		return [
			'1' => '1 - Rodoviário',
			'2' => '2 - Aéreo',
			'3' => '3 - Aquaviário',
			'4' => '4 - Ferroviário'
		];
	}

	public function estadoEmissao()
	{
		if ($this->estado_emissao == 'aprovado') {
			return "<span class='btn btn-sm btn-success px-3'>Aprovado</span>";
		} else if ($this->estado_emissao == 'cancelado') {
			return "<span class='btn btn-sm btn-danger px-3'>Cancelado</span>";
		} else if ($this->estado_emissao == 'rejeitado') {
			return "<span class='btn btn-sm btn-warning px-3'>Rejeitado</span>";
		}
		return "<span class='btn btn-sm btn-info px-3'>Novo</span>";
	}
}
