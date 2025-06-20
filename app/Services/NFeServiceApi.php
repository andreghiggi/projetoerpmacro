<?php
namespace App\Services;

error_reporting(E_ALL);
ini_set('display_errors', 'On');
use NFePHP\Common\Certificate;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Make;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;
use App\Models\Nfe;
use App\Models\Empresa;
use App\Models\ConfiguracaoSuper;

class NFeServiceApi{

	private $config; 
	private $tools;
	protected $empresa_id = null;

	public function __construct($config, $empresa){
		
		$this->empresa_id = $empresa->id;

		$this->config = $config;
		$this->tools = new Tools(json_encode($config), Certificate::readPfx($empresa->arquivo, $empresa->senha));
		$this->tools->model(55);
		
	}

	public function gerarXml(
		$documento,
		$destinatario,
		$itens,
		$frete,
		$pagamento,
		$fatura,
		$duplicatas,
		$empresa
	){

		$nfe = new Make();
		$stdInNFe = new \stdClass();
		$stdInNFe->versao = '4.00';
		$stdInNFe->Id = null;
		$stdInNFe->pk_nItem = '';

		$infNFe = $nfe->taginfNFe($stdInNFe);

		$stdIde = new \stdClass();

		$stdIde->cUF = Empresa::getCodUF($empresa->cidade->uf); // codigo uf emitente
		$stdIde->cNF = rand(11111, 99999);
		// $stdIde->natOp = $venda->natureza->natureza;
		$stdIde->natOp = $documento['natureza_operacao'];

		$stdIde->mod = 55;
		$stdIde->serie = $documento['numero_serie'];
		$stdIde->nNF = isset($documento['numero_nfe']) ? $documento['numero_nfe'] : $empresa->lastNumeroNFe((int)$documento['ambiente']); // numero sequencial da nfe
		$stdIde->dhEmi = date("Y-m-d\TH:i:sP");
		$stdIde->dhSaiEnt = date("Y-m-d\TH:i:sP");
		$stdIde->tpNF = 1;
		$stdIde->idDest = $empresa->cidade->uf == $destinatario['uf'] ? 1 : 2;
		$stdIde->cMunFG = $empresa->cidade->codigo;
		$stdIde->tpImp = 1;
		$stdIde->tpEmis = 1;
		$stdIde->cDV = 0;
		$stdIde->tpAmb = (int)$documento['ambiente'];
		$stdIde->finNFe = 1;
		$stdIde->indFinal = $documento['consumidor_final'];
		$stdIde->indPres = 1;
		$stdIde->procEmi = '0';
		$stdIde->verProc = '2.0';
		$tagide = $nfe->tagide($stdIde); //fim da tagide

		// inicia tag do emitente
		$stdEmit = new \stdClass();
		$stdEmit->xNome = $empresa->nome;
		$stdEmit->xFant = $empresa->nome_fantasia;
		$stdEmit->CRT = $empresa->tributacao == 'Regime Normal' ? 3 : 1;
		$stdEmit->IE = $empresa->ie;

		$cpf_cnpj = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);
		if (strlen($cpf_cnpj) == 14) {
			$stdEmit->CNPJ = $cpf_cnpj;
		}else{
			$stdEmit->CPF = $cpf_cnpj;
		}
		$emit = $nfe->tagemit($stdEmit);

		$stdEnderEmit = new \stdClass();
		$stdEnderEmit->xLgr = $empresa->rua;
		$stdEnderEmit->nro = $empresa->numero;
		$stdEnderEmit->xCpl = $empresa->complemento;
		$stdEnderEmit->xBairro = $empresa->bairro;
		$stdEnderEmit->cMun = $empresa->cidade->codigo;
		$stdEnderEmit->xMun = $empresa->cidade->nome;
		$stdEnderEmit->UF = $empresa->cidade->uf;
		$stdEnderEmit->CEP = $empresa->cep;
		$stdEnderEmit->cPais = '1058';
		$stdEnderEmit->xPais = 'BRASIL';
		$enderEmit = $nfe->tagenderEmit($stdEnderEmit); // fim tag do emitente

