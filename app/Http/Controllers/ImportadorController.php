<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelImport;
use App\Models\CategoriaProduto;
use App\Models\Cliente;
use App\Models\Fornecedor;
use App\Models\NaturezaOperacao;
use App\Models\Transportadora;
use App\Models\Produto;
use App\Models\Estoque;
use App\Models\Localizacao;

use App\Models\Nfe;
use App\Models\ItemNfe;
use App\Models\FaturaNfe;
use App\Models\ProdutoLocalizacao;
use App\Models\Empresa;

use App\Models\Nfce;
use App\Models\ItemNfce;
use App\Models\FaturaNfce;

use App\Models\ListaPreco;
use App\Models\ItemListaPreco;

use App\Models\ContaReceber;
use App\Models\ContaPagar;

class ImportadorController extends Controller
{
    public function index(Request $request){
        return view('importador.index');
    }

    public function store(Request $request){
        if ($request->hasFile('file')) {

            if (!is_dir(public_path('extract_importador'))) {
                mkdir(public_path('extract_importador'), 0777, true);
            }
            $zip = new \ZipArchive();
            $zip->open($request->file);
            $destino = public_path('extract_importador');

            $this->clearFolder($destino);

            if($zip->extractTo($destino) == TRUE){

                $this->salvaCategorias($destino);
                $this->salvaClientes($destino);
                $this->salvaFornecedores($destino);
                $this->salvaNaturezasOperacao($destino);
                $this->salvaTransportadoras($destino);
                $this->salvaProdutos($destino);
                $this->salvaVendasPedido($destino);
                $this->salvaVendasCaixa($destino);
                $this->salvaListaPreco($destino);
                $this->salvaContasReceber($destino);
                $this->salvaContasPagar($destino);

                session()->flash('flash_success', 'Dados importados!');
                return redirect()->route('home');

            }else {
                session()->flash('flash_error', "Erro ao desconpactar arquivo");
                return redirect()->back();
            }
            $zip->close();
        }
    }

    private function salvaCategorias($destino){
        $file = $destino."/categorias.xlsx";
        $rows = Excel::toArray(new ExcelImport, $file);
        foreach ($rows as $row) {
            foreach ($row as $key => $r) {
                if($key > 0){
                    $objeto = [
                        'empresa_id' => request()->empresa_id,
                        '_id_import' => $r[0],
                        'nome' => $r[1],
                    ];
                    CategoriaProduto::updateOrCreate($objeto);
                }
            }
        }
    }

    private function salvaClientes($destino){
        $file = $destino."/clientes.xlsx";
        $rows = Excel::toArray(new ExcelImport, $file);
        foreach ($rows as $row) {
            foreach ($row as $key => $r) {
                if($key > 0){
                    // dd($r);
                    $objeto = [
                        'empresa_id' => request()->empresa_id,
                        '_id_import' => $r[0],
                        'razao_social' => $r[1],
                        'nome_fantasia' => $r[2] ?? '',
                        'cpf_cnpj' => $r[3],
                        'ie' => $r[4],
                        'rua' => $r[5],
                        'numero' => $r[6],
                        'bairro' => $r[7],
                        'cep' => $r[8] ?? '',
                        'cidade_id' => $r[9],
                        'telefone' => $r[10],
                        'email' => $r[11],
                        'consumidor_final' => $r[12],
                        'contribuinte' => $r[13],
                        'status' => $r[14],
                    ];
                    // dd($objeto);
                    Cliente::updateOrCreate($objeto);
                }
            }
        }
    }

