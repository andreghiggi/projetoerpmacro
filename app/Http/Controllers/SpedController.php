<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use NFePHP\EFD\Elements\ICMSIPI\Z0000;
use NFePHP\EFD\Elements\ICMSIPI\Z0001;
use NFePHP\EFD\Elements\ICMSIPI\Z0005;
use NFePHP\EFD\Elements\ICMSIPI\Z0100;
use NFePHP\EFD\Elements\ICMSIPI\Z0150;
use NFePHP\EFD\Elements\ICMSIPI\Z0190;
use NFePHP\EFD\Elements\ICMSIPI\Z0200;
use NFePHP\EFD\Elements\ICMSIPI\C001;
use NFePHP\EFD\Elements\ICMSIPI\C100;
use NFePHP\EFD\Elements\ICMSIPI\C170;
use NFePHP\EFD\Elements\ICMSIPI\C190;
use NFePHP\EFD\Elements\ICMSIPI\C500;
use NFePHP\EFD\Elements\ICMSIPI\C590;
use NFePHP\EFD\Elements\ICMSIPI\D001;
use NFePHP\EFD\Elements\ICMSIPI\D100;
use NFePHP\EFD\Elements\ICMSIPI\D190;
use NFePHP\EFD\Elements\ICMSIPI\E001;
use NFePHP\EFD\Elements\ICMSIPI\E100;
use NFePHP\EFD\Elements\ICMSIPI\E110;
use NFePHP\EFD\Elements\ICMSIPI\E116;
use NFePHP\EFD\Elements\ICMSIPI\H001;
use NFePHP\EFD\Elements\ICMSIPI\H005;
use NFePHP\EFD\Elements\ICMSIPI\H010;
use NFePHP\EFD\Elements\ICMSIPI\H020;
use NFePHP\EFD\Elements\ICMSIPI\H030;
use NFePHP\EFD\Elements\ICMSIPI\K001;
use NFePHP\EFD\Elements\ICMSIPI\K010;
use NFePHP\EFD\Elements\ICMSIPI\K100;
use NFePHP\EFD\Elements\ICMSIPI\K200;
use App\Models\Empresa;
use App\Models\EscritorioContabil;
use App\Models\Nfe;
use App\Models\Estoque;
use App\Models\Nfce;
use App\Models\MovimentacaoProduto;
use App\Models\EstoqueAtualProduto;
use App\Models\Produto;
use App\Models\SpedConfig;
use App\Models\Sped;
use App\Services\SpeedService;
use App\Utils\SpedUtil;

class SpedController extends Controller
{

    protected $empresa_id = null;
    protected $util = null;

    public function __construct(SpedUtil $util){
        $this->util = $util;

    }

    public function index(){
        $firstDate = date('Y-m')."-01";
        $lastDate = date("Y-m-t");

        return view('sped.index', compact('firstDate', 'lastDate'));
    }