		// inicia tag do destinatario
		$stdDest = new \stdClass();
		$stdDest->xNome = $destinatario['nome'];
		if ($destinatario['contribuinte'] == 1) {
			if ($destinatario['ie'] == 'ISENTO') {
				$stdDest->indIEDest = "2";
			} else {
				$stdDest->indIEDest = "1";
			}
		} else {
			$stdDest->indIEDest = "9";
		}

		$cpf_cnpj = preg_replace('/[^0-9]/', '', $destinatario['cpf_cnpj']);

		if (strlen($cpf_cnpj) == 14) {
			$stdDest->CNPJ = $cpf_cnpj;
			$ie = preg_replace('/[^0-9]/', '', $destinatario['ie']);
			$stdDest->IE = $ie;
		} else {
			$stdDest->CPF = $destinatario['cpf_cnpj'];
		}

		$dest = $nfe->tagdest($stdDest);

		$stdEnderDest = new \stdClass();
		$stdEnderDest->xLgr = $destinatario['rua'];
		$stdEnderDest->nro = $destinatario['numero'];
		$stdEnderDest->xCpl = $destinatario['complemento'];
		$stdEnderDest->xBairro = $destinatario['bairro'];
		$stdEnderDest->cMun = $destinatario['cod_municipio_ibge'];
		$stdEnderDest->xMun = $destinatario['nome_municipio'];
		$stdEnderDest->UF = $destinatario['uf'];
		$stdEnderDest->CEP = $destinatario['cep'];
		$stdEnderDest->cPais = $destinatario['cod_pais'];
		$stdEnderDest->xPais = $destinatario['nome_pais'];

		$enderDest = $nfe->tagenderDest($stdEnderDest);
		//fim tag destinatario endereço

		$somaProdutos = 0;
		$somaICMS = 0;
		$somaFrete = 0;
		$somaIpi = 0;
		$totalItens = sizeof($itens);

