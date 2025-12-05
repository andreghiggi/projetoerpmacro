<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cotacao;
use App\Models\ItemCotacao;
use App\Models\Fornecedor;
use App\Models\EmailConfig;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Mail;
use App\Utils\EmailUtil;

class CotacaoController extends Controller
{

    protected $util;
    public function __construct(EmailUtil $util){
        $this->util = $util;
        $this->middleware('permission:cotacao_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:cotacao_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:cotacao_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:cotacao_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $fornecedor_id = $request->get('fornecedor_id');
        $estado = $request->get('estado');
        $referencia = $request->get('referencia');
        $gerado_compra = $request->get('gerado_compra');
        $fornecedor = null;

        $data = Cotacao::where('empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($fornecedor_id), function ($query) use ($fornecedor_id) {
            return $query->where('fornecedor_id', $fornecedor_id);
        })
        ->when($estado != "", function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->when($referencia != "", function ($query) use ($referencia) {
            return $query->where('referencia', 'LIKE', "%$referencia%");
        })
        ->when($gerado_compra != "", function ($query) use ($gerado_compra) {
            if($gerado_compra == 1){
                return $query->where('nfe_id', '>', 0);
            }
            return $query->where('nfe_id', null);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());

        if(!empty($fornecedor_id)){
            $fornecedor = Fornecedor::findOrFail($fornecedor_id);
        }
        return view('cotacoes.index', compact('data', 'fornecedor'));
    }

    public function create()
    {
        $fornecedores = Fornecedor::where('empresa_id', request()->empresa_id)
        ->orderBy('razao_social')
        ->get();
        return view('cotacoes.create', compact('fornecedores'));
    }

    public function edit($id)
    {
        $item = Cotacao::findOrfail($id);
        $fornecedores = Fornecedor::where('empresa_id', request()->empresa_id)->get();
        return view('cotacoes.edit', compact('fornecedores', 'item'));
    }

    public function store(Request $request){
        try{
            DB::transaction(function () use ($request) {
                $referencia = Str::random(7);

                for($i=0; $i<sizeof($request->fornecedor_id); $i++){
                    $fornecedor = Fornecedor::findOrFail($request->fornecedor_id[$i]);

                    $cotacao = Cotacao::create([
                        'empresa_id' => $request->empresa_id,
                        'fornecedor_id' => $fornecedor->id,
                        'hash_link' => Str::random(30),
                        'referencia' => $referencia,
                        'observacao' => $request->observacao ?? '',
                        'estado' => $request->estado
                    ]);
                    for($j=0; $j<sizeof($request->produto_id); $j++){
                        ItemCotacao::create([
                            'cotacao_id' => $cotacao->id,
                            'quantidade' => __convert_value_bd($request->quantidade[$j]),
                            'produto_id' => $request->produto_id[$j],
                        ]);
                    }
                    __createLog($request->empresa_id, 'Cotação', 'cadastrar', $cotacao->fornecedor->info . " - #$cotacao->hash_link");
                    $this->enviarEmailCotacao($cotacao);
                }
            });
            session()->flash("flash_success", 'Cotação criada!');
            return redirect()->route('cotacoes.index');
        } catch (\Exception $e) {
            // echo $e->getMessage() . '<br>' . $e->getLine();
            // die;
            __createLog($request->empresa_id, 'Cotação', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
            return redirect()->back();
        }
    }

    private function enviarEmailCotacao($cotacao){
        if($cotacao->fornecedor->email != ''){

            $email = $cotacao->fornecedor->email;

            $emailConfig = EmailConfig::where('empresa_id', request()->empresa_id)
            ->where('status', 1)
            ->first();
            if($emailConfig != null){

                $body = view('mail.cotacao', compact('cotacao'));
                $result = $this->util->enviaEmailPHPMailer($email, 'Envio de cotação', $body, $emailConfig);
            }else{
                Mail::send('mail.cotacao', ['cotacao' => $cotacao], function($m) use ($email){

                    $nomeEmail = env('MAIL_FROM_NAME');
                    $m->from(env('MAIL_USERNAME'), $nomeEmail);
                    $m->subject('Envio de cotação');
                    $m->to($email);
                });
            }
        }
    }

    public function update(Request $request, $id){
        try{
            DB::transaction(function () use ($request, $id) {

                $cotacao = Cotacao::findOrfail($id);
                $cotacao->fill($request->all())->save();
                $cotacao->itens()->delete();

                for($j=0; $j<sizeof($request->produto_id); $j++){
                    ItemCotacao::create([
                        'cotacao_id' => $cotacao->id,
                        'quantidade' => __convert_value_bd($request->quantidade[$j]),
                        'produto_id' => $request->produto_id[$j],
                    ]);
                }
                __createLog($request->empresa_id, 'Cotação', 'editar', $cotacao->fornecedor->info . " - #$cotacao->hash_link");
            });
            session()->flash("flash_success", 'Cotação atualizada!');
            return redirect()->route('cotacoes.index');
        } catch (\Exception $e) {
            // echo $e->getMessage() . '<br>' . $e->getLine();
            // die;
            __createLog($request->empresa_id, 'Cotação', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
            return redirect()->back();
        }
    }

    public function show($id)
    {
        $item = Cotacao::findOrfail($id);

        $cotacaoComCompra = Cotacao::where('referencia', $item->referencia)
        ->where('nfe_id', '>', 0)
        ->first();

        return view('cotacoes.show', compact('item', 'cotacaoComCompra'));
    }

    public function destroy($id)
    {
        $item = Cotacao::findOrFail($id);
        try {
            $descricaoLog = $item->fornecedor->info . " - #$item->hash_link";

            $item->itens()->delete();
            $item->fatura()->delete();
            $item->delete();
            __createLog(request()->empresa_id, 'Cotação', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Cotação removida com sucesso!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Cotação', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado.', $e->getMessage());
        }
        return redirect()->route('cotacoes.index');
    }

    public function purchase($id)
    {
        //gerar compra
        $item = Cotacao::findOrFail($id);
        return redirect()->route('compras.create', ['cotacao_id='.$item->id]);

    }

}