    private function salvaFornecedores($destino){
        $file = $destino."/fornecedores.xlsx";
        $rows = Excel::toArray(new ExcelImport, $file);
        foreach ($rows as $row) {
            foreach ($row as $key => $r) {
                if($key > 0){
                    // dd($r);
                    $objeto = [
                        'empresa_id' => request()->empresa_id,
                        '_id_import' => $r[0],
                        'razao_social' => $r[1],
                        'nome_fantasia' => $r[2] ?? '',
                        'cpf_cnpj' => $r[3],
                        'ie' => $r[4],
                        'rua' => $r[5],
                        'numero' => $r[6],
                        'bairro' => $r[7],
                        'cep' => $r[8] ?? '',
                        'cidade_id' => $r[9],
                        'telefone' => $r[10],
                        'email' => $r[11],
                        'contribuinte' => $r[12],
                    ];
                    // dd($objeto);
                    Fornecedor::updateOrCreate($objeto);
                }
            }
        }
    }

    private function salvaNaturezasOperacao($destino){
        $file = $destino."/natureza_operacao.xlsx";
        $rows = Excel::toArray(new ExcelImport, $file);
        foreach ($rows as $row) {
            foreach ($row as $key => $r) {
                if($key > 0){
                    $objeto = [
                        'empresa_id' => request()->empresa_id,
                        '_id_import' => $r[0],
                        'descricao' => $r[1],
                    ];
                    NaturezaOperacao::updateOrCreate($objeto);
                }
            }
        }
    }

    private function salvaTransportadoras($destino){
        $file = $destino."/transportadoras.xlsx";
        $rows = Excel::toArray(new ExcelImport, $file);
        foreach ($rows as $row) {
            foreach ($row as $key => $r) {
                if($key > 0){
                    $endereco = $r[3];

                    $objeto = [
                        'empresa_id' => request()->empresa_id,
                        '_id_import' => $r[0],
                        'razao_social' => $r[1],
                        'nome_fantasia' => '',
                        'cpf_cnpj' => $r[2],
                        'ie' => '',
                        'rua' => $endereco,
                        'numero' => '',
                        'bairro' => '',
                        'cidade_id' => $r[4],
                        'telefone' => $r[6] ?? '',
                        'email' => $r[5] ?? '',
                    ];

                    Transportadora::updateOrCreate($objeto);
                }
            }
        }
    }

    private function salvaProdutos($destino){
        $file = $destino."/produtos.xlsx";
        $rows = Excel::toArray(new ExcelImport, $file);
        $localizacaoPadrao = Localizacao::where('empresa_id', request()->empresa_id)
        ->first();
        foreach ($rows as $row) {
            foreach ($row as $key => $r) {
                if($key > 0){
                    // dd($r);
                    $categoria = CategoriaProduto::where('empresa_id', request()->empresa_id)
                    ->where('_id_import', $r[6])->first();
                    $objeto = [
                        'empresa_id' => request()->empresa_id,
                        '_id_import' => $r[0],
                        'nome' => $r[1],
                        'valor_unitario' => ($r[2]),
                        'valor_compra' => ($r[3]),
                        'unidade' => $r[4],
                        'codigo_barras' => $r[5],
                        'categoria_id' => $categoria != null ? $categoria->id : null,
                        'gerenciar_estoque' => $r[8],
                        'cfop_estadual' => $r[9],
                        'cfop_outro_estado' => $r[10],
                        'ncm' => $r[11],
                        'cest' => $r[12] ?? '',
                        'cst_csosn' => $r[13],
                        'cst_pis' => $r[14],
                        'cst_cofins' => $r[15],
                        'cst_ipi' => $r[16],
                        'perc_icms' => $r[17],
                        'perc_pis' => $r[18],
                        'perc_cofins' => $r[19],
                        'perc_ipi' => $r[20],
                        'status' => 1
                    ];
                    // dd($objeto);
                    $produto = Produto::updateOrCreate($objeto);
                    
                    ProdutoLocalizacao::updateOrCreate([
                        'produto_id' => $produto->id,
                        'localizacao_id' => $localizacaoPadrao->id
                    ]);
                    if($r[7]){

                        $objeto = [
                            'produto_id' => $produto->id,
                            'quantidade' => $r[7],
                            'produto_variacao_id' => null,
                            'local_id' => $localizacaoPadrao->id
                        ];
                        Estoque::updateOrCreate($objeto);
                    }

                }
            }
        }
    }