		foreach ($itens as $itemCont => $i) {
			$itemCont++;

			$stdProd = new \stdClass(); // tag produto inicio
			$stdProd->item = $itemCont;
			$stdProd->cEAN = $i['cod_barras'];
			$stdProd->cEANTrib = $i['cod_barras'];
			$stdProd->cProd = $i['codigo_produto'];
			$stdProd->xProd = $i['nome_produto'];
			$stdProd->NCM = $i['ncm'];
			$stdProd->CFOP = $i['cfop'];
			$stdProd->uCom = $i['unidade'];
			$stdProd->qCom = $i['quantidade'];
			$stdProd->vUnCom = $this->format($i['valor_unitario']);
			$stdProd->vProd = $this->format(($i['quantidade'] * $i['valor_unitario']));
			$stdProd->uTrib = $i['unidade'];
			$stdProd->qTrib = $i['quantidade'];
			$stdProd->vUnTrib = $this->format($i['valor_unitario']);
			$stdProd->indTot = $i['compoe_valor_total'];
			$somaProdutos += ($i['quantidade'] * $i['valor_unitario']);

			if($frete != null){
				if($frete['valor'] > 0){
					if($itemCont < $totalItens){
						$somaFrete += $vFt = 
						$this->format($frete['valor']/$totalItens, 2);
						$stdProd->vFrete = $this->format($vFt);
					}else{
						$stdProd->vFrete = $this->format(($frete['valor']-$somaFrete), 2);
					}
				}
			}

			$prod = $nfe->tagprod($stdProd); // fim tag de produtos

			$stdImposto = new \stdClass();
			$stdImposto->item = $itemCont;
			$imposto = $nfe->tagimposto($stdImposto); // tag imposto

			//frete

			if ($stdEmit->CRT == 1) { 
				$stdICMS = new \stdClass();

				$stdICMS->item = $itemCont; 
				$stdICMS->orig = 0;
				$stdICMS->CSOSN = $i['cst_csosn'];

				if(isset($i['vBCSTRet'])) $stdICMS->vBCSTRet = $i['vBCSTRet'];
				if(isset($i['pST'])) $stdICMS->pST = $i['pST'];
				if(isset($i['vICMSSTRet'])) $stdICMS->vICMSSTRet = $i['vICMSSTRet'];
				if(isset($i['vICMSSubstituto'])) $stdICMS->vICMSSubstituto = $i['vICMSSubstituto'];
				

				$stdICMS->pCredSN = $this->format($i['perc_icms']);
				$stdICMS->vCredICMSSN = $this->format($i['perc_icms']);
				$ICMS = $nfe->tagICMSSN($stdICMS);

				$somaICMS = 0;
			} else if ($stdEmit->CRT == 3) {
				
				$stdICMS = new \stdClass();
				$stdICMS->item = $itemCont; 
				$stdICMS->orig = 0;
				$stdICMS->CST = $i['cst_csosn'];
				$stdICMS->modBC = 0;
				$stdICMS->vBC = $this->format($i['valor_unitario'] * $i['quantidade']);
				$stdICMS->pICMS = $this->format($i['perc_icms']);
				$stdICMS->vICMS = $stdICMS->vBC * ($stdICMS->pICMS/100);

				if(isset($i['vBCSTRet'])) $stdICMS->vBCSTRet = $i['vBCSTRet'];
				if(isset($i['pST'])) $stdICMS->vBCSTRet = $i['pST'];
				if(isset($i['vICMSSTRet'])) $stdICMS->vBCSTRet = $i['vICMSSTRet'];
				if(isset($i['vICMSSubstituto'])) $stdICMS->vBCSTRet = $i['vICMSSubstituto'];

				$somaICMS += (($i['valor_unitario'] * $i['quantidade']) 
					* ($stdICMS->pICMS/100));
				$ICMS = $nfe->tagICMS($stdICMS);
			} // fim tag icms

			//PIS
			$stdPIS = new \stdClass();
			$stdPIS->item = $itemCont;
			$stdPIS->CST = $i['cst_pis'];
			$stdPIS->vBC = $this->format($i['perc_pis']) > 0 ? $stdProd->vProd : 0.00;
			$stdPIS->pPIS = $this->format($i['perc_pis']);
			$stdPIS->vPIS = $this->format($stdProd->vProd * ($i['perc_pis'] / 100));
			$PIS = $nfe->tagPIS($stdPIS);

			//COFINS
			$stdCOFINS = new \stdClass();
			$stdCOFINS->item = $itemCont;
			$stdCOFINS->CST = $i['cst_cofins'];
			$stdCOFINS->vBC = $this->format($i['cst_cofins']) > 0 ? $stdProd->vProd : 0.00;
			$stdCOFINS->pCOFINS = $this->format($i['perc_cofins']);
			$stdCOFINS->vCOFINS = $this->format($stdProd->vProd * ($i['perc_cofins'] / 100));
			$COFINS = $nfe->tagCOFINS($stdCOFINS);


			$std = new \stdClass(); //IPI
			$std->item = $itemCont;
			$std->clEnq = null;
			$std->CNPJProd = null;
			$std->cSelo = null;
			$std->qSelo = null;
			$std->cEnq = $i['cEnq'];
			$std->CST = $i['cst_ipi'];
			$std->vBC = $this->format($i['perc_ipi']) > 0 ? $stdProd->vProd : 0.00;
			$std->pIPI = $this->format($i['perc_ipi']);
			$somaIpi += $std->vIPI = $stdProd->vProd * $this->format(($i['perc_ipi'] / 100));
			$std->qUnid = null;
			$std->vUnid = null;

			$IPI = $nfe->tagIPI($std);

		}

