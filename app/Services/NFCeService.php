<?php

namespace App\Services;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

use NFePHP\Common\Certificate;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Make;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;
use App\Models\Nfce;
use App\Models\Empresa;
use App\Models\ConfiguracaoSuper;
use App\Models\Ibpt;
use App\Models\Contigencia;
use App\Models\ConfigGeral;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\NFe\Factories\Contingency;
use NFePHP\NFe\MakeDev;

class NFCeService
{

	private $config;
	private $tools;
	protected $empresa_id = null;

	protected $timeout = 8;

	public function __construct($config, $empresa)
	{

		$this->empresa_id = $empresa->id;

		$this->config = $config;
		$this->tools = new Tools(json_encode($config), Certificate::readPfx($empresa->arquivo, $empresa->senha));
		$this->tools->model(65);

		$config = ConfiguracaoSuper::first();
		if($config){
			if($config->timeout_nfce){
				$this->timeout = $config->timeout_nfce;
			}
		}

		$soapCurl = new SoapCurl();
		$soapCurl->httpVersion('1.1');
		$this->tools->loadSoapClass($soapCurl);

		$contigencia = $this->getContigencia();
		if($contigencia != null){
			$contingency = new Contingency($contigencia->status_retorno);
			$this->tools->contingency = $contingency;
		}
	}

	private function getContigencia(){
		$active = Contigencia::
		where('empresa_id', $this->empresa_id)
		->where('status', 1)
		->where('documento', 'NFCe')
		->first();
		return $active;
	}