    private function salvaVendasPedido($destino){
        $file = $destino."/vendas_pedido.xlsx";
        $rows = Excel::toArray(new ExcelImport, $file);
        $proximoVenda = 0;
        $proximoItem = 0;
        $proximoFatura = 0;
        $localizacaoPadrao = Localizacao::where('empresa_id', request()->empresa_id)
        ->first();
        $empresa = Empresa::findOrFail(request()->empresa_id);

        foreach ($rows as $row) {

            foreach ($row as $key => $r) {
                if($key > 0){
                    try{
                        if($r[0] == 'Venda'){
                            $proximoVenda = 1;
                            $proximoItem = 0;
                            $proximoFatura = 0;
                        }
                        elseif($r[0] == 'PRODUTO ID'){
                            $proximoItem = 1;
                        }
                        elseif($r[0] == 'TIPO DE PAGAMENTO'){
                            $proximoFatura = 1;
                            $proximoItem = 0;
                        }
                        else{

                            if($proximoVenda == 1){
                                $cliente = Cliente::where('empresa_id', request()->empresa_id)
                                ->where('_id_import', $r[1])->first();
                                $natureza = NaturezaOperacao::where('empresa_id', request()->empresa_id)
                                ->where('_id_import', $r[2])->first();

                                $transportadora = null;
                                if($r[3]){
                                    $transportadora = Transportadora::where('empresa_id', request()->empresa_id)
                                    ->where('_id_import', $r[3])->first();
                                }
                                
                                $objetoVenda = [
                                    'cliente_id' => $cliente->id,
                                    'natureza_id' => $natureza->id,
                                    'transportadora_id' => $transportadora ? $transportadora->id : null,
                                    'total' => ($r[4]),
                                    'desconto' => ($r[5]),
                                    'acrescimo' => ($r[6]),
                                    'estado' => Nfe::getEstadoImport($r[7]),
                                    'numero' => $r[8],
                                    'chave' => $r[9] ?? '',
                                    'observacao' => $r[10],
                                    'created_at' => $r[11],
                                    'local_id' => $localizacaoPadrao->id,
                                    'empresa_id' => request()->empresa_id,
                                    'tipo' => 0,
                                    'numero_serie' => $empresa->numero_serie_nfe ?? 1,
                                    'tpNF' => 1
                                ];

                                if($objetoVenda['estado'] == 'aprovado'){
                                    //upload arquivo
                                    $fileXml = $destino."/".$objetoVenda['chave'].".xml";
                                    if(file_exists($fileXml)){
                                        copy($fileXml, public_path('xml_nfe/').$objetoVenda['chave'].".xml");
                                    }
                                }

                                $nfe = Nfe::create($objetoVenda);
                                $nfe->created_at = $r[11];
                                $nfe->save();
                            //salvar itens
                            }

                            if($proximoItem == 1){

                                $produto = Produto::where('empresa_id', request()->empresa_id)
                                ->where('_id_import', $r[0])->first();
                                $objetoItem = [
                                    'nfe_id' => $nfe->id,
                                    'produto_id' => $produto->id,
                                    'quantidade' => ($r[1]),
                                    'valor_unitario' => ($r[2]),
                                    'sub_total' => ($r[3]),
                                ];
                                ItemNfe::create($objetoItem);
                            }

                            if($proximoFatura == 1){
                                $fat = FaturaNfe::create([
                                    'nfe_id' => $nfe->id,
                                    'tipo_pagamento' => $r[0],
                                    'data_vencimento' => $r[1],
                                    'valor' => ($r[2])
                                ]);
                                // dd($fat);
                            }
                            $proximoVenda = 0;
                        }
                    }catch(\Exception $e){
                        echo $e->getMessage();
                        die;

                    }
                }
            }
        }
    }

