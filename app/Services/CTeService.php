<?php

namespace App\Services;

use NFePHP\CTe\MakeCTe;
use NFePHP\CTe\Tools;
use NFePHP\CTe\Complements;
use NFePHP\CTe\Common\Standardize;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;
use App\Models\ConfigNota;
use App\Models\Cte;
use App\Models\Empresa;
use App\Models\Certificado;
use App\Models\ConfiguracaoSuper;

error_reporting(E_ALL);
ini_set('display_errors', 'On');
class CTeService{

	private $config; 
	private $tools;
	protected $empresa_id = null;
	protected $timeout = 8;

	public function __construct($config, $emitente){
		
		$this->empresa_id = $emitente->empresa_id;
		$this->config = $config;
		$this->tools = new Tools(json_encode($config), Certificate::readPfx($emitente->arquivo, $emitente->senha));
		$this->tools->model(57);

		$config = ConfiguracaoSuper::first();
		if($config){
			if($config->timeout_cte){
				$this->timeout = $config->timeout_cte;
			}
		}
	}

	public function gerarCTe($cteEmit){

		$emitente = $cteEmit->empresa;
		$cte = new MakeCTe();
		$dhEmi = date("Y-m-d\TH:i:sP");

		$cnpj = preg_replace('/[^0-9]/', '', $emitente->cpf_cnpj);
		$numeroCTE = $cteEmit->numero;

		$chave = $this->montaChave(
			Empresa::getCodUF($emitente->cidade->uf), date('y', strtotime($dhEmi)), date('m', strtotime($dhEmi)), $cnpj, $this->tools->model(), '1', $numeroCTE, '1', '10'
		);
		$infCte = new \stdClass();
		$infCte->Id = "";
		$infCte->versao = "4.00";
		$cte->taginfCTe($infCte);

		$cDV = substr($chave, -1);      
		$ide = new \stdClass();

		$ide->cUF = Empresa::getCodUF($emitente->cidade->uf); 
		$ide->cCT = rand(11111111, 99999999); 
		$ide->CFOP = $cteEmit->cfop;
		$ide->natOp = $cteEmit->natureza->descricao;
		$ide->mod = '57'; 
		$ide->serie = $cteEmit->numero_serie; 
		$nCte = $ide->nCT = $numeroCTE; 
		$ide->dhEmi = $dhEmi; 
		$ide->tpImp = '1'; 
		$ide->tpEmis = '1'; 
		$ide->cDV = $cDV; 
		$ide->tpAmb = (int)$emitente->ambiente; 
		$ide->tpCTe = $cteEmit->tipo_cte; 

		// 0- CT-e Normal; 1 - CT-e de Complemento de Valores;
// 2 -CT-e de Anulação; 3 - CT-e Substituto

		$ide->procEmi = '0'; 
		$ide->verProc = '4.0'; 
		$ide->indGlobalizado = $cteEmit->globalizado == 1 ? '1' : '';

		$ide->cMunEnv = $cteEmit->municipioEnvio->codigo; 
		$ide->xMunEnv = strtoupper($cteEmit->municipioEnvio->nome); 
		$ide->UFEnv = $cteEmit->municipioEnvio->uf; 
		$ide->modal = $cteEmit->modal; 
		$ide->tpServ = $cteEmit->tipo_servico; 

		$ide->cMunIni = $cteEmit->remetente->cidade->codigo; 
		$ide->xMunIni = strtoupper($cteEmit->remetente->cidade->nome); 
		$ide->UFIni = $cteEmit->remetente->cidade->uf; 
		$ide->cMunFim = $cteEmit->destinatario->cidade->codigo; 
		$ide->xMunFim = strtoupper($cteEmit->destinatario->cidade->nome); 
		$ide->UFFim = $cteEmit->destinatario->cidade->uf; 
		$ide->retira = $cteEmit->retira ? 0 : 1;
		$ide->xDetRetira = $cteEmit->detalhes_retira;

		if($cteEmit->tomador == 0){
			if($cteEmit->remetente->ie != ''){
				$ide->indIEToma = '1';
			}else{
				$ide->indIEToma = '9';
			}
		}else if($cteEmit->tomador == 3){
			if($cteEmit->destinatario->ie != ''){
				$ide->indIEToma = '1';
			}else{
				$ide->indIEToma = '9';
			}
		}else if($cteEmit->tomador == 4){
			$ide->indIEToma = '1';
		}
		
		// $ide->indIEToma = $cteEmit->destinatario;
		$ide->dhCont = ''; 
		$ide->xJust = '';

		$cte->tagide($ide);
// Indica o "papel" do tomador: 0-Remetente; 1-Expedidor; 2-Recebedor; 3-Destinatário

		if($cteEmit->tomador != 4){
			$toma3 = new \stdClass();
			$toma3->toma = $cteEmit->tomador;
			$cte->tagtoma3($toma3);
		}else{
			$toma4 = new \stdClass();
			$toma4->toma = 4;
			$doc = preg_replace('/[^0-9]/', '', $cteEmit->cpf_cnpj_tomador);
			if(strlen($doc) == 14){
				$toma4->CNPJ = $doc; 
			}
			else{
				$toma4->CPF = $doc; 
			}

			$toma4->xNome = $cteEmit->razao_social_tomador;
			$toma4->xFant = $cteEmit->nome_fantasia_tomador;
			$toma4->IE = $cteEmit->ie_tomador;
			$toma4->fone = preg_replace('/[^0-9]/', '', $cteEmit->telefone_tomador);
			$toma4->email = $cteEmit->email_tomador;
			$cte->tagtoma4($toma4);
		}

		$enderToma = new \stdClass();
		$enderToma->xLgr = $cteEmit->logradouro_tomador;
		$enderToma->nro = $cteEmit->numero_tomador; 
		$enderToma->xCpl = ''; 
		$enderToma->xBairro = $cteEmit->bairro_tomador; 
		$enderToma->cMun = $cteEmit->municipioTomador->codigo; 
		$enderToma->xMun = $cteEmit->municipioTomador->nome; 
		$enderToma->CEP =preg_replace('/[^0-9]/', '',  $cteEmit->cep_tomador); 
		$enderToma->UF = $cteEmit->municipioTomador->uf; 
		$enderToma->cPais = '1058'; 
		$enderToma->xPais = 'Brasil';  

		$cte->tagenderToma($enderToma);   

		$emit = new \stdClass();
		
		$emit->CNPJ = $cnpj; 

		$ie = preg_replace('/[^0-9]/', '', $emitente->ie);

		$emit->IE = $ie;
		if($ie == null || strlen($ie) <= 1){
			$emit->IE = 'ISENTO';
		}
		// $emit->IEST = "";
		$emit->xNome = $emitente->nome; 
		$emit->xFant = $emitente->nome_fantasia;
		$emit->CRT = $emitente->tributacao == 'Regime Normal' ? 3 : 1;

		$cte->tagemit($emit); 

		$enderEmit = new \stdClass();
		$enderEmit->xLgr = $emitente->rua; 
		$enderEmit->nro = $emitente->numero; 
		$enderEmit->xCpl = '';
		$enderEmit->xBairro = $emitente->bairro; 
		$enderEmit->cMun = $emitente->cidade->codigo;
		$enderEmit->xMun = $emitente->cidade->nome; 

		$cep = preg_replace('/[^0-9]/', '', $emitente->cep);
		$enderEmit->CEP = $cep; 
		$enderEmit->UF = $emitente->cidade->uf; 
		$cte->tagenderEmit($enderEmit);

		$rem = new \stdClass();

		$cnpjRemente = preg_replace('/[^0-9]/', '', $cteEmit->remetente->cpf_cnpj);
		$ieRemetente = preg_replace('/[^0-9]/', '', $cteEmit->remetente->ie);
		if(strlen($cnpjRemente) == 14){
			$rem->CNPJ = $cnpjRemente; 
		}else{
			$rem->CPF = $cnpjRemente; 
		}

		if(strlen($ieRemetente) > 2){
			$rem->IE = $ieRemetente;
		}

		$rem->xNome = $cteEmit->remetente->razao_social;
		if($cteEmit->remetente->nome_fantasia) $rem->xFant = $cteEmit->remetente->nome_fantasia; 
		$rem->fone = ''; 
		$rem->email = ''; 
		$cte->tagrem($rem);

		$enderReme = new \stdClass();
		$enderReme->xLgr = $cteEmit->remetente->rua; 
		$enderReme->nro = $cteEmit->remetente->numero; 
		$enderReme->xCpl = ''; 
		$enderReme->xBairro = $cteEmit->remetente->bairro; 
		$enderReme->cMun = $cteEmit->remetente->cidade->codigo; 
		$enderReme->xMun = strtoupper($cteEmit->remetente->cidade->nome); 
		$cepRemetente = str_replace("-", "", $cteEmit->remetente->cep);
		$enderReme->CEP = $cepRemetente; 
		$enderReme->UF = $cteEmit->remetente->cidade->uf; 
		$enderReme->cPais = '1058'; 
		$enderReme->xPais = 'Brasil'; 
		$cte->tagenderReme($enderReme);

		$dest = new \stdClass();
		$cnpjDestinatario = preg_replace('/[^0-9]/', '', $cteEmit->destinatario->cpf_cnpj);
		$ieDestinatario = preg_replace('/[^0-9]/', '', $cteEmit->destinatario->ie);

		if(strlen($cnpjDestinatario) == 14){
			$dest->CNPJ = $cnpjDestinatario; 
		}else{
			$dest->CPF = $cnpjDestinatario; 
		}

		if(strlen($ieDestinatario) > 2){
			$dest->IE = $ieDestinatario;
		}
		
		$dest->xNome = $cteEmit->destinatario->razao_social;
		$dest->fone = ''; 
		$dest->ISUF = ''; 
		$dest->email = ''; 
		$cte->tagdest($dest);

		$enderDest = new \stdClass();
		$enderDest->xLgr = $cteEmit->destinatario->rua; 
		$enderDest->nro = $cteEmit->destinatario->numero; 
		$enderDest->xCpl = ''; 
		$enderDest->xBairro = $cteEmit->destinatario->bairro; 
		$enderDest->cMun = $cteEmit->destinatario->cidade->codigo; 
		$enderDest->xMun = strtoupper($cteEmit->destinatario->cidade->nome); 

		$cepDest = str_replace("-", "", $cteEmit->destinatario->cep);
		$enderDest->CEP = $cepDest; 
		$enderDest->UF = $cteEmit->destinatario->cidade->uf; 
		$enderDest->cPais = '1058'; 
		$enderDest->xPais = 'Brasil'; 
		$cte->tagenderDest($enderDest);

		$vPrest = new \stdClass();
		$vPrest->vTPrest = $this->format($cteEmit->valor_transporte); 
		$vPrest->vRec = $this->format($cteEmit->valor_receber);      
		$cte->tagvPrest($vPrest);

		$somaVBC = 0;
		foreach($cteEmit->componentes as $c){
			$comp = new \stdClass();
			$comp->xNome = $c->nome; 
			$comp->vComp = $this->format($c->valor);  
			$cte->tagComp($comp);

			if($cteEmit->perc_icms > 0){
				$somaVBC += $c->valor;
			}
		}

		$icms = new \stdClass();
		$icms->cst = $cteEmit->cst;
		$icms->pRedBC = ''; 
		$icms->vBC = $this->format($somaVBC); 
		$icms->pICMS = $this->format($cteEmit->perc_icms);
		if($somaVBC > 0){ 
			$icms->vICMS = $this->format($somaVBC * ($cteEmit->perc_icms/100)); 
		}else{
			$icms->vICMS = 0;
		}

		$icms->vBCUFFim = 0.00; 
		$icms->pFCPUFFim = 0.00; 
		$icms->pICMSUFFim = 0.00; 
		$icms->pICMSInter = 0.00; 
		$icms->vFCPUFFim = 0.00; 
		
		$icms->vBCSTRet = ''; 
		$icms->vICMSSTRet = ''; 
		$icms->pICMSSTRet = ''; 
		$icms->vCred = ''; 
		$icms->vTotTrib = 0.00; 
		$icms->outraUF = false;    
		$icms->vICMSUFIni = 0;  
		$icms->vICMSUFFim = 0;
		$icms->infAdFisco = '';
		$cte->tagicms($icms);
		
		$infCarga = new \stdClass();
		$infCarga->vCarga = $this->format($cteEmit->valor_carga);
		$infCarga->proPred = $cteEmit->produto_predominante; 
		$infCarga->xOutCat = 0.00; 
		$cte->taginfCarga($infCarga);

		foreach($cteEmit->medidas as $m){
			$infQ = new \stdClass();
			$infQ->cUnid = $m->cod_unidade; 
			$infQ->tpMed = $m->tipo_medida; 
			$infQ->qCarga = $m->quantidade;  
			$cte->taginfQ($infQ);
		}

		if(sizeof($cteEmit->chaves_nfe) > 0){

			foreach($cteEmit->chaves_nfe as $ch){
				$infNFe = new \stdClass();
				$infNFe->chave = $ch->chave; 
				$infNFe->PIN = ''; 
				$infNFe->dPrev = $cteEmit->data_previsata_entrega;                                       
				$cte->taginfNFe($infNFe);
			}
		}else{

			$infOut = new \stdClass();

			$infOut->tpDoc = $cteEmit->tpDoc;     
			$infOut->descOutros = $cteEmit->descOutros;     
			$infOut->nDoc = $cteEmit->nDoc;     
			$infOut->dEmi = date('Y-m-d');     
			$infOut->vDocFisc = $this->format($cteEmit->vDocFisc);     
			$infOut->dPrev = $cteEmit->data_previsata_entrega;     
			$cte->taginfOutros($infOut);

		}

		$infModal = new \stdClass();
		$infModal->versaoModal = '4.00';
		$cte->taginfModal($infModal);
		if(strlen($cteEmit->referencia_cte) == 44 && $cteEmit->emitente_anterior){

			$cte->tagdocAnt();
			$emiDocAn = new \stdClass();
			$doc = preg_replace('/[^0-9]/', '', $cteEmit->doc_anterior);
			$emiDocAn->CNPJ = $doc;
			$emiDocAn->xNome = $cteEmit->emitente_anterior; 
			$emiDocAn->UF = $cteEmit->uf_anterior;
			$emiDocAn->IE = $cteEmit->ie_anterior; 
			$cte->tagemiDocAnt($emiDocAn);

			$cte->tagidDocAnt();
			$idDocAntPap = new \stdClass();
			$idDocAntPap->tpDoc = $cteEmit->tp_doc_anterior;
			$idDocAntPap->serie = $cteEmit->serie_anterior;
			$idDocAntPap->nDoc = $cteEmit->n_doc_anterior;
			$idDocAntPap->dEmi = $cteEmit->data_emissao_anterior;
			$cte->tagidDocAntPap($idDocAntPap);

			$idDocAntEle = new \stdClass();
			$idDocAntEle->chCTe = $cteEmit->referencia_cte;
			$cte->tagidDocAntEle($idDocAntEle);

		}

		if(strlen($cteEmit->referencia_cte) == 44 && $ide->tpCTe == 1){
			$infCTeComp = new \stdClass();
			$infCTeComp->chCTe = $cteEmit->referencia_cte;
			$taginfCTeComp = $cte->taginfCTeComp($infCTeComp);
		}

		// dd($taginfCTeComp);
		if($ide->tpCTe == 0){
			$cte->taginfCTeNorm();
		}

		$rodo = new \stdClass();
		if($cteEmit->veiculo->rntrc != ""){
			$rodo->RNTRC = $cteEmit->veiculo->rntrc;
		}else{
			$rodo->RNTRC = "ISENTO";
		}
		$cte->tagrodo($rodo);

		$aereo = new \stdClass();
		$aereo->nMinu = '123'; 
		$aereo->nOCA = '';
 // Número Operacional do Conhecimento Aéreo
		$aereo->dPrevAereo = date('Y-m-d');
		$aereo->natCarga_xDime = ''; 
		$aereo->natCarga_cInfManu = [  ]; 
		$aereo->tarifa_CL = 'G';
		$aereo->tarifa_cTar = ''; 
		$aereo->tarifa_vTar = 100.00; 
		$cte->tagaereo($aereo);

// 		$autXML = new \stdClass();
// 		// $cnpj = str_replace(".", "", $config->cnpj);
// 		// $cnpj = str_replace("/", "", $cnpj);
// 		// $cnpj = str_replace("-", "", $cnpj);
// 		// $cnpj = str_replace(" ", "", $cnpj);
// 		$autXML->CNPJ = '08543628000145'; 
// // CPF ou CNPJ dos autorizados para download do XML
// 		$cte->tagautXML($autXML);


		try{
			$cte->montaCTe();
			$chave = $cte->chCTe;
			$xml = $cte->getXML();
			$arr = [
				'chave' => $chave,
				'xml' => $xml,
				'nCte' => $nCte
			];
			return $arr;
		}catch(\Exception $e){
			return [
				'erros_xml' => $cte->getErrors()
			];
		}
	}