	public function gerarXml($item)
	{

		// $nfe = new MakeDev();
		$schema = 'PL_010_V1';
		$nfe = new MakeDev($schema);
		$stdInNFe = new \stdClass();
		$stdInNFe->versao = '4.00';
		$stdInNFe->Id = null;
		$stdInNFe->pk_nItem = '';

		$infNFe = $nfe->taginfNFe($stdInNFe);
		$emitente = $item->empresa;
		$emitente = __objetoParaEmissao($emitente, $item->local_id);
		$configGeral = ConfigGeral::where('empresa_id', $item->empresa_id)->first();
		
		$cliente = $item->cliente;

		if($item->pedido){
			$item->total = $item->pedido->somaProdutos();
		}

		$stdIde = new \stdClass();
		$stdIde->cUF = Empresa::getCodUF($emitente->cidade->uf); // codigo uf emitente
		$stdIde->cNF = rand(11111, 99999);
		// $stdIde->natOp = $venda->natureza->natureza;
		$stdIde->natOp = $item->natureza->descricao;

		$stdIde->mod = 65;
		$stdIde->serie = $item->numero_serie;

		// $stdIde->nNF = $item->lastNumero(); // numero sequencial da nfce
		$stdIde->nNF = $item->numero; // numero sequencial da nfe

		// $stdIde->dhEmi = \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') . date('\T') . 
		// 	\Carbon\Carbon::parse($item->created_at)->format('H:i') .":" . date('sP');
		// $stdIde->dhSaiEnt = \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') . date('\T') . 
		// 	\Carbon\Carbon::parse($item->created_at)->format('H:i') .":" . date('sP');

		$stdIde->dhEmi = date("Y-m-d\TH:i:sP");
		$stdIde->dhSaiEnt = date("Y-m-d\TH:i:sP");
		$stdIde->tpNF = 1;
		$stdIde->idDest = 1;
		$stdIde->cMunFG = $emitente->cidade->codigo;
		$stdIde->tpImp = 4;

		$contigencia = $this->getContigencia();
		$stdIde->tpEmis = $contigencia != null ? 9 : 1;
		$stdIde->cDV = 0;
		$stdIde->tpAmb = (int)$emitente->ambiente;
		$stdIde->finNFe = 1;
		$stdIde->indFinal = 1;
		$stdIde->indPres = 1;
		$stdIde->procEmi = '0';
		$stdIde->verProc = '2.0';

		if($contigencia){
			$stdIde->dhCont = date("Y-m-d\TH:i:sP");
			$stdIde->xJust = $contigencia->motivo;

		}
		$tagide = $nfe->tagide($stdIde); //fim da tagide

		// inicia tag do emitente
		$stdEmit = new \stdClass();
		$stdEmit->xNome = $emitente->nome;
		$stdEmit->xFant = $emitente->nome_fantasia;
		$stdEmit->CRT = $emitente->tributacao == 'Regime Normal' ? 3 : 1;
		if($emitente->tributacao == 'Simples Nacional, excesso sublimite de receita bruta'){
			$stdEmit->CRT = 2;	
		}
		$stdEmit->IE = preg_replace('/[^0-9]/', '', $emitente->ie);

		$cpf_cnpj = preg_replace('/[^0-9]/', '', $emitente->cpf_cnpj);

		if (strlen($cpf_cnpj) == 14) {
			$stdEmit->CNPJ = $cpf_cnpj;
		} else {
			$stdEmit->CPF = $cpf_cnpj;
		}
		$emit = $nfe->tagemit($stdEmit);


		$stdEnderEmit = new \stdClass();
		$stdEnderEmit->xLgr = $emitente->rua;
		$stdEnderEmit->nro = $emitente->numero;
		$stdEnderEmit->xCpl = $emitente->complemento;
		$stdEnderEmit->xBairro = $emitente->bairro;
		$stdEnderEmit->cMun = $emitente->cidade->codigo;
		$stdEnderEmit->xMun = $emitente->cidade->nome;
		$stdEnderEmit->UF = $emitente->cidade->uf;
		$stdEnderEmit->CEP = preg_replace('/[^0-9]/', '', $emitente->cep);
		$stdEnderEmit->cPais = '1058';
		$stdEnderEmit->fone = preg_replace('/[^0-9]/', '', $emitente->celular);
		
		$stdEnderEmit->xPais = 'BRASIL';
		$enderEmit = $nfe->tagenderEmit($stdEnderEmit); // fim tag do emitente
		// inicia tag do destinatario
		if ($cliente != null) {
			$cpf_cnpj = preg_replace('/[^0-9]/', '', $cliente->cpf_cnpj);

			if($cpf_cnpj != '00000000000'){

				$stdDest = new \stdClass();
				$stdDest->xNome = $cliente->razao_social;
				if ($cliente->contribuinte == 1) {
					if ($cliente->ie == '') {
						$stdDest->indIEDest = "2";
					} else {
						$stdDest->indIEDest = "1";
					}
				} else {
					$stdDest->indIEDest = "9";
				}

				
				if (strlen($cpf_cnpj) == 14) {
					$stdDest->CNPJ = $cpf_cnpj;
					$ie = preg_replace('/[^0-9]/', '', $cliente->ie);
					// $stdDest->IE = $ie;
				} else {
					$stdDest->CPF = $cliente->cpf_cnpj;
				}
				$dest = $nfe->tagdest($stdDest);

				$stdEnderDest = new \stdClass();
				$stdEnderDest->xLgr = $cliente->rua;
				$stdEnderDest->nro = $cliente->numero;
				$stdEnderDest->xCpl = $cliente->complemento;
				$stdEnderDest->xBairro = $cliente->bairro;
				$stdEnderDest->cMun = $cliente->cidade->codigo;
				$stdEnderDest->xMun = $cliente->cidade->nome;
				$stdEnderDest->UF = $cliente->cidade->uf;
				$stdEnderDest->fone = preg_replace('/[^0-9]/', '', $cliente->telefone);
				$stdEnderDest->CEP = preg_replace('/[^0-9]/', '', $cliente->cep);
				$stdEnderDest->cPais = '1058';
				$stdEnderDest->xPais = 'BRASIL';

				$enderDest = $nfe->tagenderDest($stdEnderDest);
			}
		}

		if ($item->cliente_cpf_cnpj != "" && $cliente == null) {
			$stdDest = new \stdClass();
			if ($item->cliente_nome) {
				$stdDest->xNome = $item->cliente_nome;
			}
			$stdDest->indIEDest = "9";

			$doc = preg_replace('/[^0-9]/', '', $item->cliente_cpf_cnpj);
			if($cpf_cnpj != '00000000000'){
				if (strlen($doc) == 14){
					$stdDest->CNPJ = $doc;
				}else{ 
					$stdDest->CPF = $doc;
				}
				$dest = $nfe->tagdest($stdDest);
			}
		}

		//fim tag destinatario endereço

		$somaProdutos = 0;
		$somaICMS = 0;
		$somaFrete = 0;
		$somaIpi = 0;
		$totalItens = sizeof($item->itens);
		$somaDesconto = 0;
		$somaAcrescimo = 0;

		$obsIbpt = "";
		$somaFederal = 0;
		$somaEstadual = 0;
		$somaMunicipal = 0;
		$somaVICMSST = 0;

		foreach ($item->itens as $itemCont => $i) {
			$itemCont++;

			$stdProd = new \stdClass(); // tag produto inicio
			$stdProd->item = $itemCont;

			$validaEan = $this->validate_EAN13Barcode($i->produto->codigo_barras);
			$stdProd->cEAN = $validaEan ? $i->produto->codigo_barras : 'SEM GTIN';
			$stdProd->cEANTrib = $validaEan ? $i->produto->codigo_barras : 'SEM GTIN';

			$cest = $i->produto->cest;
			$cest = str_replace(".", "", $cest);
			if(strlen($cest) > 1){
				$stdProd->cest = $cest;
			}

			$stdProd->cProd = $i->produto->id;
			if($i->produto->numero_sequencial){
				$stdProd->cProd = $i->produto->numero_sequencial;
			}

			if($i->produto->referencia){
				$stdProd->cProd = $i->produto->referencia;
			}
			
			$stdProd->xProd = $i->descricao();
			$stdProd->NCM = preg_replace('/[^0-9]/', '', $i->ncm);
			$ibpt = Ibpt::getItemIbpt($emitente->cidade->uf, preg_replace('/[^0-9]/', '', $i->ncm));
			$usarIbpt = 1;

			if($configGeral && $configGeral->usar_ibpt == 0){
				$usarIbpt = 0;
			}
			
			$stdProd->CFOP = $i->cfop;
			$stdProd->uCom = $i->produto->unidade;
			$stdProd->qCom = $i->quantidade;
			$stdProd->vUnCom = $this->format($i->valor_unitario);
			$stdProd->vProd = $this->format(($i->quantidade * $i->valor_unitario));
			$stdProd->uTrib = $i->produto->unidade;
			if($i->produto->unidade_tributavel != ''){
				$stdProd->uTrib = $i->produto->unidade_tributavel;
			}
			// $stdProd->qTrib = $i->quantidade;
			if($i->produto->quantidade_tributavel == 0){
				$stdProd->qTrib = $i->quantidade;
			}else{
				$stdProd->qTrib = $i->produto->quantidade_tributavel * $i->quantidade;
			}

			$stdProd->vUnTrib = $this->format($i->valor_unitario);
			if($i->produto->quantidade_tributavel > 0){
				$stdProd->vUnTrib = $stdProd->vProd/$stdProd->qTrib;
			}
			$stdProd->indTot = 1;

			// if ($item->desconto > 0) {
			// 	if ($itemCont < sizeof($item->itens)) {
			// 		$totalVenda = $item->total + $item->desconto;
			// 		$media = (((($stdProd->vProd - $totalVenda) / $totalVenda)) * 100);
			// 		$media = 100 - ($media * -1);
			// 		if ($item->desconto > 0.1) {
			// 			$tempDesc = ($item->desconto * $media) / 100;
			// 		} else {
			// 			$tempDesc = $item->desconto;
			// 		}
			// 		if ($somaDesconto >= $item->desconto) {
			// 			$tempDesc = 0;
			// 		}
			// 		$somaDesconto += $tempDesc;
			// 		if ($tempDesc > 0.01)
			// 			$stdProd->vDesc = $this->format($tempDesc);
			// 	} else {
			// 		if ($item->desconto - $somaDesconto >= 0.01)
			// 			$stdProd->vDesc = $this->format($item->desconto - $somaDesconto);
			// 	}
			// }

			if($item->valor_frete > 0){

				if($itemCont < $totalItens){
					$somaFrete += $vFt = 
					$this->format($item->valor_frete/$totalItens, 2);
					$stdProd->vFrete = $this->format($vFt);
				}else{
					$stdProd->vFrete = $this->format(($item->valor_frete-$somaFrete), 2);
				}
			}

			if($item->desconto > 0.01 && $somaDesconto < $item->desconto){
				if($itemCont < sizeof($item->itens)){
					$totalVenda = $item->total + $item->desconto;

					$media = (((($stdProd->vProd - $totalVenda)/$totalVenda))*100);
					$media = 100 - ($media * -1);

					$tempDesc = ($item->desconto*$media)/100;
					$tempDesc -= 0.01;
					if($tempDesc > 0.01){
						$somaDesconto += $this->format($tempDesc);
						$stdProd->vDesc = $this->format($tempDesc);
					}else{
						if(sizeof($item->itens) > 1){
							$somaDesconto += 0.01;
							$stdProd->vDesc = $this->format(0.01);
						}else{
							$somaDesconto = $item->desconto;
							$stdProd->vDesc = $this->format($somaDesconto);
						}
					}
				}else{
					if(($item->desconto - $somaDesconto) > 0.01){
						$stdProd->vDesc = $this->format($item->desconto - $somaDesconto);
					}
				}
			}

			if($item->acrescimo > 0.01 && $somaAcrescimo < $item->acrescimo){
				if($itemCont < sizeof($item->itens)){
					$totalVenda = $item->total;

					$media = (((($stdProd->vProd - $totalVenda)/$totalVenda))*100);
					$media = 100 - ($media * -1);

					$tempDesc = ($item->acrescimo*$media)/100;
					$tempDesc -= 0.01;
					if($tempDesc > 0.01){
						$somaAcrescimo += $this->format($tempDesc);
						$stdProd->vOutro = $this->format($tempDesc);
					}else{
						if(sizeof($item->itens) > 1){
							$somaAcrescimo += 0.01;
							$stdProd->vOutro = $this->format(0.01);
						}else{
							$somaAcrescimo = $item->acrescimo;
							$stdProd->vOutro = $this->format($somaAcrescimo);
						}
					}

				}else{
					if(($item->acrescimo - $somaAcrescimo) > 0.01){
						$stdProd->vOutro = $this->format($item->acrescimo - $somaAcrescimo, 2);
					}
				}
			}

			$prod = $nfe->tagprod($stdProd); // fim tag de produtos

			$stdImposto = new \stdClass();
			$stdImposto->item = $itemCont;

			if($usarIbpt == 1){
				if($i->produto->ibpt){

					$vProd = $stdProd->vProd;
					if($i->produto->origem == 1 || $i->produto->origem == 2){
						$federal = $this->format(($vProd*($i->produto->ibpt->importado/100)), 2);
					}else{
						$federal = $this->format(($vProd*($i->produto->ibpt->nacional/100)), 2);
					}
					$somaFederal += $federal;

					$estadual = $this->format(($vProd*($i->produto->ibpt->estadual/100)), 2);
					$somaEstadual += $estadual;

					$municipal = $this->format(($vProd*($i->produto->ibpt->municipal/100)), 2);
					$somaMunicipal += $municipal;

					$soma = $federal + $estadual + $municipal;
					$stdImposto->vTotTrib = $soma;

					$obsIbpt = " FONTE: " . $i->produto->ibpt->fonte ?? '';
					$obsIbpt .= " VERSAO: " . $i->produto->ibpt->versao ?? '';
					$obsIbpt .= " | ";

				}else{
					if($ibpt != null){

						$vProd = $stdProd->vProd;

						if($i->produto->origem == 1 || $i->produto->origem == 2){
							$federal = $this->format(($vProd*($ibpt->importado_federal/100)), 2);

						}else{
							$federal = $this->format(($vProd*($ibpt->nacional_federal/100)), 2);
						}
						$somaFederal += $federal;

						$estadual = $this->format(($vProd*($ibpt->estadual/100)), 2);
						$somaEstadual += $estadual;

						$municipal = $this->format(($vProd*($ibpt->municipal/100)), 2);
						$somaMunicipal += $municipal;

						$soma = $federal + $estadual + $municipal;
						$stdImposto->vTotTrib = $soma;

						$obsIbpt = " FONTE: " . $ibpt->versao ?? '';
						$obsIbpt .= " | ";
					}
				}
			}
			$imposto = $nfe->tagimposto($stdImposto); // tag imposto

			if ($stdEmit->CRT == 1 || $stdEmit->CRT == 4) {
				$stdICMS = new \stdClass();

				$stdICMS->item = $itemCont;
				$stdICMS->orig = $i->produto->origem;
				$stdICMS->CSOSN = $i->cst_csosn;

				$stdICMS->pCredSN = $this->format($i->perc_icms);
				$stdICMS->vCredICMSSN = $this->format($i->perc_icms);

				if($i->cst_csosn == 61){
					$stdICMS->CST = $i->cst_csosn;
					$stdICMS->qBCMonoRet = $this->format($stdProd->qTrib);
					$stdICMS->adRemICMSRet = $this->format($i->produto->adRemICMSRet, 4);
					$stdICMS->vICMSMonoRet = $this->format($i->produto->adRemICMSRet*$stdProd->qTrib, 4);
					$ICMS = $nfe->tagICMS($stdICMS);
				}else{
					if($stdICMS->CSOSN >= 101){
						$ICMS = $nfe->tagICMSSN($stdICMS);
					}
				}


				$somaICMS = 0;
			} else if ($stdEmit->CRT == 3 || $stdEmit->CRT == 2) {

				$stdICMS = new \stdClass();
				$stdICMS->item = $itemCont;
				$stdICMS->orig = $i->produto->origem;
				$stdICMS->CST = $i->cst_csosn;
				$stdICMS->modBC = 0;
				$stdICMS->vBC = $stdProd->vProd - ($stdProd->vDesc ?? 0);
				$stdICMS->pICMS = $this->format($i->perc_icms);
				$stdICMS->vICMS = $stdICMS->vBC * ($stdICMS->pICMS / 100);

				if($stdICMS->pICMS == 0){
					$stdICMS->vBC = 0;
				}
				if($i->cst_csosn == '60'){

					$stdICMS->pRedBCEfet = $i->produto->pRedBCEfet ?? 0;

					$stdICMS->vBCEfet = $stdProd->vProd - ($stdProd->vDesc ?? 0);
					$stdICMS->pICMSEfet = $i->produto->pICMSEfet;
					$stdICMS->vICMSEfet = $stdICMS->vBCEfet * ($stdICMS->pICMSEfet / 100);

					if($i->pST > 0){
						$stdICMS->pST = $i->pST;

						$stdICMS->vBCSTRet = 0.00;
						$stdICMS->vICMSSTRet = 0.00;
						$stdICMS->vBCSTDest = 0.00;
						$stdICMS->vICMSSTDest = 0.00;
					}
				}

				if($i->cst_csosn != 60){
					if($stdICMS->vICMS > 0){
						$somaProdutos += $stdICMS->vBC;
						$somaICMS += $stdICMS->vICMS;
					}
				}
				if($i->cst_csosn == 10){

					$stdICMS->modBCST = $i->produto->modBCST;
					$stdICMS->vBCST = $stdProd->vProd;
					$stdICMS->pICMSST = $this->format($i->produto->pICMSST);
					$somaVICMSST += $stdICMS->vICMSST = $stdICMS->vBCST * ($stdICMS->pICMSST/100);
				}

				if($i->cst_csosn == 61){

					$stdICMS->qBCMonoRet = $this->format($stdProd->qTrib);
					$stdICMS->adRemICMSRet = $this->format($i->produto->adRemICMSRet, 4);
					$stdICMS->vICMSMonoRet = $this->format($i->produto->adRemICMSRet*$stdProd->qTrib, 4);
				}

				// $ICMS = $nfe->tagICMS($stdICMS);
				if($i->cst_csosn < 101){
					$usaICMSST = __usaICMSST($emitente, $i->cst_csosn, $stdProd->CFOP);
					if($usaICMSST){
						$ICMS = $nfe->tagICMSST($stdICMS);
					}else{
						$ICMS = $nfe->tagICMS($stdICMS);
					}
				}
			} // fim tag icms
			//PIS
			$stdPIS = new \stdClass();
			$stdPIS->item = $itemCont;
			$stdPIS->CST = $i->cst_pis;
			$stdPIS->vBC = $this->format($i->perc_pis) > 0 ? $stdProd->vProd : 0.00;
			$stdPIS->pPIS = $this->format($i->perc_pis);
			$stdPIS->vPIS = $this->format($stdProd->vProd * ($i->perc_pis / 100));
			$PIS = $nfe->tagPIS($stdPIS);

			//COFINS
			$stdCOFINS = new \stdClass();
			$stdCOFINS->item = $itemCont;
			$stdCOFINS->CST = $i->cst_cofins;
			$stdCOFINS->vBC = $this->format($i->perc_cofins) > 0 ? $stdProd->vProd : 0.00;
			$stdCOFINS->pCOFINS = $this->format($i->perc_cofins);
			$stdCOFINS->vCOFINS = $this->format($stdProd->vProd * ($i->perc_cofins / 100));
			$COFINS = $nfe->tagCOFINS($stdCOFINS);
			//ibs e cbs
			if($i->produto->cst_ibscbs && $i->produto->cclass_trib){

				$stdIBSCBS = new \stdClass();
				$stdIBSCBS->item = $itemCont;
				$stdIBSCBS->CST = $i->produto->cst_ibscbs;
				$stdIBSCBS->cClassTrib = $i->produto->cclass_trib;
				$stdIBSCBS->vBC = $stdProd->vProd;

				// if($i->produto->perc_ibs_uf > 0){
				$stdIBSCBS->gIBSUF_pIBSUF = $i->produto->perc_ibs_uf;
				$stdIBSCBS->gIBSUF_vIBSUF = $stdIBSCBS->vBC * ($stdIBSCBS->gIBSUF_pIBSUF/100);
				// }

				// if($i->produto->perc_ibs_mun > 0){
				$stdIBSCBS->gIBSMun_pIBSMun = $i->produto->perc_ibs_mun;
				$stdIBSCBS->gIBSMun_vIBSMun = $stdIBSCBS->vBC * ($stdIBSCBS->gIBSMun_pIBSMun/100);
				// }

				// if($i->produto->perc_cbs > 0){
				$stdIBSCBS->gCBS_pCBS = $i->produto->perc_cbs;
				$stdIBSCBS->gCBS_vCBS = $stdIBSCBS->vBC * ($stdIBSCBS->gCBS_pCBS/100);
				// }

				if($i->produto->perc_dif > 0){
					$stdIBSCBS->gIBSUF_pDif = $i->produto->perc_dif;
					$stdIBSCBS->gIBSUF_vDif = $stdIBSCBS->vBC * ($stdIBSCBS->gIBSUF_pDif/100);
				}

				$IBSCBS = $nfe->tagIBSCBS($stdIBSCBS);
			}

			if(strlen($i->produto->codigo_anp) > 2){
				$stdComb = new \stdClass();
				$stdComb->item = $itemCont; 
				$stdComb->cProdANP = $i->produto->codigo_anp;
				$stdComb->descANP = $i->produto->getDescricaoAnp();
				if($i->produto->perc_glp > 0){
					$stdComb->pGLP = $this->format($i->produto->perc_glp);
				}

				if($i->produto->perc_gnn > 0){
					$stdComb->pGNn = $this->format($i->produto->perc_gnn);
				}

				if($i->produto->perc_gni > 0){
					$stdComb->pGNi = $this->format($i->produto->perc_gni);
				}

				$stdComb->vPart = $this->format($i->produto->valor_partida);
				$stdComb->UFCons = $item->cliente ? $item->cliente->cidade->uf : $emitente->cidade->uf;

				if($i->produto->pBio > 0){
					$stdComb->pBio = $i->produto->pBio;
				}
				$nfe->tagcomb($stdComb);
			}

			if($stdIde->indFinal == 0 && strlen($i->produto->codigo_anp) > 2){
				$stdOrigComb = new \stdClass();

				$stdOrigComb->item = $itemCont; 
				$stdOrigComb->indImport = $i->produto->indImport;
				$stdOrigComb->cUFOrig = $i->produto->cUFOrig;
				$stdOrigComb->pOrig = $i->produto->pOrig;
				$nfe->tagorigComb($stdOrigComb);
			}

			// $cest = $i->produto->cest;

			// $cest = str_replace(".", "", $cest);
			// $stdProd->CEST = $cest;
			// if(strlen($cest) > 0){
			// 	$std = new \stdClass();
			// 	$std->item = $itemCont; 
			// 	$std->CEST = $cest;
			// 	$nfe->tagCEST($std);
			// }
		}

		$stdICMSTot = new \stdClass();
		$stdICMSTot->vBC = $this->format($somaProdutos);
		$stdICMSTot->vICMS = $this->format($somaICMS);
		$stdICMSTot->vICMSDeson = 0.00;
		$stdICMSTot->vBCST = 0.00;
		$stdICMSTot->vST = 0.00;
		$stdICMSTot->vProd = 0;
		$stdICMSTot->vFrete = 0.00;
		$stdICMSTot->vSeg = 0.00;
		$stdICMSTot->vDesc = $this->format($item->desconto);
		$stdICMSTot->vII = 0.00;
		$stdICMSTot->vIPI = 0.00;
		$stdICMSTot->vPIS = 0.00;
		$stdICMSTot->vCOFINS = 0.00;
		$stdICMSTot->vOutro = 0.00;
		$stdICMSTot->vNF = $this->format($item->total  + $somaVICMSST);

		$stdICMSTot->vTotTrib = 0.00;
		$ICMSTot = $nfe->tagICMSTot($stdICMSTot);
		$stdTransp = new \stdClass();
		$stdTransp->modFrete = $item->tipo != null ? $item->tipo : 9;

		$transp = $nfe->tagtransp($stdTransp);

		if($item->transportadora){
			$std = new \stdClass();
			$std->xNome = $item->transportadora->razao_social;
			$std->xEnder = $item->transportadora->endereco;
			$std->xMun = $item->transportadora->cidade ? $item->transportadora->cidade->nome : '';
			$std->UF = $item->transportadora->cidade ? $item->transportadora->cidade->uf : '';

			$cnpj_cpf = preg_replace('/[^0-9]/', '', $item->transportadora->cpf_cnpj);

			if(strlen($cnpj_cpf) == 14) $std->CNPJ = $cnpj_cpf;
			else $std->CPF = $cnpj_cpf;

			$nfe->tagtransporta($std);
		}

		$respTec = ConfiguracaoSuper::first();
		if ($respTec != null && $respTec->usar_resp_tecnico == 1) {
			$stdResp = new \stdClass();
			$doc = preg_replace('/[^0-9]/', '', $respTec->cpf_cnpj);
			if (strlen($doc) == 14) $stdResp->CNPJ = $doc;
			else $stdResp->CPF = $doc;

			$stdResp->xContato = $respTec->name;
			$stdResp->email = $respTec->email;
			$stdResp->fone = preg_replace('/[^0-9]/', '', $respTec->telefone);
			$nfe->taginfRespTec($stdResp);
		}

		if($configGeral && strlen($configGeral->resp_tec_cpf_cnpj) > 0){
			$stdResp = new \stdClass();
			$doc = preg_replace('/[^0-9]/', '', $configGeral->resp_tec_cpf_cnpj);

			if (strlen($doc) == 14) $stdResp->CNPJ = $doc;
			else $stdResp->CPF = $doc;

			$stdResp->xContato = $configGeral->resp_tec_nome;
			$stdResp->email = $configGeral->resp_tec_email;
			$stdResp->fone = preg_replace('/[^0-9]/', '', $configGeral->resp_tec_telefone);
			$nfe->taginfRespTec($stdResp);
		}

		// dd($item->desconto);
		//Fatura
		$stdFat = new \stdClass();
		$stdFat->nFat = $stdIde->nNF;
		$stdFat->vOrig = $this->format($item->total);
		$stdFat->vDesc = $this->format($item->desconto);
		$stdFat->vLiq = $this->format($item->total);

		// $fatura = $nfe->tagfat($stdFat);		
		$contFatura = 1;

		$stdPag = new \stdClass();
		if ($item->dinheiro_recebido > 0) {
			$vPag = $item->dinheiro_recebido;
			$stdPag->vTroco = $vPag - $item->total;
		}

		if($item->troco > 0 && $item->dinheiro_recebido == 0){
			$vPag = $item->total + $item->troco;
			$stdPag->vTroco = $item->troco;
		}
		// dd($this->format($item->total));
		$pag = $nfe->tagpag($stdPag);


		if ($item->dinheiro_recebido > 0) {
			$stdDetPag = new \stdClass();
			$stdDetPag->tPag = $item->tipo_pagamento;
			if($item->tipo_pagamento == '06'){
				$stdDetPag->tPag = '05'; 
			}
			$stdDetPag->vPag = $this->format($item->dinheiro_recebido);
			$stdDetPag->indPag = 1;
			$detPag = $nfe->tagdetPag($stdDetPag);
		} else {

			if (sizeof($item->fatura) > 0) {
				foreach ($item->fatura as $ft) {

					$stdDetPag = new \stdClass();
					if($ft->tipo_pagamento == '30'){
						$ft->tipo_pagamento = '03';
					}elseif($ft->tipo_pagamento == '31'){
						$ft->tipo_pagamento = '04';
					}elseif($ft->tipo_pagamento == '32'){
						$ft->tipo_pagamento = '17';
					}

					$stdDetPag->tPag = $ft->tipo_pagamento;
					if($ft->tipo_pagamento == '06'){
						$stdDetPag->tPag = '05'; 
					}

					$stdDetPag->vPag = $this->format($ft->valor);

					if($item->pedido){
						$stdDetPag->vPag = $item->pedido->somaProdutos();
					}
					$stdDetPag->indPag = 1;

					if($ft->tipo_pagamento == '03' || $ft->tipo_pagamento == '04' || $ft->tipo_pagamento == '17'){
						
						$stdDetPag->tBand = $item->bandeira_cartao;
						if(!$item->bandeira_cartao){
							$stdDetPag->tBand = '01';
						}

						if($item->cnpj_cartao){
							$stdDetPag->CNPJ = preg_replace('/[^0-9]/', '', $item->cnpj_cartao);
						}

						if($item->cAut_cartao != ""){
							$stdDetPag->cAut = $item->cAut_cartao;
						}


						$stdDetPag->tpIntegra = 2;
					}
					$detPag = $nfe->tagdetPag($stdDetPag);
				}
			}
		}


		$stdInfoAdic = new \stdClass();
		$obs = $item->observacao;

		if($somaEstadual > 0 || $somaFederal > 0 || $somaMunicipal > 0){
			$obs .= " Trib. aprox. ";
			if($somaFederal > 0){
				$obs .= "R$ " . number_format($somaFederal, 2, ',', '.') ." Federal"; 
			}
			if($somaEstadual > 0){
				$obs .= ", R$ ".number_format($somaEstadual, 2, ',', '.')." Estadual"; 
			}
			if($somaMunicipal > 0){
				$obs .= ", R$ ".number_format($somaMunicipal, 2, ',', '.')." Municipal"; 
			}
			// $ibpt = IBPT::where('uf', $config->UF)->first();

			$obs .= $obsIbpt;
		}
		if(trim($emitente->observacao_padrao_nfce) != ""){
			$obs .= " " . $emitente->observacao_padrao_nfce;
		}

		$stdInfoAdic->infCpl = $obs;
		$infoAdic = $nfe->taginfAdic($stdInfoAdic);

		try {
			$nfe->montaNFe();
			$arr = [
				'chave' => $nfe->getChave(),
				'xml' => $nfe->getXML(),
				'numero' => $stdIde->nNF
			];
			return $arr;
		} catch (\Exception $e) {
			return [
				'erros_xml' => $nfe->getErrors()
			];
		}
	}