    private function salvaVendasCaixa($destino){
        $file = $destino."/vendas_caixa.xlsx";
        $rows = Excel::toArray(new ExcelImport, $file);
        $proximoVenda = 0;
        $proximoItem = 0;
        $proximoFatura = 0;
        $localizacaoPadrao = Localizacao::where('empresa_id', request()->empresa_id)
        ->first();
        $empresa = Empresa::findOrFail(request()->empresa_id);
        foreach ($rows as $row) {
            foreach ($row as $key => $r) {
                if($key > 0){
                    // dd($r);
                    try{
                        if($r[0] == 'Venda'){
                            $proximoVenda = 1;
                            $proximoItem = 0;
                            $proximoFatura = 0;
                        }
                        elseif($r[0] == 'PRODUTO ID'){
                            $proximoItem = 1;

                        }
                        elseif($r[0] == 'TIPO DE PAGAMENTO'){
                            $proximoFatura = 1;
                            $proximoItem = 0;
                        }
                        else{

                            if($proximoVenda == 1){

                                $cliente = null;
                                $cliente = Cliente::where('empresa_id', request()->empresa_id)
                                ->where('_id_import', $r[1])->first();
                                $natureza = NaturezaOperacao::where('empresa_id', request()->empresa_id)
                                ->where('_id_import', $r[2])->first();

                                $transportadora = null;
                                if($r[3]){
                                    $transportadora = Transportadora::where('empresa_id', request()->empresa_id)
                                    ->where('_id_import', $r[3])->first();
                                }
                                
                                $objetoVenda = [
                                    'cliente_id' => $cliente ? $cliente->id : null,
                                    'natureza_id' => $natureza->id,
                                    'ambiente' => $empresa->ambiente,
                                    'total' => ($r[3]),
                                    'desconto' => ($r[4]),
                                    'acrescimo' => ($r[5]),
                                    'estado' => Nfe::getEstadoImport($r[11]),
                                    'numero' => $r[12],
                                    'chave' => $r[13] ?? '',
                                    'observacao' => $r[14],
                                    'local_id' => $localizacaoPadrao->id,
                                    'empresa_id' => request()->empresa_id,
                                    'numero_serie' => $empresa->numero_serie_nfce ?? 1,
                                    'cliente_cpf_cnpj' => $r[9] ?? '',
                                    'cliente_nome' => $r[10] ?? '',
                                    'troco' => $r[6] ?? 0,
                                ];
                                // dd($objetoVenda);
                                if($objetoVenda['estado'] == 'aprovado'){
                                    //upload arquivo
                                    $fileXml = $destino."/".$objetoVenda['chave'].".xml";
                                    if(file_exists($fileXml)){
                                        copy($fileXml, public_path('xml_nfce/').$objetoVenda['chave'].".xml");
                                    }
                                }

                            // dd($objetoVenda);
                                $nfe = Nfce::create($objetoVenda);
                                $nfe->created_at = $r[15];
                                $nfe->save();
                            //salvar itens
                            }

                            if($proximoItem == 1){

                                $produto = Produto::where('empresa_id', request()->empresa_id)
                                ->where('_id_import', $r[0])->first();
                                $objetoItem = [
                                    'nfce_id' => $nfe->id,
                                    'produto_id' => $produto->id,
                                    'quantidade' => ($r[1]),
                                    'valor_unitario' => ($r[2]),
                                    'sub_total' => ($r[3]),
                                ];
                                // dd($objetoItem);
                                ItemNfce::create($objetoItem);
                            }

                            if($proximoFatura == 1){
                                $fat = FaturaNfce::create([
                                    'nfce_id' => $nfe->id,
                                    'tipo_pagamento' => $r[0],
                                    'data_vencimento' => $r[1],
                                    'valor' => ($r[2])
                                ]);
                                // dd($fat);
                            }
                            $proximoVenda = 0;
                        }
                    }catch(\Exception $e){
                        // echo $e->getMessage();

                    }
                }
            }
        }
    }