	public function sign($xml){
		return $this->tools->signCTe($xml);
	}

	public function transmitir($signXml, $chave){
		try{
			$idLote = substr(str_replace(',', '', number_format(microtime(true) * 1000000, 0)), 0, 15);
			$resp = $this->tools->sefazEnviaCTe($signXml);
			$st = new Standardize($resp);
			// sleep(4);
			sleep($this->timeout);

			$std = $st->toStd();

			if ($std->cStat != 100) {
				return [
					'erro' => 1,
					'error' => "[$std->cStat] - $std->xMotivo"
				];
			}
			$recibo = $std->protCTe->infProt->nProt;

			try {
				$xml = Complements::toAuthorize($signXml, $resp);

				file_put_contents(public_path('xml_cte/').$chave.'.xml', $xml);
				return [
					'erro' => 0,
					'success' => $recibo
				];
			} catch (\Exception $e) {
				return [
					'erro' => 1,
					'error' => "algo deu errado"
				];
			}

		} catch(\Exception $e){
			return [
				'erro' => 1,
				'error' => $e->getMessage()
			];
		}

	}	

	private function format($number, $dec = 2){
		return number_format((float) $number, $dec, ".", "");
	}

	private function montaChave($cUF, $ano, $mes, $cnpj, $mod, $serie, 
		$numero, $tpEmis, $codigo = ''){
		if ($codigo == '') {
			$codigo = $numero;
		}
		$forma = "%02d%02d%02d%s%02d%03d%09d%01d%08d";
		$chave = sprintf(
			$forma, $cUF, $ano, $mes, $cnpj, $mod, $serie, $numero, $tpEmis, $codigo
		);
		return $chave . $this->calculaDV($chave);
	}

