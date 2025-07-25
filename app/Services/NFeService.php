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
use App\Models\Difal;
use App\Models\Empresa;
use App\Models\ConfigGeral;
use App\Models\ConfiguracaoSuper;
use App\Models\Ibpt;
use NFePHP\NFe\Factories\Contingency;
use App\Models\Contigencia;
use NFePHP\Common\Soap\SoapCurl;

class NFeService{

	private $config; 
	private $tools;
	protected $empresa_id = null;

	protected $timeout = 8;

	public function __construct($config, $empresa){
		
		$this->empresa_id = $empresa->id;

		$this->config = $config;
		$this->tools = new Tools(json_encode($config), Certificate::readPfx($empresa->arquivo, $empresa->senha));
		$this->tools->model(55);

		$config = ConfiguracaoSuper::first();
		if($config){
			if($config->timeout_nfe){
				$this->timeout = $config->timeout_nfe;
			}
		}

		$soapCurl = new SoapCurl();
		$soapCurl->httpVersion('1.1');
		$this->tools->loadSoapClass($soapCurl);
		
		$contigencia = $this->getContigencia();
		if($contigencia != null){
			$contingency = new Contingency($contigencia->status_retorno);
			dd($contingency);
			$this->tools->contingency = $contingency;
		}
	}

	private function getContigencia(){
		$active = Contigencia::
		where('empresa_id', $this->empresa_id)
		->where('status', 1)
		->where('documento', 'NFe')
		->first();
		return $active;
	}