	private function validate_EAN13Barcode($ean)
	{

		$sumEvenIndexes = 0;
		$sumOddIndexes  = 0;

		$eanAsArray = array_map('intval', str_split($ean));

		if(strlen($ean) == 14){
			return true;
		}

		if (!$this->has13Numbers($eanAsArray) ) {
			return false;
		};

		for ($i = 0; $i < count($eanAsArray)-1; $i++) {
			if ($i % 2 === 0) {
				$sumOddIndexes  += $eanAsArray[$i];
			} else {
				$sumEvenIndexes += $eanAsArray[$i];
			}
		}

		$rest = ($sumOddIndexes + (3 * $sumEvenIndexes)) % 10;

		if ($rest !== 0) {
			$rest = 10 - $rest;
		}

		return $rest === $eanAsArray[12];
	}

	private function has13Numbers(array $ean)
	{
		return count($ean) === 13 || count($ean) === 14;
	}

	public function format($number, $dec = 2)
	{
		return number_format((float) $number, $dec, ".", "");
	}

	public function sign($xml)
	{
		return $this->tools->signNFe($xml);
	}

	// public function transmitir($signXml, $chave)
	// {
	// 	try {
	// 		$idLote = str_pad(100, 15, '0', STR_PAD_LEFT);
	// 		$resp = $this->tools->sefazEnviaLote([$signXml], $idLote, 1);

