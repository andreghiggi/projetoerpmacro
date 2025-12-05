<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\Empresa;
use App\Models\ContaReceber;
use App\Models\ContaPagar;
use App\Models\Caixa;
use App\Models\FaturaNfe;
use App\Models\Cliente;
use App\Models\ProdutoFornecedor;
use App\Models\Fornecedor;
use App\Models\ProdutoUnico;
use App\Models\ComissaoVenda;
use App\Models\ConfigGeral;
use App\Models\UsuarioEmpresa;
use App\Models\Produto;
use App\Models\MargemComissao;
use App\Models\Transportadora;
use App\Models\Cidade;
use App\Models\ItemNfe;
use App\Models\ItemDimensaoNfe;
use App\Models\ItemProducao;
use App\Models\NaturezaOperacao;
use App\Models\Funcionario;
use NFePHP\DA\NFe\Danfe;
use Illuminate\Support\Facades\DB;
use App\Utils\EstoqueUtil;
use App\Services\NFeService;
use NFePHP\DA\NFe\Daevento;

class ContadorAdminNFeController extends Controller
{

    protected $estoqueUtil;

    public function __construct(EstoqueUtil $estoqueUtil){
        $this->estoqueUtil = $estoqueUtil;

        if (!is_dir(public_path('zips'))) {
            mkdir(public_path('zips'), 0777, true);
        }
    }

    public function nfe(Request $request){
        $contador = Empresa::findOrFail(request()->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $tpNF = $request->get('tpNF');
        $estado = $request->get('estado');

        $data = Nfe::where('empresa_id', $empresaSelecionada)->where('orcamento', 0)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($tpNF != "", function ($query) use ($tpNF) {
            return $query->where('tpNF', $tpNF);
        })
        ->when($estado != "", function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());

        $contXml = $this->preparaXmls($start_date, $end_date, $tpNF, $empresaSelecionada, $estado);
        return view('contador.nfe', compact('data', 'contXml'));
    }

    private function preparaXmls($start_date, $end_date, $tpNF, $empresaSelecionada, $estado){

        $data = Nfe::where('empresa_id', $empresaSelecionada)->where('orcamento', 0)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($tpNF != "", function ($query) use ($tpNF) {
            return $query->where('tpNF', $tpNF);
        })
        ->where('estado', $estado)
        ->get();

        $dir = 'xml_nfe/';
        if($estado == 'cancelado'){
            $dir = 'xml_nfe_cancelada/';
        }

        $cont = 0;
        foreach($data as $item){
            if (file_exists(public_path($dir) . $item->chave . '.xml')) {
                $cont++;
            }
        }
        return $cont;

    }

    public function downloadNFe($id){
        $item = Nfe::findOrFail($id);
        $dir = 'xml_nfe/';
        if($item->estado == 'cancelado'){
            $dir = 'xml_nfe_cancelada/';
        }
        if (file_exists(public_path($dir) . $item->chave . '.xml')) {
            return response()->download(public_path($dir) . $item->chave . '.xml');
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }

    public function danfe($id){
        $item = Nfe::findOrFail($id);

        if (file_exists(public_path('xml_nfe/') . $item->chave . '.xml')) {
            $xml = file_get_contents(public_path('xml_nfe/') . $item->chave . '.xml');

            $danfe = new Danfe($xml);
            $pdf = $danfe->render();
            return response($pdf)
            ->header('Content-Type', 'application/pdf');
        } else {
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }

    public function imprimirCancela($id)
    {
        $item = Nfe::findOrFail($id);
        __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);

        $xml = file_get_contents(public_path('xml_nfe_cancelada/') . $item->chave . '.xml');
        $dadosEmitente = $this->getEmitente($item->empresa);

        $daevento = new Daevento($xml, $dadosEmitente);
        $daevento->debugMode(true);
        $pdf = $daevento->render();
        return response($pdf)
        ->header('Content-Type', 'application/pdf');
    }

    public function imprimirCorrecao($id)
    {
        $item = Nfe::findOrFail($id);
        __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);

        $xml = file_get_contents(public_path('xml_nfe_correcao/') . $item->chave . '.xml');
        $dadosEmitente = $this->getEmitente($item->empresa);
        $daevento = new Daevento($xml, $dadosEmitente);
        $daevento->debugMode(true);
        $pdf = $daevento->render();
        return response($pdf)
        ->header('Content-Type', 'application/pdf');
    }

    private function getEmitente($empresa)
    {
        return [
            'razao' => $empresa->nome,
            'logradouro' => $empresa->rua,
            'numero' => $empresa->numero,
            'complemento' => '',
            'bairro' => $empresa->bairro,
            'CEP' => preg_replace('/[^0-9]/', '', $empresa->cep),
            'municipio' => $empresa->cidade->nome,
            'UF' => $empresa->cidade->uf,
            'telefone' => $empresa->telefone,
            'email' => ''
        ];
    }

    public function edit($id){
        $item = Nfe::findOrFail($id);
        __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);

        $transportadoras = Transportadora::where('empresa_id', $item->empresa_id)->get();
        $cidades = Cidade::all();
        $naturezas = NaturezaOperacao::where('empresa_id', $item->empresa_id)->get();
        $contador = Empresa::findOrFail(request()->empresa_id);
        $empresaSelecionada = $contador->empresa_selecionada;

        $caixa = $item->caixa;

        $funcionarios = Funcionario::where('empresa_id', $item->empresa_id)
        ->where('status', 1)->get();

        return view('contador.nfe_edit', compact('item', 'transportadoras', 'cidades', 'naturezas', 'caixa', 'funcionarios', 'empresaSelecionada'));
    }

