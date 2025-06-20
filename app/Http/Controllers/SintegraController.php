<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Nfe;
use App\Models\Nfce;
use App\Models\EscritorioContabil;
use App\Utils\SintegraUtil;

class SintegraController extends Controller
{
    protected $util = null;
    public function __construct(SintegraUtil $util){
        $this->util = $util;
    }

    private function soNumero($str) {
        return preg_replace("/[^0-9]/", "", $str);
    }

    private function removeAcentos($texto) {
        $aFind = array('&', 'á', 'à', 'ã', 'â', 'é', 'ê',
            'í', 'ó', 'ô', 'õ', 'ú', 'ü', 'ç', 'Á', 'À', 'Ã', 'Â',
            'É', 'Ê', 'Í', 'Ó', 'Ô', 'Õ', 'Ú', 'Ü', 'Ç');
        $aSubs = array('e', 'a', 'a', 'a', 'a', 'e', 'e',
            'i', 'o', 'o', 'o', 'u', 'u', 'c', 'A', 'A', 'A', 'A',
            'E', 'E', 'I', 'O', 'O', 'O', 'U', 'U', 'C');
        $novoTexto = str_replace($aFind, $aSubs, $texto);
        $novoTexto = preg_replace("/[^a-zA-Z0-9 @,-.;:\/_]/", "", $novoTexto);
        return $novoTexto;
    }

    public function index(){
        return view('sintegra.index');
    }