	// 		$st = new Standardize();
	// 		$std = $st->toStd($resp);
	// 		sleep($this->timeout);

	// 		if ($std->cStat != 103 && $std->cStat != 104) {

	// 			return [
	// 				'erro' => 1,
	// 				'error' => "[$std->cStat] - $std->xMotivo",
	// 				'cStat' => $std->cStat
	// 			];
	// 		}

	// 		sleep(1);
	// 		try {
	// 			$xml = Complements::toAuthorize($signXml, $resp);
	// 			file_put_contents(public_path('xml_nfce/') . $chave . '.xml', $xml);
	// 			return [
	// 				'erro' => 0,
	// 				'success' => $std->protNFe->infProt->nProt
	// 			];
	// 		} catch (\Exception $e) {
	// 			return [
	// 				'erro' => 1,
	// 				'error' => $st->toArray($resp),
	// 				'recibo' => $resp
	// 			];
	// 		}
	// 	} catch (\Exception $e) {
	// 		return [
	// 			'erro' => 1,
	// 			'error' => $e->getMessage()
	// 		];
	// 	}
	// }

	public function transmitir($signXml, $chave)
	{
		try {
			$idLote = str_pad(100, 15, '0', STR_PAD_LEFT);
			$resp = $this->tools->sefazEnviaLote([$signXml], $idLote, 1);

			$st = new Standardize();
			$std = $st->toStd($resp);

			if (!in_array($std->cStat, [103, 104])) {
				return [
					'erro' => 1,
					'error' => "[{$std->cStat}] - {$std->xMotivo}",
					'cStat' => $std->cStat
				];
			}

			sleep($this->timeout);

			if ($std->cStat == 204) {
				$consulta = $this->tools->sefazConsultaChave($chave);
				$stdc = $st->toStd($consulta);

				if (isset($stdc->protNFe->infProt->nProt)) {
					return [
						'erro' => 0,
						'success' => $stdc->protNFe->infProt->nProt,
						'msg' => 'NFC-e já autorizada anteriormente.'
					];
				}

				return [
					'erro' => 1,
					'error' => 'Duplicidade detectada mas sem protocolo retornado pela SEFAZ'
				];
			}
			$xml = Complements::toAuthorize($signXml, $resp);
			file_put_contents(public_path('xml_nfce/') . $chave . '.xml', $xml);

			return [
				'erro' => 0,
				'success' => $std->protNFe->infProt->nProt ?? null
			];

		} catch (\Exception $e) {
			return [
				'erro' => 1,
				'error' => $e->getMessage()
			];
		}
	}


