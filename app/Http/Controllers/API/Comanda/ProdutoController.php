<?php

namespace App\Http\Controllers\API\Comanda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\CategoriaProduto;
use App\Models\PadraoTributacaoProduto;
use App\Models\ProdutoLocalizacao;
use App\Models\Localizacao;
use App\Models\Funcionario;
use App\Models\Estoque;
use App\Utils\EstoqueUtil;
use Illuminate\Support\Facades\DB;
use App\Utils\UploadUtil;

class ProdutoController extends Controller
{

    protected $utilEstoque;
    protected $utilUpload;
    public function __construct(EstoqueUtil $utilEstoque, UploadUtil $utilUpload)
    {
        $this->utilEstoque = $utilEstoque;
        $this->utilUpload = $utilUpload;
    }

    public function index(Request $request){

        $data = Produto::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->orderBy('nome')
        ->with(['categoria', 'variacoes', 'estoque', 'adicionais', 'pizzaValores'])
        ->select('id', 'nome', 'valor_unitario', 'categoria_id', 'codigo_barras', 'unidade', 'referencia',
            'valor_prazo', 'gerenciar_estoque', 'imagem', 'valor_compra')
        ->get();

        return response()->json($data, 200);
    }

    public function categorias(Request $request){

        $data = CategoriaProduto::where('empresa_id', $request->empresa_id)
        ->where('status', 1)
        ->select('nome', 'id')
        ->where('categoria_id', null)
        ->orderBy('nome')
        ->get();

        return response()->json($data, 200);
    }

    public function update(Request $request, $id){
        $item = Produto::findOrFail($id);
        $valorVenda = __convert_value_bd(str_replace("R$ ", "", $request->valor_venda));
        $valorCompra = __convert_value_bd(str_replace("R$ ", "", $request->valor_compra));
        try{
            $data = [
                'nome' => $request->nome,
                'categoria_id' => $request->categoria_id,
                'codigo_barras' => $request->codigo_barras,
                'valor_unitario' => $valorVenda,
                'valor_compra' => $valorCompra,
            ];

            $item->update($data);

            if($request->estoque_atual){
                $quantidade = $request->estoque_atual;
                $funcionario = Funcionario::where('codigo', $request->codigo_operador)
                ->where('empresa_id', $request->empresa_id)->first();

                if($funcionario && $funcionario->usuario){
                    $estoque = Estoque::where('produto_id', $id)->first();

                    if($estoque){
                        $diferenca = 0;
                        $tipo = 'incremento';

                        if($estoque->quantidade > $quantidade){
                            $diferenca = $estoque->quantidade - $quantidade;
                            $tipo = 'reducao';
                        }else{
                            $diferenca = $quantidade - $estoque->quantidade;
                        }
                        $estoque->quantidade = $quantidade;
                        $estoque->save();

                        $codigo_transacao = $estoque->id;
                        $tipo_transacao = 'alteracao_estoque';

                        $this->utilEstoque->movimentacaoProduto($estoque->produto_id, $diferenca, $tipo, $codigo_transacao, $tipo_transacao, 
                            $funcionario->usuario->id);
                    }else{
                        $this->utilEstoque->incrementaEstoque($produto->id, $quantidade, null);
                        $transacao = Estoque::where('produto_id', $produto->id)->orderBy('id', 'desc')->first();

                        $codigo_transacao = $transacao->id;
                        $tipo_transacao = 'alteracao_estoque';
                        $this->utilEstoque->movimentacaoProduto($produto->id, $quantidade, $tipo, $codigo_transacao, 
                            $tipo_transacao, $funcionario->usuario->id);
                    }
                }
            }

            return response()->json("ok", 200);

        }catch(\Exception $e){
            return response()->json($e->getMessage(), 401);
        }
    }