	private function calculaDV($chave43){
		$multiplicadores = array(2, 3, 4, 5, 6, 7, 8, 9);
		$iCount = 42;
		$somaPonderada = 0;
		while ($iCount >= 0) {
			for ($mCount = 0; $mCount < count($multiplicadores) && $iCount >= 0; $mCount++) {
				$num = (int) substr($chave43, $iCount, 1);
				$peso = (int) $multiplicadores[$mCount];
				$somaPonderada += $num * $peso;
				$iCount--;
			}
		}
		$resto = $somaPonderada % 11;
		if ($resto == '0' || $resto == '1') {
			$cDV = 0;
		} else {
			$cDV = 11 - $resto;
		}
		return (string) $cDV;
	}


	public function cancelar($cte, $justificativa){

		try {

			$chave = $cte->chave;
			$response = $this->tools->sefazConsultaChave($chave);
			$stdCl = new Standardize($response);
			$arr = $stdCl->toArray();
			$js = $stdCl->toJson();
			sleep(3);
			$xJust = $justificativa;

			if(!isset($arr['protCTe'])){
				return [
					'erro' => 1,
					'mensagem' => $arr['xMotivo']
				];
			}

			$nProt = $arr['protCTe']['infProt']['nProt'];
			$response = $this->tools->sefazCancela($chave, $xJust, $nProt);

			$stdCl = new Standardize($response);
			$std = $stdCl->toStd();
			$arr = $stdCl->toArray();
			$json = $stdCl->toJson();
			// return $json;
			$cStat = $std->infEvento->cStat;

			if ($cStat == '101' || $cStat == '135' || $cStat == '155') {
				$xml = Complements::toAuthorize($this->tools->lastRequest, $response);
				// header('Content-type: text/xml; charset=UTF-8');
				file_put_contents(public_path('xml_cte_cancelada/').$chave.'.xml',$xml);
				return $arr;
			}else{
				return ['erro' => true, 'data' => $arr, 'status' => 402];	

			}

		} catch (\Exception $e) {

			return ['erro' => true, 'data' => $e->getMessage(), 'status' => 402];	

		}
	}