    public function store(Request $request){

        $sped = '';
        $dataInicial = $request->data_inicial;
        $dataFinal = $request->data_final;
        $inventario = $request->inventario;

        $dataInventario = $request->data_inventario;
        $motivoInventario = $request->motivo_inventario;

        $config = Empresa::findOrFail($request->empresa_id);
        $cnpj = preg_replace('/[^0-9]/', '', $config->cpf_cnpj);

        $dInicial = \Carbon\Carbon::parse($dataInicial)->format('dmY');
        $dFinal = \Carbon\Carbon::parse($dataFinal)->format('dmY');
        $mesRef = \Carbon\Carbon::parse($dataFinal)->format('mY');

        $spedConfig = SpedConfig::where('empresa_id', $request->empresa_id)
        ->first();

        $std = new \stdClass();
        $std->COD_VER = '019';
        $std->COD_FIN = '0';
        $std->DT_INI = $dInicial;
        $std->DT_FIN = $dFinal;

        $std->NOME = $config->nome;
        $std->CNPJ = $cnpj;
        $std->CPF = '';
        $std->UF = $config->cidade->uf;
        $std->IE = $config->ie;
        $std->COD_MUN = $config->cidade->codigo;
        $std->IM = '';
        $std->SUFRAMA = '';
        $std->IND_PERFIL = 'B';
        $std->IND_ATIV = '1';

        try {
            $z0000 = new Z0000($std);
            $sped .= $z0000;
            $sped .= "\r\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        $std = new \stdClass();
        $std->IND_MOV = '0';

        try {
            $z0001 = new Z0001($std);
            $sped .= $z0001;
            $sped .= "\r\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        $std = new \stdClass();
        $std->FANTASIA = $config->nome_fantasia;
        $std->CEP = preg_replace('/[^0-9]/', '', $config->cep);
        $std->END = $config->rua;
        $std->NUM = $config->numero;
        $std->COMPL = $config->complemento;
        $std->BAIRRO = $config->bairro;
        $std->FONE = preg_replace('/[^0-9]/', '', $config->fone);
        // $std->FAX = $config->fone;
        $std->EMAIL = $config->email;

        try {
            $z0005 = new Z0005($std);
            $sped .= $z0005;
            $sped .= "\r\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        $contador = EscritorioContabil::where('empresa_id', $request->empresa_id)->first();

        if($contador == null){
            session()->flash("flash_error", "Configure o contador primeiro!");
            return redirect()->route('escritorio-contabil.index');
        }
        $std = new \stdClass();
        $std->NOME = $contador->razao_social;
        $doc = preg_replace('/[^0-9]/', '', $contador->cnpj);
        $std->CNPJ = $doc;

        if($contador->cpf != ""){
            $std->CPF = preg_replace('/[^0-9]/', '', $contador->cpf);
        }

        $std->CRC = $contador->crc;
        $std->CEP = preg_replace('/[^0-9]/', '', $contador->cep);
        $std->END = $contador->logradouro;
        $std->NUM = $contador->numero;
        // $std->COMPL = $contador->contabilidade_xCpl;
        $std->BAIRRO = $contador->bairro;
        $fone = preg_replace('/[^0-9]/', '', $contador->fone);
        $std->FONE = $fone;
        // $std->FAX = $fone;
        $std->EMAIL = $contador->email;
        $std->COD_MUN = $contador->cidade->codigo;

        try {
            $z0100 = new Z0100($std);
            $sped .= $z0100;
            $sped .= "\r\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        $vendas = Nfe::whereDate('data_emissao', '>=', $dataInicial)
        ->whereDate('data_emissao', '<=', $dataFinal)
        ->where('estado', 'aprovado')
        ->where('empresa_id', $request->empresa_id)
        ->where('tpNF', 1)
        ->get();

        $vendasPdv = Nfce::whereDate('data_emissao', '>=', $dataInicial)
        ->whereDate('data_emissao', '<=', $dataFinal)
        ->where('estado', 'aprovado')
        ->where('empresa_id', $request->empresa_id)
        ->get();

        $compras = Nfe::whereDate('data_emissao', '>=', $dataInicial)
        ->whereDate('data_emissao', '<=', $dataFinal)
        ->where('estado', 'aprovado')
        ->where('empresa_id', $request->empresa_id)
        ->where('tpNF', 0)
        ->where('chave_importada', '')
        ->get();

        $comprasImportadas = Nfe::whereDate('data_emissao', '>=', $dataInicial)
        ->whereDate('data_emissao', '<=', $dataFinal)
        // ->where('estado', 'aprovado')
        ->where('empresa_id', $request->empresa_id)
        ->where('tpNF', 0)
        ->where('chave_importada', '!=' ,'')
        ->get();

        // dd($comprasImportadas);
        

        $comprasManifesto = [];
        $produtosAdicionados = [];
        $unidadesAdicionadas = [];

        $speedService = new SpeedService($config);
        $dataXml = [];
        foreach($vendas as $v){
            $xml = $speedService->getXml($v, 'xml_nfe/');
            $temp = [
                'xml_importado' => 0,
                'tipo' => 'venda',
                'xml' => $xml
            ];
            if($xml != null){
                array_push($dataXml, $temp);
            }
        }

        foreach($vendasPdv as $v){
            $xml = $speedService->getXml($v, 'xml_nfce/');
            if($xml != null){
                $temp = [
                    'xml_importado' => 0,
                    'tipo' => 'pdv',
                    'xml' => $xml
                ];
                array_push($dataXml, $temp);
            }
        }

        foreach($compras as $v){
            $xml = $speedService->getXml($v, 'xml_entrada_emitida/');
            if($xml != null){
                $temp = [
                    'xml_importado' => 0,
                    'tipo' => 'compra',
                    'xml' => $xml
                ];
                array_push($dataXml, $temp);
            }
        }

        foreach($comprasImportadas as $v){
            $xml = $speedService->getXml($v, 'xml_entrada/', 'chave_importada');
            if($xml != null){
                $temp = [
                    'xml_importado' => 1,
                    'tipo' => 'compra',
                    'xml' => $xml,
                    'compra' => $v
                ];
                array_push($dataXml, $temp);
            }
        }

        foreach($comprasManifesto as $v){
            $xml = $speedService->getXml($v, 'xml_dfe/', 'chave_importada');
            if($xml != null){
                $temp = [
                    'xml_importado' => 1,
                    'tipo' => 'compra',
                    'xml' => $xml,
                    'compra' => $v
                ];
                array_push($dataXml, $temp);
            }
        }

        $destAdicionados = [];
        $codPart = 1;

        $dataDestinatarios = [];
        foreach($dataXml as $l){
            $mod = null;
            try{
                $mod = $l['xml']->NFe->infNFe->ide->mod;
            }catch(\Exception $e){

            }
            if($mod == 55){

                if($l['tipo'] == 'compra'){
                    $destinatario = $speedService->getEmitente($l['xml']);
                }else{
                    $destinatario = $speedService->getDestinatario($l['xml']);
                }

                $docDestinatario = isset($destinatario->CNPJ) ? $destinatario->CNPJ : $destinatario->CPF;

            // dd($destinatario);
                $docDestinatario = (string)$docDestinatario;
                if (!in_array($docDestinatario, $destAdicionados)){

                    array_push($destAdicionados, $docDestinatario);
                    if($l['tipo'] == 'compra'){
                        $clienteDatabase = $this->util->getFornecedor($docDestinatario, $this->empresa_id);
                    }else{
                        $clienteDatabase = $this->util->getCliente($docDestinatario, $this->empresa_id);
                    }

                    // $codPart = rand(0, 100000);
                    // if($clienteDatabase != null){
                    //     $codPart = $clienteDatabase->id;
                    // }
                    $temp = [
                        'COD_PART' => (string)$codPart,
                        'DOC' => (string)$docDestinatario
                    ];
                    array_push($dataDestinatarios, $temp);
                    // echo "sim";

                    $std = new \stdClass();
                    $std->COD_PART = (string)$codPart;
                    $std->COD_PAIS = '1058';
                    $std->NOME = (string)$destinatario->xNome;
                    if (strlen($docDestinatario) == 11){
                        $std->CPF = $docDestinatario;
                    }else{
                        $std->CNPJ = $docDestinatario;
                    }
                    if($l['tipo'] == 'compra'){
                        $endereco = $destinatario->enderEmit;
                    }else{
                        $endereco = $destinatario->enderDest;
                    }

                    $std->IE = (string)$destinatario->IE;
                    $std->COD_MUN = (string)$endereco->cMun;
                    $std->SUFRAMA = '';
                    $std->END = (string)$endereco->xLgr;
                    $std->NUM = (string)$endereco->nro;
                    $std->COMPL = '';
                    $std->BAIRRO = (string)$endereco->xBairro;

                    try {
                        $z0150 = new Z0150($std);
                        $sped .= $z0150;
                        $sped .= "\r\n";
                    } catch (\Exception $e) {
                        echo $e->getMessage();
                    }
                    $codPart++;
                }
            }
        }
        // dd($dataDestinatarios);

        $sz0200 = '';
        $sz0190 = '';
        $uncom = [];

        $estoque = [];

        foreach($dataXml as $l){
            $itens = $speedService->getItemNfe($l['xml']);

            foreach($itens as $item){

                $prod = $item->prod;
                $imposto = $item->imposto;
                $cProd = (string)$prod->cProd;

                $cProd = str_replace(".", "_", $cProd);
                $cProd = str_replace("/", "_", $cProd);
                $cProd = str_replace("'", "_", $cProd);
                $cProd = str_replace("-", "_", $cProd);
                $cProd = str_replace("(", "", $cProd);
                $cProd = str_replace(")", "", $cProd);
                $cProd = str_replace(" ", "", $cProd);
                $cProd = str_replace(":", "", $cProd);
                $cProd = str_replace("[", "", $cProd);
                $cProd = str_replace("]", "", $cProd);
                $cProd = str_replace("'", "", $cProd);
                // $estoqueProduto = Estoque::where('produto_id', $cProd)->first();

                $p = Produto::where('referencia_xml', $cProd)->where('empresa_id', $request->empresa_id)
                ->first();

                $estoqueProduto = null;
                if($p){
                    $estoqueProduto = $p->estoque;
                }else{
                    $xProd = (string)$prod->xProd;

                    $p = Produto::where('nome', $xProd)->where('empresa_id', $request->empresa_id)
                    ->first();
                    if($p){
                        $estoqueProduto = $p->estoque;
                    }
                }

                // $spedConfig && $spedConfig->gerar_bloco_k ver depois
                if($l['xml_importado'] == 1 || (($estoqueProduto != null && $estoqueProduto->quantidade > 0) && $inventario == 1)){

                    $arr = (array_values((array)$imposto->ICMS));
                    $cst_csosn = $arr[0]->CST ? $arr[0]->CST : $arr[0]->CSOSN;
                    $pICMS = $arr[0]->pICMS ?? 0;
                    $unid = strtoupper((string)$prod->uCom);
                    if (!in_array($unid, $unidadesAdicionadas)){
                        array_push($unidadesAdicionadas, $unid);
                        $std = new \stdClass();

                        $std->UNID = $unid;
                        // if($prod->uCom == 'UNID'){
                        //     $prod->uCom = 'UN';
                        // }
                        $std->DESCR = 'UNIDADE DE MEDIDA ' . $unid;

                        try {
                            $z0190 = new Z0190($std);
                            $sz0190 .= $z0190;
                            $sz0190 .= "\r\n";
                        } catch (\Exception $e) {
                            echo $e->getMessage();
                        }
                    }

                    if (!in_array((string)$prod->cProd, $produtosAdicionados)){
                        array_push($produtosAdicionados, (string)$prod->cProd);
                        $qtdEstoque = 0;

                        $cProd = (string)$prod->cProd;

                        $cProd = str_replace(".", "_", $cProd);
                        $cProd = str_replace("/", "_", $cProd);
                        $cProd = str_replace("'", "_", $cProd);
                        $cProd = str_replace("-", "_", $cProd);
                        $cProd = str_replace("(", "", $cProd);
                        $cProd = str_replace(")", "", $cProd);
                        $cProd = str_replace(" ", "", $cProd);
                        $cProd = str_replace(":", "", $cProd);
                        $cProd = str_replace("[", "", $cProd);
                        $cProd = str_replace("]", "", $cProd);
                        $cProd = str_replace("'", "", $cProd);

                        $p = Produto::where('referencia_xml', $cProd)->where('empresa_id', $request->empresa_id)
                        ->first();

                        $pId = null;
                        if($p == null){
                            $xProd = (string)$prod->xProd;
                            $p = Produto::where('nome', $xProd)->where('empresa_id', $request->empresa_id)
                            ->first();
                            if($p){
                                $estoqueProduto = $p->estoque;
                            }
                        }

                        if($p != null){
                            $pId = $p->id;
                        }

                        if($p && $p->estoque){
                            $qtdEstoque = $p->estoque->quantidade;
                        }

                        $estoque[] = ["produto" => (string)$prod->cProd, "quantidade" => $qtdEstoque, "id" => $pId];
                        $std = new \stdClass();

                        $codBarras = (string)$prod->cEAN;
                        $std->COD_ITEM = strtoupper((string)$prod->cProd);
                        // dd((string)$prod->cProd);
                        $std->DESCR_ITEM = (string)$prod->xProd;
                        $std->COD_BARRA = $codBarras;
                        $std->COD_ANT_ITEM = '';

                        if ($prod->uCom == ''){
                            $std->UNID_INV = 'UN';
                        }else{                    
                            $std->UNID_INV = strtoupper((string)$prod->uCom);
                        }

                        $std->TIPO_ITEM = '00';

                        if($p == null){
                            $p = Produto::where('nome', $std->DESCR_ITEM)->where('empresa_id', $request->empresa_id)->first();
                        }
                        if($p != null){
                            if($p->tipo_item_sped){
                                $std->TIPO_ITEM = $p->tipo_item_sped;
                            }
                        }

                        $std->COD_NCM = (string)$prod->NCM;
                        $std->EX_IPI = '';
                        $std->COD_GEN = substr($prod->NCM, 0, 2);
                        $std->COD_LST = '';
                        $std->ALIQ_ICMS = (float)$pICMS;
                        $std->CEST = trim((string)$prod->CEST);

                        try {

                            $z0200 = new Z0200($std);
                            $sz0200 .= $z0200;
                            $sz0200 .= "\r\n";
                        } catch (\Exception $e) {
                            echo $e->getMessage();
                        }
                    }
                }
            }
        }

        $sped .= $sz0190;
        $sped .= $sz0200;
        $total000 = $this->totalizeBloco($sped, '0');
        $sped .= '|0990|' . $total000 . '|';
        $sped .= "\r\n";

        $sped .= '|B001|1|';
        $sped .= "\r\n";
        $sped .= '|B990|2|';
        $sped .= "\r\n";

        $std = new \stdClass();
        $std->IND_MOV = '0';
        try {
            $c001 = new C001($std);
            $sped .= $c001;
            $sped .= "\r\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        //FIM C001
        $somaICMSSaida = 0;
        $somaICMSEntrada = 0;
        foreach($dataXml as $key => $l){
            $dataC190 = [];

            $ide = $speedService->getIde($l['xml']);
            $chave = $speedService->getChave($l['xml']);

            $total = $speedService->getTotal($l['xml']);
            $destinatario = $speedService->getDestinatario($l['xml']);
            if($l['tipo'] == 'compra'){
                $destinatario = $speedService->getEmitente($l['xml']);
            }

            $emit = $speedService->getEmitente($l['xml']);
            $docEmit = isset($emit->CNPJ) ? (string)$emit->CNPJ : (string)$emit->CPF;

            $std = new \stdClass();
            // echo $ide->tpNF . "<br>";

            $std->IND_OPER = $l['tipo'] == 'compra' ? 0 : 1;
            $std->IND_EMIT = $cnpj == $docEmit ? 0 : 1;

            $std->COD_PART = null;
            $std->VL_BC_ICMS_ST = null;
            $std->VL_ICMS_ST = null;
            $std->VL_IPI = null;
            $std->VL_PIS = null;
            $std->VL_COFINS = null;
            $std->VL_PIS_ST = null;
            $std->VL_COFINS_ST = null;

            if ($ide->mod == '65'){
                $std->IND_OPER = 1;
                $std->COD_PART = null;
                $std->VL_BC_ICMS_ST = null;
                $std->VL_ICMS_ST = null;
                $std->VL_IPI = null;
                $std->VL_PIS = null;
                $std->VL_COFINS = null;
                $std->VL_PIS_ST = null;
                $std->VL_COFINS_ST = null;

                $dhEmi = substr((string)$ide->dhEmi, 0, 10);
                $dhSaiEnt = substr((string)$ide->dhEmi, 0, 10);
            }else{

                $codPart = null;
                $docDestinatario = isset($destinatario->CNPJ) ? $destinatario->CNPJ : $destinatario->CPF;

                foreach($dataDestinatarios as $d){
                    if($docDestinatario == $d['DOC']){
                        $codPart = $d['COD_PART'];
                    }
                }
                $std->COD_PART = $codPart;
                $std->VL_BC_ICMS_ST = (float)$total->vBCST;
                $std->VL_ICMS_ST = (float)$total->vST;
                $std->VL_IPI = (float)$total->vIPI;
                $std->VL_PIS = (float)$total->vPIS;
                $std->VL_COFINS = (float)$total->vCOFINS;
                $std->VL_PIS_ST = '0.00';
                $std->VL_COFINS_ST = '0.00';

                $dhEmi = substr((string)$ide->dhEmi, 0, 10);
                $dhSaiEnt = substr((string)$ide->dhSaiEnt, 0, 10);
                if($l['tipo'] == 'compra'){
                    $compra = $this->getCompra($chave, $l['xml_importado']);
                    if($compra){
                        $dhSaiEnt = substr((string)$compra->created_at, 0, 10);
                    }
                }
            }

            if ($ide->finNFe == '2'){
                $std->COD_SIT = '06';
            }else{
                $std->COD_SIT = '00';
            }
            $std->COD_MOD = (string)$ide->mod;
            $std->COD_SIT = '00';
            $std->SER = (string)$ide->serie;
            $std->NUM_DOC = (string)$ide->nNF;
            $std->CHV_NFE = $chave;

            if(isset($l['compra'])){
                $dhEmi = \Carbon\Carbon::parse($l['compra']->data_emissao)->format('dmY');
                $dhSaiEnt = \Carbon\Carbon::parse($l['compra']->data_emissao)->format('dmY');
            }else{
                $dhEmi = \Carbon\Carbon::parse($dhEmi)->format('dmY');
                $dhSaiEnt = \Carbon\Carbon::parse($dhSaiEnt)->format('dmY');
            }

            $std->DT_DOC = $dhEmi;
            $std->DT_E_S = $dhSaiEnt;
            $std->VL_DOC = (float)$total->vNF;

            $std->IND_PGTO = '2';
            $std->VL_DESC = (float)$total->vDesc;

            $std->VL_ABAT_NT = '0.00';
            $std->VL_MERC = (float)$total->vProd;
            $std->IND_FRT = '3';
            $std->VL_FRT = (float)$total->vFrete;
            $std->VL_SEG = (float)$total->vSeg;
            $std->VL_OUT_DA = (float)$total->vOutro;
            $std->VL_BC_ICMS = (float)$total->vBC;
            $std->VL_ICMS = (float)$total->vICMS;
            if($std->VL_ICMS > 0 && $l['tipo'] != 'compra'){
                $somaICMSSaida += $std->VL_ICMS;
            }
            if($std->VL_ICMS > 0 && $l['tipo'] == 'compra'){
                $somaICMSEntrada += $std->VL_ICMS;
            }
            try {
                $c100 = new C100($std);
                $sped .= $c100;
                $sped .= "\r\n";
            } catch (\Exception $e) {
                echo $e->getMessage();
            }

            $itens = $speedService->getItemNfe($l['xml']);

            $cont = 0;
            //c170
            if ($l['xml_importado'] == 1){
                foreach($itens as $item){

                    $cont++;
                    $prod = $item->prod;
                    $imposto = $item->imposto;
                    $std = new \stdClass();

                    $std->NUM_ITEM = $cont;
                    $std->COD_ITEM = strtoupper((string)$prod->cProd);
                    $std->DESCR_COMPL = strtoupper((string)$prod->xProd);
                    $std->QTD = (float)$prod->qCom;
                    $std->UNID = strtoupper((string)$prod->uCom);

                    $std->VL_ITEM = (float)$prod->vProd;
                    $std->VL_DESC = (float)$prod->vDesc;
                    $std->IND_MOV = '0';
                    $arr = (array_values((array)$imposto->ICMS));
                    $vBC = isset($arr[0]->vBC) ? (float)$arr[0]->vBC : 0;
                    $vBCST = isset($arr[0]->vBCST) ? (float)$arr[0]->vBCST : 0;
                    $vICMSST = isset($arr[0]->vICMSST) ? (float)$arr[0]->vICMSST : 0;
                    $pICMSST = isset($arr[0]->pICMSST) ? (float)$arr[0]->pICMSST : 0;
                    $vICMS = isset($arr[0]->vICMS) ? (float)$arr[0]->vICMS : 0;
                    $pRedBC = isset($arr[0]->pRedBC) ? (float)$arr[0]->pRedBC : 0;
                    $cst_csosn = $arr[0]->CST ? $arr[0]->CST : $arr[0]->CSOSN;
                    $cst_csosn = (string)$cst_csosn;
                    if(strlen($cst_csosn) == 2){
                        $cst_csosn = "0".$cst_csosn;
                    }
                    $std->CST_ICMS = $cst_csosn;
                    $cfop = $this->util->trataCfop((string)$prod->CFOP, $l['tipo'], $l['xml_importado']);
                    $std->CFOP = $cfop;

                    $cfopIsentoTributacao = $this->util->cfopIsentoTributacao($cfop);
                    $std->COD_NAT = '';
                    $std->VL_BC_ICMS = $cfopIsentoTributacao ? 0 : $vBC;
                    $pICMS = $arr[0]->pICMS ?? 0;

                    $std->ALIQ_ICMS = $cfopIsentoTributacao ? 0 : number_format((float)$pICMS, 2, '.', '');
                    $std->VL_ICMS = $cfopIsentoTributacao ? 0 : $vICMS;
                    $std->VL_BC_ICMS_ST = $vBCST;

                    $std->ALIQ_ST = $pICMSST;
                    $std->VL_ICMS_ST = $vICMSST;
                    $std->IND_APUR = '0';

                    $std->COD_ENQ = null;
                    $arr = (array_values((array)$imposto->IPI));
                    if(isset($arr[1])){
                        $cst_ipi = (string)$arr[1]->CST ?? '99';
                        $cst_ipi = $this->converterCstIpi($cst_ipi);
                        $std->CST_IPI = $cfopIsentoTributacao ? '49' : $cst_ipi;
                        $pIPI = $arr[0]->IPI ?? 0;

                        if(isset($arr[1]->pIPI)){
                            $pIPI = $arr[1]->pIPI ?? 0;
                        }else{
                            if(isset($arr[4]->pIPI)){
                                $ipi = $arr[4]->CST;
                                $pIPI = $arr[4]->pIPI;
                            }else{
                                $pIPI = 0;
                            }
                        }
                        $std->ALIQ_IPI = $cfopIsentoTributacao ? 0 : (float)$pIPI;
                        $std->VL_BC_IPI = $cfopIsentoTributacao ? 0 : (string)$arr[1]->vBC;
                        $std->VL_IPI = $cfopIsentoTributacao ? 0 : (float)$arr[1]->vIPI;

                    }

                    $arr = (array_values((array)$imposto->PIS));

                    $std->CST_PIS = $cfopIsentoTributacao ? '98' : (string)$arr[0]->CST;
                    $std->VL_BC_PIS = $cfopIsentoTributacao ? 0 : (float)$arr[0]->vBC;
                    $std->ALIQ_PIS = $cfopIsentoTributacao ? 0 : (float)$arr[0]->pPIS;

                    $std->QUANT_BC_PIS = '0.00';
                    $std->ALIQ_PIS_QUANT = '0.00';
                    $std->VL_PIS = $cfopIsentoTributacao ? 0 : (float)$arr[0]->vPIS;

                    $arr = (array_values((array)$imposto->COFINS));
                    $std->CST_COFINS = $cfopIsentoTributacao ? '98' : (string)$arr[0]->CST;
                    $std->VL_BC_COFINS = $cfopIsentoTributacao ? 0 : (float)$arr[0]->vBCCOFINS;
                    $std->ALIQ_COFINS = $cfopIsentoTributacao ? 0 : (float)$arr[0]->pCOFINS;
                    $std->QUANT_BC_COFINS = '0.00';
                    $std->ALIQ_COFINS_QUANT = '0.00';
                    $std->VL_COFINS = $cfopIsentoTributacao ? 0 : (float)$arr[0]->vCOFINS;
                    $std->COD_CTA = '';
                    $std->VL_ABAT_NT = '0.00';
                    // dd($cfopIsentoTributacao);

                    try {
                        $c170 = new C170($std);

                        $sped .= $c170;
                        $sped .= "\r\n";
                    } catch (\Exception $e) {
                        echo $e->getMessage();
                    }
                }
            }else{

            }

            //fim c170

            // if ($ide->tpNF == 1){
            foreach($itens as $item){

                $prod = $item->prod;
                $imposto = $item->imposto;
                $std = new \stdClass();

                $arr = (array_values((array)$imposto->ICMS));
                $vBC = isset($arr[0]->vBC) ? (float)$arr[0]->vBC : 0;

                $vBCST = isset($arr[0]->vBCST) ? (float)$arr[0]->vBCST : 0;
                $vICMSST = isset($arr[0]->vICMSST) ? (float)$arr[0]->vICMSST : 0;
                $vICMS = isset($arr[0]->vICMS) ? (float)$arr[0]->vICMS : 0;
                $pRedBC = isset($arr[0]->pRedBC) ? (float)$arr[0]->pRedBC : 0;
                $cst_csosn = $arr[0]->CST ? $arr[0]->CST : $arr[0]->CSOSN;
                $cst_csosn = (string)$cst_csosn;
                if(strlen($cst_csosn) == 2){
                    $cst_csosn = "0".$cst_csosn;
                }

                $std->CST_ICMS = $cst_csosn;
                $cfop = $this->util->trataCfop((string)$prod->CFOP, $l['tipo'], $l['xml_importado']);
                $std->CFOP = $cfop;
                $cfopIsentoTributacao = $this->util->cfopIsentoTributacao($cfop);
                // $std->CFOP = (string)$prod->CFOP;
                $pICMS = $arr[0]->pICMS ?? 0;
                $std->ALIQ_ICMS = number_format((float)$pICMS, 2, '.', '');

                $arr = (array_values((array)$imposto->IPI));
                $vIPI = 0;
                if(isset($arr[1])){
                    $vIPI = (float)$arr[1]->vIPI ?? 0;
                }
                $std->VL_OPR = (float)$prod->vProd;
                $std->VL_BC_ICMS = $vBC;
                $std->VL_ICMS = $cfopIsentoTributacao ? 0 : $vICMS;
                $std->VL_BC_ICMS_ST = $vBCST;
                $std->VL_ICMS_ST = $vICMSST;
                $std->VL_RED_BC = $pRedBC;
                $std->VL_IPI = $vIPI;
                $std->COD_OBS = '';
                // echo $std->CFOP . "<br>";
                $dataC190 = $this->util->updateOrCreateC190($dataC190, $std);
                // $dataC190[] = $std;
            }

            foreach($dataC190 as $std){
                try {
                    $c190 = new C190($std);

                    $sped .= $c190;
                    $sped .= "\r\n";
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }

            // }
            // $total000 = $this->totalizeBloco($sped, 'C');
            // $sped .= '|C990|' . $total000 . '|';
            // $sped .= "\r\n";

            // dd($total);
        }
        // dd($dataC190);


        $total000 = $this->totalizeBloco($sped, 'C');
        $sped .= '|C990|' . $total000 . '|';
        $sped .= "\r\n";


        $std = new \stdClass();
        $std->IND_MOV = 1;

        try {
            $D001 = new D001($std);
            $sped .= $D001;
            $sped .= "\r\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }


        $total000 = $this->totalizeBloco($sped, 'D');
        $sped .= '|D990|' . $total000 . '|';
        $sped .= "\r\n";

        $std = new \stdClass();
        $std->IND_MOV = '0';

        try {
            $E001 = new E001($std);
            $sped .= $E001;
            $sped .= "\r\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        $std = new \stdClass();
        $std->DT_INI = $dInicial;
        $std->DT_FIN = $dFinal;

        try {
            $E100 = new E100($std);
            $sped .= $E100;
            $sped .= "\r\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        // inicio e110

        $saldoCredor = 0;
        $mes = (int)substr($mesRef, 0, 2);
        $ano = (int)substr($mesRef, 2, 4);
        if($mes == 1){
            $ano--;
            $mes = 12;
        }else if($mes == 2){
            $ano++;
            $mes = 1;
        }else{
            $mes--;
        }

        if($mes < 10){
            $mes = "0".$mes;
        }
        $mes = (string)$mes;
        $ref = ($mes.$ano);
        $saldoAnterior = Sped::where('empresa_id', $request->empresa_id)->where('data_refrencia', $ref)
        ->first();

        $std = new \stdClass();

        $std->VL_TOT_DEBITOS = $somaICMSSaida;
        $std->VL_AJ_DEBITOS = 0;
        $std->VL_TOT_AJ_DEBITOS = 0;
        $std->VL_ESTORNOS_CRED = 0;
        $std->VL_TOT_CREDITOS = $somaICMSEntrada;
        $std->VL_AJ_CREDITOS = 0;
        $std->VL_TOT_AJ_CREDITOS = 0;
        $std->VL_ESTORNOS_DEB = 0;
        $std->VL_SLD_CREDOR_ANT = 0;
        if($saldoAnterior != null && $saldoAnterior->saldo_credor > 0){
            $std->VL_SLD_CREDOR_ANT = $saldoAnterior->saldo_credor;
        }

        $VLOP = ($std->VL_TOT_DEBITOS - $std->VL_TOT_CREDITOS) + $std->VL_SLD_CREDOR_ANT;
        if($VLOP < 0){
            $VLOP = $VLOP *-1;
        }
        $VL_OR = $std->VL_SLD_APURADO = $VLOP;
        $std->VL_TOT_DED = 0;
        $std->VL_ICMS_RECOLHER = $std->VL_SLD_APURADO;
        $std->VL_SLD_CREDOR_TRANSPORTAR = 0;
        $std->DEB_ESP = 0;

        // dd($std);
        try {
            $E110 = new E110($std);

            $sped .= $E110;
            $sped .= "\r\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        if($std->VL_TOT_CREDITOS > $std->VL_TOT_DEBITOS){
            $saldoCredor = $std->VL_TOT_CREDITOS - $std->VL_TOT_DEBITOS;
        }

        // inicio e116

        $mes = (int)substr($mesRef, 0, 2);
        $ano = date('Y');
        if($mes == 12){
            $mes = 1;
            $ano++;
        }else{
            $mes++;
        }

        $mes = $mes < 10 ? "0".$mes : (string)$mes;
        if($std->VL_TOT_DEBITOS > $std->VL_TOT_CREDITOS){
            $std = new \stdClass();
            $std->COD_OR = $spedConfig->codigo_obrigacao;
            $std->VL_OR = $VL_OR;
            $std->DT_VCTO = $spedConfig->data_vencimento.$mes.$ano;
            $std->COD_REC = $spedConfig ? $spedConfig->codigo_receita : '';
            $std->MES_REF = $mesRef;
            // dd($std);
            try {
                $E116 = new E116($std);

                $sped .= $E116;
                $sped .= "\r\n";
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }

        $total000 = $this->totalizeBloco($sped, 'E');
        $sped .= '|E990|' . $total000 . '|';
        $sped .= "\r\n";

        $sped .= '|G001|1|';
        $sped .= "\r\n";
        $sped .= '|G990|2|';
        $sped .= "\r\n";

        if($inventario == 0){

            $sped .= '|H001|1|';
            $sped .= "\r\n";
            $sped .= '|H990|2|';
            $sped .= "\r\n";
        }else{
            $somaEstoque = $this->somaEstoque();

            // $sped .= '|H001|0|';
            // $sped .= "\r\n";
            $std = new \stdClass();
            $std->IND_MOV = 0;
            $H001 = new H001($std);
            $sped .= $H001;
            $sped .= "\r\n";

            $std = new \stdClass();
            $std->DT_INV = \Carbon\Carbon::parse($dataInventario)->format('dmY');
            $std->VL_INV = number_format($somaEstoque, 2, '.', '');
            $std->MOT_INV = $motivoInventario;
            $H005 = new H005($std);
            $sped .= $H005;
            $sped .= "\r\n";

            $itensDoEstoque = $this->getItensEstoque();
            // dd($itensDoEstoque->pluck('produto_id'));
            foreach($itensDoEstoque as $i){
                $std = new \stdClass();
                $std->COD_ITEM = $i->produto->id;
                $std->UNID = $i->produto->unidade_venda;
                $std->QTD = number_format($i->quantidade, 2, '.', '');
                $std->VL_UNIT = number_format($i->valor_compra, 2, '.', '');
                $std->VL_ITEM = number_format($i->quantidade*$i->valor_compra, 2, '.', '');
                $std->IND_PROP = 1;
                $std->COD_PART = $cnpj;
                $std->TXT_COMPL = $i->produto->nome;
                $std->COD_CTA = $spedConfig->codigo_conta_analitica;
                $H010 = new H010($std);

                $sped .= $H010;
                $sped .= "\r\n";
            }


            // $std = new \stdClass();
            // $std->COD_ITEM = $i->produto->id;
            // $H020 = new H020($std);
            // $sped .= $H020;
            // $sped .= "\r\n";
        }

        if($spedConfig && $spedConfig->gerar_bloco_k){

            $std = new \stdClass();
            $std->ind_mov = 0;
            try {
                $K001 = new K001($std);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        //FIM K001

        //GERA K100
            $std = new \stdClass();
            $std->DT_INI = $dInicial;
            $std->DT_FIN = $dFinal;

            try {
                $K100 = new K100($std);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }

            $k200a = '';
            $gk = 'N';

            foreach ($estoque as $item){

                if($item['quantidade'] > 0){

                    $qtd = $item['quantidade'];

                    $estoqueAtualProduto = EstoqueAtualProduto::where('produto_id', $item['id'])
                    ->whereDate('created_at', '>=', $request->data_inicial)
                    ->whereDate('created_at', '<=', $request->data_final)
                    ->orderBy('id', 'desc')
                    ->first();
                    if($estoqueAtualProduto){
                        $qtd = $estoqueAtualProduto->quantidade;
                    }
                    $gk = 'S';
                    $std = new \stdClass();
                    $std->DT_EST = $dFinal;
                    $std->COD_ITEM = $item['produto'];
                    $std->QTD = $qtd;
                    $std->IND_EST = 0;
                    $std->COD_PART = null;

                    try {
                        $K200 = new K200($std);
                        $k200a .= $K200;
                        $k200a .= "\r\n";
                    } catch (\Exception $e) {
                        echo $e->getMessage();
                    }
                }
            }

            if ($gk === 'S'){

                try {
                    $std = new \stdClass();
                    $std->ind_tp_leiaute = $spedConfig->layout_bloco_k;

                    $K010 = new K010($std);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }

                $sped .= $K001;
                $sped .= "\r\n";

                $sped .= $K010;
                $sped .= "\r\n";

                $sped .= $K100;
                $sped .= "\r\n";

                $sped .= $k200a;

                $total000 = $this->totalizeBloco($sped, 'K');
                $sped .= '|K990|' . $total000 . '|';
                $sped .= "\r\n";

            }else{
                $sped .= '|K001|1|';
                $sped .= "\r\n";
                $sped .= '|K990|2|';
                $sped .= "\r\n";
            }
        }else{
            $sped .= '|K001|1|'; // ver aqui
            $sped .= "\r\n";
            $sped .= '|K990|2|';
            $sped .= "\r\n";
        }

        //BLOCO 1001 VAZIO

        $sped .= '|1001|0|';
        $sped .= "\r\n";
        $sped .= '|1010|N|N|N|N|N|N|N|N|N|N|N|N|N|';
        $sped .= "\r\n";
        $sped .= "|1990|3|";
        $sped .= "\r\n";

        $total = $this->totalize($sped);
        $sped .= $total;

        $arquivo = fopen(public_path("sped_files/")."SPED-EFD-" . $cnpj . ".txt", "w");
        fwrite($arquivo, $sped);
        fclose($arquivo);

        // echo $sped;
        // dd($sped);

        $spedRegistro = Sped::where('empresa_id', $request->empresa_id)->where('data_refrencia', $mesRef)
        ->first();
        if($spedRegistro != null){
            $spedRegistro->delete();
        }

        Sped::create([
            'data_refrencia' => $mesRef,
            'empresa_id' => $request->empresa_id,
            'saldo_credor' => $saldoCredor
        ]);

        return response()->download(public_path("sped_files/")."SPED-EFD-" . $cnpj . ".txt");
    }

    private function totalizeBloco($efd, $bloco) {
        $tot = '';
        $keys = [];
        $aefd = explode("\n", $efd);
        foreach ($aefd as $element) {
            $param = explode("|", $element);
            if (!empty($param[1])) {
                $key = $param[1];
                if (!empty($keys[$key])) {
                    $keys[$key] += 1;
                } else {
                    $keys[$key] = 1;
                }
            }
        }
        $tot = 0;
        foreach ($keys as $key => $value) {
            if (!empty($key)) {
                if (substr($key, 0, 1) == $bloco) {
                    $tot = $tot + $value;
                }
            }
        }

        return $tot + 1;
    }

    function totalize($efd) {
        $tot = '';
        $keys = [];
        $aefd = explode("\n", $efd);
        foreach ($aefd as $element) {
            $param = explode("|", $element);
            if (!empty($param[1])) {
                $key = $param[1];
                if (!empty($keys[$key])) {
                    $keys[$key] += 1;
                } else {
                    $keys[$key] = 1;
                }
            }
        }

        $tot .= "|9001|0|\n";
        $n = 0;
        // dd($keys);
        foreach ($keys as $key => $value) {
            if (!empty($key)) {
                $tot .= "|9900|$key|$value|\n";
                $n++;
            }
        }
        $n++;
        $tot .= "|9900|9001|1|\n";
        $tot .= "|9900|9900|" . ($n + 3) . "|\n";
        $tot .= "|9900|9990|1|\n";
        $tot .= "|9900|9999|1|\n";
        $tot .= "|9990|" . ($n + 6) . "|\n";
        $efd .= $tot;
        $n = count(explode("\n", $efd));
        $tot .= "|9999|$n|\n";
        return $tot;
    }

    private function somaEstoque(){
        $produtos = Estoque::select('quantidade', 'valor_compra')
        ->where('empresa_id', $request->empresa_id)->get();
        $soma = 0;
        foreach($produtos as $p){
            $soma += $p->quantidade * $p->valor_compra;
        }
        return $soma;
    }

    private function getItensEstoque(){
        return Estoque::where('empresa_id', $request->empresa_id)
        ->distinct('produto_id')
        ->get();
    }

    private function getCompra($chave, $importado){
        $item = Nfe::where($importado ? 'chave' : 'chave_importada', $chave)->where('empresa_id', request()->empresa_id)
        ->first();
        return $item;
    }

    private function converterCstIpi($cst_ipi){
        if($cst_ipi == '50') return '00';
        else if($cst_ipi == '51') return '01';
        else if($cst_ipi == '52') return '02';
        else if($cst_ipi == '53') return '03';
        else if($cst_ipi == '54') return '04';
        else if($cst_ipi == '55') return '05';
        else if($cst_ipi == '99') return '49';
        else return $cst_ipi;
    }
}