    public function xmlTemp($id)
    {
        $item = Nfe::findOrFail($id);
        __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);

        $empresa = $item->empresa;
        $empresa = __objetoParaEmissao($empresa, $item->local_id);

        if ($empresa->arquivo == null) {
            session()->flash("flash_error", "Certificado não encontrado para este emitente");
            return redirect()->route('config.index');
        }

        $nfe_service = new NFeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => (int)$empresa->ambiente,
            "razaosocial" => $empresa->nome,
            "siglaUF" => $empresa->cidade->uf,
            "cnpj" => preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj),
            // "schemes" => "PL_009_V4",
            "schemes" => "PL_010_V1.21",
            "versao" => "4.00",
        ], $empresa);

        $doc = $nfe_service->gerarXml($item);

        if (!isset($doc['erros_xml'])) {
            $xml = $doc['xml'];

            return response($xml)
            ->header('Content-Type', 'application/xml');
        } else {
            return response()->json($doc['erros_xml'], 401);
        }
    }

    public function downloadZip(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $tpNF = $request->get('tpNF');
        $estado = $request->get('estado');

        $contador = Empresa::findOrFail(request()->empresa_id);
        $doc = preg_replace('/[^0-9]/', '', $contador->cpf_cnpj);
        $empresaSelecionada = $contador->empresa_selecionada;
        $data = Nfe::where('empresa_id', $empresaSelecionada)->where('orcamento', 0)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($tpNF != "", function ($query) use ($tpNF) {
            return $query->where('tpNF', $tpNF);
        })
        ->where('estado', $estado)
        ->get();
        $zip = new \ZipArchive();
        $zip_file = public_path('zips') . '/xml-'.$doc.'.zip';
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $dir = 'xml_nfe/';
        if($estado == 'cancelado'){
            $dir = 'xml_nfe_cancelada/';
        }

        foreach($data as $item){
            if (file_exists(public_path($dir) . $item->chave . '.xml')) {
                $filename = public_path($dir) . $item->chave . '.xml';

                $zip->addFile($filename, $item->chave . '.xml');
            }
        }
        $zip->close();
        if (file_exists($zip_file)){
            return response()->download($zip_file, 'nfe_'.$doc.'.zip');
        }else{
            session()->flash("flash_error", "Arquivo não encontrado");
            return redirect()->back();
        }
    }

    public function nfeUpdate(Request $request, $id){
        try{

            DB::transaction(function () use ($request, $id) {

                $item = Nfe::findOrFail($id);
                $transportadora_id = $request->transportadora_id;
                if ($request->transportadora_id == null) {
                    $transportadora_id = $this->cadastrarTransportadora($request, $item->empresa_id);
                }
                $config = Empresa::find($item->empresa_id);
                $tipoPagamento = $request->tipo_pagamento;

                $request->merge([
                    'emissor_nome' => $config->nome,
                    'emissor_cpf_cnpj' => $config->cpf_cnpj,
                    'ambiente' => $config->ambiente,
                    'chave' => '',
                    'transportadora_id' => $transportadora_id,
                    'numero' => $request->numero_nfe ? $request->numero_nfe : 0,
                    'total' => __convert_value_bd($request->valor_total),
                    'desconto' => __convert_value_bd($request->desconto),
                    'acrescimo' => __convert_value_bd($request->acrescimo),
                    'valor_produtos' => __convert_value_bd($request->valor_total) ?? 0,
                    'valor_frete' => $request->valor_frete ? __convert_value_bd($request->valor_frete) : 0,
                    'tipo_pagamento' => $request->tipo_pagamento[0],
                    'empresa_id' => $item->empresa_id
                ]);

                $item->fill($request->all())->save();

                if(isset($request->data_emissao)){
                    $dataEmissao = str_replace("/", "-", $request->data_emissao);
                    $dataEmissao = \Carbon\Carbon::parse($dataEmissao)->format('Y-m-d H:i');
                    $item->data_emissao = $dataEmissao;
                    $item->save();
                }

                foreach($item->itens as $x){
                    $product = $x->produto;
                    if ($product->gerenciar_estoque && $item->orcamento == 0) {
                        if (isset($request->is_compra)) {
                            $this->estoqueUtil->reduzEstoque($product->id, $x->quantidade, $x->variacao_id, $item->local_id);
                        } else {
                            $this->estoqueUtil->incrementaEstoque($product->id, $x->quantidade, $x->variacao_id, $item->local_id);
                        }
                    }
                }

                foreach($item->itens as $it){
                    $it->itensDimensao()->delete();
                    $it->delete();
                }
                $item->fatura()->delete();

                for ($i = 0; $i < sizeof($request->produto_id); $i++) {
                    $product = Produto::findOrFail($request->produto_id[$i]);
                    $variacao_id = isset($request->variacao_id[$i]) ? $request->variacao_id[$i] : null;

                    $itemNfe = ItemNfe::create([
                        'nfe_id' => $item->id,
                        'produto_id' => (int)$request->produto_id[$i],
                        'quantidade' => __convert_value_bd($request->quantidade[$i]),
                        'valor_unitario' => __convert_value_bd($request->valor_unitario[$i]),
                        'sub_total' => __convert_value_bd($request->sub_total[$i]),
                        'perc_icms' => __convert_value_bd($request->perc_icms[$i]),
                        'perc_pis' => __convert_value_bd($request->perc_pis[$i]),
                        'perc_cofins' => __convert_value_bd($request->perc_cofins[$i]),
                        'perc_ipi' => __convert_value_bd($request->perc_ipi[$i]),
                        'cst_csosn' => $request->cst_csosn[$i],
                        'cst_pis' => $request->cst_pis[$i],
                        'cst_cofins' => $request->cst_cofins[$i],
                        'cst_ipi' => $request->cst_ipi[$i],
                        'perc_red_bc' => $request->perc_red_bc[$i] ? __convert_value_bd($request->perc_red_bc[$i]) : 0,
                        'cfop' => $request->cfop[$i],
                        'ncm' => $request->ncm[$i],
                        'codigo_beneficio_fiscal' => $request->codigo_beneficio_fiscal[$i],
                        'variacao_id' => $variacao_id,
                        'xPed' => $request->xPed[$i],
                        'nItemPed' => $request->nItemPed[$i],
                        'infAdProd' => $request->infAdProd[$i],
                    ]);

                    if(isset($request->dimensao_largura)){
                        if(isset($request->_line[$i])){
                            for($l=0; $l<sizeof($request->dimensao_largura); $l++){
                                if($i == $request->_line[$i]){
                                    ItemDimensaoNfe::create([
                                        'item_nfe_id' => $itemNfe->id,
                                        'valor_unitario_m2' => __convert_value_bd($request->dimensao_valor_unitario_m2[$l]),
                                        'largura' => $request->dimensao_largura[$l],
                                        'altura' => $request->dimensao_altura[$l],
                                        'quantidade' => $request->dimensao_quantidade[$l],
                                        'm2_total' => $request->dimensao_m2_total[$l],
                                        'espessura' => $request->dimensao_espessura[$l] ?? 0,
                                        'observacao' => $request->dimensao_observacao[$l] ?? '',
                                        'sub_total' => __convert_value_bd($request->dimensao_sub_total[$l])
                                    ]);
                                }
                            }
                        }
                    }

                    if ($product->gerenciar_estoque && $item->orcamento == 0) {
                        if (isset($request->is_compra)) {
                            $this->estoqueUtil->incrementaEstoque($product->id, __convert_value_bd($request->quantidade[$i]), $variacao_id, $item->local_id);
                        } else {
                            $this->estoqueUtil->reduzEstoque($product->id, __convert_value_bd($request->quantidade[$i]), $variacao_id, $item->local_id);
                        }
                    }
                }

                ContaReceber::where('nfe_id', $item->id)->delete();
                ContaPagar::where('nfe_id', $item->id)->delete();
                FaturaNfe::where('nfe_id', $item->id)->delete();

                if ($request->tpNF == 1) {

                    if ($request->gerar_conta_receber) {

                        for ($i = 0; $i < sizeof($tipoPagamento); $i++) {
                            if(isset($request->valor_fatura[$i])){
                                ContaReceber::create([
                                    'empresa_id' => $item->empresa_id,
                                    'nfe_id' => $item->id,
                                    'cliente_id' => $request->cliente_id,
                                    'valor_integral' => __convert_value_bd($request->valor_fatura[$i]),
                                    'tipo_pagamento' => $tipoPagamento[$i],
                                    'data_vencimento' => $request->data_vencimento[$i],
                                    'local_id' => $item->local_id
                                ]);
                            }
                        }
                    }
                } else {
                    if ($request->gerar_conta_pagar) {
                        ContaPagar::create([
                            'empresa_id' => $item->empresa_id,
                            'nfe_id' => $item->id,
                            'fornecedor_id' => $request->fornecedor_id,
                            'valor_integral' => __convert_value_bd($request->valor_fatura[$i]),
                            'tipo_pagamento' => $request->tipo_pagamento[$i],
                            'data_vencimento' => $request->data_vencimento[$i],
                            'local_id' => $item->local_id
                        ]);
                    }
                }

                if ($request->tipo_pagamento) {
                    for ($i = 0; $i < sizeof($tipoPagamento); $i++) {
                        $d = FaturaNfe::create([
                            'nfe_id' => $item->id,
                            'tipo_pagamento' => $tipoPagamento[$i],
                            'data_vencimento' => $request->data_vencimento[$i],
                            'valor' => __convert_value_bd($request->valor_fatura[$i])
                        ]);
                    }
                }

                if ($request->funcionario_id != null) {

                    $comissao = ComissaoVenda::where('empresa_id', $item->empresa_id)
                    ->where('nfe_id', $item->id)->first();

                    if($comissao){
                        $comissao->delete();
                    }

                    $funcionario = Funcionario::findOrFail($request->funcionario_id);
                    $comissao = $funcionario->comissao;
                    $valorRetorno = $this->calcularComissaoVenda($item, $comissao, $item->empresa_id);

                    if($valorRetorno > 0){
                        ComissaoVenda::create([
                            'funcionario_id' => $request->funcionario_id,
                            'nfce_id' => null,
                            'nfe_id' => $item->id,
                            'tabela' => 'nfe',
                            'valor' => $valorRetorno,
                            'valor_venda' => __convert_value_bd($request->valor_total),
                            'status' => 0,
                            'empresa_id' => $item->empresa_id
                        ]);
                    }
                }
            });
session()->flash("flash_success", "Venda alterada com sucesso!");

} catch (\Exception $e) {
    session()->flash("flash_error", 'Algo deu errado: ' . $e->getMessage());
    return redirect()->back();
}

return redirect()->route('contador-empresa.nfe');

}