	public function consultar($cte){
		try {

			$chave = $cte->chave;
			$response = $this->tools->sefazConsultaChave($chave);

			// return $response;
			$stdCl = new Standardize($response);
			$arr = $stdCl->toArray();

			return $arr;

		} catch (\Exception $e) {
			echo $e->getMessage();
		}
	}

	public function inutilizar($nInicio, $nFinal, $justificativa, $config){
		try{

			$nSerie = $config->numero_serie_cte;
			$nIni = $nInicio;
			$nFin = $nFinal;
			$xJust = $justificativa;
			$tpAmb = 2;
			$response = $this->tools->sefazInutiliza($nSerie, $nIni, $nFin, $xJust, $tpAmb);

			$stdCl = new Standardize($response);

			$std = $stdCl->toStd();

			$arr = $stdCl->toArray();

			$json = $stdCl->toJson();

			return $arr;

		} catch (\Exception $e) {
			echo $e->getMessage();
		}
	}

	public function cartaCorrecao($cte, $grupo, $campo, $valor){
		try {

			$chave = $cte->chave;

			$nSeqEvento = $cte->sequencia_cce+1;
			$infCorrecao[] = [
				'grupoAlterado' => $grupo,
				'campoAlterado' => $campo,
				'valorAlterado' => $valor,
				'nroItemAlterado' => '01'
			];
			$response = $this->tools->sefazCCe($chave, $infCorrecao, $nSeqEvento);
			sleep(3);

			$stdCl = new Standardize($response);
			$std = $stdCl->toStd();
			$arr = $stdCl->toArray();
			$json = $stdCl->toJson();
			$cStat = $std->infEvento->cStat;

			if ($cStat == '101' || $cStat == '135' || $cStat == '155') {
				$xml = Complements::toAuthorize($this->tools->lastRequest, $response);
				file_put_contents(public_path('xml_cte_correcao/').$chave.'.xml', $xml);
				$cte->sequencia_cce = $cte->sequencia_cce + 1;
				$cte->save();
				return $arr;
			}else{
				return ['erro' => true, 'data' => $arr, 'status' => 402];
			}

		} catch (\Exception $e) {
			return ['erro' => true, 'data' => $e->getMessage(), 'status' => 404];
		}
	}