		$stdICMSTot = new \stdClass();
		$stdICMSTot->vBC = $stdEmit->CRT == 3 ? $this->format($somaProdutos) : 0.00;
		$stdICMSTot->vICMS = $this->format($somaICMS);
		$stdICMSTot->vICMSDeson = 0.00;
		$stdICMSTot->vBCST = 0.00;
		$stdICMSTot->vST = 0.00;
		$stdICMSTot->vProd = 0;
		$stdICMSTot->vFrete = $frete != null ? $frete['valor'] : 0;
		$stdICMSTot->vSeg = 0.00;
		$stdICMSTot->vDesc = 0.00;
		$stdICMSTot->vII = 0.00;
		$stdICMSTot->vIPI = 0.00;
		$stdICMSTot->vPIS = 0.00;
		$stdICMSTot->vCOFINS = 0.00;
		$stdICMSTot->vOutro = 0.00;
		$stdICMSTot->vNF = $this->format($fatura['total_nf'] + $fatura['desconto']);
		$stdICMSTot->vTotTrib = 0.00;
		$ICMSTot = $nfe->tagICMSTot($stdICMSTot);

		$stdTransp = new \stdClass();
		$stdTransp->modFrete = $frete != null ? $frete['modelo'] : 9;

		$transp = $nfe->tagtransp($stdTransp);

		if ($frete != null) {
			$stdVol = new \stdClass();
			$stdVol->item = 1;
			$stdVol->qVol = $frete['quantidade_volumes'];
			$stdVol->esp = $frete['especie'];

			$stdVol->nVol = $frete['numero_volumes'];
			$stdVol->pesoL = $frete['peso_liquido'];
			$stdVol->pesoB = $frete['peso_bruto'];
			$vol = $nfe->tagvol($stdVol);
		}

		$respTec = ConfiguracaoSuper::first();
		if($respTec != null){
			$stdResp = new \stdClass();
			$stdResp->CNPJ = preg_replace('/[^0-9]/', '', $respTec->cpf_cnpj);
			$stdResp->xContato = $respTec->name;
			$stdResp->email = $respTec->email;
			$stdResp->fone = preg_replace('/[^0-9]/', '', $respTec->telefone);
			$nfe->taginfRespTec($stdResp);
		}

		//Fatura
		$stdFat = new \stdClass();
		$stdFat->nFat = $stdIde->nNF;
		$stdFat->vOrig = $this->format($fatura['total_nf']);
		$stdFat->vDesc = $this->format($fatura['desconto']);
		$stdFat->vLiq = $this->format($fatura['total_nf'] - $fatura['desconto']);

		$fatura = $nfe->tagfat($stdFat);

		$contFatura = 1;
		foreach ($duplicatas as $ft) {
			$stdDup = new \stdClass();
			$stdDup->nDup = "00" . $contFatura;
			$stdDup->dVenc = substr($ft['data_vencimento'], 0, 10);
			$stdDup->vDup = $this->format($ft['valor']);

			$nfe->tagdup($stdDup);
			$contFatura++;
		}

		$stdPag = new \stdClass();
		$pag = $nfe->tagpag($stdPag);

		$stdDetPag = new \stdClass();
		$stdDetPag->tPag = $pagamento['tipo'];
		$stdDetPag->vPag = $this->format($somaProdutos);
		$stdDetPag->indPag = 1;
		$detPag = $nfe->tagdetPag($stdDetPag);

		$stdInfoAdic = new \stdClass();
		$stdInfoAdic->infCpl = $documento['info_complementar'];
		$infoAdic = $nfe->taginfAdic($stdInfoAdic);