	public function consultar($nfe)
	{
		try {

			$this->tools->model('65');

			$chave = $nfe->chave;
			$response = $this->tools->sefazConsultaChave($chave);

			$stdCl = new Standardize($response);
			$arr = $stdCl->toArray();
			if($arr['xMotivo'] == 'Autorizado o uso da NF-e'){
				if($nfe->estado != 'aprovado'){

					$empresa = Empresa::findOrFail($nfe->empresa_id);
					$empresa = __objetoParaEmissao($empresa, $nfe->local_id);

					$chave = $arr['protNFe']['infProt']['chNFe'];
					$nRec = $nfe->recibo;
					$nfe->estado = 'aprovado';
					$nfe->save();

					if($empresa->ambiente == 1){
						$empresa->numero_ultima_nfce_producao = $nfe->numero;
					}else{
						$empresa->numero_ultima_nfce_homologacao = $nfe->numero;
					}
					try{
						$xml = Complements::toAuthorize($nfe->signed_xml, $nRec);
						file_put_contents(public_path('xml_nfce/').$chave.'.xml', $xml);
					}catch(\Exception $e){
						
					}

				}
			}

			return $arr;
		} catch (\Exception $e) {
			return ['erro' => true, 'data' => $e->getMessage(), 'status' => 402];
		}
	}

	public function cancelar($nfe, $motivo)
	{
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
				if ($cStat == '101' || $cStat == '135' || $cStat == '155') {
					$xml = Complements::toAuthorize($this->tools->lastRequest, $response);
					file_put_contents(public_path('xml_nfce_cancelada/') . $chave . '.xml', $xml);

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

	public function consultaStatus($tpAmb, $uf)
	{
		try {
			$response = $this->tools->sefazStatus($uf, $tpAmb);
			$stdCl = new Standardize($response);
			$arr = $stdCl->toArray();
			return $arr;
		} catch (\Exception $e) {
			echo $e->getMessage();
		}
	}
}