	public function getXml($chave){
		// $resp = file_get_contents('ctes.xml');
		try{
			$resp = $this->tools->sefazDistDFe(0,0);
			// file_put_contents("ctes.xml", $resp);

			$dom = new \DOMDocument();
			$dom->loadXML($resp);
			$xMotivo = $dom->getElementsByTagName('xMotivo')->item(0)->nodeValue;

			if($xMotivo == 'Rejeicao: Consumo indevido'){
				echo $xMotivo;
				die;
			}

			$arrayDocs = [];
			$dom = new \DOMDocument();
			$dom->loadXML($resp);
			$node = $dom->getElementsByTagName('retDistDFeInt')->item(0);
			$lote = $node->getElementsByTagName('loteDistDFeInt')->item(0);

			$docs = $lote->getElementsByTagName('docZip');
			foreach ($docs as $doc) {
				$content = gzdecode(base64_decode($doc->nodeValue));
				$xml = simplexml_load_string($content);

				$temp = $xml->CTe->infCte;

				if(isset($temp->emit)){

					$chaveTemp = substr((string)$temp['Id'], 3, strlen((string)$temp['Id']));
					if($chaveTemp == $chave){
						return $content;
					}
				}
			}
		}catch(\Exception $e){
			echo "Erro: " . $e->getMessage();
		}
	}