		try{
			$nfe->montaNFe();
			$arr = [
				'chave' => $nfe->getChave(),
				'xml' => $nfe->getXML(),
				'numero' => $stdIde->nNF
			];
			return $arr;
		}catch(\Exception $e){
			return [
				'erros_xml' => $nfe->getErrors()
			];
		}

	}

	public function format($number, $dec = 2)
	{
		return number_format((float) $number, $dec, ".", "");
	}

	public function sign($xml){
		return $this->tools->signNFe($xml);
	}

	public function transmitir($signXml, $chave){
		try{
			$idLote = str_pad(100, 15, '0', STR_PAD_LEFT);
			$resp = $this->tools->sefazEnviaLote([$signXml], $idLote);

			$st = new Standardize();
			$std = $st->toStd($resp);
			sleep(2);
			if ($std->cStat != 103) {

				// return "Erro: [$std->cStat] - $std->xMotivo";
				return [
					'erro' => 1,
					'error' => "[$std->cStat] - $std->xMotivo"
				];
			}
			$recibo = $std->infRec->nRec; 
			
			$protocolo = $this->tools->sefazConsultaRecibo($recibo);
			sleep(4);
			try {
				$xml = Complements::toAuthorize($signXml, $protocolo);
				file_put_contents(public_path('xml_nfe/').$chave.'.xml',$xml);
				return [
					'erro' => 0,
					'success' => $recibo
				];
				// $this->printDanfe($xml);
			} catch (\Exception $e) {
				return [
					'erro' => 1,
					'error' => $st->toArray($protocolo)
				];
			}

		} catch(\Exception $e){
			return [
				'erro' => 1,
				'error' => $e->getMessage()
			];
		}
	}

	public function consultar($nfe){
		try {
			
			$this->tools->model('55');

			$chave = $nfe->chave;
			$response = $this->tools->sefazConsultaChave($chave);

			$stdCl = new Standardize($response);
			$arr = $stdCl->toArray();

			return $arr;

		} catch (\Exception $e) {
			return ['erro' => true, 'data' => $e->getMessage(), 'status' => 402];	
		}
	}

	public function correcao($nfe, $correcao){
		try {

			$chave = $nfe->chave;
			$xCorrecao = $correcao;
			$nSeqEvento = $nfe->sequencia_cce+1;
			$response = $this->tools->sefazCCe($chave, $xCorrecao, $nSeqEvento);
			sleep(2);

			$stdCl = new Standardize($response);

			$std = $stdCl->toStd();

			$arr = $stdCl->toArray();

			$json = $stdCl->toJson();

			if ($std->cStat != 128) {
        //TRATAR
			} else {
				$cStat = $std->retEvento->infEvento->cStat;
				if ($cStat == '135' || $cStat == '136') {
					$xml = Complements::toAuthorize($this->tools->lastRequest, $response);
					file_put_contents(public_path('xml_nfe_correcao/').$chave.'.xml',$xml);

					$nfe->sequencia_cce = $nfe->sequencia_cce + 1;
					$nfe->save();
					return $arr;

				} else {
            //houve alguma falha no evento 
					return ['erro' => true, 'data' => $arr, 'status' => 402];
				}
			}    
		} catch (\Exception $e) {
			return ['erro' => true, 'data' => $e->getMessage(), 'status' => 404];
		}
	}

	public function cancelar($nfe, $motivo){
		try {
			
			$chave = $nfe->chave;
			$response = $this->tools->sefazConsultaChave($chave);
			$stdCl = new Standardize($response);
			$arr = $stdCl->toArray();
			sleep(1);

			$nProt = $arr['protNFe']['infProt']['nProt'];

			$response = $this->tools->sefazCancela($chave, $motivo, $nProt);
			sleep(2);
			$stdCl = new Standardize($response);
			$std = $stdCl->toStd();
			$arr = $stdCl->toArray();
			$json = $stdCl->toJson();

			if ($std->cStat != 128) {
        //TRATAR
			} else {
				$cStat = $std->retEvento->infEvento->cStat;
				if ($cStat == '101' || $cStat == '135' || $cStat == '155' ) {
					$xml = Complements::toAuthorize($this->tools->lastRequest, $response);
					file_put_contents(public_path('xml_nfe_cancelada/').$chave.'.xml',$xml);

					return $arr;
				} else {
					
					return ['erro' => true, 'data' => $arr, 'status' => 402];	
				}
			}    
		} catch (\Exception $e) {
			// echo $e->getMessage();
			return ['erro' => true, 'data' => $e->getMessage(), 'status' => 402];	
    //TRATAR
		}
	}

}