	public function gerarXml($item){

		$nfe = new Make();
		$stdInNFe = new \stdClass();
		$stdInNFe->versao = '4.00';
		$stdInNFe->Id = null;
		$stdInNFe->pk_nItem = '';

		$infNFe = $nfe->taginfNFe($stdInNFe);
		$emitente = $item->empresa;
		$emitente = __objetoParaEmissao($emitente, $item->local_id);
		$configGeral = ConfigGeral::where('empresa_id', $item->empresa_id)->first();
		$cliente = $item->cliente;

		$stdIde = new \stdClass();
		$stdIde->cUF = Empresa::getCodUF($emitente->cidade->uf); // codigo uf emitente
		$stdIde->cNF = rand(11111, 99999);
		// $stdIde->natOp = $venda->natureza->natureza;
		$stdIde->natOp = $item->natureza->descricao;

		$contact = $item->cliente;

		if($contact == null){
			$contact = $item->fornecedor;
		}
		$stdIde->mod = 55;
		$stdIde->serie = $item->numero_serie;
		// $stdIde->nNF = $item->lastNumero(); // numero sequencial da nfe

		if($emitente->ambiente == 2){
			$nNF = $emitente->numero_ultima_nfe_homologacao;
		}else{
			$nNF = $emitente->numero_ultima_nfe_producao;
		}

		$stdIde->nNF = $item->numero; 
		$stdIde->dhEmi = date("Y-m-d\TH:i:sP");
		if($item->data_emissao_retroativa){
			$stdIde->dhEmi = $item->data_emissao_retroativa.date("\TH:i:sP");
		}
		$stdIde->dhSaiEnt = date("Y-m-d\TH:i:sP");
		if($item->data_emissao_saida){
			$stdIde->dhSaiEnt = $item->data_emissao_saida.date("\TH:i:sP");
		}
		$stdIde->tpNF = $item->tpNF;
		$stdIde->idDest = $emitente->cidade->uf == $contact->cidade->uf ? 1 : 2;

		if($contact->codigo_pais != null && $contact->codigo_pais != '1058'){
			$stdIde->idDest = 3;
		}

		$stdIde->cMunFG = $emitente->cidade->codigo;
		$stdIde->tpImp = 1;
		$stdIde->tpEmis = 1;
		$stdIde->cDV = 0;
		$stdIde->tpAmb = (int)$emitente->ambiente;
		$stdIde->finNFe = $item->finNFe;
		$stdIde->indFinal = $contact->consumidor_final;
		$stdIde->indPres = 1;
		$stdIde->procEmi = '0';
		$stdIde->verProc = '2.0';
		$tagide = $nfe->tagide($stdIde); //fim da tagide

		// inicia tag do emitente
		$stdEmit = new \stdClass();
		$stdEmit->xNome = $emitente->nome;
		$stdEmit->xFant = $emitente->nome_fantasia;
		// $stdEmit->CRT = $emitente->tributacao == 'Regime Normal' ? 3 : 1;
		// if($emitente->tributacao == 'Simples Nacional, excesso sublimite de receita bruta'){
		// 	$stdEmit->CRT = 2;	
		// }

		$stdEmit->CRT = $emitente->getCRT();
		if($item->crt != null){
			$stdEmit->CRT = $item->crt;
		}
		$stdEmit->IE = preg_replace('/[^0-9]/', '', $emitente->ie);

		$cpf_cnpj = preg_replace('/[^0-9]/', '', $emitente->cpf_cnpj);
		if (strlen($cpf_cnpj) == 14) {
			$stdEmit->CNPJ = $cpf_cnpj;
		}else{
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
		$stdEnderEmit->xPais = 'BRASIL';
		$enderEmit = $nfe->tagenderEmit($stdEnderEmit); // fim tag do emitente

		// inicia tag do destinatario
		$stdDest = new \stdClass();
		$stdDest->xNome = $contact->razao_social;
		if ($contact->contribuinte == 1) {
			if ($contact->ie == '') {
				$stdDest->indIEDest = "2";
			} else {
				$stdDest->indIEDest = "1";
			}
		} else {
			$stdDest->indIEDest = "9";
		}

		if($contact->codigo_pais != '1058'){
			$stdDest->indIEDest = "9";
			$stdDest->idEstrangeiro = $contact->id_estrangeiro;
		}

		$cpf_cnpj = preg_replace('/[^0-9]/', '', $contact->cpf_cnpj);

		if (strlen($cpf_cnpj) == 14) {
			$stdDest->CNPJ = $cpf_cnpj;
			$ie = preg_replace('/[^0-9]/', '', $contact->ie);
			$stdDest->IE = $ie;
		} else {
			$stdDest->CPF = $contact->cpf_cnpj;
		}

		$dest = $nfe->tagdest($stdDest);

		$stdEnderDest = new \stdClass();
		$stdEnderDest->xLgr = $contact->rua;
		$stdEnderDest->nro = $contact->numero;
		$stdEnderDest->xCpl = $contact->complemento;
		$stdEnderDest->xBairro = $contact->bairro;
		$stdEnderDest->cMun = $contact->cidade->codigo;
		$stdEnderDest->xMun = $contact->cidade->nome;
		$stdEnderDest->UF = $contact->cidade->uf;
		$stdEnderDest->fone = preg_replace('/[^0-9]/', '', $contact->telefone);
		$stdEnderDest->CEP = preg_replace('/[^0-9]/', '', $contact->cep);

		if($contact->codigo_pais == null || $contact->codigo_pais == '1058'){
			$stdEnderDest->cPais = '1058';
			$stdEnderDest->xPais = 'BRASIL';
		}else{
			$stdEnderDest->cPais = $contact->codigo_pais;
			$stdEnderDest->xPais = __getPais($contact->codigo_pais);

			$stdEnderDest->cMun = 9999999;
			$stdEnderDest->xMun = "EXTERIOR";
			$stdEnderDest->UF = "EX";
		}

		$enderDest = $nfe->tagenderDest($stdEnderDest);

		//fim tag destinatario endereço

		if(strlen($item->referencia) >= 44){
			$std = new \stdClass();
			$std->refNFe = $item->referencia;
			$nfe->tagrefNFe($std);
		}

		$somaProdutos = 0;
		$somaICMS = 0;
		$somaFrete = 0;
		$somaDesconto = 0;
		$somaAcrescimo = 0;
		$somaIpi = 0;
		$totalItens = sizeof($item->itens);
		$VBC = 0;
		$obsIbpt = "";
		$somaFederal = 0;
		$somaEstadual = 0;
		$somaMunicipal = 0;
		$somaVICMSST = 0;
		$somaApCredito = 0;
		$somavFCPST = 0;

		foreach ($item->itens as $itemCont => $i) {

			$itemCont++;

			$stdProd = new \stdClass(); // tag produto inicio
			$stdProd->item = $itemCont;

			$validaEan = $this->validate_EAN13Barcode($i->produto->codigo_barras);
			$stdProd->cEAN = $validaEan ? $i->produto->codigo_barras : 'SEM GTIN';
			$stdProd->cEANTrib = $validaEan ? $i->produto->codigo_barras : 'SEM GTIN';
			$stdProd->cProd = $i->produto->id;
			if($i->produto->numero_sequencial){
				$stdProd->cProd = $i->produto->numero_sequencial;
			}
			$stdProd->xProd = $i->descricao();
			$stdProd->NCM = preg_replace('/[^0-9]/', '', $i->ncm);

			$ibpt = Ibpt::getItemIbpt($emitente->cidade->uf, preg_replace('/[^0-9]/', '', $i->ncm));
			if($stdIde->finNFe == 4){
				$ibpt = null;
			}

			if($configGeral && $configGeral->usar_ibpt == 0){
				$ibpt = null;
			}

			$stdProd->CFOP = $i->cfop;
			if($item->natureza->sobrescrever_cfop == 1){
				if($stdIde->tpNF == 1){
					if($emitente->cidade->uf != $contact->cidade->uf && $item->natureza->cfop_outro_estado){
						$stdProd->CFOP = $item->natureza->cfop_outro_estado;
					}elseif($emitente->cidade->uf == $contact->cidade->uf && $item->natureza->cfop_estadual){
						$stdProd->CFOP = $item->natureza->cfop_estadual;
					}
				}else{
					if($emitente->cidade->uf != $contact->cidade->uf && $item->natureza->cfop_entrada_outro_estado){
						$stdProd->CFOP = $item->natureza->cfop_entrada_outro_estado;
					}elseif($emitente->cidade->uf == $contact->cidade->uf && $item->natureza->cfop_entrada_estadual){
						$stdProd->CFOP = $item->natureza->cfop_entrada_estadual;
					}
				}
			}

			$stdProd->uCom = $i->produto->unidade;
			$stdProd->qCom = $i->quantidade;
			$stdProd->vUnCom = $this->format($i->valor_unitario);
			$stdProd->vProd = $this->format(($i->quantidade * $i->valor_unitario));
			$stdProd->uTrib = $i->produto->unidade;
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
			$somaProdutos += $stdProd->vProd;
			if($i->codigo_beneficio_fiscal){
				$stdProd->cBenef = $i->codigo_beneficio_fiscal;
			}

			if($item->valor_frete > 0){

				if($itemCont < $totalItens){
					$somaFrete += $vFt = 
					$this->format($item->valor_frete/$totalItens, 2);
					$stdProd->vFrete = $this->format($vFt);
				}else{
					$stdProd->vFrete = $this->format(($item->valor_frete-$somaFrete), 2);
				}
			}

			if($i->xPed != ""){
				$stdProd->xPed = $i->xPed;
			}
			if($i->nItemPed != ""){
				$stdProd->nItemPed = $i->nItemPed;
			}

			// if($item->desconto > 0.01){

			// 	if($itemCont < $totalItens){
			// 		$totalVenda = $item->total;

			// 		$media = (((($stdProd->vProd - $totalVenda)/$totalVenda))*100);
			// 		$media = 100 - ($media * -1);

			// 		$tempDesc = ($item->desconto*$media)/100;

			// 		if($tempDesc > 0.01){
			// 			$somaDesconto += $tempDesc;
			// 			$stdProd->vDesc = $this->format($tempDesc);
			// 		}else{
			// 			$somaDesconto = $venda->desconto;
			// 			$stdProd->vDesc = $this->format($somaDesconto);
			// 		}

			// 	}else{
			// 		if(($item->desconto - $somaDesconto) > 0.01){
			// 			$stdProd->vDesc = $this->format($item->desconto - $somaDesconto, 2);
			// 		}
			// 	}
			// }

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
						$stdProd->vDesc = $this->format($item->desconto - $somaDesconto, 2);
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

			if($i->infAdProd != null){
				$std = new \stdClass();
				$std->item = $itemCont;
				$std->infAdProd = $i->infAdProd;
				$nfe->taginfAdProd($std);
			}

			$stdImposto = new \stdClass();
			$stdImposto->item = $itemCont;

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
			$imposto = $nfe->tagimposto($stdImposto); // tag imposto


			if($item->natureza->cst_csosn){
				$i->cst_csosn = $item->natureza->cst_csosn;
			}

			if($item->natureza->perc_icms){
				$i->perc_icms = $item->natureza->perc_icms;
			}

			if ($stdEmit->CRT == 1 || $stdEmit->CRT == 4) {

				$stdICMS = new \stdClass();

				$stdICMS->item = $itemCont; 
				$stdICMS->orig = $i->produto->origem;
				$stdICMS->CSOSN = $i->cst_csosn;

				if($i->cst_csosn == '500'){
					$stdICMS->vBCSTRet = 0.00;
					$stdICMS->pST = 0.00;
					$stdICMS->vICMSSTRet = 0.00;
				}

				if($i->perc_icms > 0){
					if($i->perc_red_bc > 0){
						$stdICMS->pRedBC = $this->format($i->perc_red_bc);

						$tempB = 100 - $i->perc_red_bc;
						$v = $stdProd->vProd * ($tempB/100);
						$v += $stdProd->vFrete;
						$VBC += $stdICMS->vBC = number_format($v,2,'.','');
						$stdICMS->pICMS = $this->format($i->perc_icms);
						$somaICMS += $stdICMS->vICMS = (($stdProd->vProd - $stdProd->vDesc) * ($tempB/100)) * ($stdICMS->pICMS/100);
						$stdICMS->pRedBC = $this->format($i->produto->pRedBC);
					}else{
						if($i->cst_csosn > 103){
							$VBC += $stdICMS->vBC = $stdProd->vProd + $stdProd->vFrete + $stdProd->vOutro - $stdProd->vDesc;
							$stdICMS->pICMS = $this->format($i->perc_icms);
							$somaICMS += $stdICMS->vICMS = $stdICMS->vBC * ($stdICMS->pICMS/100);
						}
					}
				}else{
					$stdICMS->vBC = 0;
				}

				if($i->cst_csosn == 900){
					$stdICMS->modBC = 0;
				}
				
				if($i->cst_csosn == 201 || $i->cst_csosn == 202){

					$stdICMS->modBCST = $i->produto->modBCST;
					$stdICMS->vBCST = $stdProd->vProd;
					$stdICMS->pICMSST = $this->format($i->produto->pICMSST);
					$somaVICMSST += $stdICMS->vICMSST = $stdICMS->vBCST * ($stdICMS->pICMSST/100);
				}

				if($emitente->perc_ap_cred > 0 && $stdICMS->CSOSN == 101){
					$stdICMS->pCredSN = $this->format($emitente->perc_ap_cred);
					$somaApCredito += $stdICMS->vCredICMSSN = $this->format($stdProd->vProd*($emitente->perc_ap_cred/100));
				}else{
					$stdICMS->pCredSN = 0;
					$stdICMS->vCredICMSSN = 0;
				}


				if($stdIde->finNFe == 4){
					if($i->modBCST){
						$stdICMS->modBCST = $i->modBCST;
					}
					if($i->pMVAST){
						$stdICMS->pMVAST = $i->pMVAST;
					}
					if($i->vBCST){
						$stdICMS->vBCST = $i->vBCST;
					}
					if($i->pICMSST){
						$stdICMS->pICMSST = $i->pICMSST;
					}
					if($i->vICMSST){
						$stdICMS->vICMSST = $i->vICMSST;
					}
					if($i->vBCFCPST > 0){
						$stdICMS->vBCFCPST = $i->vBCFCPST;
					}
					if($i->pFCPST > 0){
						$stdICMS->pFCPST = $i->pFCPST;
					}
					if($i->vFCPST > 0){
						$somavFCPST += $stdICMS->vFCPST = $i->vFCPST;
					}
				}
				if(isset($stdICMS->vICMSST)){
					$somaVICMSST += $stdICMS->vICMSST;
				}
				
				if($i->cst_csosn == 61){
					$stdICMS->CST = $i->cst_csosn;
					$stdICMS->qBCMonoRet = $this->format($stdProd->qTrib);
					$stdICMS->adRemICMSRet = $this->format($i->produto->adRemICMSRet, 4);
					$stdICMS->vICMSMonoRet = $this->format($i->produto->adRemICMSRet*$stdProd->qTrib, 4);
					$ICMS = $nfe->tagICMS($stdICMS);
				}else{
					$ICMS = $nfe->tagICMSSN($stdICMS);
				}

			} else if ($stdEmit->CRT == 3 || $stdEmit->CRT == 2) {

				if($stdIde->idDest == 2 && $stdIde->indFinal == 1){
					$difal = Difal::where('cfop', $stdProd->CFOP)
					->where('empresa_id', $item->empresa_id)
					->where('uf', $stdEnderDest->UF)->first();
					if($difal){
						$i->perc_icms = $difal->pICMSInter;
					}
				}

				$stdICMS = new \stdClass();
				$stdICMS->item = $itemCont; 
				$stdICMS->orig = $i->produto->origem;
				$stdICMS->CST = $i->cst_csosn;
				$stdICMS->modBC = 0;
				$stdICMS->vBC = $stdProd->vProd - $stdProd->vDesc;

				if($i->vbc_icms > 0){
					$stdICMS->vBC = $i->vbc_icms;
				}
				$stdICMS->pICMS = $this->format($i->perc_icms);
				if($stdICMS->pICMS == 0){
					$stdICMS->vBC = 0;
				}
				$stdICMS->vICMS = $stdICMS->vBC * ($stdICMS->pICMS/100);

				if($i->perc_red_bc > 0){
					$stdICMS->pRedBC = $this->format($i->perc_red_bc);
					$tempB = 100 - $i->perc_red_bc;
					$v = $stdProd->vProd * ($tempB/100);
					$v += $stdProd->vFrete;
					$stdICMS->vBC = number_format($v,2,'.','');
					$stdICMS->pICMS = $this->format($i->perc_icms);
					$stdICMS->vICMS = ($stdProd->vProd * ($tempB/100)) * ($stdICMS->pICMS/100);

					if($i->cst_csosn != '60'){
						$VBC += $stdICMS->vBC;
						$somaICMS += $stdICMS->vICMS;
					}

				}else{

					if($i->cst_csosn != '60'){
						$VBC += $stdICMS->vBC;
						$somaICMS += $stdICMS->vICMS;
					}
				}

				if($i->cst_csosn == 10){

					$stdICMS->modBCST = $i->produto->modBCST ?? 0;
					$stdICMS->vBCST = $stdProd->vProd;
					$stdICMS->pICMSST = $this->format($i->produto->pICMSST);
					$stdICMS->vICMSST = $stdICMS->vBCST * ($stdICMS->pICMSST/100);
				}
				
				if($i->cst_csosn == 60){
					$stdICMS->vBCSTRet = 0.00;
					$stdICMS->vICMSSTRet = 0.00;
					$stdICMS->vBCSTDest = 0.00;
					$stdICMS->vICMSSTDest = 0.00;
				}

				if($stdIde->finNFe == 4){
					if($i->modBCST){
						$stdICMS->modBCST = $i->modBCST;
					}
					if($i->pMVAST){
						$stdICMS->pMVAST = $i->pMVAST;
					}
					if($i->vBCST){
						$stdICMS->vBCST = $i->vBCST;
					}
					if($i->pICMSST){
						$stdICMS->pICMSST = $i->pICMSST;
					}
					if($i->vICMSST){
						$stdICMS->vICMSST = $i->vICMSST;
					}

					if($i->vBCFCPST > 0){
						$stdICMS->vBCFCPST = $i->vBCFCPST;
					}
					if($i->pFCPST > 0){
						$stdICMS->pFCPST = $i->pFCPST;
					}
					if($i->vFCPST > 0){
						$somavFCPST += $stdICMS->vFCPST = $i->vFCPST;
					}
				}
				if(isset($stdICMS->vICMSST)){
					$somaVICMSST += $stdICMS->vICMSST;
				}

				if($i->cst_csosn == 61){
					$stdICMS->qBCMonoRet = $this->format($stdProd->qTrib);
					$stdICMS->adRemICMSRet = $this->format($i->produto->adRemICMSRet, 4);
					$stdICMS->vICMSMonoRet = $this->format($i->produto->adRemICMSRet*$stdProd->qTrib, 4);
				}

				if($i->cst_csosn == 60){
					$ICMS = $nfe->tagICMSST($stdICMS);
				}else{
					$ICMS = $nfe->tagICMS($stdICMS);
				}
			} // fim tag icms

			//PIS

			$vbcPis = $stdICMS->vBC;
			if($emitente->exclusao_icms_pis_cofins){
				$vbcPis -= $stdICMS->vICMS;
			}

			if($i->vbc_pis > 0){
				$vbcPis = $i->vbc_pis;
			}

			if($item->natureza->perc_pis){
				$i->perc_pis = $item->natureza->perc_pis;
			}
			if($item->natureza->cst_pis){
				$i->cst_pis = $item->natureza->cst_pis;
			}
			$stdPIS = new \stdClass();
			$stdPIS->item = $itemCont;
			$stdPIS->CST = $i->cst_pis;

			$stdPIS->vBC = $this->format($i->perc_pis) > 0 ? $vbcPis : 0.00;
			$stdPIS->pPIS = $this->format($i->perc_pis);
			$stdPIS->vPIS = $this->format($vbcPis * ($i->perc_pis / 100));
			$PIS = $nfe->tagPIS($stdPIS);

			//COFINS
			$vbcCofins = $stdICMS->vBC;
			if($emitente->exclusao_icms_pis_cofins){
				$vbcCofins -= $stdICMS->vICMS;
			}

			if($i->vbc_cofins > 0){
				$vbcCofins = $i->vbc_cofins;
			}
			if($item->natureza->perc_cofins){
				$i->perc_cofins = $item->natureza->perc_cofins;
			}
			if($item->natureza->cst_cofins){
				$i->cst_cofins = $item->natureza->cst_cofins;
			}
			$stdCOFINS = new \stdClass();
			$stdCOFINS->item = $itemCont;
			$stdCOFINS->CST = $i->cst_cofins;
			$stdCOFINS->vBC = $this->format($i->perc_cofins) > 0 ? $vbcCofins : 0.00;
			$stdCOFINS->pCOFINS = $this->format($i->perc_cofins);
			$stdCOFINS->vCOFINS = $this->format($vbcCofins * ($i->perc_cofins / 100));
			$COFINS = $nfe->tagCOFINS($stdCOFINS);

			$vbcIpi = $stdProd->vProd;
			if($i->vbc_ipi > 0){
				$vbcIpi = $i->vbc_ipi;
			}
			if(!$i->cEnq){
				$i->cEnq = $i->produto->cEnq;
			}
			if(!$i->cEnq){
				$i->cEnq = '999';
			}

			if($item->natureza->perc_ipi){
				$i->perc_ipi = $item->natureza->perc_ipi;
			}
			if($item->natureza->cst_ipi){
				$i->cst_ipi = $item->natureza->cst_ipi;
			}
			$std = new \stdClass(); //IPI
			$std->item = $itemCont;
			$std->clEnq = null;
			$std->CNPJProd = null;
			$std->cSelo = null;
			$std->qSelo = null;
			$std->cEnq = $i->cEnq;
			$std->CST = $i->cst_ipi;
			$std->vBC = $this->format($i->perc_ipi) > 0 ? $vbcIpi : 0.00;
			$std->pIPI = $this->format($i->perc_ipi);
			$somaIpi += $std->vIPI = $stdProd->vProd * $this->format(($i->perc_ipi / 100));
			$std->qUnid = null;
			$std->vUnid = null;

			$IPI = $nfe->tagIPI($std);

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
				$stdComb->UFCons = $item->cliente->cidade->uf;

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

			$cest = $i->produto->cest;
			$cest = str_replace(".", "", $cest);
			$stdProd->CEST = $cest;
			if(strlen($cest) > 0){
				$std = new \stdClass();
				$std->item = $itemCont; 
				$std->CEST = $cest;
				$nfe->tagCEST($std);
			}

			if($stdIde->idDest == 2 && $stdIde->indFinal == 1 && $stdEmit->CRT == 3){
				// $difal = Difal::where('cfop', $stdProd->CFOP)->first();
				$difal = Difal::where('cfop', $stdProd->CFOP)
				->where('empresa_id', $item->empresa_id)
				->where('uf', $stdEnderDest->UF)->first();

				if($difal){

					$std = new \stdClass();
					$std->item = $itemCont; 
					$std->vBCUFDest = $stdICMS->vBC;
					$std->vBCFCPUFDest = $stdICMS->vBC;
					$std->pFCPUFDest = $this->format($difal->pFCPUFDest);
					$std->pICMSUFDest = $this->format($difal->pICMSUFDest);

					$std->pICMSInter = $this->format($difal->pICMSInter);
					$std->pICMSInterPart = $this->format($difal->pICMSInterPart);
					$std->vFCPUFDest = $this->format($std->vBCUFDest * ($std->pFCPUFDest/100));

					$vICMSUFDest = $std->vBCFCPUFDest * ($std->pICMSInter/100);
					$vICMSUFDestAux = $stdICMS->vBC * ($std->pICMSUFDest/100);
					$std->vICMSUFDest = $this->format($vICMSUFDestAux-$vICMSUFDest);
					$std->vICMSUFRemet = $this->format($vICMSUFDestAux-$vICMSUFDest) - $std->vICMSUFDest;

					$nfe->tagICMSUFDest($std);
				}
			}
		}
		// dd($somaICMS);
		$stdICMSTot = new \stdClass();
		$stdICMSTot->vBC = $this->format($VBC);
		$stdICMSTot->vICMS = $this->format($somaICMS);
		$stdICMSTot->vICMSDeson = 0.00;
		$stdICMSTot->vBCST = 0.00;
		$stdICMSTot->vST = 0.00;
		$stdICMSTot->vProd = 0;
		$stdICMSTot->vFrete = $item->valor_frete;
		$stdICMSTot->vSeg = 0.00;
		$stdICMSTot->vDesc = $this->format($item->desconto);
		$stdICMSTot->vII = 0.00;
		$stdICMSTot->vIPI = 0.00;
		$stdICMSTot->vPIS = 0.00;
		$stdICMSTot->vCOFINS = 0.00;
		$stdICMSTot->vOutro = 0.00;

		$stdICMSTot->vNF = $this->format($somaProdutos + $somaVICMSST + $somaIpi + $item->valor_frete + $somavFCPST - $stdICMSTot->vDesc);
		$stdICMSTot->vTotTrib = 0.00;
		$ICMSTot = $nfe->tagICMSTot($stdICMSTot);

		$stdTransp = new \stdClass();

		$stdTransp->modFrete = $item->tipo;

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

		if($item->placa != ''){
			$std = new \stdClass();
			$placa = str_replace("-", "", $item->placa);
			$std->placa = strtoupper($placa);
			$std->UF = $item->uf;

			$nfe->tagveicTransp($std);
		}

		if ($item->qtd_volumes && $item->numeracao_volumes && $item->peso_liquido) {
			$stdVol = new \stdClass();
			$stdVol->item = 1;
			$stdVol->qVol = $item->qtd_volumes;
			$stdVol->esp = $item->especie;

			$stdVol->nVol = $item->numeracao_volumes;
			$stdVol->pesoL = $item->peso_liquido;
			$stdVol->pesoB = $item->peso_bruto;
			$vol = $nfe->tagvol($stdVol);
		}

		if($contact->codigo_pais != null && $contact->codigo_pais != '1058'){
			$std = new \stdClass();
			$std->UFSaidaPais = $stdEnderEmit->UF;
			$std->xLocExporta = $stdEnderEmit->xMun;
			$nfe->tagexporta($std);
		}

		if($item->aut_xml != ''){
			$std = new \stdClass();
			$cnpj = preg_replace('/[^0-9]/', '', $item->aut_xml);
			$std->CNPJ = $cnpj;
			$aut = $nfe->tagautXML($std);
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

		//Fatura
		$stdFat = new \stdClass();
		$stdFat->nFat = $stdIde->nNF;
		$stdFat->vOrig = $this->format($item->itens->sum('sub_total') + $item->valor_frete + $item->acrescimo);
		$stdFat->vDesc = $this->format($item->desconto);
		$stdFat->vLiq = $this->format($item->total);

		$fatura = $nfe->tagfat($stdFat);

		$contFatura = 1;

		foreach ($item->fatura as $ft) {
			if($ft->valor > 0){
				$stdDup = new \stdClass();
				$stdDup->nDup = "00" . $contFatura;
				$stdDup->dVenc = substr($ft->data_vencimento, 0, 10);
				$stdDup->vDup = $this->format($ft->valor);

				$nfe->tagdup($stdDup);
				$contFatura++;
			}
		}

		$stdPag = new \stdClass();
		$pag = $nfe->tagpag($stdPag);

		if(sizeof($item->fatura) > 0){
			foreach ($item->fatura as $ft) {

				$stdDetPag = new \stdClass();
				$stdDetPag->tPag = $ft->tipo_pagamento;
				if($stdDetPag->tPag == '06'){
					$stdDetPag->tPag = '05'; 
				}
				$stdDetPag->vPag = $this->format($ft->valor);
				$stdDetPag->indPag = 1;
				$detPag = $nfe->tagdetPag($stdDetPag);
			}
		}else{
			$stdDetPag = new \stdClass();
			$stdDetPag->tPag = 90;
			$stdDetPag->vPag = 0;
			$stdDetPag->indPag = 1;
			$detPag = $nfe->tagdetPag($stdDetPag);
		}

		$stdInfoAdic = new \stdClass();

		$obs = $item->observacao;

		if($somaApCredito > 0){
			if($emitente->mensagem_aproveitamento_credito != ""){
				$msg = $emitente->mensagem_aproveitamento_credito;
				$msg = str_replace("%", number_format($emitente->perc_ap_cred, 2, ",",  ".") . "%", $msg);
				$msg = str_replace('R$', 'R$ ' . number_format($somaApCredito, 2, ",",  "."), $msg);
				$obs .= $msg;
			}
		}

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

		if(trim($emitente->observacao_padrao_nfe) != ""){
			$obs .= $emitente->observacao_padrao_nfe;
		}

		if(strlen($item->referencia) >= 44){
			$obs .= "Chave referênciada: " . $item->referencia;
		}

		$stdInfoAdic->infCpl = $obs;
		$infoAdic = $nfe->taginfAdic($stdInfoAdic);

		if($emitente->aut_xml != null){
			$std = new \stdClass();
			$std->CNPJ = preg_replace('/[^0-9]/', '', $emitente->aut_xml);
			$aut = $nfe->tagautXML($std);
		}

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

	public function sign($xml){
		return $this->tools->signNFe($xml);
	}

	public function transmitir($signXml, $chave){
		try{
			$idLote = str_pad(100, 15, '0', STR_PAD_LEFT);
			$resp = $this->tools->sefazEnviaLote([$signXml], $idLote);

			$st = new Standardize();
			$std = $st->toStd($resp);
			sleep($this->timeout);
			if ($std->cStat != 103) {
				return [
					'erro' => 1,
					'error' => "[$std->cStat] - $std->xMotivo"
				];
			}
			$recibo = $std->infRec->nRec;
			
			$protocolo = $this->tools->sefazConsultaRecibo($recibo);
			sleep(1);
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
					'error' => $st->toArray($protocolo),
					'recibo' => $recibo
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

			if($arr['xMotivo'] == 'Autorizado o uso da NF-e'){
				if($nfe->estado != 'aprovado'){
					$empresa = Empresa::findOrFail($nfe->empresa_id);
					$empresa = __objetoParaEmissao($empresa, $nfe->local_id);

					$chave = $arr['protNFe']['infProt']['chNFe'];
					$nRec = $nfe->recibo;
					$protocolo = $this->tools->sefazConsultaRecibo($nRec);
					sleep(5);
					$st = new Standardize();
					$std = $st->toStd($protocolo);
					// return $std;
					if($std->protNFe->infProt->cStat == 100){
						$nfe->estado = 'aprovado';
						$nfe->save();
						if($empresa->ambiente == 1){
							$empresa->numero_ultima_nfe_producao = $nfe->numero;
						}else{
							$empresa->numero_ultima_nfe_homologacao = $nfe->numero;
						}

						$empresa->save();
						$xml = Complements::toAuthorize($nfe->signed_xml, $protocolo);
						file_put_contents(public_path('xml_nfe/').$chave.'.xml', $xml);
					}
				}
			}

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
			sleep(3);

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
					file_put_contents(public_path('xml_nfe_correcao/').$chave.'.xml', $xml);

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
			sleep(4);
			if(!isset($arr['protNFe'])){
				return ['erro' => true, 'data' => $arr, 'status' => 402];

			}
			$nProt = $arr['protNFe']['infProt']['nProt'];

			$response = $this->tools->sefazCancela($chave, $motivo, $nProt);
			sleep(1);
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
					file_put_contents(public_path('xml_nfe_cancelada/').$chave.'.xml', $xml);

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


	public function inutilizar($inutil){
		try{

			$this->tools->model($inutil->modelo);

			$nSerie = $inutil->numero_serie;
			$nIni = $inutil->numero_inicial;
			$nFin = $inutil->numero_final;
			$xJust = $inutil->justificativa;
			$response = $this->tools->sefazInutiliza($nSerie, $nIni, $nFin, $xJust);

			$stdCl = new Standardize($response);
			$arr = $stdCl->toArray();
			return $arr;

		} catch (\Exception $e) {
			return ["erro" => true, "data" => $e->getMessage()];
		}
	}

	public function consultaStatus($tpAmb, $uf){
		try{
			$response = $this->tools->sefazStatus($uf, $tpAmb);
			$stdCl = new Standardize($response);
			$arr = $stdCl->toArray();
			return $arr;
		} catch (\Exception $e) {
			echo $e->getMessage();
		}
	}

}