private function calcularComissaoVenda($nfce, $comissao, $empresa_id)
{
    $valorRetorno = 0;
    $config = ConfigGeral::where('empresa_id', $empresa_id)->first();

    $tipoComissao = 'percentual_vendedor';
    if($config != null && $config->tipo_comissao == 'percentual_margem'){
        $tipoComissao = 'percentual_margem';
    }
    if($tipoComissao == 'percentual_vendedor'){
        $valorRetorno = ((float)$nfce->total * (float)$comissao) / 100;
    }else{
        foreach ($nfce->itens as $i) {

            $percentualLucro = ((($i->produto->valor_compra-$i->valor_unitario)/$i->produto->valor_compra)*100)*-1;
            $margens = MargemComissao::where('empresa_id', $empresa_id)->get();
            $margemComissao = null;
            $dif = 0;
            $difAnterior = 100;
            foreach($margens as $m){
                $margem = $m->margem;
                if($percentualLucro >= $margem){
                    $dif = $percentualLucro - $margem;
                    if($dif < $difAnterior){
                        $margemComissao = $m;
                        $difAnterior = $dif;
                    }
                }
            }
            if($margemComissao){
                $valorRetorno += ($i->sub_total * $margemComissao->percentual) / 100;
            }
        }
    }
    return $valorRetorno;
}

