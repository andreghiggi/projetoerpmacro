<?php

use App\Models\Caixa;
use App\Models\ContadorEmpresa;
use App\Models\Localizacao;
use App\Models\UsuarioLocalizacao;
use App\Models\AcaoLog;
use App\Models\ApiLog;
use App\Models\ApiConfig;
use App\Models\ConfigGeral;
use App\Models\ConfiguracaoSuper;
use App\Models\FinanceiroBoleto;
use App\Models\ProdutoTributacaoLocal;
use App\Models\MarketPlaceConfig;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

function __convert_value_bd($valor)
{
	if (strlen($valor) >= 8) {
		$valor = str_replace(".", "", $valor);
	}
	$valor = str_replace(",", ".", $valor);

	return (float)$valor;
}

function __usaICMSST($empresa, $cst, $cfop){
	if (!$empresa->substituto_tributario) {
		return false;
	}

	$cstPermitidos = ['10','30','70','90'];

	if (!in_array($cst, $cstPermitidos)) {
		return false;
	}

	$cfopsST = ['5401','5402','5403','6401','6402','6403'];

	if (!in_array($cfop, $cfopsST)) {
		return false;
	}

	return true;
}

function __itensPagina(){

	if(!Auth::user()->empresa){
		return env('PAGINACAO');
	}
	$empresa_id = Auth::user()->empresa->empresa_id;
	$config = ConfigGeral::where('empresa_id', $empresa_id)->first();
	if($config == null){
		return env('PAGINACAO');
	}
	return $config->itens_por_pagina;
}

function __validaObjetoEmpresa($objeto)
{
	if(!Auth::user()->empresa){
		return true;
	}
	$empresa_id = Auth::user()->empresa->empresa_id;

	if(isset($objeto->empresa_id)){
		if($objeto->empresa_id !=  $empresa_id){
			abort(403);
		}
	}
	return true;
}

function valor_por_extenso($valor = 0, $maiusculas = false) {

	$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
	$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões",
		"quatrilhões");

	$c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
		"quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
	$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
		"sessenta", "setenta", "oitenta", "noventa");
	$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
		"dezesseis", "dezesete", "dezoito", "dezenove");
	$u = array("", "um", "dois", "três", "quatro", "cinco", "seis",
		"sete", "oito", "nove");

	$z = 0;
	$rt = "";

	$valor = number_format($valor, 2, ".", ".");
	$inteiro = explode(".", $valor);
	for($i=0;$i<count($inteiro);$i++)
		for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
			$inteiro[$i] = "0".$inteiro[$i];

		$fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
		for ($i=0;$i<count($inteiro);$i++) {
			$valor = $inteiro[$i];
			$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
			$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
			$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

			$r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd &&
				$ru) ? " e " : "").$ru;
			$t = count($inteiro)-1-$i;
			$r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
			if ($valor == "000")$z++; elseif ($z > 0) $z--;
			if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t];
			if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) &&
				($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
		}

	if(!$maiusculas){
		return($rt ? $rt : "zero");
	} else {

		if ($rt) $rt=ereg_replace(" E "," e ",ucwords($rt));
		return (($rt) ? ($rt) : "Zero");
	}

}

function __validaObjetoEmpresaContador($contador_empresa_id, $empresa_id)
{

	$item = ContadorEmpresa::where('empresa_id', $empresa_id)
	->where('contador_id', $contador_empresa_id)->first();
	if($item == null){
		abort(403, 'Você não possui acesso a esse registro!');
	}
	return true;
}

function __tipoMenu()
{
	if(!Auth::user()->empresa){
		return env('MENU_PADRAO');
	}
	$empresa_id = Auth::user()->empresa->empresa_id;
	$config = ConfigGeral::where('empresa_id', $empresa_id)->first();
	if($config == null){
		return env('MENU_PADRAO');
	}
	return $config->tipo_menu;
}

function __tipoSmallHeader()
{
	if(!Auth::user()->empresa){
		return '../images/small/small-4.jpg';
	}
	$empresa_id = Auth::user()->empresa->empresa_id;
	$config = ConfigGeral::where('empresa_id', $empresa_id)->first();
	if($config == null){
		return '../images/small/small-4.jpg';
	}
	return '../images/small/'.$config->small_header_user;

}

