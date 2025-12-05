<?php

namespace App\Http\Controllers\API\Token;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\ProdutoLocalizacao;
use App\Models\Localizacao;
use App\Models\Estoque;
use App\Models\PadraoTributacaoProduto;
use App\Utils\EstoqueUtil;

class ProdutoController extends Controller
{
    protected $prefix = 'produtos';
    protected $utilEstoque;

    public function __construct(EstoqueUtil $utilEstoque){
        $this->utilEstoque = $utilEstoque;
    }

    public function index(Request $request){

        if(!__validaPermissaoToken($request->token, $this->prefix.".read")){
            return response()->json("Permissão negada!", 403);
        }

        $data = Produto::where('empresa_id', $request->empresa_id)
        ->select('id', 'nome', 'codigo_barras', 'ncm', 'cest', 'unidade', 'perc_icms', 'perc_pis',
            'perc_cofins', 'perc_ipi', 'cst_csosn', 'cst_pis', 'cst_cofins', 'cst_ipi', 'valor_unitario',
            'origem', 'perc_red_bc', 'cfop_estadual', 'cfop_outro_estado', 'cEnq', 'pST',
            'categoria_id', 'gerenciar_estoque', 'valor_compra', 'status', 'marca_id', 'cfop_entrada_estadual', 
            'cfop_entrada_outro_estado', 'modBCST', 'pMVAST', 'pICMSST', 'redBCST', 'balanca_pdv', 'exportar_balanca', 
            'referencia_balanca', 'numero_sequencial', 'valor_atacado', 'quantidade_atacado')
        ->with(['marca', 'categoria', 'estoque'])
        ->get();
        __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'read', $this->prefix);
        return response()->json($data, 200);
    }

    public function find(Request $request, $id){

        if(!__validaPermissaoToken($request->token, $this->prefix.".read")){
            return response()->json("Permissão negada!", 403);
        }

        $empresa_id = $request->empresa_id;
        $item = Produto::where('empresa_id', $empresa_id)
        ->select('id', 'nome', 'codigo_barras', 'ncm', 'cest', 'unidade', 'perc_icms', 'perc_pis',
            'perc_cofins', 'perc_ipi', 'cst_csosn', 'cst_pis', 'cst_cofins', 'cst_ipi', 'valor_unitario',
            'origem', 'perc_red_bc', 'cfop_estadual', 'cfop_outro_estado', 'cEnq', 'pST',
            'categoria_id', 'gerenciar_estoque', 'valor_compra', 'status', 'marca_id', 'cfop_entrada_estadual', 
            'cfop_entrada_outro_estado', 'modBCST', 'pMVAST', 'pICMSST', 'redBCST', 'balanca_pdv', 'exportar_balanca',
            'referencia_balanca', 'numero_sequencial', 'valor_atacado', 'quantidade_atacado')
        ->with(['marca', 'categoria', 'estoque'])
        ->findOrFail($id);
        __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'read', $this->prefix);

        return response()->json($item, 200);
    }

    public function store(Request $request){
        if(!__validaPermissaoToken($request->token, $this->prefix.".create")){
            return response()->json("Permissão negada!", 403);
        }
        try{
            $empresa_id = $request->empresa_id;
            $padraoTributacao = PadraoTributacaoProduto::where('empresa_id', $empresa_id)->where('padrao', 1)
            ->first();

            $validaRequest = $this->validaRequest($request);
            if(sizeof($validaRequest) > 0){
                return response()->json($validaRequest, 403);
            }

            if($padraoTributacao != null){
                if(!isset($request->perc_icms)){
                    $request->merge(['perc_icms' => $padraoTributacao->perc_icms]);
                }
                if(!isset($request->perc_pis)){
                    $request->merge(['perc_pis' => $padraoTributacao->perc_pis]);
                }
                if(!isset($request->perc_cofins)){
                    $request->merge(['perc_cofins' => $padraoTributacao->perc_cofins]);
                }
                if(!isset($request->perc_ipi)){
                    $request->merge(['perc_ipi' => $padraoTributacao->perc_ipi]);
                }
                if(!isset($request->cst_csosn)){
                    $request->merge(['cst_csosn' => $padraoTributacao->cst_csosn]);
                }
                if(!isset($request->cst_pis)){
                    $request->merge(['cst_pis' => $padraoTributacao->cst_pis]);
                }
                if(!isset($request->cst_cofins)){
                    $request->merge(['cst_cofins' => $padraoTributacao->cst_cofins]);
                }
                if(!isset($request->cst_ipi)){
                    $request->merge(['cst_ipi' => $padraoTributacao->cst_ipi]);
                }
                if(!isset($request->perc_red_bc)){
                    $request->merge(['perc_red_bc' => $padraoTributacao->perc_red_bc]);
                }
                if(!isset($request->cEnq)){
                    $request->merge(['cEnq' => $padraoTributacao->cEnq]);
                }
                if(!isset($request->pST)){
                    $request->merge(['pST' => $padraoTributacao->pST]);
                }
                if(!isset($request->cfop_estadual)){
                    $request->merge(['cfop_estadual' => $padraoTributacao->cfop_estadual]);
                }
                if(!isset($request->cfop_outro_estado)){
                    $request->merge(['cfop_outro_estado' => $padraoTributacao->cfop_outro_estado]);
                }
                if(!isset($request->cest)){
                    $request->merge(['cest' => $padraoTributacao->cest]);
                }
                if(!isset($request->ncm)){
                    $request->merge(['ncm' => $padraoTributacao->ncm]);
                }
                if(!isset($request->codigo_beneficio_fiscal)){
                    $request->merge(['codigo_beneficio_fiscal' => $padraoTributacao->codigo_beneficio_fiscal]);
                }
                if(!isset($request->cfop_entrada_estadual)){
                    $request->merge(['cfop_entrada_estadual' => $padraoTributacao->cfop_entrada_estadualc]);
                }
                if(!isset($request->cfop_entrada_outro_estado)){
                    $request->merge(['cfop_entrada_outro_estado' => $padraoTributacao->cfop_entrada_outro_estado]);
                }
                if(!isset($request->modBCST)){
                    $request->merge(['modBCST' => $padraoTributacao->modBCST]);
                }
                if(!isset($request->pMVAST)){
                    $request->merge(['pMVAST' => $padraoTributacao->pMVAST]);
                }
                if(!isset($request->pICMSST)){
                    $request->merge(['pICMSST' => $padraoTributacao->pICMSST]);
                }
                if(!isset($request->redBCST)){
                    $request->merge(['redBCST' => $padraoTributacao->redBCST]);
                }
            }
            // return response()->json($request->all(), 403);

            // $last = Produto::where('empresa_id', $empresa_id)
            // ->orderBy('numero_sequencial', 'desc')
            // ->where('numero_sequencial', '>', 0)->first();
            // $numeroSequencial = $last != null ? $last->numero_sequencial : 0;
            // $numeroSequencial++;
            $numeroSequencial = __getUltimoNumeroSequencial($empresa_id, 'produtos');
            $numeroSequencial++;

            $request->merge([
                'numero_sequencial' => $numeroSequencial,
                'perc_icms' => __convert_value_bd($request->perc_icms),
                'perc_pis' => __convert_value_bd($request->perc_pis),
                'perc_cofins' => __convert_value_bd($request->perc_cofins),
                'perc_ipi' => __convert_value_bd($request->perc_ipi),
                'valor_unitario' => __convert_value_bd($request->valor_unitario),
                'perc_red_bc' => __convert_value_bd($request->perc_red_bc),
                'valor_atacado' => $request->valor_atacado ? __convert_value_bd($request->valor_atacado) : null,
                'valor_compra' => $request->valor_compra ? __convert_value_bd($request->valor_compra) : 0,
                'unidade' => isset($request->unidade) ? $request->unidade : 'UN',
                'cEnq' => isset($request->cEnq) ? $request->cEnq : '999',
                'percentual_lucro' => 100,
                'valor_prazo' => 0
            ]);

            $percentual_lucro = 100;
            if($request->valor_compra > 0){
                $percentual_lucro = 100-($request->valor_compra*100)/$request->valor_unitario;
                $request->merge([
                    'percentual_lucro' => $percentual_lucro
                ]);
            }

            $item = Produto::create($request->all());

            $localizacao = Localizacao::where('empresa_id', $empresa_id)->first();
            ProdutoLocalizacao::updateOrCreate([
                'produto_id' => $item->id, 
                'localizacao_id' => $localizacao->id
            ]);

            if(isset($request->estoque)){
                $qtd = __convert_value_bd($request->estoque);
                $this->utilEstoque->incrementaEstoque($item->id, $qtd, null, $localizacao->id);

                $transacao = Estoque::where('produto_id', $item->id)->orderBy('id', 'desc')->first();
                $tipo = 'incremento';
                $codigo_transacao = $transacao->id;
                $tipo_transacao = 'alteracao_estoque';
                $this->utilEstoque->movimentacaoProduto($item->id, $qtd, $tipo, $codigo_transacao, $tipo_transacao, null, null); 
            }

            $item = Produto::where('empresa_id', $empresa_id)
            ->select('id', 'nome', 'codigo_barras', 'ncm', 'cest', 'unidade', 'perc_icms', 'perc_pis',
                'perc_cofins', 'perc_ipi', 'cst_csosn', 'cst_pis', 'cst_cofins', 'cst_ipi', 'valor_unitario',
                'origem', 'perc_red_bc', 'cfop_estadual', 'cfop_outro_estado', 'cEnq', 'pST',
                'categoria_id', 'gerenciar_estoque', 'valor_compra', 'status', 'marca_id', 'cfop_entrada_estadual', 
                'cfop_entrada_outro_estado', 'modBCST', 'pMVAST', 'pICMSST', 'redBCST', 'balanca_pdv', 'exportar_balanca',
                'referencia_balanca', 'numero_sequencial', 'valor_atacado', 'quantidade_atacado')
            ->with(['marca', 'categoria'])
            ->findOrFail($item->id);
            __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'create', $this->prefix);
            return response()->json($item, 200);

        }catch(\Exception $e){
            __createApiLog($request->empresa_id, $request->token, 'erro', $e->getMessage(), 'create', $this->prefix);
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }

    private function validaRequest($request){
        $dataMessage = [];

        $padraoTributacao = PadraoTributacaoProduto::where('empresa_id', $request->empresa_id)->where('padrao', 1)
        ->first();

        if(!isset($request->nome) || $request->nome == ""){
            array_push($dataMessage, "Informe a nome");
        }
        if(!isset($request->valor_unitario) || $request->valor_unitario <= 0){
            array_push($dataMessage, "Informe a valor unitário");
        }

        if($padraoTributacao == null){
            if(!isset($request->perc_icms)){
                array_push($dataMessage, "Informe o % icms");
            }
            if(!isset($request->perc_pis)){
                array_push($dataMessage, "Informe o % pis");
            }
            if(!isset($request->perc_cofins)){
                array_push($dataMessage, "Informe o % cofins");
            }
            if(!isset($request->perc_ipi)){
                array_push($dataMessage, "Informe o % ipi");
            }
            if(!isset($request->cst_csosn)){
                array_push($dataMessage, "Informe o cst_csosn");
            }
            if(!isset($request->cst_pis)){
                array_push($dataMessage, "Informe o cst_pis");
            }
            if(!isset($request->cst_cofins)){
                array_push($dataMessage, "Informe o cst_cofins");
            }
            if(!isset($request->cst_ipi)){
                array_push($dataMessage, "Informe o cst_ipi");
            }
            if(!isset($request->cfop_estadual)){
                array_push($dataMessage, "Informe o cfop estadual");
            }
            if(!isset($request->cfop_outro_estado)){
                array_push($dataMessage, "Informe o cfop outro estado");
            }
        }

        return $dataMessage;
    }

    public function update(Request $request){
        if(!__validaPermissaoToken($request->token, $this->prefix.".update")){
            return response()->json("Permissão negada!", 403);
        }
        try{
            $empresa_id = $request->empresa_id;

            $request->merge([
                'perc_icms' => __convert_value_bd($request->perc_icms),
                'perc_pis' => __convert_value_bd($request->perc_pis),
                'perc_cofins' => __convert_value_bd($request->perc_cofins),
                'perc_ipi' => __convert_value_bd($request->perc_ipi),
                'valor_unitario' => __convert_value_bd($request->valor_unitario),
                'perc_red_bc' => __convert_value_bd($request->perc_red_bc),
                'valor_compra' => __convert_value_bd($request->valor_compra),
                'valor_atacado' => $request->valor_atacado ? __convert_value_bd($request->valor_atacado) : null,
            ]);

            $item = Produto::where('empresa_id', $empresa_id)
            ->findOrFail($request->id);

            if($item == null){
                return response()->json("Produto não encontrado!", 403);
            }
            
            if($item->empresa_id !=  $empresa_id){
                return response()->json("Não permitido!", 403);
            }

            if(isset($request->estoque)){
                $estoque = Estoque::where('produto_id', $item->id)->first();
                $qtd = __convert_value_bd($request->estoque);
                if($estoque == null){

                    $this->utilEstoque->incrementaEstoque($item->id, $qtd, null, $localizacao->id);

                    $transacao = Estoque::where('produto_id', $item->id)->orderBy('id', 'desc')->first();
                    $tipo = 'incremento';
                    $codigo_transacao = $transacao->id;
                    $tipo_transacao = 'alteracao_estoque';
                    $this->utilEstoque->movimentacaoProduto($item->id, $qtd, $tipo, $codigo_transacao, $tipo_transacao, null, null); 
                }else{
                    $diferenca = 0;
                    $tipo = 'incremento';

                    if($estoque->quantidade > $qtd){
                        $diferenca = $estoque->quantidade - $qtd;
                        $tipo = 'reducao';
                    }else{
                        $diferenca = $qtd - $estoque->quantidade;
                    }
                    $estoque->quantidade = $qtd;
                    $estoque->save();

                    $codigo_transacao = $estoque->id;
                    $tipo_transacao = 'alteracao_estoque';

                    $this->utilEstoque->movimentacaoProduto($estoque->produto_id, $diferenca, $tipo, $codigo_transacao, $tipo_transacao, null);
                }
            }

            $item->fill($request->all())->save();
            __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'update', $this->prefix);

            $item = Produto::where('empresa_id', $empresa_id)
            ->select('id', 'nome', 'codigo_barras', 'ncm', 'cest', 'unidade', 'perc_icms', 'perc_pis',
                'perc_cofins', 'perc_ipi', 'cst_csosn', 'cst_pis', 'cst_cofins', 'cst_ipi', 'valor_unitario',
                'origem', 'perc_red_bc', 'cfop_estadual', 'cfop_outro_estado', 'cEnq', 'pST',
                'categoria_id', 'gerenciar_estoque', 'valor_compra', 'status', 'marca_id', 'cfop_entrada_estadual', 
                'cfop_entrada_outro_estado', 'modBCST', 'pMVAST', 'pICMSST', 'redBCST', 'balanca_pdv', 'exportar_balanca',
                'referencia_balanca', 'numero_sequencial', 'valor_atacado', 'quantidade_atacado')
            ->with(['marca', 'categoria'])
            ->findOrFail($item->id);

            return response()->json($item, 200);

        }catch(\Exception $e){
            __createApiLog($request->empresa_id, $request->token, 'erro', $e->getMessage(), 'update', $this->prefix);
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }

    public function delete(Request $request){
        if(!__validaPermissaoToken($request->token, $this->prefix.".delete")){
            return response()->json("Permissão negada!", 403);
        }
        try{
            $empresa_id = $request->empresa_id;

            $item = Produto::where('empresa_id', $empresa_id)
            ->select('id', 'nome', 'codigo_barras', 'ncm', 'cest', 'unidade', 'perc_icms', 'perc_pis',
                'perc_cofins', 'perc_ipi', 'cst_csosn', 'cst_pis', 'cst_cofins', 'cst_ipi', 'valor_unitario',
                'origem', 'perc_red_bc', 'cfop_estadual', 'cfop_outro_estado', 'cEnq', 'pST',
                'categoria_id', 'gerenciar_estoque', 'valor_compra', 'status', 'marca_id', 'cfop_entrada_estadual', 
                'cfop_entrada_outro_estado', 'modBCST', 'pMVAST', 'pICMSST', 'redBCST', 'balanca_pdv', 'exportar_balanca',
                'referencia_balanca', 'numero_sequencial', 'valor_atacado', 'quantidade_atacado')
            ->with(['marca', 'categoria'])
            ->find($request->id);

            if($item == null){
                return response()->json("Produto não encontrado!", 403);
            }

            if($item->empresa_id != $empresa_id){
                return response()->json("Não permitido!", 403);
            }

            $item->variacoes()->delete();
            $item->variacoesMercadoLivre()->delete();
            $item->itemLista()->delete();
            $item->itemNfe()->delete();
            if($item->estoque){
                $item->estoque->delete();
            }
            $item->itemNfce()->delete();
            $item->itemCarrinhos()->delete();
            $item->movimentacoes()->delete();
            $item->composicao()->delete();
            $item->itemPreVenda()->delete();
            $item->locais()->delete();
            $item->delete();
            __createApiLog($request->empresa_id, $request->token, 'sucesso', '', 'delete', $this->prefix);
            return response()->json($item, 200);

        }catch(\Exception $e){
            __createApiLog($request->empresa_id, $request->token, 'erro', $e->getMessage(), 'delete', $this->prefix);
            return response()->json("Algo deu errado: " . $e->getMessage(), 403);
        }
    }

}