    public function store(Request $request){

        // $dataInicial = '2023-11-01';
        // $dataFinal = '2023-11-30';

        $dataInicial = $request->start_date;
        $dataFinal = $request->end_date;
        $local_id = $request->local_id;

        $emitente = Empresa::findOrFail($request->empresa_id);
        if($local_id){
            $emitente = __objetoParaEmissao($emitente, $local_id);
        }

        $cnpj = preg_replace('/[^0-9]/', '', $emitente->cpf_cnpj);

        $dInicial = \Carbon\Carbon::parse($dataInicial)->format('dmY');
        $dFinal = \Carbon\Carbon::parse($dataFinal)->format('dmY');

        $vendas = Nfe::whereDate('data_emissao', '>=', $dataInicial)
        ->whereDate('data_emissao', '<=', $dataFinal)
        ->where('estado', 'aprovado')
        ->where('tpNF', 1)
        ->where('empresa_id', $request->empresa_id)
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->get();

        $vendasPdv = Nfce::whereDate('data_emissao', '>=', $dataInicial)
        ->whereDate('data_emissao', '<=', $dataFinal)
        ->where('estado', 'aprovado')
        ->where('empresa_id', $request->empresa_id)
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->get();

        $dataXml = [];

        $dInicio = \Carbon\Carbon::parse($dataInicial)->format('Ymd');
        $dFinal = \Carbon\Carbon::parse($dataFinal)->format('Ymd');

        $registro10 = '10';
        $registro10 .= str_pad($cnpj, 14, '0', STR_PAD_LEFT);
        $registro10 .= str_pad($emitente->ie, 14, ' ');
        $registro10 .= str_pad(substr($this->removeAcentos($emitente->nome), 0, 35), 35, ' ');
        $registro10 .= str_pad($this->removeAcentos($emitente->cidade->nome), 30, ' ');
        $registro10 .= str_pad($emitente->cidade->uf, 2, ' ');
        $registro10 .= str_pad(substr($this->soNumero($emitente->celular), 0, 10), 10, ' ');
        $registro10 .= $dInicio;
        $registro10 .= $dFinal;
        $registro10 .= '3';
        $registro10 .= '3';
        $registro10 .= '1'; // validar
        $registro10 .= "\r\n";

        $sintegra = strtoupper($registro10);
        //fim registro 10

        $contato = ' ';
        $registro11 = '11';
        $registro11 .= str_pad(substr($this->removeAcentos($emitente->rua), 0, 34), 34, ' ');
        $registro11 .= str_pad($this->soNumero($emitente->numero), 5, '0', STR_PAD_LEFT);
        $registro11 .= str_pad(substr($this->removeAcentos($emitente->complemento), 0, 22), 22, ' ');
        $registro11 .= str_pad(substr($this->removeAcentos($emitente->bairro), 0, 15), 15, ' ');
        $registro11 .= str_pad($this->soNumero($emitente->cep), 8, '0', STR_PAD_LEFT);
        $registro11 .= str_pad(substr($this->removeAcentos($emitente->nome), 0, 28), 28, ' ');
        $registro11 .= str_pad(substr($this->soNumero($emitente->celular), 0, 12), 12, '0', STR_PAD_LEFT);
        $registro11 .= "\r\n";

        $sintegra .= strtoupper($registro11);
        // fim registro 11

        $totalregistro50 = 0;
        $totalregistro61 = 0;
        $totalregistro54 = 0;
        $totalregistro75 = 0;
        $registro50 = '';
        $produtos50 = [];
        $totalregistro51 = 0;
        $registro54 = '';
        $registro75 = '';
        $registro61R = '';
        $produtos = [];

        foreach($vendas as $v){
            $xml = $this->util->getXml($v, 'xml_nfe/');
            $temp = [
                'tipo' => 'venda',
                'xml' => $xml
            ];
            if($xml != null){
                array_push($dataXml, $temp);
            }
        }

        foreach($vendasPdv as $v){
            $xml = $this->util->getXml($v, 'xml_nfce/');
            $temp = [
                'tipo' => 'venda',
                'xml' => $xml
            ];
            if($xml != null){
                array_push($dataXml, $temp);
            }
        }

        foreach($dataXml as $l){

            $destinatario = $this->util->getDestinatario($l['xml']);
            $ide = $this->util->getIde($l['xml']);
            $itens = $this->util->getItensNfe($l['xml']);
            $total = $this->util->getTotal($l['xml']);
            $enderecoDest = $destinatario->enderDest;
            $docDestinatario = isset($destinatario->CNPJ) ? $destinatario->CNPJ : $destinatario->CPF;

            $cfop = $itens->prod->CFOP;
            if($ide->mod < 55){
                $totalregistro50 = $totalregistro50 + 1;
                $registro50 .= '50';
                $registro50 .= str_pad($this->soNumero($docDestinatario), 14, '0', STR_PAD_LEFT);
                $registro50 .= str_pad($this->soNumero($destinatario->IE), 14, ' ');
                $registro50 .= $ide->dhEmi;
                $registro50 .= str_pad($enderecoDest->UF, 2, ' ');
                $registro50 .= str_pad($this->soNumero($ide->mod), 2, '0');
                $registro50 .= str_pad($this->soNumero($ide->serie), 3, ' ');
                $registro50 .= str_pad(substr($this->soNumero($ide->nNF), -6), 6, '0', STR_PAD_LEFT);
                $registro50 .= str_pad(substr($this->soNumero($cfop), 0, 4), 4, '0', STR_PAD_LEFT);
                $registro50 .= 'T';
                $registro50 .= str_pad($this->soNumero(number_format($total->vNF + $total->vOutro, 2, '.', '')), 13, '0', STR_PAD_LEFT);
                $registro50 .= str_pad('0', 13, '0', STR_PAD_LEFT);
                $registro50 .= str_pad('0', 13, '0', STR_PAD_LEFT);
                $registro50 .= str_pad('0', 13, '0', STR_PAD_LEFT);
                $registro50 .= str_pad($this->soNumero($total->vOutro), 13, '0', STR_PAD_LEFT);
                $registro50 .= str_pad('0', 4, '0', STR_PAD_LEFT);
                $registro50 .= 'N';
                $registro50 .= "\r\n";
            }

            $totalItens = sizeof($itens);

            $contItem = 1;
            $tvDescnf = 0;
            $tvFretenf = 0;
            $tvOutronf = 0;
            $vDescnf = 0;

            $vDescTotal = (float)$total->vDesc;
            $vFreteTotal = (float)$total->vFrete;
            $vOutroTotal = (float)$total->vOutro;

            // inicio registro 50
            foreach($itens as $item){
                $prod = $item->prod;
                $imposto = $item->imposto;

                $arr = (array_values((array)$imposto->ICMS));
                $pICMS = $arr[0]->pICMS ?? 0;
                $vBCICMS = isset($arr[0]->vBC) ? (float)$arr[0]->vBC : 0;
                $cst_csosn = $arr[0]->CST ? $arr[0]->CST : $arr[0]->CSOSN;
                $cst_csosn = (string)$cst_csosn;

                $vICMSST = isset($arr[0]->vICMSST) ? (float)$arr[0]->vICMSST : 0;
                $vFCPST = isset($arr[0]->vFCPST) ? (float)$arr[0]->vFCPST : 0;
                $vICMS = isset($arr[0]->vICMS) ? (float)$arr[0]->vICMS : 0;

                if (!in_array($this->soNumero($contItem . $pICMS . $prod->CFOP), $produtos50)){

                    $produtos50[] = $this->soNumero($contItem . $pICMS . $prod->CFOP);
                    $totalregistro50 = $totalregistro50 + 1;
                    $registro50 .= '50';
                    if($destinatario){
                        $registro50 .= str_pad($this->soNumero($docDestinatario), 14, '0', STR_PAD_LEFT);

                        if ($destinatario->IE == ''){
                            $registro50 .= 'ISENTO        ';
                        }else{
                            if (strlen($docDestinatario) < 14) :
                                $registro50 .= 'ISENTO        ';
                            else :
                                $registro50 .= str_pad($this->soNumero($destinatario->IE), 14, ' ');
                            endif;
                        }
                    }

                    $registro50 .= \Carbon\Carbon::parse($ide->dhEmi)->format('Ymd');

                    if($destinatario){
                        $registro50 .= str_pad($enderecoDest->UF, 2, ' ');
                    }
                    $registro50 .= str_pad($this->soNumero($ide->mod), 2, '0');
                    $registro50 .= str_pad($this->soNumero($ide->serie), 3, ' ');
                    $registro50 .= str_pad(substr($this->soNumero($ide->nNF), -6), 6, '0', STR_PAD_LEFT);
                    $registro50 .= str_pad(substr($this->soNumero($item->prod->CFOP), 0, 4), 4, '0', STR_PAD_LEFT);

                    if($ide->tpEmis == 1){
                        $registro50 .= 'P';
                    }else{
                        $registro50 .= 'T';
                    }

                    $vProd = (float) $item->prod->vProd;
                    $arr = (array_values((array)$imposto->IPI));
                    $vIPI = 0;
                    if(isset($arr[1])){
                        $vIPI = (float)$arr[1]->vIPI ?? 0;
                    }

                    $vFrete = (float)isset($item->prod->vFrete) ? $item->prod->vFrete : 0;
                    $vOutro = (float)isset($item->prod->vOutro) ? $item->prod->vOutro : 0;
                    $vDesc = (float)isset($item->prod->vDesc) ? $item->prod->vDesc : 0;

                    $registro50 .= str_pad($this->soNumero(number_format($vProd + $vIPI + $vICMSST + $vFCPST + $vFrete + $vOutro - $vDesc, 2, '.', '')), 13, '0', STR_PAD_LEFT);

                    if ($vICMS > 0){
                        $registro50 .= str_pad($this->soNumero($vBCICMS), 13, '0', STR_PAD_LEFT);
                        $registro50 .= str_pad($this->soNumero(number_format($vICMS, 2, '.', '')), 13, '0', STR_PAD_LEFT);
                        $registro50 .= str_pad('0', 13, '0', STR_PAD_LEFT);
                    }else{
                        $registro50 .= str_pad('0', 13, '0', STR_PAD_LEFT);
                        $registro50 .= str_pad('0', 13, '0', STR_PAD_LEFT);
                        $registro50 .= str_pad($this->soNumero($vProd), 13, '0', STR_PAD_LEFT);
                    }

                    $registro50 .= str_pad(($this->soNumero($vOutro)), 13, '0', STR_PAD_LEFT);
                    $registro50 .= str_pad(substr($this->soNumero($pICMS), 0, 4), 4, '0', STR_PAD_LEFT);

                    if ($cst_csosn == '101'){
                        $registro50 .= 'S';
                    }else{
                        $registro50 .= 'N';
                    }

                    $registro50 .= "\r\n";
                    $contItem++;


                }
            }

            // final do registro 50

            // inicio registro 54
            $contItem = 1;
            foreach($itens as $item){
                $prod = $item->prod;
                $imposto = $item->imposto;
                $totalregistro54 = $totalregistro54 + 1;
                // $registro54 .= '54';

                $cfop = $item->prod->CFOP;

                $arr = (array_values((array)$imposto->ICMS));
                $cst_csosn = $arr[0]->CST ? $arr[0]->CST : $arr[0]->CSOSN;
                $cst_csosn = (string)$cst_csosn;
                $vBCICMS = isset($arr[0]->vBC) ? (float)$arr[0]->vBC : 0;
                $vBCST = isset($arr[0]->vBCST) ? (float)$arr[0]->vBCST : 0;
                $pMVAST = isset($arr[0]->pMVAST) ? (float)$arr[0]->pMVAST : 0;
                $pICMS = $arr[0]->pICMS ?? 0;

                $arr = (array_values((array)$imposto->IPI));
                $vIPI = 0;
                $pIPI = 0;

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
                if(isset($arr[1])){
                    $vIPI = (float)$arr[1]->vIPI ?? 0;
                }
                if ($ide->mod != '65' && $ide->mod != '57'){
                    $totalregistro54 = $totalregistro54 + 1;
                    $registro54 .= '54';
                    // dd($this->soNumero($docDestinatario));
                    $registro54 .= str_pad($this->soNumero($docDestinatario), 14, '0', STR_PAD_LEFT);
                    $registro54 .= str_pad($this->soNumero($ide->mod), 2, '0');
                    $registro54 .= str_pad($this->soNumero($ide->serie), 3, ' ');
                    $registro54 .= str_pad(substr($this->soNumero($ide->nNF), -6), 6, '0', STR_PAD_LEFT);
                    $registro54 .= str_pad(substr($this->soNumero($cfop), 0, 4), 4, '0', STR_PAD_LEFT);
                    $registro54 .= str_pad(substr($this->soNumero($cst_csosn), 0, 3), 3, '0', STR_PAD_LEFT);
                    $registro54 .= str_pad(substr($this->soNumero($contItem), 0, 3), 3, '0', STR_PAD_LEFT);

                    $vDesc = (float)isset($prod->vDesc) ? $prod->vDesc : 0;

                    $registro54 .= str_pad(substr($prod->cProd, 0, 14), 14, ' ');
                    $registro54 .= str_pad($this->soNumero($prod->qCom), 11, '0', STR_PAD_LEFT);
                    $registro54 .= str_pad($this->soNumero(number_format($prod->vProd - $vDesc, 2, '.', '')), 12, '0', STR_PAD_LEFT);
                    $registro54 .= str_pad($this->soNumero($vDesc), 12, '0', STR_PAD_LEFT);
                    $registro54 .= str_pad($this->soNumero($vBCICMS), 12, '0', STR_PAD_LEFT);
                    $registro54 .= str_pad($this->soNumero($vBCST), 12, '0', STR_PAD_LEFT);
                    $registro54 .= str_pad($this->soNumero($vIPI), 12, '0', STR_PAD_LEFT);
                    $registro54 .= str_pad($this->soNumero($pICMS), 4, '0', STR_PAD_LEFT);
                    $registro54 .= "\r\n";
                    $contItem++;
                    // dd($registro54);
                }else{
                    $totalregistro61 = $totalregistro61 + 1;
                    $registro61R .= '61R';
                    $registro61R .= str_pad($this->soNumero(\Carbon\Carbon::parse($ide->dhEmi)->format('mY')), 2, '0');
                    $registro61R .= str_pad(substr($prod->cProd, 0, 14), 14, ' ');
                    $registro61R .= str_pad($this->soNumero($prod->qCom), 13, '0', STR_PAD_LEFT);
                    $registro61R .= str_pad($this->soNumero($prod->vProd), 16, '0', STR_PAD_LEFT);
                    $registro61R .= str_pad($this->soNumero($vBCICMS), 16, '0', STR_PAD_LEFT);
                    $registro61R .= str_pad($this->soNumero($pICMS), 4, '0', STR_PAD_LEFT);
                    $registro61R .= str_pad(' ', 54, ' ');
                    $registro61R .= "\r\n";
                    $contItem++;
                }

                if (!in_array(substr($prod->cProd, 0, 14), $produtos, true)){
                    $produtos[] = substr($prod->cProd, 0, 14);
                    $totalregistro75 = $totalregistro75 + 1;

                    $registro75 .= '75';
                    $registro75 .= $dInicio;
                    $registro75 .= $dFinal;
                    $registro75 .= str_pad(substr($prod->cProd, 0, 14), 14, ' ');
                    $registro75 .= str_pad(substr($prod->NCM, 0, 8), 8, ' ');

                    if ($prod->xProd == ''){
                        $registro75 .= str_pad(substr($this->removeAcentos(trim(BuscaDesc($prod->cProd))), 0, 53), 53, ' ');
                    }else{
                        $registro75 .= str_pad(substr($this->removeAcentos(trim($prod->xProd)), 0, 53), 53, ' ');
                    }


                    $registro75 .= str_pad(substr($prod->uCom, 0, 6), 6, ' ');
                    
                    $registro75 .= str_pad($this->soNumero($pIPI), 5, '0', STR_PAD_LEFT);
                    $registro75 .= str_pad($this->soNumero($pICMS), 4, '0', STR_PAD_LEFT);
                    $registro75 .= str_pad($this->soNumero($pMVAST), 5, '0', STR_PAD_LEFT);
                    $registro75 .= str_pad('0', 13, '0', STR_PAD_LEFT);
                    $registro75 .= "\r\n";
                }
            }

            if($vFreteTotal > 0){
                $totalregistro54 = $totalregistro54 + 1;
                $registro54 .= '54';
                $cfop = $itens->prod->CFOP;

                $registro54 .= str_pad($this->soNumero($docDestinatario), 14, '0', STR_PAD_LEFT);
                $registro54 .= str_pad($this->soNumero($ide->mod), 2, '0');
                $registro54 .= str_pad($this->soNumero($ide->serie), 3, ' ');
                $registro54 .= str_pad(substr($this->soNumero($ide->nNF), -6), 6, '0', STR_PAD_LEFT);
                $registro54 .= str_pad(substr($cfop, 0, 4), 4, '0', STR_PAD_LEFT);
                $registro54 .= str_pad(substr('0', 0, 3), 3, '0', STR_PAD_LEFT);
                $registro54 .= str_pad(substr('991', 0, 3), 3, '0', STR_PAD_LEFT);
                $registro54 .= str_pad('', 14, ' ');
                $registro54 .= str_pad('', 11, '0', STR_PAD_LEFT);
                $registro54 .= str_pad($this->soNumero($vFreteTotal), 12, '0', STR_PAD_LEFT);
                $registro54 .= str_pad($this->soNumero($vFreteTotal), 12, '0', STR_PAD_LEFT);
                $registro54 .= str_pad('', 12, '0', STR_PAD_LEFT);
                $registro54 .= str_pad('', 12, '0', STR_PAD_LEFT);
                $registro54 .= str_pad('', 12, '0', STR_PAD_LEFT);
                $registro54 .= str_pad('', 4, '0', STR_PAD_LEFT);
                $registro54 .= "\r\n";

            }

            if ($vOutroTotal > 0){
                $totalregistro54 = $totalregistro54 + 1;
                $registro54 .= '54';
                $cfop = $itens->prod->CFOP;
//              
                $registro54 .= str_pad($this->soNumero($docDestinatario), 14, '0', STR_PAD_LEFT);
                $registro54 .= str_pad($this->soNumero($ide->mod), 2, '0');
                $registro54 .= str_pad($this->soNumero($ide->serie), 3, ' ');
                $registro54 .= str_pad(substr($this->soNumero($ide->nNF), -6), 6, '0', STR_PAD_LEFT);
                $registro54 .= str_pad(substr($cfop, 0, 4), 4, '0', STR_PAD_LEFT);
                $registro54 .= str_pad(substr('0', 0, 3), 3, '0', STR_PAD_LEFT);
                $registro54 .= str_pad(substr('999', 0, 3), 3, '0', STR_PAD_LEFT);
                $registro54 .= str_pad('', 14, ' ');
                $registro54 .= str_pad('', 11, '0', STR_PAD_LEFT);
                $registro54 .= str_pad('', 12, '0', STR_PAD_LEFT);
                $registro54 .= str_pad($this->soNumero($vOutroTotal), 12, '0', STR_PAD_LEFT);
                $registro54 .= str_pad('', 12, '0', STR_PAD_LEFT);
                $registro54 .= str_pad('', 12, '0', STR_PAD_LEFT);
                $registro54 .= str_pad('', 12, '0', STR_PAD_LEFT);
                $registro54 .= str_pad('', 4, '0', STR_PAD_LEFT);
                $registro54 .= "\r\n";
            }

        }
        $sintegra .= strtoupper($registro50);
        $sintegra .= strtoupper($registro54);
        // dd($registro54);

        foreach($dataXml as $l){
            $ide = $this->util->getIde($l['xml']);
            $total = $this->util->getTotal($l['xml']);
            if ($ide->mod == '65'){

                $totalregistro61 = $totalregistro61 + 1;
                $registro61 = '61';
                $registro61 .= str_pad(' ', 14, ' ');
                $registro61 .= str_pad(' ', 14, ' ');
                $registro61 .= \Carbon\Carbon::parse($ide->dhEmi)->format('Ymd');
                $registro61 .= str_pad($this->soNumero($ide->mod), 2, '0');
                $registro61 .= str_pad($this->soNumero($ide->serie), 3, ' ');
                $registro61 .= str_pad('', 2, ' ');
                $registro61 .= str_pad(substr($this->soNumero($ide->nNF), -6), 6, '0', STR_PAD_LEFT);
                $registro61 .= str_pad(substr($this->soNumero($ide->nNF), -6), 6, '0', STR_PAD_LEFT);
                $registro61 .= str_pad($this->soNumero($total->vNF), 13, '0', STR_PAD_LEFT);
                $registro61 .= str_pad($this->soNumero($total->vBC), 13, '0', STR_PAD_LEFT);
                $registro61 .= str_pad($this->soNumero($total->vICMS), 12, '0', STR_PAD_LEFT);
                $registro61 .= str_pad('0', 13, '0', STR_PAD_LEFT);
                $registro61 .= str_pad($this->soNumero($total->vOutro), 13, '0', STR_PAD_LEFT);
                $registro61 .= str_pad('0', 4, '0', STR_PAD_LEFT);
                $registro61 .= ' ';
                $registro61 .= "\r\n";
                $sintegra .= strtoupper($registro61);
            }
        }

        $sintegra .= strtoupper($registro61R);

        $totalregistro70 = 0;
        foreach($dataXml as $l){
            $ide = $this->util->getIde($l['xml']);
            $total = $this->util->getTotal($l['xml']);

            if ($ide->mod == '57'){
                $destinatario = $this->util->getDestinatario($l['xml']);
                $docDestinatario = isset($destinatario->CNPJ) ? $destinatario->CNPJ : $destinatario->CPF;

                $itens = $this->util->getItensNfe($l['xml']);
                $cfop = $itens[0]->prod->CFOP;

                $totalregistro70 = $totalregistro70 + 1;
                $registro70 = '70';
                $registro70 .= str_pad($this->soNumero($docDestinatario), 14, '0', STR_PAD_LEFT);
                $registro70 .= str_pad($this->soNumero($destinatario->IE), 14, ' ');
                $registro70 .= \Carbon\Carbon::parse($ide->dhEmi)->format('Ymd');
                $registro70 .= str_pad($docDestinatario->UF, 2, ' ');
                $registro70 .= str_pad($this->soNumero($ide->modelo), 2, '0');
                $registro70 .= str_pad($this->soNumero($ide->serie), 1, ' ');
                $registro70 .= str_pad('', 2, ' ');
                $registro70 .= str_pad(substr($this->soNumero($ide->nNF), -6), 6, '0', STR_PAD_LEFT);
                $registro70 .= str_pad(substr($this->soNumero($cfop), 0, 4), 4, '0', STR_PAD_LEFT);
                $registro70 .= str_pad($this->soNumero($total->vNF), 13, '0', STR_PAD_LEFT);
                $registro70 .= str_pad($this->soNumero($total->vBC), 14, '0', STR_PAD_LEFT);
                $registro70 .= str_pad($this->soNumero($total->vICMS), 14, '0', STR_PAD_LEFT);
                $registro70 .= str_pad('0', 14, '0', STR_PAD_LEFT);
                $registro70 .= str_pad(($this->soNumero($total->vOutro)), 14, '0', STR_PAD_LEFT);
                $registro70 .= '1';

                $arr = (array_values((array)$itens[0]->imposto->ICMS));
                $cst_csosn = $arr[0]->CST ? $arr[0]->CST : $arr[0]->CSOSN;
                $cst_csosn = (string)$cst_csosn;
                if ($cst_csosn == '101'){
                    $registro70 .= 'S';
                }else{
                    $registro70 .= 'N';
                }

                $registro70 .= "\r\n";
                $sintegra .= strtoupper($registro70);
            }
        }

        $totalregistro74 = 0;
        //inventario

        $totalgeral = $totalregistro50 + $totalregistro51 + $totalregistro54 + $totalregistro61 + $totalregistro70 + 
        $totalregistro74 + $totalregistro75 + 3;

        $sintegra .= strtoupper($registro75);
        $registro90 = '90';
        $registro90 .= str_pad($this->soNumero($cnpj), 14, '0', STR_PAD_LEFT);
        $registro90 .= str_pad($emitente->IE, 14, ' ');
        $registro90 .= '50';
        $registro90 .= str_pad($totalregistro50, 8, '0', STR_PAD_LEFT);
        $registro90 .= '54';
        $registro90 .= str_pad($totalregistro54, 8, '0', STR_PAD_LEFT);
        $registro90 .= '61';
        $registro90 .= str_pad($totalregistro61, 8, '0', STR_PAD_LEFT);
        $registro90 .= '70';
        $registro90 .= str_pad($totalregistro70, 8, '0', STR_PAD_LEFT);
        $registro90 .= '75';
        $registro90 .= str_pad($totalregistro75, 8, '0', STR_PAD_LEFT);
        $registro90 .= '99';
        $registro90 .= str_pad($totalgeral, 8, '0', STR_PAD_LEFT);
        $total90 = strlen($registro90);
        $total90 = 125 - $total90;
        $registro90 .= str_pad(' ', $total90, ' ');
        $registro90 .= '1';
        $sintegra .= strtoupper($registro90);

        $mes = \Carbon\Carbon::parse($dataInicial)->format('m');
        $nomearquivo = "sintegra-" . $cnpj . "-" . $this->getMes($mes-1) . ".txt";

        // dd($sintegra);
        $arquivo = fopen(public_path("sintegra_files/").$nomearquivo, "w");
        fwrite($arquivo, $sintegra);
        fclose($arquivo);

        return response()->download(public_path("sintegra_files/").$nomearquivo);

        // dd($dataXml);
    }

    private function getMes($indice){
        $meses = [
            'janeiro',
            'fevereiro',
            'março',
            'abril',
            'maio',
            'junho',
            'julho',
            'agosto',
            'setembro',
            'outubro',
            'novembro',
            'dezembro',
        ];
        return $meses[$indice];
    }
}