public function create(){

    $contador = Empresa::findOrFail(request()->empresa_id);
    $empresaSelecionada = $contador->empresa_selecionada;

    $caixa = Caixa::where('empresa_id', $empresaSelecionada)->where('status', 1)->first();

    if($caixa == null){
        session()->flash("flash_error", "Caixa esta fechado!");
        return redirect()->back();
    }

    $transportadoras = Transportadora::where('empresa_id', $empresaSelecionada)->get();
    $cidades = Cidade::all();
    $naturezas = NaturezaOperacao::where('empresa_id', $empresaSelecionada)->get();
    $empresa = Empresa::findOrFail($empresaSelecionada);

    $empresa = __objetoParaEmissao($empresa, $caixa->local_id);
    $numeroNfe = Nfe::lastNumero($empresa);
    $isOrcamento = 0;

    $naturezaPadrao = NaturezaOperacao::where('empresa_id', $empresaSelecionada)
    ->where('padrao', 1)->first();

    $config = ConfigGeral::where('empresa_id', $empresaSelecionada)->first();
    $funcionarios = Funcionario::where('empresa_id', $empresaSelecionada)
    ->where('status', 1)->get();

    $user_id = 0;

    $user = UsuarioEmpresa::
    select('usuario_empresas.usuario_id')
    ->where('empresa_id', $empresaSelecionada)
    ->first();

    if($user != null){
        $user_id = $user->usuario_id;
    }

    return view('contador.nfe_create', 
        compact('transportadoras', 'cidades', 'naturezas', 'numeroNfe', 'empresa', 'caixa', 
            'isOrcamento', 'naturezaPadrao', 'config', 'funcionarios', 'empresaSelecionada', 'user_id')
    );

}