function __infoTopoMenu(){
	$config = ConfiguracaoSuper::first();
	if($config == null || $config->info_topo_menu == 1) return 1;
	return 0;
}

function __casas_decimais_quantidade()
{
	try{
		if(!Auth::user()->empresa){
			return 2;
		}
		$empresa_id = Auth::user()->empresa->empresa_id;
		$config = ConfigGeral::where('empresa_id', $empresa_id)->first();
		if($config == null){
			return 2;
		}
		return $config->casas_decimais_quantidade;
	}catch(\Exception $e){
		return 2;
	}
}

function __dataTopBar()
{
	if(!Auth::user()->empresa){
		return 'light';
	}
	$empresa_id = Auth::user()->empresa->empresa_id;
	$config = ConfigGeral::where('empresa_id', $empresa_id)->first();
	if($config == null){
		return 'light';
	}
	return $config->cor_top_bar;
}

function __usuarioEscolherPlano(){
	$config = ConfiguracaoSuper::first();
	if($config == null || $config->usuario_alterar_plano == 1) return 1;
	return 0;
}

function __finalizacaoPdv(){
	if(Auth::user()->finalizacao_pdv == null) return 'todos';
	return Auth::user()->finalizacao_pdv;
}

function __dataMenuBar()
{
	if(!Auth::user()->empresa){
		return 'light';
	}
	$empresa_id = Auth::user()->empresa->empresa_id;
	$config = ConfigGeral::where('empresa_id', $empresa_id)->first();
	if($config == null){
		return 'light';
	}
	return $config->cor_menu;
}

function __dataThemeDefault()
{
	
	if(Auth::user() && Auth::user()->tema_padrao){
		return Auth::user()->tema_padrao;
	}
	$config = ConfiguracaoSuper::first();
	if($config == null){
		return 'light';
	}
	return $config->tema_padrao;
}

function __moeda($valor, $casas_decimais = 2)
{
	return number_format($valor, $casas_decimais, ',', '.');
}

function __qtd($valor)
{
	return number_format($valor, __casas_decimais_quantidade(), ',', '.');
}

function __calcPercentual($v1, $v2){
	if($v1 > $v2){
		return number_format(100+(($v2-$v1)/$v1*100), 1);
	}else{
		return 100;
	}
}

function __moedaInput($valor, $casas_decimais = 2)
{
	return number_format($valor, $casas_decimais, ',', '');
}

function __data_pt($data, $hora = true)
{
	if ($hora) {
		return \Carbon\Carbon::parse($data)->format('d/m/Y H:i');
	} else {
		return \Carbon\Carbon::parse($data)->format('d/m/Y');
	}
}

function __hora_pt($data)
{
	return \Carbon\Carbon::parse($data)->format('H:i');
}

function __isMaster()
{
	if (Auth::user()->email == env("MAILMASTER")) {
		return 1;
	}
	return 0;
}

function __isSuporte()
{
	return Auth::user()->suporte;
}

function __isEmpresaMaster($empresa)
{
	foreach($empresa->usuarios as $u){
		if($u->usuario->email == env("MAILMASTER")){
			return 1;
		}
	}
	return 0;
}

function __isContador()
{
	if (Auth::user()->tipo_contador == 1) {
		return 1;
	}
	return 0;
}

function __isContadorPlano()
{
	if (Auth::user()->empresa->empresa->cadastrar_planos == 1) {
		return 1;
	}
	return 0;
}

function __escolheLocalidade()
{
	return Auth::user()->escolher_localidade_venda;
}

function __empresasDoContador()
{
	$contador_id = Auth::user()->empresa->empresa_id;
	return ContadorEmpresa::where('contador_id', $contador_id)->get();
}

function __faturaBoleto()
{
	if(!Auth::user()->empresa) return null;
	$empresa_id = Auth::user()->empresa->empresa_id;
	if(Auth::user()->empresa->empresa->receber_com_boleto == 0) return null;

	$fatura = FinanceiroBoleto::where('empresa_id', $empresa_id)
	->whereMonth('vencimento', date('m'))
	->where('status', 0)
	->first();
	if($fatura != null){
		$config = ConfiguracaoSuper::first();

		$dif = strtotime($fatura->vencimento) - strtotime(date("Y-m-d"));
		$dif = (int)floor($dif / (60 * 60 * 24));

		if($dif <= $config->dias_alerta_boleto){
			// dd($fatura);
			return $fatura;
		}
	}
	return null;
}