	public function download($chave){
		try {

			$this->tools->setEnvironment(1);
			$chave = $chave;
			$response = $this->tools->sefazDownload($chave);
			return $response;

		} catch (\Exception $e) {
			echo str_replace("\n", "<br/>", $e->getMessage());
		}
	}

	public function consultaDocumentos(){
		$resp = $this->tools->sefazDistDFe(0,0);
		// file_put_contents("ctes.xml", $resp);
		// $resp = file_get_contents('ctes.xml');
		$dom = new \DOMDocument();
		$dom->loadXML($resp);
		$xMotivo = $dom->getElementsByTagName('xMotivo')->item(0)->nodeValue;

		if($xMotivo == 'Rejeicao: Consumo indevido'){
			echo $xMotivo;
			die;
		}

		$arrayDocs = [];
		$dom = new \DOMDocument();
		$dom->loadXML($resp);
		$node = $dom->getElementsByTagName('retDistDFeInt')->item(0);
		$lote = $node->getElementsByTagName('loteDistDFeInt')->item(0);

		$docs = $lote->getElementsByTagName('docZip');

		foreach ($docs as $doc) {
			$content = gzdecode(base64_decode($doc->nodeValue));
			$xml = simplexml_load_string($content);

			$xml = $xml->CTe->infCte;

			if(isset($xml->emit)){

				$chave = substr((string)$xml['Id'], 3, strlen((string)$xml['Id']));
				$temp = [
					'documento' => (int)$xml->emit->CNPJ,
					'nome' => (string)$xml->emit->xNome,
					'data_emissao' => (string)$xml->ide->dhEmi,
					'valor' => (float)$xml->vPrest->vTPrest,
					'chave' => $chave,
					'tipo' => 0,
					'sequencia_evento' => 0,
					'empresa_id' => $this->empresa_id
				];

			}

			array_push($arrayDocs, $temp);
		}

		return $arrayDocs;
	}

	public function desacordo($chave, $nSeqEvento, $xJust, $uf){
		try {
			$chNFe = $chave;
			$tpEvento = '610110'; 
			$nSeqEvento = $nSeqEvento;


			$response = $this->tools->sefazManifesta($chNFe, $tpEvento, $xJust, $nSeqEvento, 
				$uf);

			$st = new Standardize($response);

			$arr = $st->toArray();

			return $arr;

		} catch (\Exception $e) {
			echo $e->getMessage();
		}
	}
}