public function destroy($id)
{
    $item = Nfe::findOrFail($id);
    __validaObjetoEmpresaContador(request()->empresa_id, $item->empresa_id);

    try {
        if($item->estado != 'cancelado'){
            foreach ($item->itens as $i) {
                if ($i->produto->gerenciar_estoque) {
                    if ($item->tpNF == 1) {
                        $this->util->incrementaEstoque($i->produto_id, $i->quantidade, $i->variacao_id, $item->local_id);
                    }else{
                        $this->util->reduzEstoque($i->produto_id, $i->quantidade, $i->variacao_id, $item->local_id);
                    }
                }

                ItemProducao::where('item_id', $i->id)->delete();
                ItemDimensaoNfe::where('item_nfe_id', $i->id)->delete();
            }
        }

        $comissao = ComissaoVenda::where('empresa_id', $item->empresa_id)
        ->where('nfe_id', $item->id)->first();

        if($comissao){
            $comissao->delete();
        }

        ContaPagar::where('nfe_id', $item->id)->delete();
        ContaReceber::where('nfe_id', $item->id)->delete();

        $item->itens()->delete();
        $item->fatura()->delete();
        ProdutoUnico::where('nfe_id', $id)->delete();
        $item->delete();

        if($item->orcamento == 1){
            $descricaoLog = $item->cliente->info . " R$ " . __moeda($item->total);
            session()->flash("flash_success", "Orçamento removido!");
        }else if($item->tpNF == 0){

            if($item->manifesto){
                $manifesto = $item->manifesto;
                $manifesto->compra_id = null;
                $manifesto->save();
            }
            $descricaoLog = $item->fornecedor->info . " R$ " . __moeda($item->total);
            __createLog(request()->empresa_id, 'Compra', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Compra removida!");
        }else{
            $descricaoLog = $item->cliente->info . " R$ " . __moeda($item->total);
            __createLog(request()->empresa_id, 'Venda', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Venda removida!");
        }
    } catch (\Exception $e) {

        session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
    }
    return redirect()->back();
}

private function setNumeroSequencial($empresa_id){

    $last = Nfe::where('empresa_id', $empresa_id)
    ->orderBy('numero_sequencial', 'desc')
    ->where('numero_sequencial', '>', 0)->first();
    $numero = $last != null ? $last->numero_sequencial : 0;
    $numero++;

    return $numero;
}

public function store(Request $request)
{

    try {

        $nfe = DB::transaction(function () use ($request) {
            $cliente_id = isset($request->cliente_id) ? $request->cliente_id : null;
            $fornecedor_id = isset($request->fornecedor_id) ? $request->fornecedor_id : null;

            $contador = Empresa::findOrFail(request()->empresa_id);
            $empresaSelecionada = $contador->empresa_selecionada;
            $empresa = Empresa::findOrFail($empresaSelecionada);

            if (isset($request->cliente_id)) {
                if ($request->cliente_id == null) {
                    $cliente_id = $this->cadastrarCliente($request, $empresaSelecionada);
                } else {
                    $this->atualizaCliente($request);
                }
            }
            if (isset($request->fornecedor_id)) {
                if ($request->fornecedor_id == null) {
                    $fornecedor_id = $this->cadastrarFornecedor($request, $empresaSelecionada);
                } else {
                    $this->atualizaFornecedor($request);
                }
            }
            $transportadora_id = $request->transportadora_id;
            if ($request->transportadora_id == null) {
                $transportadora_id = $this->cadastrarTransportadora($request, $empresaSelecionada);
            } else {
                $this->atualizaTransportadora($request);
            }
            $config = Empresa::find($empresaSelecionada);

            $tipoPagamento = $request->tipo_pagamento;

            $caixa = Caixa::where('empresa_id', $empresaSelecionada)->where('status', 1)->first();

            $local_id = $caixa->local_id;
            if(isset($request->local_id)){
                $local_id = $request->local_id;
            }
            $valor_produto = number_format($request->valor_produtos, 2);

            if($caixa != null){
                $empresa = __objetoParaEmissao($empresa, $local_id);
            }

            $user = UsuarioEmpresa::
            select('usuario_empresas.usuario_id')
            ->where('empresa_id', $empresaSelecionada)
            ->first();

            $request->merge([
                'numero_sequencial' => $this->setNumeroSequencial($empresaSelecionada),
                'emissor_nome' => $config->nome,
                'emissor_cpf_cnpj' => $config->cpf_cnpj,
                'ambiente' => $config->ambiente,
                'chave' => '',
                'cliente_id' => $cliente_id,
                'fornecedor_id' => $fornecedor_id,
                'transportadora_id' => $transportadora_id,
                'numero_serie' => $empresa->numero_serie_nfe ? $empresa->numero_serie_nfe : 0,
                'numero' => $request->numero_nfe ? $request->numero_nfe : 0,
                'estado' => 'novo',
                'total' => __convert_value_bd($request->valor_total),
                'desconto' => $request->desconto ? __convert_value_bd($request->desconto) : 0,
                'acrescimo' => $request->acrescimo ? __convert_value_bd($request->acrescimo) : 0,
                'valor_produtos' => __convert_value_bd($valor_produto),
                'valor_frete' => $request->valor_frete ? __convert_value_bd($request->valor_frete) : 0,
                'caixa_id' => $caixa ? $caixa->id : null,
                'local_id' => $local_id,
                    // 'numero' => $request->numero ?? 0,
                'tipo_pagamento' => $request->tipo_pagamento[0],
                'user_id' => $user ? $user->usuario_id : null,
                'tpNF' => isset($request->is_compra) ? 0 : 1,
                'empresa_id' => $empresaSelecionada
            ]);

            if($request->orcamento){
                $request->merge([
                    'gerar_conta_receber' => 0,
                ]);
            }

            $nfe = Nfe::create($request->all());

            for ($i = 0; $i < sizeof($request->produto_id); $i++) {

                $product = Produto::findOrFail($request->produto_id[$i]);
                $variacao_id = isset($request->variacao_id[$i]) ? $request->variacao_id[$i] : null;

                $itemNfe = ItemNfe::create([
                    'nfe_id' => $nfe->id,
                    'produto_id' => (int)$request->produto_id[$i],
                    'quantidade' => __convert_value_bd($request->quantidade[$i]),
                    'valor_unitario' => __convert_value_bd($request->valor_unitario[$i]),
                    'sub_total' => __convert_value_bd($request->sub_total[$i]),
                    'perc_icms' => __convert_value_bd($request->perc_icms[$i]),
                    'perc_pis' => __convert_value_bd($request->perc_pis[$i]),
                    'perc_cofins' => __convert_value_bd($request->perc_cofins[$i]),
                    'perc_ipi' => __convert_value_bd($request->perc_ipi[$i]),
                    'cst_csosn' => $request->cst_csosn[$i],
                    'cst_pis' => $request->cst_pis[$i],
                    'cst_cofins' => $request->cst_cofins[$i],
                    'cst_ipi' => $request->cst_ipi[$i],
                    'perc_red_bc' => $request->perc_red_bc[$i] ? __convert_value_bd($request->perc_red_bc[$i]) : 0,
                    'cfop' => $request->cfop[$i],
                    'ncm' => $request->ncm[$i],
                    'codigo_beneficio_fiscal' => $request->codigo_beneficio_fiscal[$i],
                    'variacao_id' => $variacao_id,
                    'cEnq' => $product->cEnq,
                    'xPed' => $request->xPed[$i],
                    'nItemPed' => $request->nItemPed[$i],
                    'infAdProd' => $request->infAdProd[$i],
                ]);

                if($request->orcamento == 0 && $product->tipo_producao){
                    if(isset($request->dimensao_largura)){
                        for($l=0; $l<sizeof($request->dimensao_largura); $l++){
                            if($request->_key[$i] == $request->_line[$l]){
                                ItemProducao::create([
                                    'produto_id' => $itemNfe->produto_id,
                                    'dimensao' => $request->dimensao_altura[$l] . ' x ' . $request->dimensao_largura[$l],
                                    'quantidade' => $request->dimensao_quantidade[$l],
                                    'status' => 0,
                                    'item_id' => $itemNfe->id,
                                    'observacao' => $request->dimensao_observacao[$l] ?? ''
                                ]);
                            }
                        }
                    }else{
                        ItemProducao::create([
                            'produto_id' => $itemNfe->produto_id,
                            'quantidade' => $itemNfe->quantidade,
                            'status' => 0,
                            'item_id' => $itemNfe->id,
                            'observacao' => '',
                            'dimensao' => ''
                        ]);
                    }
                }

                if(isset($request->dimensao_largura)){
                    for($l=0; $l<sizeof($request->dimensao_largura); $l++){
                        if($request->_key[$i] == $request->_line[$l]){

                            ItemDimensaoNfe::create([
                                'item_nfe_id' => $itemNfe->id,
                                'valor_unitario_m2' => __convert_value_bd($request->dimensao_valor_unitario_m2[$l]),
                                'largura' => $request->dimensao_largura[$l],
                                'altura' => $request->dimensao_altura[$l],
                                'quantidade' => $request->dimensao_quantidade[$l],
                                'm2_total' => $request->dimensao_m2_total[$l],
                                'espessura' => $request->dimensao_espessura[$l] ?? 0,
                                'observacao' => $request->dimensao_observacao[$l] ?? '',
                                'sub_total' => __convert_value_bd($request->dimensao_sub_total[$l])
                            ]);
                        }
                    }
                }


                if (isset($request->is_compra)) {

                    $product->valor_compra = __convert_value_bd($request->valor_unitario[$i]);
                    $product->save();

                    ProdutoFornecedor::updateOrCreate([
                        'produto_id' => $product->id,
                        'fornecedor_id' => $fornecedor_id
                    ]);
                }

                if ($product->gerenciar_estoque && $request->orcamento == 0) {
                    if (isset($request->is_compra)) {

                        $this->estoqueUtil->incrementaEstoque($product->id, __convert_value_bd($request->quantidade[$i]), 
                            $variacao_id, $local_id);
                    } else {
                        $this->estoqueUtil->reduzEstoque($product->id, __convert_value_bd($request->quantidade[$i]), 
                            $variacao_id, $local_id);
                    }
                }

                if ($request->is_compra) {

                    $tipo = 'incremento';
                    $codigo_transacao = $nfe->id;
                    $tipo_transacao = 'compra';
                    $this->estoqueUtil->movimentacaoProduto($product->id, __convert_value_bd($request->quantidade[$i]), $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $variacao_id);
                } else {
                    $tipo = 'reducao';
                    $codigo_transacao = $nfe->id;
                    $tipo_transacao = 'venda_nfe';
                    $this->estoqueUtil->movimentacaoProduto($product->id, __convert_value_bd($request->quantidade[$i]), $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $variacao_id);
                }
            }

            if($request->tipo_pagamento){
                if ($request->tipo_pagamento[0] != '') {
                    for ($i = 0; $i < sizeof($tipoPagamento); $i++) {
                        FaturaNfe::create([
                            'nfe_id' => $nfe->id,
                            'tipo_pagamento' => $tipoPagamento[$i],
                            'data_vencimento' => $request->data_vencimento[$i] ? $request->data_vencimento[$i] : date('Y-m-d'),
                            'valor' => __convert_value_bd($request->valor_fatura[$i])
                        ]);
                    }

                    if ($request->tpNF == 1) {
                        if ($request->gerar_conta_receber) {
                            for ($i = 0; $i < sizeof($tipoPagamento); $i++) {
                                ContaReceber::create([
                                    'empresa_id' => $empresaSelecionada,
                                    'nfe_id' => $nfe->id,
                                    'cliente_id' => $cliente_id,
                                    'valor_integral' => __convert_value_bd($request->valor_fatura[$i]),
                                    'tipo_pagamento' => $tipoPagamento[$i],
                                    'data_vencimento' => $request->data_vencimento[$i] ? $request->data_vencimento[$i] : date('Y-m-d'),
                                    'local_id' => $local_id,
                                    'descricao' => "Parcela " . $i+1 . " de " . sizeof($tipoPagamento)
                                ]);
                            }
                        }
                    } else {
                        for ($i = 0; $i < sizeof($tipoPagamento); $i++) {
                            if ($request->gerar_conta_pagar) {
                                ContaPagar::create([
                                    'empresa_id' => $empresaSelecionada,
                                    'nfe_id' => $nfe->id,
                                    'fornecedor_id' => $fornecedor_id,
                                    'valor_integral' => __convert_value_bd($request->valor_fatura[$i]),
                                    'tipo_pagamento' => $tipoPagamento[$i],
                                    'data_vencimento' => $request->data_vencimento[$i] ? $request->data_vencimento[$i] : date('Y-m-d'),
                                    'local_id' => $local_id,
                                    'descricao' => "Parcela " . $i+1 . " de " . sizeof($tipoPagamento)
                                ]);
                            }
                        }
                    }
                }
            }

            if ($request->funcionario_id != null) {

                $funcionario = Funcionario::findOrFail($request->funcionario_id);
                $comissao = $funcionario->comissao;
                $valorRetorno = $this->calcularComissaoVenda($nfe, $comissao, $empresaSelecionada);

                if($valorRetorno > 0){
                    ComissaoVenda::create([
                        'funcionario_id' => $request->funcionario_id,
                        'nfce_id' => null,
                        'nfe_id' => $nfe->id,
                        'tabela' => 'nfe',
                        'valor' => $valorRetorno,
                        'valor_venda' => __convert_value_bd($request->valor_total),
                        'status' => 0,
                        'empresa_id' => $empresaSelecionada
                    ]);
                }
            }

            return $nfe;
        });
session()->flash("flash_success", "Venda cadastrada!");
return redirect()->route('contador-empresa.nfe');

} catch (\Exception $e) {

    session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
    return redirect()->back();
}


}

private function cadastrarCliente($request, $empresa_id)
{
    $cliente = Cliente::create([
        'empresa_id' => $empresa_id,
        'razao_social' => $request->cliente_nome,
        'nome_fantasia' => $request->nome_fantasia ?? '',
        'cpf_cnpj' => $request->cliente_cpf_cnpj,
        'ie' => $request->ie,
        'contribuinte' => $request->contribuinte,
        'consumidor_final' => $request->consumidor_final,
        'email' => $request->email ?? '',
        'telefone' => $request->telefone ?? '',
        'cidade_id' => $request->cliente_cidade,
        'rua' => $request->cliente_rua,
        'cep' => $request->cep,
        'numero' => $request->cliente_numero,
        'bairro' => $request->cliente_bairro,
        'complemento' => $request->complemento
    ]);
    return $cliente->id;
}

private function atualizaCliente($request)
{
    $cliente = Cliente::findOrFail($request->cliente_id);
    $cliente->update([
        'razao_social' => $request->cliente_nome,
        'nome_fantasia' => $request->nome_fantasia ?? '',
        'cpf_cnpj' => $request->cliente_cpf_cnpj,
        'ie' => $request->ie,
        'contribuinte' => $request->contribuinte,
        'consumidor_final' => $request->consumidor_final,
        'email' => $request->email ?? '',
        'telefone' => $request->telefone ?? '',
        'cidade_id' => $request->cliente_cidade,
        'rua' => $request->cliente_rua,
        'cep' => $request->cep,
        'numero' => $request->cliente_numero,
        'bairro' => $request->cliente_bairro,
        'complemento' => $request->complemento
    ]);
    return $cliente->id;
}

private function cadastrarFornecedor($request, $empresa_id)
{
    $fornecedor = Fornecedor::create([
        'empresa_id' => $empresa_id,
        'razao_social' => $request->fornecedor_nome,
        'nome_fantasia' => $request->nome_fantasia ?? '',
        'cpf_cnpj' => $request->fornecedor_cpf_cnpj,
        'ie' => $request->ie,
        'contribuinte' => $request->contribuinte,
        'consumidor_final' => $request->consumidor_final,
        'email' => $request->email ?? '',
        'telefone' => $request->telefone ?? '',
        'cidade_id' => $request->fornecedor_cidade,
        'rua' => $request->fornecedor_rua,
        'cep' => $request->cep,
        'numero' => $request->fornecedor_numero,
        'bairro' => $request->fornecedor_bairro,
        'complemento' => $request->complemento
    ]);
    return $fornecedor->id;
}

private function atualizaFornecedor($request)
{
    $fornecedor = Fornecedor::findOrFail($request->fornecedor_id);
    $fornecedor->update([
        'razao_social' => $request->fornecedor_nome,
        'nome_fantasia' => $request->nome_fantasia ?? '',
        'cpf_cnpj' => $request->fornecedor_cpf_cnpj,
        'ie' => $request->ie,
        'contribuinte' => $request->contribuinte,
        'consumidor_final' => $request->consumidor_final,
        'email' => $request->email ?? '',
        'telefone' => $request->telefone ?? '',
        'cidade_id' => $request->fornecedor_cidade,
        'rua' => $request->fornecedor_rua,
        'cep' => $request->cep,
        'numero' => $request->fornecedor_numero,
        'bairro' => $request->fornecedor_bairro,
        'complemento' => $request->complemento
    ]);
    return $fornecedor->id;
}

private function cadastrarTransportadora($request, $empresa_id)
{
    if ($request->razao_social_transp) {
        $transportadora = Transportadora::create([
            'empresa_id' => $empresa_id,
            'razao_social' => $request->razao_social_transp,
            'nome_fantasia' => $request->nome_fantasia_transp ?? '',
            'cpf_cnpj' => $request->cpf_cnpj_transp,
            'ie' => $request->ie_transp,
            'antt' => $request->antt,
            'email' => $request->email_transp,
            'cidade_id' => $request->cidade_transp,
            'telefone' => $request->telefone_transp,
            'rua' => $request->rua_transp,
            'cep' => $request->cep_transp,
            'numero' => $request->numero_transp,
            'bairro' => $request->bairro_transp,
            'complemento' => $request->complemento_transp
        ]);
        return $transportadora->id;
    }
    return null;
}

private function atualizaTransportadora($request)
{
    if ($request->razao_social_transp) {
        $transportadora = Transportadora::findOrFail($request->transportadora_id);
        $transportadora->update([
            'empresa_id' => $request->empresa_id,
            'razao_social' => $request->razao_social_transp,
            'nome_fantasia' => $request->nome_fantasia_transp ?? '',
            'cpf_cnpj' => $request->cpf_cnpj_transp,
            'ie' => $request->ie_transp,
            'antt' => $request->antt,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'cidade_id' => $request->cidade_transp,
            'rua' => $request->rua_transp,
            'cep' => $request->cep_transp,
            'numero' => $request->numero_transp,
            'bairro' => $request->bairro_transp,
            'complemento' => $request->complemento_transp
        ]);
        return $transportadora->id;
    }
    return null;
}

}