function __isAdmin()
{
	return Auth::user()->admin;
}

function __getError($e)
{
	return "Linha: " . $e->getLine() . ", mensagem: " . $e->getMessage() . ", arquivo: " . $e->getFile();
}

function __isCaixaAberto()
{
	$usuario_id = Auth::user()->id;
	return Caixa::where('usuario_id', $usuario_id)->where('status', 1)->first();
}

function get_id_user()
{
	$usr = Auth::user()->id;
	return $usr;
}

function get_name_user()
{
	$usr = Auth::user()->name;
	return $usr;
}

function __mask($val, $mask){
	$maskared = '';
	$k = 0;
	for ($i = 0; $i <= strlen($mask) - 1; ++$i) {
		if ($mask[$i] == '#') {
			if (isset($val[$k])) {
				$maskared .= $val[$k++];
			}
		} else {
			if (isset($mask[$i])) {
				$maskared .= $mask[$i];
			}
		}
	}

	return $maskared;
}

function __setMask($doc){
	$doc = preg_replace('/[^0-9]/', '', $doc);
	$mask = '##.###.###/####-##';
	if (strlen($doc) == 11) {
		$mask = '###.###.###-##';
	}
	return __mask($doc, $mask);
}

function __isPlanoFiscal(){
	$empresa = auth::user()->empresa;
	if(!$empresa) return false;

	$plano = $empresa->empresa->plano;
	if($plano){
		if($plano->plano->fiscal) return 1;
	}

	if(__isContador()){
		return 1;
	}
	return false;
}

function __periodoExpirar(){
	$empresa = auth::user()->empresa;
	if(!$empresa) return false;

	$plano = $empresa->empresa->plano;
	if(!$plano) return false;

	// $plano->data_expiracao = "2025-04-16";
	$dif = strtotime($plano->data_expiracao) - strtotime(date("Y-m-d"));
	$dif = floor($dif / (60 * 60 * 24));

	if($dif <= 5) return 1;
	return 0;
}

function __isActivePlan($empresa, $menu){
	if(!$empresa) return false;
	$plano = $empresa->empresa->plano;
	if($plano){
		$modulos = json_decode($plano->plano->modulos) ?? [];
		if(in_array($menu, $modulos)) return true;
		else return false;
	}
	return false;
}

function __isInternacionalizar($empresa){
	if(!$empresa) return false;
	$config = $empresa->empresa->configuracaoCardapio;
	if(!$config) return false;
	if($config->intercionalizar == 1) return 1;
	return false;
}

function __isNotificacao($empresa){
	if(!$empresa) return false;
	$config = $empresa->empresa->configuracaoCardapio;
	if(!$config) return false;
	return 1;
}

function __isNotificacaoMarketPlace($empresa){
	if(!$empresa) return false;
	$config = $empresa->empresa->configuracaoMarketPlace;
	if(!$config) return false;
	return 1;
}

function __isNotificacaoEcommerce($empresa){
	if(!$empresa) return false;
	$config = $empresa->empresa->configuracaoEcommerce;
	if(!$config) return false;
	return 1;
}

function __countLocalAtivo(){
	try{
		if(!Auth::user()->empresa){
			return 0;
		}
		$empresa_id = Auth::user()->empresa->empresa_id;
		return Localizacao::where('empresa_id', $empresa_id)
		->where('status', 1)->count();
	}catch(\Exception $e){
		return 0;
	}
}

function __getLocaisAtivos(){
	$empresa_id = Auth::user()->empresa->empresa_id;
	return Localizacao::where('empresa_id', $empresa_id)
	->where('status', 1)->get();
}

function __getLocalAtivo(){
	if(!Auth::user()->empresa){
		return 0;
	}
	$empresa_id = Auth::user()->empresa->empresa_id;
	return Localizacao::where('empresa_id', $empresa_id)
	->where('status', 1)->first();
}

function __getLocaisAtivoUsuario(){
	$usuario_id = Auth::user()->id;
	return Localizacao::where('usuario_localizacaos.usuario_id', $usuario_id)
	->select('localizacaos.*')
	->join('usuario_localizacaos', 'usuario_localizacaos.localizacao_id', '=', 'localizacaos.id')
	->where('localizacaos.status', 1)->get();
}