    private function salvaListaPreco($destino){
        $file = $destino."/lista_preco.xlsx";
        $rows = Excel::toArray(new ExcelImport, $file);
        $proximoItem = 0;
        $proximoLista = 0;

        foreach ($rows as $row) {
            foreach ($row as $key => $r) {
                if($key > 0){
                    if($r[0] == 'LISTA'){
                        $proximoLista = 1;
                        $proximoItem = 0;
                    }
                    elseif($r[0] == 'LISTA ID'){
                        $proximoItem = 1;
                    }
                    else{
                        if($proximoLista == 1){
                            $objeto = [
                                'empresa_id' => request()->empresa_id,
                                'nome' => $r[1],
                                'percentual_alteracao' => ($r[2]),
                                'ajuste_sobre' => $r[3] == 1 ? 'valor_compra' : 'valor_venda',
                                'tipo' => $r[3] == 1 ? 'incremento' : 'reducao',
                            ];
                            $lista = ListaPreco::create($objeto);
                            
                        }

                        if($proximoItem == 1){
                            $produto = Produto::where('empresa_id', request()->empresa_id)
                            ->where('_id_import', $r[1])->first();
                            ItemListaPreco::create([
                                'lista_id' => $lista->id,
                                'produto_id' => $produto->id,
                                'valor' => ($r[3]),
                                'percentual_lucro' => ($r[2]),
                            ]);
                        }

                        $proximoLista = 0;

                    }
                }
            }
        }
    }

    private function salvaContasReceber($destino){
        $file = $destino."/contas_receber.xlsx";
        $rows = Excel::toArray(new ExcelImport, $file);
        $localizacaoPadrao = Localizacao::where('empresa_id', request()->empresa_id)
        ->first();
        foreach ($rows as $row) {
            foreach ($row as $key => $r) {
                if($key > 0){

                    $cliente = Cliente::where('empresa_id', request()->empresa_id)
                    ->where('_id_import', $r[1])->first();
                    $objeto = [
                        'empresa_id' => request()->empresa_id,
                        'cliente_id' => $cliente ? $cliente->id : null,
                        'data_vencimento' => $r[2],
                        'data_recebimento' => $r[3],
                        'valor_integral' => ($r[4]),
                        'valor_recebido' => ($r[5]),
                        'status' => $r[6],
                        'observacao' => $r[7] ?? '',
                        'tipo_pagamento' => $r[8] ?? '01',
                        'local_id' => $localizacaoPadrao->id
                    ];
                    // dd($objeto);
                    $conta = ContaReceber::create($objeto);
                    $conta->created_at = $r[9];
                    $conta->save();
                }
            }
        }
    }

    private function salvaContasPagar($destino){
        $file = $destino."/contas_pagar.xlsx";
        $rows = Excel::toArray(new ExcelImport, $file);
        $localizacaoPadrao = Localizacao::where('empresa_id', request()->empresa_id)
        ->first();
        foreach ($rows as $row) {
            foreach ($row as $key => $r) {
                if($key > 0){

                    $fornecedor = Fornecedor::where('empresa_id', request()->empresa_id)
                    ->where('_id_import', $r[1])->first();
                    $objeto = [
                        'empresa_id' => request()->empresa_id,
                        'fornecedor' => $fornecedor ? $fornecedor->id : null,
                        'data_vencimento' => $r[2],
                        'data_pagamento' => $r[3],
                        'valor_integral' => ($r[4]),
                        'valor_pago' => ($r[5]),
                        'status' => $r[6],
                        'observacao' => $r[7] ?? '',
                        'tipo_pagamento' => $r[8] ?? '01',
                        'local_id' => $localizacaoPadrao->id
                    ];
                    // dd($objeto);
                    $conta = ContaPagar::create($objeto);
                    $conta->created_at = $r[9];
                    $conta->save();
                }
            }
        }
    }

    private function clearFolder($destino){
        $files = glob($destino."/*");
        foreach($files as $file){ 
            if(is_file($file)) unlink($file); 
        }
    }
}
