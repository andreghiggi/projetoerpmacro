<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProduto;
use Illuminate\Http\Request;
use App\Models\TipoDespesaFrete;
use App\Models\DespesaFrete;
use App\Models\Produto;
use App\Models\Cliente;
use App\Models\Reserva;
use App\Models\ComissaoVenda;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\Fornecedor;
use App\Models\Acomodacao;
use App\Models\ItemNfe;
use App\Models\Empresa;
use App\Models\ItemNfce;
use App\Models\Nfe;
use App\Models\Nfce;
use App\Models\Cte;
use App\Models\Mdfe;
use App\Models\Funcionario;
use App\Models\OrdemServico;
use App\Models\Localizacao;
use App\Models\Marca;
use App\Models\TaxaPagamento;
use App\Models\Estoque;
use App\Models\MovimentacaoProduto;
use Dompdf\Dompdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RelatorioEstoqueExport;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    public function index()
    {
        $marcas = Marca::where('empresa_id', request()->empresa_id)->get();
        $categorias = CategoriaProduto::where('empresa_id', request()->empresa_id)
        // ->where('categoria_id', null)
        ->where('status', 1)->get();
        $funcionarios = Funcionario::where('empresa_id', request()->empresa_id)->get();
        $tiposDespesaFrete = TipoDespesaFrete::where('empresa_id', request()->empresa_id)->get();

        return view('relatorios.index', compact('funcionarios', 'marcas', 'categorias', 'tiposDespesaFrete'));
    }

    public function produtos(Request $request)
    {
        // dd($request);
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);
        $estoque = $request->estoque;
        $tipo = $request->tipo;
        $marca_id = $request->marca_id;
        $categoria_id = $request->categoria_id;
        $local_id = $request->local_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $data = Produto::select('produtos.*')
        ->where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('produtos.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('produtos.created_at', '<=', $end_date);
        })
        ->when($estoque != '', function ($query) use ($estoque) {
            if ($estoque == 1) {
                return $query->join('estoques', 'estoques.produto_id', '=', 'produtos.id')
                ->where('estoques.quantidade', '>', 0);
            } elseif($estoque == -1) {
                // return $query->leftJoin('estoques', 'estoques.produto_id', '=', 'produtos.id')
                // ->whereNull('estoques.produto_id')
                // ->orWhere(function ($q) use ($query) {
                //     return $q->join('estoques', 'estoques.produto_id', '=', 'produtos.id')
                //     ->where('estoques.quantidade', '=', 0);
                // });
                return $query->leftJoin('estoques', 'estoques.produto_id', '=', 'produtos.id')
                ->where(function ($q) {
                   $q->whereNull('estoques.id')
                   ->orWhere('estoques.quantidade', '=', 0);
               });
            }else{
                return $query->join('estoques', 'estoques.produto_id', '=', 'produtos.id')
                ->whereColumn('estoques.quantidade', '<', 'produtos.estoque_minimo')
                ->where('produtos.estoque_minimo', '>', 0);
            }
        })
        ->when(!empty($categoria_id), function ($query) use ($categoria_id) {
            return $query->where(function($t) use ($categoria_id) 
            {
                $t->where('categoria_id', $categoria_id)->orWhere('sub_categoria_id', $categoria_id);
            });
        })
        ->when(!empty($marca_id), function ($query) use ($marca_id) {
            return $query->where('marca_id', $marca_id);
        })
        ->when(!empty($local_id), function ($query) use ($local_id) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('produto_localizacaos.localizacao_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->whereIn('produto_localizacaos.localizacao_id', $locais);
        })
        ->get();

        if ($tipo != '') {
            if ($tipo ==1 || $tipo == -1) {
                foreach ($data as $item) {
                    $sumNfe = ItemNfe::where('produto_id', $item->id)
                    ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
                    ->where('nves.tpNF', 1)
                    ->sum('quantidade');

                    $sumNfce = ItemNfce::where('produto_id', $item->id)
                    ->sum('quantidade');

                    $item->quantidade_vendida = $sumNfe + $sumNfce;
                }
            }else{
                foreach ($data as $item) {
                    $sumNfe = ItemNfe::where('produto_id', $item->id)
                    ->select('item_nves.*')
                    ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
                    ->where('nves.tpNF', 0)
                    ->sum('quantidade');

                    $item->quantidade_vendida = $sumNfe;
                }
            }

            $data = $data->filter(function ($item) {
                return $item->quantidade_vendida > 0;
            });

            if ($tipo ==1 || $tipo == -1) {
                if ($tipo == 1) {
                    $data = $data->sortByDesc('quantidade_vendida');
                } else {
                    $data = $data->sortBy('quantidade_vendida');
                }
            }else{
                if ($tipo == 2) {
                    $data = $data->sortByDesc('quantidade_vendida');
                } else {
                    $data = $data->sortBy('quantidade_vendida');
                }
            }
        }

        $marca = null;
        if($marca_id != null){
            $marca = Marca::findOrFail($marca_id);
        }

        $categoria = null;
        if($categoria_id != null){
            $categoria = CategoriaProduto::findOrFail($categoria_id);
        }

        $p = view('relatorios/produtos', compact('data', 'tipo', 'marca', 'categoria'))
        ->with('title', 'Relatório de Produtos');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);

        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Produtos.pdf", array("Attachment" => false));
    }

    public function clientes(Request $request)
    {
        $tipo = $request->tipo;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $data = Cliente::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })->get();

        if ($tipo != '') {
            foreach ($data as $item) {
                $sumNfe = Nfe::where('cliente_id', $item->id)
                ->sum('total');

                $sumNfce = Nfce::where('cliente_id', $item->id)
                ->sum('total');

                $item->total = $sumNfe + $sumNfce;
            }

            if ($tipo == 1) {
                $data = $data->sortByDesc('total');
            } else {
                $data = $data->sortBy('total');
            }
        }

        $p = view('relatorios/clientes', compact('data', 'tipo'))
        ->with('title', 'Relatório de Clientes');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Clientes.pdf", array("Attachment" => false));
    }

    public function fornecedores(Request $request)
    {
        $tipo = $request->tipo;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $data = Fornecedor::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })->get();

        if ($tipo != '') {
            foreach ($data as $item) {
                $sumNfe = Nfe::where('fornecedor_id', $item->id)
                ->where('tpNF', 0)
                ->sum('total');

                $item->total = $sumNfe;
            }

            if ($tipo == 1) {
                $data = $data->sortByDesc('total');
            } else {
                $data = $data->sortBy('total');
            }
        }

        $p = view('relatorios/fornecedores', compact('data', 'tipo'))
        ->with('title', 'Relatório de Fornecedores');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Fornecedores.pdf", array("Attachment" => false));
    }

    public function nfe(Request $request)
    {

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $tipo = $request->tipo;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $finNFe = $request->finNFe;
        $cliente = $request->cliente;
        $estado = $request->estado;
        $local_id = $request->local_id;

        $data = Nfe::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data_emissao', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('data_emissao', '<=', $end_date);
        })
        ->when(!empty($cliente), function ($query) use ($cliente) {
            return $query->where('cliente_id', $cliente);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when(!empty($tipo), function ($query) use ($tipo) {
            return $query->where('tpNF', $tipo);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->where('orcamento', 0)
        ->when(!empty($finNFe), function ($query) use ($finNFe) {
            return $query->where('finNFe', $finNFe);
        })->get();

        $p = view('relatorios/nfe', compact('data'))
        ->with('title', 'Relatório de NFe');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de NFe.pdf", array("Attachment" => false));
    }

    public function nfce(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $cliente_id = $request->cliente;
        $estado = $request->estado;
        $local_id = $request->local_id;

        $data = Nfce::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data_emissao', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('data_emissao', '<=', $end_date);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })->get();

        $p = view('relatorios/nfce', compact('data'))
        ->with('title', 'Relatório de NFCe');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de NFCe.pdf", array("Attachment" => false));
    }

    public function cte(Request $request)
    {

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $estado = $request->estado;
        $local_id = $request->local_id;

        $data = Cte::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();

        $p = view('relatorios/cte', compact('data'))
        ->with('title', 'Relatório de CTe');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de CTe.pdf", array("Attachment" => false));
    }

    public function mdfe(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $estado = $request->estado;
        $local_id = $request->local_id;

        $data = Mdfe::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado_emissao', $estado);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();


        $p = view('relatorios/mdfe', compact('data'))
        ->with('title', 'Relatório de MDFe');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de MDFe.pdf", array("Attachment" => false));
    }

    public function conta_pagar(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status;
        $local_id = $request->local_id;
        $fornecedor_id = $request->fornecedor_id;

        $data = ContaPagar::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data_vencimento', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('data_vencimento', '<=', $end_date);
        })
        ->when(!empty($status), function ($query) use ($status) {
            if ($status == -1) {
                return $query->where('status', '!=', 1);
            } else {
                return $query->where('status', $status);
            }
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->when($fornecedor_id, function ($query) use ($fornecedor_id) {
            return $query->where('fornecedor_id', $fornecedor_id);
        })
        ->orderBy('data_vencimento')
        ->get();

        $p = view('relatorios/conta_pagar', compact('data'))
        ->with('title', 'Relatório de Contas a Pagar');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Contas a Pagar.pdf", array("Attachment" => false));
    }

    public function conta_receber(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status;
        $local_id = $request->local_id;
        $cliente_id = $request->cliente;

        $data = ContaReceber::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('data_vencimento', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('data_vencimento', '<=', $end_date);
        })
        ->when(!empty($status), function ($query) use ($status) {
            if ($status == -1) {
                return $query->where('status', '!=', 1);
            } else {
                return $query->where('status', $status);
            }
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->when($cliente_id, function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->orderBy('data_vencimento')
        ->get();

        $p = view('relatorios/conta_receber', compact('data'))
        ->with('title', 'Relatório de Contas a Receber');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Contas a Receber.pdf", array("Attachment" => false));
    }

    public function comissao(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $funcionario_id = $request->funcionario_id;

        $data = ComissaoVenda::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($funcionario_id), function ($query) use ($funcionario_id) {
            return $query->where('funcionario_id', $funcionario_id);
        })
        ->get();

        $p = view('relatorios/comissao', compact('data'))
        ->with('title', 'Relatório de Comissao');

        // if ($funcionario_id == null) {
        //     session()->flash('flash_error', 'Selecione um funcionário para continuar');
        //     return redirect()->back();
        // }

        $p = view('relatorios/comissao', compact('data'))
        ->with('funcionário', $funcionario_id)
        ->with('title', 'Relatório de Comissão');

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Comissão.pdf", array("Attachment" => false));
    }

    public function vendas(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $tipo = $request->tipo;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $local_id = $request->local_id;
        $cliente_id = $request->cliente;
        $start_time = $request->start_time;
        $end_time = $request->end_time;
        $estado = $request->estado;

        if($start_date){
            if($start_time){
                $start_date .= " $start_time:59";
            }else{
                $start_date .= " 00:00:00";
            }
        }

        if($end_date){
            if($end_time){
                $end_date .= " $end_time:59";
            }else{
                $end_date .= " 23:59:59";
            }
        }
        // dd($start_date);


        $vendas = Nfe::where('empresa_id', $request->empresa_id)->where('tpNF', 1)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->where('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->where('created_at', '<=', $end_date);
        })
        // ->where('nves.estado', '!=', 'cancelado')
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when(empty($estado), function ($query) use ($estado) {
            return $query->where('estado', '!=', 'cancelado');
        })
        ->limit($total_resultados ?? 1000000)
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->when($cliente_id, function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->get();

        $vendasCaixa = Nfce::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->where('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->where('created_at', '<=', $end_date);
        })

        ->where('nfces.empresa_id', $request->empresa_id)
        ->when(!empty($estado), function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when(empty($estado), function ($query) use ($estado) {
            return $query->where('estado', '!=', 'cancelado');
        })
        ->limit($total_resultados ?? 1000000)
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->when($cliente_id, function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->get();

        // echo (sizeof($vendas)+sizeof($vendasCaixa));
        // die;

        $data = $this->uneArrayVendas($vendas, $vendasCaixa);

        usort($data, function($a, $b){
            return $a['data'] > $b['data'] ? 1 : -1;
        });
        // dd($data);
        $p = view('relatorios/vendas', compact('data', 'tipo'))
        ->with('title', 'Relatório de Vendas');
        // return $p;
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Vendas.pdf", array("Attachment" => false));
    }

    private function uneArrayVendas($vendas, $vendasCaixa)
    {
        $adicionados = [];
        $arr = [];
        foreach ($vendas as $v) {
            $temp = [
                'id' => $v->numero_sequencial,
                'data' => $v->created_at,
                'tipo' => 'Pedido',
                'total' => $v->total,
                'cliente' => $v->cliente ? $v->cliente->info : '--',
                'localizacao' => $v->localizacao
                // 'itens' => $v->itens
            ];
            array_push($adicionados, $v->id);
            array_push($arr, $temp);
        }
        foreach ($vendasCaixa as $v) {
            $temp = [
                'id' => $v->numero_sequencial,
                'data' => $v->created_at,
                'tipo' => 'PDV',
                'total' => $v->total,
                'cliente' => $v->cliente ? $v->cliente->info : '--',
                'localizacao' => $v->localizacao
                // 'itens' => $v->itens
            ];
            array_push($adicionados, $v->id);
            array_push($arr, $temp);
        }
        return $arr;
    }

    public function despesaFrete(Request $request)
    {

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $tipo_despesa_frete_id = $request->tipo_despesa_frete_id;

        $data = DespesaFrete::
        select('despesa_fretes.*')
        ->join('fretes', 'fretes.id', '=', 'despesa_fretes.frete_id')
        ->where('fretes.empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('despesa_fretes.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('despesa_fretes.created_at', '<=', $end_date);
        })
        ->when($tipo_despesa_frete_id, function ($query) use ($tipo_despesa_frete_id) {
            return $query->where('despesa_fretes.tipo_despesa_id', $tipo_despesa_frete_id);
        })
        ->get();

        $p = view('relatorios/despesa_fretes', compact('data'))
        ->with('title', 'Relatório de Despesas de Frete');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Despesas de Frete.pdf", array("Attachment" => false));
    }

    public function compras(Request $request)
    {

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $local_id = $request->local_id;

        $data = Nfe::where('empresa_id', request()->empresa_id)->where('tpNF', 0)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->where('nves.empresa_id', $request->empresa_id)
        ->limit($total_resultados ?? 1000000)
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();

        $p = view('relatorios/compras', compact('data'))
        ->with('title', 'Relatório de Compras');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Compras.pdf", array("Attachment" => false));
    }

    public function taxas(Request $request)
    {
        $data_inicial = $request->data_inicial;
        $data_final = $request->data_final;
        $local_id = $request->local_id;
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);
        
        if ($data_final && $data_final) {
            $data_inicial = $this->parseDate($data_inicial);
            $data_final = $this->parseDate($data_final);
        }
        $taxas = TaxaPagamento::where('empresa_id', request()->empresa_id)->get();
        $tipos = $taxas->pluck('tipo_pagamento')->toArray();
        $vendas = Nfe::where('empresa_id', request()->empresa_id)
        ->when($data_inicial != '', function ($q) use ($data_inicial) {
            return $q->whereDate('created_at', '>=', $data_inicial);
        })
        ->when($data_final != '', function ($q) use ($data_final) {
            return $q->whereDate('created_at', '<=', $data_final);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();

        $data = [];
        foreach ($vendas as $v) {
            $bandeira_cartao = $v->bandeira_cartao;
            if (sizeof($v->fatura) > 1) {
                foreach ($v->fatura as $ft) {
                    $fp = $ft->tipo_pagamento;
                    if (in_array($fp, $tipos)) {
                        $taxa = TaxaPagamento::where('empresa_id', request()->empresa_id)
                        ->where('tipo_pagamento', $fp)
                        ->when($bandeira_cartao != '' && $bandeira_cartao != '99', function ($q) use ($bandeira_cartao) {
                            return $q->where('bandeira_cartao', $bandeira_cartao);
                        })
                        ->first();
                        if ($taxa != null) {
                            $item = [
                                'cliente' => $v->cliente ? ($v->cliente->razao_social . " " . $v->cliente->cpf_cnpj) :
                                'Consumidor final',
                                'total' => $ft->valor,
                                'taxa_perc' => $taxa ? $taxa->taxa : 0,
                                'taxa' => $taxa ? ($ft->valor * ($taxa->taxa / 100)) : 0,
                                'data' => \Carbon\Carbon::parse($v->created_at)->format('d/m/Y H:i'),
                                'tipo_pagamento' => Nfe::getTipo($fp),
                                'venda_id' => $v->id,
                                'tipo' => 'PEDIDO'
                            ];
                            array_push($data, $item);
                        }
                    }
                }
            } else {
                if (in_array($v->tipo_pagamento, $tipos)) {
                    $total = $v->valor_total - $v->desconto + $v->acrescimo;
                    $taxa = TaxaPagamento::where('empresa_id', request()->empresa_id)
                    ->when($bandeira_cartao != '' && $bandeira_cartao != '99', function ($q) use ($bandeira_cartao) {
                        return $q->where('bandeira_cartao', $bandeira_cartao);
                    })
                    ->where('tipo_pagamento', $v->tipo_pagamento)->first();
                    if ($taxa != null) {
                        $item = [
                            'cliente' => $v->cliente ? ($v->cliente->razao_social . " " . $v->cliente->cpf_cnpj) :
                            'Consumidor final',
                            'total' => $v->total,
                            'taxa_perc' => $taxa->taxa,
                            'taxa' => $taxa ? ($total * ($taxa->taxa / 100)) : 0,
                            'data' => \Carbon\Carbon::parse($v->created_at)->format('d/m/Y H:i'),
                            'tipo_pagamento' => Nfe::getTipo($v->tipo_pagamento),
                            'venda_id' => $v->id,
                            'tipo' => 'PEDIDO'
                        ];
                        array_push($data, $item);
                    } else {
                        echo $bandeira_cartao;
                        die;
                    }
                }
            }
        }

        $vendasCaixa = Nfce::where('empresa_id', request()->empresa_id)
        ->when($data_inicial != '', function ($q) use ($data_inicial) {
            return $q->whereDate('created_at', '>=', $data_inicial);
        })
        ->when($data_final != '', function ($q) use ($data_final) {
            return $q->whereDate('created_at', '<=', $data_final);
        })
        ->get();

        foreach ($vendasCaixa as $v) {
            $bandeira_cartao = $v->bandeira_cartao;
            if (sizeof($v->fatura) > 1) {
                foreach ($v->fatura as $ft) {
                    if (in_array($ft->tipo_pagamento, $tipos)) {
                        $taxa = TaxaPagamento::where('empresa_id', request()->empresa_id)
                        ->when($bandeira_cartao != '' && $bandeira_cartao != '99', function ($q) use ($bandeira_cartao) {
                            return $q->where('bandeira_cartao', $bandeira_cartao);
                        })
                        ->where('tipo_pagamento', $ft->tipo_pagamento)->first();

                        if ($taxa != null) {
                            $item = [
                                'cliente' => $v->cliente ? ($v->cliente->razao_social . " " . $v->cliente->cpf_cnpj) :
                                'Consumidor final',
                                'total' => $ft->valor,
                                'taxa_perc' => $taxa->taxa,
                                'taxa' => $taxa ? ($ft->valor * ($taxa->taxa / 100)) : 0,
                                'data' => \Carbon\Carbon::parse($v->created_at)->format('d/m/Y H:i'),
                                'tipo_pagamento' => Nfe::getTipo($ft->tipo_pagamento),
                                'venda_id' => $v->id,
                                'tipo' => 'PDV'
                            ];
                            array_push($data, $item);
                        }
                    }
                }
            } else {
                if (in_array($v->tipo_pagamento, $tipos)) {
                    $taxa = TaxaPagamento::where('empresa_id', request()->empresa_id)
                    ->when($bandeira_cartao != '' && $bandeira_cartao != '99', function ($q) use ($bandeira_cartao) {
                        return $q->where('bandeira_cartao', $bandeira_cartao);
                    })
                    ->where('tipo_pagamento', $v->tipo_pagamento)->first();

                    if ($taxa != null) {
                        $item = [
                            'cliente' => $v->cliente ? ($v->cliente->razao_social . " " . $v->cliente->cpf_cnpj) :
                            'Consumidor final',
                            'total' => $v->total,
                            'taxa_perc' => $taxa->taxa,
                            'taxa' => $taxa ? ($v->total * ($taxa->taxa / 100)) : 0,
                            'data' => \Carbon\Carbon::parse($v->created_at)->format('d/m/Y H:i'),
                            'tipo_pagamento' => Nfe::getTipo($v->tipo_pagamento),
                            'venda_id' => $v->id,
                            'tipo' => 'PDV'
                        ];
                        array_push($data, $item);
                    }
                }
            }
        }

        $p = view('relatorios/taxas')
        ->with('data', $data)
        ->with('title', 'Taxas de Pagamento');

        // return $p;
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Taxas de pagamento.pdf", array("Attachment" => false));
    }

    public function lucro(Request $request)
    {

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $local_id = $request->local_id;

        $nfe = Nfe::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->where('orcamento', 0)
        ->where('tpNF', 1)
        ->get();

        $nfce = Nfce::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();

        $data = [];

        foreach($nfe as $n){
            $item = [
                'cliente' => $n->cliente ? $n->cliente->info : 'CONSUMIDOR FINAL',
                'data' => __data_pt($n->created_at),
                'valor_venda' => $n->total,
                'valor_custo' => $this->calculaCusto($n->itens),
                'localizacao' => $n->localizacao
            ];
            array_push($data, $item);
        }

        foreach($nfce as $n){
            $item = [
                'cliente' => $n->cliente ? $n->cliente->info : 'CONSUMIDOR FINAL',
                'data' => __data_pt($n->created_at),
                'valor_venda' => $n->total,
                'valor_custo' => $this->calculaCusto($n->itens),
                'localizacao' => $n->localizacao
            ];
            array_push($data, $item);
        }

        usort($data, function($a, $b){
            return $a['data'] < $b['data'] ? 1 : -1;
        });

        $p = view('relatorios/lucro', compact('data'))
        ->with('title', 'Relatório de Lucros');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Lucros.pdf", array("Attachment" => false));
    }

    public function vendaProdutos(Request $request){
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $local_id = $request->local_id;
        $marca_id = $request->marca_id;
        $categoria_id = $request->categoria_id;
        $produto_id = $request->produto_id;

        $diferenca = strtotime($end_date) - strtotime($start_date);
        $dias = floor($diferenca / (60 * 60 * 24));

        $dataAtual = $start_date;
        if($dias <= 0){
            $dias = 1;
        }

        $data = [];
        for($aux = 0; $aux < $dias; $aux++){
            $itensNfe = ItemNfe::
            select(\DB::raw('sum(sub_total) as subtotal, sum(quantidade) as soma_quantidade, item_nves.produto_id as produto_id, avg(item_nves.valor_unitario) as media, item_nves.valor_unitario as valor_unitario'))
            ->whereBetween('item_nves.created_at', 
                [
                    $dataAtual . " 00:00:00",
                    $dataAtual . " 23:59:59"
                ]
            )
            ->join('produtos', 'produtos.id', '=', 'item_nves.produto_id')
            ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
            ->groupBy('item_nves.produto_id')
            ->where('produtos.empresa_id', $request->empresa_id)
            ->when(!empty($categoria_id), function ($query) use ($categoria_id) {
                return $query->join('categoria_produtos', 'categoria_produtos.id', '=', 'produtos.categoria_id')
                ->where(function($t) use ($categoria_id) 
                {
                    $t->where('produtos.categoria_id', $categoria_id)->orWhere('produtos.sub_categoria_id', $categoria_id);
                });
            })
            ->when(!empty($produto_id), function ($query) use ($produto_id) {
                return $query->where('item_nves.produto_id', $produto_id);
            })
            ->when(!empty($marca_id), function ($query) use ($marca_id) {
                return $query->join('marcas', 'marcas.id', '=', 'produtos.marca_id')
                ->where('produtos.marca_id', $marca_id);
            })
            ->when(!empty($local_id), function ($query) use ($local_id) {
                return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
                ->where('produto_localizacaos.localizacao_id', $local_id);
            })
            ->get();

            $itensNfce = ItemNfce::
            select(\DB::raw('sum(sub_total) as subtotal, sum(quantidade) as soma_quantidade, item_nfces.produto_id as produto_id, avg(item_nfces.valor_unitario) as media, item_nfces.valor_unitario as valor_unitario'))
            ->whereBetween('item_nfces.created_at', 
                [
                    $dataAtual . " 00:00:00",
                    $dataAtual . " 23:59:59"
                ]
            )
            ->join('produtos', 'produtos.id', '=', 'item_nfces.produto_id')
            ->join('nfces', 'nfces.id', '=', 'item_nfces.nfce_id')
            ->groupBy('item_nfces.produto_id')
            ->where('produtos.empresa_id', $request->empresa_id)
            ->when(!empty($categoria_id), function ($query) use ($categoria_id) {
                // return $query->join('categoria_produtos', 'categoria_produtos.id', '=', 'produtos.categoria_id')
                // ->where('produtos.categoria_id', $categoria_id);
                return $query->join('categoria_produtos', 'categoria_produtos.id', '=', 'produtos.categoria_id')
                ->where(function($t) use ($categoria_id) 
                {
                    $t->where('produtos.categoria_id', $categoria_id)->orWhere('produtos.sub_categoria_id', $categoria_id);
                });
            })
            ->when(!empty($produto_id), function ($query) use ($produto_id) {
                return $query->where('item_nfces.produto_id', $produto_id);
            })
            ->when(!empty($marca_id), function ($query) use ($marca_id) {
                return $query->join('marcas', 'marcas.id', '=', 'produtos.marca_id')
                ->where('produtos.marca_id', $marca_id);
            })
            ->when(!empty($local_id), function ($query) use ($local_id) {
                return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
                ->where('produto_localizacaos.localizacao_id', $local_id);
            })
            ->get();

            $itens = $this->uneArrayItens($itensNfe, $itensNfce, $request->ordem);
            $temp = [
                'data' => $dataAtual,
                'itens' => $itens,
            ];
            array_push($data, $temp);
            $dataAtual = date('Y-m-d', strtotime($dataAtual. '+1day'));
        }

        $p = view('relatorios/venda_por_produtos', compact('data', 'start_date', 'end_date'))
        ->with('title', 'Relatório de Venda por Produtos');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de venda por produtos.pdf", array("Attachment" => false));
    }

    private function uneArrayItens($itens, $itensCaixa, $ordem){
        $data = [];
        $adicionados = [];
        foreach($itens as $i){

            $temp = [
                'quantidade' => $i->soma_quantidade,
                'subtotal' => $i->subtotal,
                'valor' => $i->produto->valor_unitario,
                'media' => $i->media,
                'produto' => $i->produto,
            ];
            array_push($data, $temp);
            // array_push($adicionados, $i->produto->id);
        }

        // print_r($data[0]['produto']);
        foreach($itensCaixa as $i){
            $indiceAdicionado = $this->jaAdicionadoProduto($data, $i->produto->id);
            if($indiceAdicionado == -1){

                $temp = [
                    'quantidade' => $i->soma_quantidade,
                    'subtotal' => $i->subtotal,
                    'valor' => $i->produto->valor_unitario,
                    'media' => $i->media,
                    'produto' => $i->produto,
                ];
                array_push($data, $temp);
            }else{
                $data[$indiceAdicionado]['quantidade'] += $i->soma_quantidade; 
                $data[$indiceAdicionado]['subtotal'] += $i->subtotal; 
                $data[$indiceAdicionado]['media'] = ($data[$indiceAdicionado]['media'] + $i->media) / 2; 
            }
        }
        
        usort($data, function($a, $b) use ($ordem){
            if($ordem == 'asc') return $a['quantidade'] > $b['quantidade'] ? 1 : 0;
            else if($ordem == 'desc') return $a['quantidade'] < $b['quantidade'] ? 1 : 0;
            else return $a['produto']->nome > $b['produto']->nome ? 1 : 0;
        });
        return $data;
    }

    private function calculaCusto($itens){
        $custo = 0;
        foreach($itens as $i){
            $custo += $i->quantidade * $i->produto->valor_compra;
        }
        return $custo;
    }

    private function jaAdicionadoProduto($array, $produtoId){
        for($i=0; $i<sizeof($array); $i++){
            if($array[$i]['produto']->id == $produtoId){
                return $i;
            }
        }
        return -1;
    }

    public function estoque(Request $request){
        $estoque_minimo = $request->estoque_minimo;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $categoria_id = $request->categoria_id;
        $esportar_excel = $request->esportar_excel;
        $local_id = $request->local_id;

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $data = [];

        if($estoque_minimo == 1){

            $produtosComEstoqueMinimo = Produto::where('produtos.empresa_id', $request->empresa_id)
            ->select('produtos.*')
            ->when($categoria_id, function ($query) use ($categoria_id) {
                return $query->where(function($t) use ($categoria_id) 
                {
                    $t->where('categoria_id', $categoria_id)->orWhere('sub_categoria_id', $categoria_id);
                });
            })
            ->when(!empty($local_id), function ($query) use ($local_id) {
                return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
                ->where('produto_localizacaos.localizacao_id', $local_id);
            })
            ->when(!$local_id, function ($query) use ($locais) {
                return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
                ->whereIn('produto_localizacaos.localizacao_id', $locais);
            })
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('produtos.created_at', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date,) {
                return $query->whereDate('produtos.created_at', '<=', $end_date);
            })
            // ->limit(20)
            ->where('produtos.estoque_minimo', '>', 0)->get();
            foreach($produtosComEstoqueMinimo as $produto){
                $estoque = Estoque::where('produto_id', $produto->id)->first();
                
                if($estoque == null || $estoque->quantidade <= $produto->estoque_minimo){

                    if(sizeof($produto->variacoes) == 0){
                        $qtd = $estoque ? $estoque->quantidade : '0';
                        if(!$local_id){
                            $qtd = $produto->estoqueTotalProduto();
                        }
                        $linha = [
                            'produto' => $produto->nome,
                            'quantidade' => $qtd,
                            'estoque_minimo' => $produto->estoque_minimo,
                            'valor_compra' => $produto->valor_compra,
                            'valor_venda' => $produto->valor_unitario,
                            'categoria' => $produto->categoria ? $produto->categoria->nome : '--',
                            'data_cadastro' => __data_pt($produto->created_at)
                        ];
                        array_push($data, $linha);
                    }else{

                        foreach($produto->variacoes as $v){
                            $linha = [
                                'produto' => $produto->nome . " " . $v->descricao,
                                'quantidade' => $v->estoque ? $v->estoque->quantidade : '',
                                'estoque_minimo' => $produto->estoque_minimo,
                                'valor_compra' => $produto->valor_compra,
                                'valor_venda' => $v->valor,
                                'categoria' => $produto->categoria ? $produto->categoria->nome : '--',
                                'data_cadastro' => __data_pt($produto->created_at)
                            ];
                            array_push($data, $linha);
                        }
                    }

                    
                }
            }
        }else if($start_date || $end_date){
            $movimentacoes = MovimentacaoProduto::
            select('movimentacao_produtos.*')
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('movimentacao_produtos.created_at', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date,) {
                return $query->whereDate('movimentacao_produtos.created_at', '<=', $end_date);
            })
            ->join('produtos', 'produtos.id', '=', 'movimentacao_produtos.produto_id')
            ->when($categoria_id, function ($query) use ($categoria_id) {
                return $query->where(function($t) use ($categoria_id) 
                {
                    $t->where('categoria_id', $categoria_id)->orWhere('sub_categoria_id', $categoria_id);
                });
            })
            ->when(!empty($local_id), function ($query) use ($local_id) {
                return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
                ->where('produto_localizacaos.localizacao_id', $local_id);
            })
            ->when(!$local_id, function ($query) use ($locais) {
                return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
                ->whereIn('produto_localizacaos.localizacao_id', $locais);
            })
            ->where('produtos.empresa_id', $request->empresa_id)
            ->groupBy('produtos.id')
            ->orderBy('movimentacao_produtos.created_at', 'desc')
            ->get();

            foreach($movimentacoes as $m){

                $produto = $m->produto;
                if(sizeof($produto->variacoes) == 0){
                    $linha = [
                        'produto' => $m->produto->nome,
                        'quantidade' => $m->produto->estoqueTotalProduto(),
                        'estoque_minimo' => $m->produto->estoque_minimo,
                        'valor_compra' => $m->produto->valor_compra,
                        'valor_venda' => $m->produto->valor_unitario,
                        'categoria' => $m->produto->categoria ? $m->produto->categoria->nome : '--',
                        'data_cadastro' => __data_pt($m->produto->created_at)
                    ];
                    array_push($data, $linha);
                }else{
                    foreach($produto->variacoes as $v){
                        $linha = [
                            'produto' => $m->produto->nome . " " . $v->descricao,
                            'quantidade' => $v->estoque ? $v->estoque->quantidade : '',
                            'estoque_minimo' => $m->produto->estoque_minimo,
                            'valor_compra' => $m->produto->valor_compra,
                            'valor_venda' => $v->valor,
                            'categoria' => $m->produto->categoria ? $m->produto->categoria->nome : '--',
                            'data_cadastro' => __data_pt($m->produto->created_at)
                        ];
                        array_push($data, $linha);
                    }
                }
            }

        }else{

            $estoque = Estoque::
            select('estoques.*')
            ->join('produtos', 'produtos.id', '=', 'estoques.produto_id')
            ->groupBy('produtos.id')
            ->when(!empty($local_id), function ($query) use ($local_id) {
                return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
                ->where('estoques.local_id', $local_id);
            })
            ->when($categoria_id, function ($query) use ($categoria_id) {
                return $query->where(function($t) use ($categoria_id) 
                {
                    $t->where('categoria_id', $categoria_id)->orWhere('sub_categoria_id', $categoria_id);
                });
            })
            ->where('produtos.empresa_id', $request->empresa_id)->get();

            foreach($estoque as $m){
                $produto = $m->produto;
                if(sizeof($produto->variacoes) == 0){
                    $linha = [
                        'produto' => $m->produto->nome,
                        'quantidade' => $m->quantidade,
                        'estoque_minimo' => $m->produto->estoque_minimo,
                        'valor_compra' => $m->produto->valor_compra,
                        'valor_venda' => $m->produto->valor_unitario,
                        'categoria' => $m->produto->categoria ? $m->produto->categoria->nome : '--',
                        'data_cadastro' => __data_pt($m->produto->created_at)
                    ];
                    array_push($data, $linha);
                }else{
                    foreach($produto->variacoes as $v){
                        $linha = [
                            'produto' => $m->produto->nome . " " . $v->descricao,
                            'quantidade' => $v->estoque ? $v->estoque->quantidade : '',
                            'estoque_minimo' => $m->produto->estoque_minimo,
                            'valor_compra' => $m->produto->valor_compra,
                            'valor_venda' => $v->valor,
                            'categoria' => $m->produto->categoria ? $m->produto->categoria->nome : '--',
                            'data_cadastro' => __data_pt($m->produto->created_at)
                        ];
                        array_push($data, $linha);
                    }
                }
            }
        }

        if($esportar_excel == -1){
            $localizacao = null;
            if($local_id){
                $localizacao = Localizacao::findOrFail($local_id);
            }
            $p = view('relatorios/estoque', compact('data', 'start_date', 'end_date', 'estoque_minimo', 'localizacao'))
            ->with('title', 'Relatório de Estoque');
            $domPdf = new Dompdf(["enable_remote" => true]);
            $domPdf->loadHtml($p);

            // return $p;

            $domPdf->setPaper("A4", "landscape");
            $domPdf->render();
            $domPdf->stream("Relatório de estoque.pdf", array("Attachment" => false));
        }else{

            $relatorioEx = new RelatorioEstoqueExport($data);
            return Excel::download($relatorioEx, 'estoque.xlsx');
        }
    }

    public function totalizaProdutos(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $local_id = $request->local_id;

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $data = Produto::select('produtos.*')
        ->where('produtos.empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('produtos.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('produtos.created_at', '<=', $end_date);
        })
        ->when(!empty($local_id), function ($query) use ($local_id) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('produto_localizacaos.localizacao_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->whereIn('produto_localizacaos.localizacao_id', $locais);
        })->get();

        $local = null;
        if($local_id){
            $local = Localizacao::findOrFail($local_id);
        }

        $p = view('relatorios/totaliza_produtos', compact('data', 'local_id', 'local'))
        ->with('title', 'Relatório Totalizador Produtos');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        // return $p;

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório totalizador de produtos.pdf", array("Attachment" => false));
    }

    public function vendasPorVendedor(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $funcionario_id = $request->funcionario_id;
        $local_id = $request->local_id;

        $funcionario = Funcionario::findOrFail($funcionario_id);
        $nves = Nfe::
        where('empresa_id', $request->empresa_id)
        ->where('funcionario_id', $funcionario_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })->get();

        $nfces = Nfce::
        where('empresa_id', $request->empresa_id)
        ->where('funcionario_id', $funcionario_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })->get();

        $data = [];
        foreach($nves as $n){
            $data[] = [
                'id' => $n->numero_sequencial,
                'cliente' => $n->cliente ? $n->cliente->info : 'Consumidor final',
                'data' => $n->created_at,
                'total' => $n->total,
                'localizacao' => $n->localizacao
            ];
        }

        foreach($nfces as $n){
            $data[] = [
                'id' => $n->numero_sequencial,
                'cliente' => $n->cliente ? $n->cliente->info : 'Consumidor final',
                'data' => $n->created_at,
                'total' => $n->total,
                'localizacao' => $n->localizacao
            ];
        }
        $p = view('relatorios/vendas_por_vendedor', compact('data', 'funcionario'))
        ->with('title', 'Relatório Vendas por Vendedor');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        // return $p;

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório vendas por vendedor.pdf", array("Attachment" => false));
    }

    public function custoMedio(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $local_id = $request->local_id;
        $categoria_id = $request->categoria_id;
        $ordem = $request->ordem;

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $data = Produto::select('produtos.*')
        ->where('produtos.empresa_id', $request->empresa_id)
        ->where('gerenciar_estoque', 1)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('produtos.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('produtos.created_at', '<=', $end_date);
        })
        ->when(!empty($categoria_id), function ($query) use ($categoria_id) {
            return $query->where(function($t) use ($categoria_id) 
            {
                $t->where('categoria_id', $categoria_id)->orWhere('sub_categoria_id', $categoria_id);
            });
        })
        ->when(!empty($local_id), function ($query) use ($local_id) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('produto_localizacaos.localizacao_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->whereIn('produto_localizacaos.localizacao_id', $locais);
        })
        // ->limit(10)
        ->get();


        $local = null;
        if($local_id){
            $local = Localizacao::findOrFail($local_id);
        }

        foreach($data as $item){
            $valor = ItemNfe::where('produto_id', $item->id)
            ->sum('sub_total');

            $item->custo_medio = $valor/($item->estoque && $item->estoque->quantidade > 0 ? $item->estoque->quantidade : 1);
            $item->quantidade = $item->estoque ? $item->estoque->quantidade : 0;
            $item->categoria_nome = $item->categoria ? $item->categoria->nome : '--';
            $item->nome = $item->nome;
        }

        $data = $data->toArray();

        usort($data, function($a, $b) use ($ordem){
            if($ordem == 'asc') return $a['quantidade'] > $b['quantidade'] ? 1 : -1;
            else if($ordem == 'desc') return $a['quantidade'] < $b['quantidade'] ? 1 : -1;
            else return $a['nome'] > $b['nome'] ? 1 : -1;
        });

        $p = view('relatorios/inventario_custo_medio', compact('data', 'local_id', 'local'))
        ->with('title', 'Relatório inventário custo médio');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório inventário custo médio.pdf", array("Attachment" => false));

    }

    public function registroInventario(Request $request){
        $date = $request->date;
        $livro = $request->livro;
        $tipo_custo = $request->tipo_custo;

        // $data = MovimentacaoProduto::
        // select('movimentacao_produtos.*')
        // ->whereDate('movimentacao_produtos.created_at', '<=', $date)
        // ->join('produtos', 'produtos.id', '=', 'movimentacao_produtos.produto_id')
        // ->where('produtos.empresa_id', $request->empresa_id)
        // ->groupBy('movimentacao_produtos.produto_id')
        // ->orderBy('produtos.nome')
        // ->having('movimentacao_produtos.quantidade', '>', 0)
        // ->limit(10)
        // ->get();

        $sub = MovimentacaoProduto::select(
            'produto_id',
            DB::raw('MAX(movimentacao_produtos.created_at) as ultima_data')
        )
        ->whereDate('movimentacao_produtos.created_at', '<=', $date)
        ->join('produtos', 'produtos.id', '=', 'movimentacao_produtos.produto_id')
        ->where('produtos.empresa_id', $request->empresa_id)
        ->groupBy('produto_id');

        $data = MovimentacaoProduto::select('movimentacao_produtos.*', 'produtos.nome')
        ->join('produtos', 'produtos.id', '=', 'movimentacao_produtos.produto_id')
        ->joinSub($sub, 'ultimas', function ($join) {
            $join->on('ultimas.produto_id', '=', 'movimentacao_produtos.produto_id')
            ->on('ultimas.ultima_data', '=', 'movimentacao_produtos.created_at');
        })
        ->where('produtos.empresa_id', $request->empresa_id)
        ->where('movimentacao_produtos.quantidade', '>', 0)
        ->orderBy('produtos.nome')
        // ->limit(10)
        ->get();

        if($tipo_custo == 'media'){
            // ver como faz
            foreach($data as $item){

                $valor = ItemNfe::where('produto_id', $item->produto_id)
                ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
                ->where('nves.tpNF', 0)
                ->sum('sub_total');

                $item->quantidade = $item->estoque_atual;
                $item->valor_unitario = $item->produto->valor_compra;

                if($valor > 0){

                    $qtd = ItemNfe::where('produto_id', $item->produto_id)
                    ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
                    ->where('nves.tpNF', 0)
                    ->sum('quantidade');
                    $custo_medio = $valor/$qtd;
                    $item->valor_unitario = $custo_medio;
                }
                $item->sub_total = $item->valor_unitario * $item->quantidade;

            }
        }else{
            foreach($data as $item){
                $item->valor_unitario = $item->produto->valor_compra;

                $item->quantidade = $item->estoque_atual;
                $item->sub_total = $item->produto->valor_compra * $item->quantidade;                
            }
        }

        $empresa = Empresa::findOrFail($request->empresa_id);

        $p = view('relatorios.registro_inventario', compact('data', 'livro', 'empresa'))
        ->with('title', 'Relatório registro inventário');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório registro inventário", array("Attachment" => false));
    }

    public function inventario(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $local_id = $request->local_id;
        $ordem = $request->ordem;
        $livro = $request->livro;

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);
        
        $data = Produto::select('produtos.*')
        ->where('produtos.empresa_id', $request->empresa_id)
        ->where('gerenciar_estoque', 1)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('produtos.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('produtos.created_at', '<=', $end_date);
        })

        ->when(!empty($local_id), function ($query) use ($local_id) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('produto_localizacaos.localizacao_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->whereIn('produto_localizacaos.localizacao_id', $locais);
        })
        // ->limit(10)
        ->get();


        $local = null;
        $empresa = Empresa::findOrFail($request->empresa_id);
        if($local_id){
            $local = Localizacao::findOrFail($local_id);
        }

        foreach($data as $item){

            $item->custo_unuitario = $item->valor_compra;
            $item->quantidade = $item->estoque ? $item->estoque->quantidade : 0;
            $item->sub_total = $item->quantidade * $item->valor_compra;
            $item->nome = $item->nome;
        }

        $data = $data->toArray();

        usort($data, function($a, $b) use ($ordem){
            if($ordem == 'asc') return $a['quantidade'] > $b['quantidade'] ? 1 : -1;
            else if($ordem == 'desc') return $a['quantidade'] < $b['quantidade'] ? 1 : -1;
            else return $a['nome'] > $b['nome'] ? 1 : -1;
        });

        $p = view('relatorios/inventario', compact('data', 'local_id', 'local', 'livro', 'empresa'))
        ->with('title', 'Relatório inventário');
        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório inventário.pdf", array("Attachment" => false));

    }

    public function curvaAbcClientes(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $nfe = Nfe::where('nves.empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('nves.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('nves.created_at', '<=', $end_date);
        })
        ->join('clientes', 'clientes.id', '=', 'nves.cliente_id')
        ->groupBy('cliente_id')
        ->select('clientes.id as cliente_id', 'clientes.razao_social as nome', \DB::raw('sum(nves.total) as total'), \DB::raw('count(nves.id) as count'))
        ->get();

        $nfce = Nfce::where('nfces.empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('nfces.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('nfces.created_at', '<=', $end_date);
        })
        ->join('clientes', 'clientes.id', '=', 'nfces.cliente_id')
        ->groupBy('cliente_id')
        ->select('clientes.id as cliente_id', 'clientes.razao_social as nome', \DB::raw('sum(nfces.total) as total'), \DB::raw('count(nfces.id) as count'))
        ->get();


        $data = $this->agrupaArrayCurva($nfe, $nfce);

        $soma = 0;
        foreach($data as $a){
            $soma += $a['total'];
        }

        foreach($data as $key => $a){
            $totalLinha = $data[$key]['total'];
            $v = 100 - (((($totalLinha-$soma)/$soma)*100)*-1);

            $data[$key]['percentual'] = number_format($v, 2);
        }

        $p = view('relatorios/curva_abc_clientes')
        ->with('data', $data)
        ->with('soma', $soma)
        ->with('title', 'Curva ABC Clientes');

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Curva ABC Clientes.pdf", array("Attachment" => false));

    }

    public function entregaDeProdutos(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $vendas = $request->vendas;

        $vNfe = [];
        $vNfce = [];
        $filtroVenda = 0;

        if($vendas){
            $filtroVenda = 1;
            foreach($vendas as $v){
                $ex = explode("_", $v);
                if($ex[0] == 'pedido'){
                    $vNfe[] = $ex[1];
                }else{
                    $vNfce[] = $ex[1];
                }
            }
        }

        $itensNfe = ItemNfe::where('nves.empresa_id', $request->empresa_id)->where('nves.tpNF', 1)
        ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->where('nves.created_at', '>=', $start_date);
        })
        ->when(sizeof($vNfe) > 0, function ($query) use ($vNfe) {
            return $query->whereIn('nves.id', $vNfe);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->where('nves.created_at', '<=', $end_date);
        })
        ->where('nves.empresa_id', $request->empresa_id)
        ->get();

        if($filtroVenda == 1 && sizeof($vNfe) == 0){
            $itensNfe = [];
        }

        $itensNfce = ItemNfce::where('nfces.empresa_id', $request->empresa_id)
        ->join('nfces', 'nfces.id', '=', 'item_nfces.nfce_id')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->where('nfces.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->where('nfces.created_at', '<=', $end_date);
        })
        ->when(sizeof($vNfce) > 0, function ($query) use ($vNfce) {
            return $query->whereIn('nfces.id', $vNfce);
        })
        ->where('nfces.empresa_id', $request->empresa_id)
        ->get();

        if($filtroVenda == 1 && sizeof($vNfce) == 0){
            $itensNfce = [];
        }

        $data = [];
        $dataPushId = [];

        foreach($itensNfe as $i){
            if(!in_array($i->produto_id, $dataPushId)){
                $obj = [
                    'produto_id' => $i->produto_id,
                    'numero_sequencial' => $i->produto->numero_sequencial,
                    'quantidade' => (int)$i->quantidade,
                    'produto_nome' => $i->produto->nome
                ];

                $data[] = $obj;
                $dataPushId[] = $i->produto_id;
            }else{

                for($j=0; $j<sizeof($data); $j++){
                    if($data[$j]['produto_id'] == $i->produto_id){
                        $data[$j]['quantidade'] += (int)$i->quantidade;
                    }
                }
            }
        }

        foreach($itensNfce as $i){
            if(!in_array($i->produto_id, $dataPushId)){
                $obj = [
                    'produto_id' => $i->produto_id,
                    'numero_sequencial' => $i->produto->numero_sequencial,
                    'quantidade' => (int)$i->quantidade,
                    'produto_nome' => $i->produto->nome
                ];

                $data[] = $obj;
                $dataPushId[] = $i->produto_id;
            }else{

                for($j=0; $j<sizeof($data); $j++){
                    if($data[$j]['produto_id'] == $i->produto_id){
                        $data[$j]['quantidade'] += (int)$i->quantidade;
                    }
                }
            }
        }

        $p = view('relatorios/entrega_produtos')
        ->with('data', $data)
        ->with('title', 'Entrega de Produtos');

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4");
        $domPdf->render();
        $domPdf->stream("Entrega de Produtos.pdf", array("Attachment" => false));
    }    

    private function agrupaArrayCurva($nfe, $nfce){
        $clientes = [];
        $clientesId = [];
        foreach($nfe as $v){
            $temp = [
                'nome' => $v->nome,
                'total' => $v->total,
                'cliente_id' => $v->cliente_id,
                'count' => $v->count,
                'percentual' => 0
            ];
            $clientesId[] = $v->cliente_id;
            array_push($clientes, $temp);
        }

        foreach($nfce as $v){

            if(!in_array($v->cliente_id, $clientesId)){
                $temp = [
                    'nome' => $v->nome,
                    'total' => $v->total,
                    'cliente_id' => $v->cliente_id,
                    'count' => $v->count,
                    'percentual' => 0
                ];
                array_push($clientes, $temp);
            }else{
                $v['total'] += $v->total;
                $v['count'] += $v->count;
            }

        }
        return $clientes;
    }

    public function movimentacao(Request $request){
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $local_id = $request->local_id;
        $marca_id = $request->marca_id;
        $categoria_id = $request->categoria_id;
        $produto_id = $request->produto_id;
        $ordem = $request->ordem;
        $fiscal = $request->fiscal;

        $produtos = Produto::where('status', 1)->where('empresa_id', $request->empresa_id)
        ->when(!empty($categoria_id), function ($query) use ($categoria_id) {
            return $query->where(function($t) use ($categoria_id) 
            {
                $t->where('categoria_id', $categoria_id)->orWhere('sub_categoria_id', $categoria_id);
            });
        })
        ->select('produtos.*')
        ->orderBy('nome')
        ->when(!empty($produto_id), function ($query) use ($produto_id) {
            return $query->where('produtos.id', $produto_id);
        })
        ->when(!empty($marca_id), function ($query) use ($marca_id) {
            return $query->where('marca_id', $marca_id);
        })
        ->when(!empty($local_id), function ($query) use ($local_id) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->where('produto_localizacaos.localizacao_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->join('produto_localizacaos', 'produto_localizacaos.produto_id', '=', 'produtos.id')
            ->whereIn('produto_localizacaos.localizacao_id', $locais);
        })
        // ->limit(200)
        ->get();

        $data = [];
        foreach($produtos as $p){
            $countNfeSaida = ItemNfe::select('item_nfves.*')
            ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
            ->where('nves.tpNF', 1)->where('item_nves.produto_id', $p->id)
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('nves.created_at', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date,) {
                return $query->whereDate('nves.created_at', '<=', $end_date);
            })
            ->when(!empty($fiscal), function ($query) use ($fiscal) {
                if($fiscal == 1){
                    return $query->where('nves.estado', 'aprovado');
                }else{
                    return $query->where('nves.estado', '!=', 'aprovado');
                }
            })
            ->sum('item_nves.quantidade');

            $countNfce = ItemNfce::select('item_nfces.*')
            ->join('nfces', 'nfces.id', '=', 'item_nfces.nfce_id')
            ->where('item_nfces.produto_id', $p->id)
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('nfces.created_at', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date,) {
                return $query->whereDate('nfces.created_at', '<=', $end_date);
            })
            ->when(!empty($fiscal), function ($query) use ($fiscal) {
                if($fiscal == 1){
                    return $query->where('nfces.estado', 'aprovado');
                }else{
                    return $query->where('nfces.estado', '!=', 'aprovado');
                }
            })
            ->sum('item_nfces.quantidade');

            $countNfeEntrada = ItemNfe::select('item_nfves.*')
            ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
            ->where('nves.tpNF', 0)->where('item_nves.produto_id', $p->id)
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('nves.created_at', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date,) {
                return $query->whereDate('nves.created_at', '<=', $end_date);
            })
            ->when(!empty($fiscal), function ($query) use ($fiscal) {
                if($fiscal == 1){
                    return $query->where('nves.estado', 'aprovado');
                }else{
                    return $query->where('nves.estado', '!=', 'aprovado');
                }
            })
            ->sum('item_nves.quantidade');

            $descricao = $p->nome;

            $itemParaNome = ItemNfe::where('produto_id', $p->id)
            ->first();

            if($itemParaNome){
                $descricao = $itemParaNome->descricao();
            }

            if($countNfeEntrada > 0 || ($countNfce + $countNfeSaida) > 0){
                $data[] = [
                    'nome_produto' => $descricao,
                    'qtd_compra' => $countNfeEntrada,
                    'qtd_saida' => $countNfce + $countNfeSaida,
                    'vl_venda' => $p->valor_unitario,
                    'vl_compra' => $p->valor_compra,
                    'subtotal_venda' => $p->valor_unitario * ($countNfce + $countNfeSaida),
                    'subtotal_compra' => $p->valor_compra * $countNfeEntrada,
                ];
            }
        }

        if($ordem){
            usort($data, function($a, $b) use($ordem){
                if($ordem == 'mais_vendidos'){
                    return $a['qtd_saida'] < $b['qtd_saida'] ? 1 : -1;
                }
                if($ordem == 'mais_comprados'){
                    return $a['qtd_compra'] < $b['qtd_compra'] ? 1 : -1;
                }

                if($ordem == 'menos_vendidos'){
                    return $a['qtd_saida'] > $b['qtd_saida'] ? 1 : -1;
                }
                if($ordem == 'menos_comprados'){
                    return $a['qtd_compra'] > $b['qtd_compra'] ? 1 : -1;
                }
            });
        }

        // dd($data);
        $p = view('relatorios/movimentacao')
        ->with('data', $data)
        ->with('start_date', $start_date)
        ->with('end_date', $end_date)
        ->with('title', 'Movimentação');

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Movimentação.pdf", array("Attachment" => false));
    }

    public function ordemServico(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $cliente_id = $request->cliente;
        $local_id = $request->local_id;

        $data = OrdemServico::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();


        $p = view('relatorios/ordem_servico', compact('data'))
        ->with('title', 'Relatório de Ordem de Serviço');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Ordem de Serviço.pdf", array("Attachment" => false));
    }

    public function tiposDePagamento(Request $request)
    {
        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $tipo_pagamento = $request->tipo_pagamento;
        $local_id = $request->local_id;

        $nves = Nfe::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();

        $nfces = Nfce::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when($local_id, function ($query) use ($local_id) {
            return $query->where('local_id', $local_id);
        })
        ->when(!$local_id, function ($query) use ($locais) {
            return $query->whereIn('local_id', $locais);
        })
        ->get();

        $data = $this->getTiposPagamento($tipo_pagamento);
        foreach($nves as $n){
            foreach($n->fatura as $f){
                if(isset($data[$f->tipo_pagamento])){
                    $data[$f->tipo_pagamento] += $f->valor;
                }
            }
        }

        foreach($nfces as $n){
            foreach($n->fatura as $f){
                if(isset($data[$f->tipo_pagamento])){
                    $data[$f->tipo_pagamento] += $f->valor;
                }
            }
        }

        $p = view('relatorios/tipos_pagamento', compact('data'))
        ->with('title', 'Relatório de Tipos de Pagamento');

        // return $p;

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Tipos de Pagamento.pdf", array("Attachment" => false));
    }

    private function getTiposPagamento($tipo_pagamento = null){
        $data = [];
        foreach(Nfe::tiposPagamento() as $key => $n){
            if($tipo_pagamento != null){
                if($tipo_pagamento == $key){
                    $data[$key] = 0;
                }
            }else{
                $data[$key] = 0;
            }
        }
        return $data;
    }

    public function reservas(Request $request)
    {

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $estado = $request->estado;
        $vagos = $request->vagos;

        if($vagos == 1){

            $reservas = Reserva::where('empresa_id', $request->empresa_id)
            ->where(function ($query) use ($start_date, $end_date) {
                $query->whereDate('data_checkin', '<=', $start_date)
                ->whereDate('data_checkout', '>=', $end_date);
            })
            ->where('estado', '!=', 'cancelado')
            ->pluck('acomodacao_id')
            ->all();

            $data = Acomodacao::where('empresa_id', request()->empresa_id)
            ->whereNotIn('id', $reservas)
            ->where('status', 1)
            ->get();

            $p = view('relatorios/reserva_vagos', compact('data', 'start_date', 'end_date'))
            ->with('title', 'Relatório de acomodações vagas por período');
        }else{
            $data = Reserva::where('empresa_id', $request->empresa_id)
            ->when(!empty($start_date), function ($query) use ($start_date) {
                return $query->whereDate('data_checkin', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date,) {
                return $query->whereDate('data_checkout', '<=', $end_date);
            })
            ->when($estado != "", function ($query) use ($estado) {
                return $query->where('estado', $estado);
            })
            ->get();

            $p = view('relatorios/reservas', compact('data', 'start_date', 'end_date'))
            ->with('title', 'Relatório de Reservas');
        }

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Reservas.pdf", array("Attachment" => false));
    }

    public function lucroProduto(Request $request){

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $marca_id = $request->marca_id;
        $categoria_id = $request->categoria_id;
        $produto_id = $request->produto_id;

        $dataNfe = ItemNfe::where('produtos.empresa_id', $request->empresa_id)
        ->select('produtos.id as produto_id')
        ->join('produtos', 'produtos.id', '=', 'item_nves.produto_id')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('item_nves.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('item_nves.created_at', '<=', $end_date);
        })
        ->when(!empty($categoria_id), function ($query) use ($categoria_id) {
            return $query->where(function($t) use ($categoria_id) 
            {
                $t->where('produtos.categoria_id', $categoria_id)->orWhere('produtos.sub_categoria_id', $categoria_id);
            });
        })
        ->when(!empty($marca_id), function ($query) use ($marca_id) {
            return $query->where('produtos.marca_id', $marca_id);
        })
        ->when(!empty($produto_id), function ($query) use ($produto_id) {
            return $query->where('produtos.id', $produto_id);
        })
        ->groupBy('produto_id')
        ->pluck('produto_id')->toArray();

        $dataNfce = ItemNfce::where('produtos.empresa_id', $request->empresa_id)
        ->select('produtos.id as produto_id')
        ->join('produtos', 'produtos.id', '=', 'item_nfces.produto_id')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('item_nfces.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('item_nfces.created_at', '<=', $end_date);
        })
        ->when(!empty($categoria_id), function ($query) use ($categoria_id) {
            return $query->where(function($t) use ($categoria_id) 
            {
                $t->where('produtos.categoria_id', $categoria_id)->orWhere('produtos.sub_categoria_id', $categoria_id);
            });
        })
        ->when(!empty($marca_id), function ($query) use ($marca_id) {
            return $query->where('produtos.marca_id', $marca_id);
        })
        ->when(!empty($produto_id), function ($query) use ($produto_id) {
            return $query->where('produtos.id', $produto_id);
        })
        ->groupBy('produto_id')
        ->pluck('produto_id')->toArray();

        $resultado = array_unique(array_merge($dataNfe, $dataNfce));
        $data = [];
        foreach($resultado as $produto_id){
            $produto = Produto::findOrFail($produto_id);

            $subVenda = ItemNfe::where('produto_id', $produto_id)
            ->where('nves.tpNF', 1)
            ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
            ->sum('sub_total');

            $subVendaNfce = ItemNfce::where('produto_id', $produto_id)
            ->join('nfces', 'nfces.id', '=', 'item_nfces.nfce_id')
            ->sum('sub_total');

            $subCompra = ItemNfe::where('produto_id', $produto_id)
            ->where('nves.tpNF', 0)
            ->join('nves', 'nves.id', '=', 'item_nves.nfe_id')
            ->sum('sub_total');

            $data[] = [
                'produto_id' => $produto_id,
                'numero_sequencial' => $produto->numero_sequencial,
                'produto_nome' => $produto->nome,
                'total_vendas' => $subVenda + $subVendaNfce,
                'total_compras' => $subCompra,
            ];
        }

        $p = view('relatorios.lucro_produto', compact('data', 'start_date', 'end_date'))
        ->with('title', 'Relatório de Lucro por Produto');

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);

        $pdf = ob_get_clean();

        $domPdf->setPaper("A4", "landscape");
        $domPdf->render();
        $domPdf->stream("Relatório de Lucro por Produto.pdf", array("Attachment" => false));
    }
}