function __objetoParaEmissao($empresa, $local_id){

	$primeiraLocalizacao = Localizacao::where('empresa_id', $empresa->id)
	->where('status', 1)->first();

	$count = Localizacao::where('empresa_id', $empresa->id)
	->where('status', 1)->count();
	if($count <= 1) return $empresa;

	$localizacao = Localizacao::findOrFail($local_id);
	if($primeiraLocalizacao == $localizacao) return $empresa;
	return $localizacao;
}

function __createLog($empresa_id, $local, $acao, $descricao){
	AcaoLog::create([
		'empresa_id' => $empresa_id,
		'local' => $local,
		'acao' => $acao,
		'descricao' => substr($descricao, 0, 255),
	]);
}

function __createApiLog($empresa_id, $token, $status, $descricao, $tipo, $prefixo){
	ApiLog::create([
		'empresa_id' => $empresa_id,
		'token' => $token,
		'status' => $status,
		'descricao' => substr($descricao, 0, 255),
		'tipo' => $tipo,
		'prefixo' => $prefixo
	]);
}

function __validaPermissaoToken($token, $permissao){
	$item = ApiConfig::where('token', $token)->first();
	if($item){
		$permissoes_acesso = $item->permissoes_acesso != 'null' ? json_decode($item->permissoes_acesso) : [];

		if(in_array($permissao, $permissoes_acesso)) return 1;
	}
	return 0;
}

function __isSegmentoPlanoOtica(){
	$empresa = auth::user()->empresa;
	if(!$empresa) return false;

	$plano = $empresa->empresa->plano;
	if($plano){
		if($plano->plano->segmento && $plano->plano->segmento->nome == 'Ótica') return 1;
	}
	return false;
}

function __isSegmentoServico($empresa_id){
	$config = MarketPlaceConfig::where('empresa_id', $empresa_id)->first();
	if($config == null) return 0;
	$segmento = json_decode($config->segmento);
	if(in_array('servicos', $segmento)) return 1;
	return 0;
}

function __isSegmentoProduto($empresa_id){
	$config = MarketPlaceConfig::where('empresa_id', $empresa_id)->first();
	if($config == null) return 0;
	$segmento = json_decode($config->segmento);
	if(in_array('produtos', $segmento)) return 1;
	return 0;
}

function __isProdutoServicoDelivery($empresa_id){
	if(__isSegmentoProduto($empresa_id) && __isSegmentoServico($empresa_id)) return 1;
	return 0;
}

function __tributacaoProdutoLocal($item, $campo, $local_id){
	$itemLocal = ProdutoTributacaoLocal::where('produto_id', $item->id)
	->where('local_id', $local_id)->first();

	if($itemLocal != null){
		return $itemLocal[$campo];
	}
	return $item[$campo];
}

function __tributacaoProdutoLocalNcm($item, $local_id){
	$itemLocal = ProdutoTributacaoLocal::where('produto_id', $item->id)
	->where('local_id', $local_id)->first();
	if($itemLocal != null){
		return $itemLocal->_ncm ? [$itemLocal->ncm => $itemLocal->_ncm->descricao] : [];
	}
	return $item->_ncm ? [$item->ncm => $item->_ncm->descricao] : [];
}

function __primeiroLocal($local_id, $empresa_id){
	$local = Localizacao::where('empresa_id', $empresa_id)
	->where('status', 1)->first();
	return $local_id == $local->id;
}

function __tributacaoProdutoLocalVenda($produto, $local_id){

	$itemLocal = ProdutoTributacaoLocal::where('produto_id', $produto->id)
	->where('local_id', $local_id)->first();
	
	if($itemLocal == null || __primeiroLocal($local_id, $produto->empresa_id)){
		return $produto;
	}

	$produto->ncm = $itemLocal->ncm;
	$produto->perc_icms = $itemLocal->perc_icms;
	$produto->perc_pis = $itemLocal->perc_pis;
	$produto->perc_cofins = $itemLocal->perc_cofins;
	$produto->perc_ipi = $itemLocal->perc_ipi;

	$produto->cest = $itemLocal->cest;
	$produto->origem = $itemLocal->origem;
	$produto->cst_csosn = $itemLocal->cst_csosn;
	$produto->cst_pis = $itemLocal->cst_pis;
	$produto->cst_cofins = $itemLocal->cst_cofins;

	$produto->cst_ipi = $itemLocal->cst_ipi;
	$produto->valor_unitario = $itemLocal->valor_unitario;
	$produto->cfop_estadual = $itemLocal->cfop_estadual;
	$produto->cfop_outro_estado = $itemLocal->cfop_outro_estado;

	return $produto;
}