    public function store(Request $request){
        $padraoTributacaoProduto = PadraoTributacaoProduto::where('empresa_id', $request->empresa_id)
        ->where('padrao', 1)->first();

        if($padraoTributacaoProduto == null){
            return response()->json("Defina um padrão de tributação primeiro!", 401);
        }

        $valorVenda = __convert_value_bd(str_replace("R$ ", "", $request->valor_venda));
        $valorCompra = __convert_value_bd(str_replace("R$ ", "", $request->valor_compra));

        $data = [
            'nome' => $request->nome,
            'categoria_id' => $request->categoria_id,
            'codigo_barras' => $request->codigo_barras,
            'valor_unitario' => $valorVenda,
            'valor_compra' => $valorCompra,

            'empresa_id' => $request->empresa_id,
            'perc_icms' => $padraoTributacaoProduto->perc_icms,
            'perc_pis' => $padraoTributacaoProduto->perc_pis,
            'perc_cofins' => $padraoTributacaoProduto->perc_cofins,
            'perc_ipi' => $padraoTributacaoProduto->perc_ipi,
            'cst_csosn' => $padraoTributacaoProduto->cst_csosn,
            'cst_pis' => $padraoTributacaoProduto->cst_pis,
            'cst_cofins' => $padraoTributacaoProduto->cst_cofins,
            'cst_ipi' => $padraoTributacaoProduto->cst_ipi,
            'perc_red_bc' => $padraoTributacaoProduto->perc_red_bc,
            'cEnq' => $padraoTributacaoProduto->cEnq,
            'pST' => $padraoTributacaoProduto->pST,
            'cfop_estadual' => $padraoTributacaoProduto->cfop_estadual,
            'cfop_outro_estado' => $padraoTributacaoProduto->cfop_outro_estado,
            'cest' => $padraoTributacaoProduto->cest,
            'ncm' => $padraoTributacaoProduto->ncm,
            'codigo_beneficio_fiscal' => $padraoTributacaoProduto->codigo_beneficio_fiscal,
            'cfop_entrada_estadual' => $padraoTributacaoProduto->cfop_entrada_estadual,
            'cfop_entrada_outro_estado' => $padraoTributacaoProduto->cfop_entrada_outro_estado,
            'modBCST' => $padraoTributacaoProduto->modBCST,
            'pMVAST' => $padraoTributacaoProduto->pMVAST,
            'pICMSST' => $padraoTributacaoProduto->pICMSST,
            'redBCST' => $padraoTributacaoProduto->redBCST,
        ];

        $produto = Produto::create($data);
        $localPadrao = Localizacao::where('status', 1)->where('empresa_id', $request->empresa_id)
        ->first();
        ProdutoLocalizacao::updateOrCreate([
            'produto_id' => $produto->id, 
            'localizacao_id' => $localPadrao->id
        ]);


        if($request->estoque_inicial){
            $funcionario = Funcionario::where('codigo', $request->codigo_operador)
            ->where('empresa_id', $request->empresa_id)->first();
            if($funcionario && $funcionario->usuario){
                $this->utilEstoque->incrementaEstoque($produto->id, $request->estoque_inicial, null);
                $transacao = Estoque::where('produto_id', $produto->id)->orderBy('id', 'desc')->first();

                $codigo_transacao = $transacao->id;
                $tipo_transacao = 'alteracao_estoque';
                $this->utilEstoque->movimentacaoProduto($produto->id, $request->estoque_inicial, $tipo, $codigo_transacao, 
                    $tipo_transacao, $funcionario->usuario->id);
            }
        }
        return response()->json("ok", 200);
    }

    public function destroy($id){
        $item = Produto::findOrFail($id);

        try{
            $descricaoLog = $item->nome;

            DB::transaction(function () use ($item) {
                if($item->estoque){
                    $item->estoque->delete();
                }
                $item->movimentacoes()->delete();
                $item->variacoes()->delete();
                $item->variacoesMercadoLivre()->delete();
                $item->itemLista()->delete();
                $item->ibpt()->delete();
                // $item->itemNfe()->delete();
                // $item->estoque()->delete();
                $item->galeria()->delete();

                // $item->itemNfce()->delete();
                $item->itemCarrinhos()->delete();
                $item->composicao()->delete();
                $item->itemPreVenda()->delete();
                $item->itensDoCombo()->delete();
                $item->fornecedores()->delete();
                $item->locais()->delete();

                \App\Models\ImpressoraPedidoProduto::where('produto_id', $item->id)->delete();

                $item->delete();
            });
            $this->utilUpload->unlinkImage($item, '/produtos');

            __createLog($item->empresa_id, 'Produto', 'excluir', $descricaoLog);
            return response()->json("ok", 200);

        }catch(\Exception $e){
            __createLog($item->empresa_id, 'Produto', 'erro', $e->getMessage());
            return response()->json($e->getMessage(), 401);
        }
    }

    public function upload(Request $request){

        if (!$request->hasFile('image')) {
            return response()->json('Nenhuma imagem enviada', 401);
        }

        $file = $request->file('image');
        $file_name = $this->utilUpload->uploadImage($request, '/produtos');

        if($request->produto_id == null){
            $produto = Produto::where('empresa_id', $request->empresa_id)
            ->orderBy('id', 'desc')->first();
        }else{
            $produto = Produto::findOrFail($request->produto_id);
            $this->utilUpload->unlinkImage($produto, '/produtos');
        }
        $produto->imagem = $file_name;
        $produto->save();

        return response()->json("ok", 200);
    }

}