function __valorProdutoLocal($produto, $local_id){
	$itemLocal = ProdutoTributacaoLocal::where('produto_id', $produto->id)
	->where('local_id', $local_id)->first();
	
	if($itemLocal == null || __primeiroLocal($local_id, $produto->empresa_id)){
		return $produto->valor_unitario;
	}

	return $itemLocal->valor_unitario;
}

function __getPais($codigo){
	if(isset(__getPaises()[$codigo])){
		return __getPaises()[$codigo];
	}
	return 'Brasil';
}

function __getPaises(){
	return [
		'0132' => "Afeganistão",   
		'7560' => "África do Sul",     
		'0175' => "Albânia, República da",     
		'0230' => "Alemanha",
		'0370' => "Andorra Sim",
		'0400' => "Angola",
		'0418' => "Anguilla Sim",
		'0434' => "Antigua e Barbuda Sim",
		'0477' => "Antilhas Holandesas Sim",
		'0531' => "Arábia Saudita", 
		'0590' => "Argélia",
		'0639' => "Argentina",     
		'0647' => "Armênia, República da",     
		'0655' => "Aruba",     
		'0698' => "Austrália",     
		'0728' => "Áustria",   
		'0736' => "Azerbaijão, República do",  
		'0779' => "Bahamas, Ilhas Sim",
		'0809' => "Bahrein, Ilhas Sim",
		'0817' => "Bangladesh",   
		'0833' => "Barbados Sim",
		'0850' => "Belarus",  
		'0876' => "Bélgica",  
		'0884' => "Belize Sim",
		'2291' => "Benin",    
		'0906' => "Bermudas Sim",
		'0973' => "Bolívia",  
		'0981' => "Bósnia-Herzegovina",    
		'1015' => "Botsuana",  
		'1058' => "Brasil",    
		'1082' => "Brunei",    
		'1112' => "Bulgária, República da",    
		'0310' => "Burkina Faso",  
		'1155' => "Burundi",   
		'1198' => "Butão",     
		'1279' => "Cabo Verde, República de",  
		'1457' => "Camarões",  
		'1414' => "Camboja",   
		'1490' => "Canadá",    
		'1504' => "Canal, Ilhas do (Jersey e Guernsey) Sim",
		'1511' => "Canárias, Ilhas",   
		'1546' => "Catar",     
		'1376' => "Cayman, Ilhas Sim",
		'1538' => "Cazaquistão, República do",     
		'7889' => "Chade",     
		'1589' => "Chile",     
		'1600' => "China, República Popular da",   
		'1635' => "Chipre Sim",
		'5118' => "Christmas, Ilha (Navidad)",     
		'7412' => "Cingapura",     
		'1651' => "Cocos (Keeling), Ilhas",    
		'1694' => "Colômbia",  
		'1732' => "Comores, Ilhas",    
		'8885' => "Congo, República Democrática do",   
		'1775' => "Congo, República do",   
		'1830' => "Cook, Ilhas Sim",
		'1872' => "Coréia, Rep. Pop. Democrática da",  
		'1902' => "Coréia, República da",  
		'1937' => "Costa do Marfim",   
		'1961' => "Costa Rica  Sim",
		'1988' => "Coveite",   
		'1953' => "Croácia, República da",     
		'1996' => "Cuba",  
		'2321' => "Dinamarca",     
		'7838' => "Djibuti Sim",
		'2356' => "Dominica, Ilha Sim",
		'2402' => "Egito",     
		'6874' => "El Salvador",   
		'2445' => "Emirados Árabes Unidos",    
		'2399' => "Equador",   
		'2437' => "Eritréia",  
		'6289' => "Escócia", 
		'2470' => "Eslovaca, República",   
		'2461' => "Eslovênia, República da",   
		'2453' => "Espanha",   
		'2496' => "Estados Unidos",    
		'2518' => "Estônia, República da",     
		'2534' => "Etiópia", 
		'2550' => "Falkland (Ilhas Malvinas)",     
		'2593' => "Feroe, Ilhas",  
		'8702' => "Fiji",  
		'2674' => "Filipinas",     
		'2712' => "Finlândia",     
		'1619' => "Formosa (Taiwan)",  
		'2755' => "França",    
		'2810' => "Gabão",     
		'6289' => "Gales, País de",    
		'2852' => "Gâmbia",    
		'2895' => "Gana",  
		'2917' => "Geórgia, República da",     
		'2933' => "Gibraltar Sim",
		'6289' => "Grã-Bretanha",  
		'2976' => "Granada Sim",
		'3018' => "Grécia",    
		'3050' => "Groenlândia",   
		'3093' => "Guadalupe",     
		'3131' => "Guam",  
		'3174' => "Guatemala",     
		'3379' => "Guiana",    
		'3255' => "Guiana Francesa",   
		'3298' => "Guiné",     
		'3344' => "Guiné-Bissau",  
		'3310' => "Guiné-Equatorial",  
		'3417' => "Haiti",     
		'5738' => "Holanda (Países Baixos)", 
		'3450' => "Honduras",  
		'3514' => "Hong Kong, Região Adm. Especial", 
		'3557' => "Hungria, República da",     
		'3573' => "Iêmen",     
		'3611' => "Índia",     
		'3654' => "Indonésia",     
		'6289' => "Inglaterra",    
		'3727' => "Irã, República Islâmica do",    
		'3697' => "Iraque",    
		'3751' => "Irlanda",   
		'6289' => "Irlanda do Norte",  
		'3794' => "Islândia",  
		'3832' => "Israel",    
		'3867' => "Itália",    
		'3883' => "Iugoslávia, República Fed. da",
		'3913' => "Jamaica",   
		'3999' => "Japão",     
		'3964' => "Johnston, Ilhas", 
		'4030' => "Jordânia",  
		'4111' => "Kiribati",  
		'4200' => "Laos, Rep. Pop. Democrática do",    
		'4235' => "Lebuan Sim",
		'4260' => "Lesoto",    
		'4278' => "Letônia, República da",     
		'4316' => "Líbano",    
		'4340' => "Libéria Sim",
		'4383' => "Líbia", 
		'4405' => "Liechtenstein Sim",
		'4421' => "Lituânia, República da",    
		'4456' => "Luxemburgo",    
		'4472' => "Macau",     
		'4499' => "Macedônia",     
		'4502' => "Madagascar",    
		'4525' => "Madeira, Ilha da Sim",
		'4553' => "Malásia", 
		'4588' => "Malavi",    
		'4618' => "Maldivas",  
		'4642' => "Máli",  
		'4677' => "Malta Sim",
		'3595' => "Man, Ilhas Sim",
		'4723' => "Marianas do Norte",     
		'4740' => "Marrocos",  
		'4766' => "Marshall, Ilhas Sim",
		'4774' => "Martinica",     
		'4855' => "Maurício Sim",
		'4880' => "Mauritânia",    
		'4936' => "México",    
		'0930' => "Mianmar (Birmânia)",    
		'4995' => "Micronésia",    
		'4901' => "Midway, Ilhas",     
		'5053' => "Moçambique",    
		'4944' => "Moldávia, República da",    
		'4952' => "Mônaco Sim",
		'4979' => "Mongólia",  
		'5010' => "Montserrat, Ilhas Sim",
		'5070' => "Namíbia",   
		'5088' => "Nauru Sim",
		'5177' => "Nepal",     
		'5215' => "Nicarágua",     
		'5258' => "Niger",     
		'5282' => "Nigéria",   
		'5312' => "Niue, Ilha Sim",
		'5355' => "Norfolk, Ilha",     
		'5380' => "Noruega",   
		'5428' => "Nova Caledônia",    
		'5487' => "Nova Zelândia",     
		'5568' => "Omã",   
		'5738' => "Países Baixos (Holanda)",   
		'5754' => "Palau",     
		'5800' => "Panamá Sim",
		'5452' => "Papua Nova Guiné",  
		'5762' => "Paquistão",     
		'5860' => "Paraguai",  
		'5894' => "Peru",  
		'5932' => "Pitcairn, Ilha",    
		'5991' => "Polinésia Francesa",    
		'6033' => "Polônia, República da",     
		'6114' => "Porto Rico",    
		'6076' => "Portugal",  
		'6238' => "Quênia",    
		'6254' => "Quirguiz, República",   
		'6289' => "Reino Unido",   
		'6408' => "República Centro-Africana",     
		'6475' => "República Dominicana",  
		'6602' => "Reunião, Ilha",     
		'6700' => "Romênia",   
		'6750' => "Ruanda",    
		'6769' => "Rússia",    
		'6858' => "Saara Ocidental",   
		'6777' => "Salomão, Ilhas",    
		'6904' => "Samoa Sim",
		'6912' => "Samoa Americana",   
		'6971' => "San Marino Sim",
		'7102' => "Santa Helena",  
		'7153' => "Santa Lúcia Sim",
		'6955' => "São Cristóvão e Neves Sim",
		'7005' => "São Pedro e Miquelon",  
		'7200' => "São Tomé e Príncipe, Ilhas",    
		'7056' => "São Vicente e Granadinas Sim",
		'7285' => "Senegal",   
		'7358' => "Serra Leoa",    
		'7315' => "Seychelles Sim",
		'7447' => "Síria, República Árabe da",     
		'7480' => "Somália",   
		'7501' => "Sri Lanka",     
		'7544' => "Suazilândia",   
		'7595' => "Sudão",     
		'7641' => "Suécia",    
		'7676' => "Suíça",     
		'7706' => "Suriname",  
		'7722' => "Tadjiquistão",  
		'7765' => "Tailândia",     
		'7803' => "Tanzânia, República Unida da",  
		'7919' => "Tcheca, República",     
		'7820' => "Território Britânico Oc. Índico ", 
		'7951' => "Timor Leste",     
		'8001' => "Togo",  
		'8109' => "Tonga Sim",
		'8052' => "Toquelau, Ilhas",   
		'8150' => "Trinidad e Tobago",     
		'8206' => "Tunísia",   
		'8230' => "Turcas e Caicos, Ilhas Sim",
		'8249' => "Turcomenistão, República do",   
		'8273' => "Turquia",   
		'8281' => "Tuvalu",    
		'8311' => "Ucrânia",   
		'8338' => "Uganda",    
		'8451' => "Uruguai",   
		'8478' => "Uzbequistão, República do",     
		'5517' => "Vanuatu Sim",
		'8486' => "Vaticano, Estado da Cidade do",     
		'8508' => "Venezuela",     
		'8583' => "Vietnã",    
		'8630' => "Virgens, Ilhas (Britânicas) Sim",
		'8664' => "Virgens, Ilhas (E.U.A.) Sim",
		'8737' => "Wake, Ilha",    
		'8753' => "Wallis e Futuna, Ilhas",    
		'8907' => "Zâmbia",    
		'6653' => "Zimbábue",  
		'8958' => "Zona do Canal do Panamá"  
	];
}

function __getUltimoNumeroSequencial($empresa_id, $tabela){
	$config = ConfigGeral::where('empresa_id', $empresa_id)->first();
	$ultimo = DB::table($tabela)->where('empresa_id', $empresa_id)
	->orderBy('numero_sequencial', 'desc')
	->where('numero_sequencial', '>', 0)->first();

	$numero = $ultimo != null ? $ultimo->numero_sequencial : 0;
	if($config == null){
		return $numero;
	}
	if($tabela == 'produtos'){
		return $config->ultimo_codigo_produto > $numero ? $config->ultimo_codigo_produto : $numero;
	}

	if($tabela == 'clientes'){
		return $config->ultimo_codigo_cliente > $numero ? $config->ultimo_codigo_cliente : $numero;
	}

	if($tabela == 'fornecedors'){
		return $config->ultimo_codigo_fornecedor > $numero ? $config->ultimo_codigo_fornecedor : $numero;
	}
}

function __setUltimoNumeroSequencial($empresa_id, $tabela, $numero){
	$config = ConfigGeral::where('empresa_id', $empresa_id)->first();

	if($config != null){
		if($tabela == 'produtos'){
			$config->ultimo_codigo_produto = (int)$numero;
		}

		if($tabela == 'clientes'){
			$config->ultimo_codigo_cliente = (int)$numero;
		}

		if($tabela == 'fornecedors'){
			$config->ultimo_codigo_fornecedor = (int)$numero;
		}

		$config->save();
	}
}
