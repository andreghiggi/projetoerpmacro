<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContaReceber;
use App\Models\ContaBoleto;
use App\Models\Boleto;
use App\Models\Cliente;
use App\Utils\BoletoUtil;
use Illuminate\Support\Facades\DB;

class BoletoController extends Controller
{

    protected $util;

    public function __construct(BoletoUtil $util)
    {
        $this->util = $util;
        if (!is_dir(public_path('boletos_pdf'))) {
            mkdir(public_path('boletos_pdf'), 0777, true);
        }
    }

    public function index(Request $request){

        $contasBoleto = ContaBoleto::where('empresa_id', request()->empresa_id)
        ->get();

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');
        $banco = $request->get('banco');

        $cliente = null;
        if($cliente_id){
            $cliente = Cliente::findOrFail($cliente_id);
        }

        $data = Boleto::where('boletos.empresa_id', $request->empresa_id)
        ->select('boletos.*')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('boletos.vencimento', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('boletos.vencimento', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('conta_recebers.cliente_id', $cliente_id)
            ->join('conta_recebers', 'conta_recebers.id', '=', 'boletos.conta_receber_id');
        })
        ->when(!empty($banco), function ($query) use ($banco) {
            return $query->where('boletos.conta_boleto_id', $banco);
        })
        ->orderBy('boletos.created_at', 'desc')
        ->paginate(__itensPagina());
        return view('boletos.index', compact('data', 'contasBoleto', 'cliente'));
    }

    public function create($id){
        $conta = ContaReceber::findOrFail($id);
        $contasBoleto = ContaBoleto::where('empresa_id', request()->empresa_id)
        ->get();
        $contaPadrao = ContaBoleto::where('empresa_id', request()->empresa_id)
        ->where('padrao', 1)
        ->first();

        $contas = [];
        return view('boletos.create', compact('conta', 'contasBoleto', 'contas', 'contaPadrao'));
    }

    public function createSeveral(Request $request){

        $conta = null;
        $contasBoleto = ContaBoleto::where('empresa_id', $request->empresa_id)
        ->get();

        $contas = [];
        for($i=0; $i<sizeof($request->conta_id); $i++){
            $conta = ContaReceber::findOrFail($request->conta_id[$i]);
            if($conta->boleto){
                session()->flash("flash_warning", 'Já existe um boleto para alguma conta selecionada!');
                return redirect()->back();
            }
            array_push($contas, $conta);
        }
        $contaPadrao = ContaBoleto::where('empresa_id', request()->empresa_id)
        ->where('padrao', 1)
        ->first();
        return view('boletos.create', compact('conta', 'contasBoleto', 'contas', 'contaPadrao'));
    }

    public function show($id){
        $item = Boleto::findOrFail($id);
        return view('boletos.show', compact('item'));
    }

    public function print($id){
        $item = Boleto::findOrFail($id);

        if(!file_exists(public_path('boletos_pdf/'.$item->nome_arquivo))){
            session()->flash("flash_error", 'Arquivo não existe');
            return redirect()->back();
        }
        $pdf = file_get_contents(public_path('boletos_pdf/'.$item->nome_arquivo));
        return response($pdf)
        ->header('Content-Type', 'application/pdf');
    }

    public function store(Request $request){
        try{

            $contaBoleto = ContaBoleto::findOrFail($request->conta_boleto);
            $beneficiario = new \Eduardokum\LaravelBoleto\Pessoa([
                'nome' => $contaBoleto->titular,
                'endereco' => "$contaBoleto->rua, $contaBoleto->numero",
                'bairro' => $contaBoleto->bairro,
                'cep' => $contaBoleto->cep,
                'uf' => $contaBoleto->cidade->uf,
                'cidade' => $contaBoleto->cidade->nome,
                'documento' => $contaBoleto->documento,
            ]);

            for($i=0; $i<sizeof($request->conta_id); $i++){

                $contaReceber = ContaReceber::findOrFail($request->conta_id[$i]);

                $pagador = new \Eduardokum\LaravelBoleto\Pessoa([
                    'nome' => $contaReceber->cliente->razao_social,
                    'endereco' => $contaReceber->cliente->rua . ", " . $contaReceber->cliente->numero,
                    'bairro' => $contaReceber->cliente->bairro,
                    'cep' => $contaReceber->cliente->cep,
                    'uf' => $contaReceber->cliente->cidade->uf,
                    'cidade' => $contaReceber->cliente->cidade->nome,
                    'documento' => $contaReceber->cliente->cpf_cnpj,
                ]);

                $dataBoleto = [
                    'conta_boleto_id' => $request->conta_boleto,
                    'conta_receber_id' => $contaReceber->id,
                    'numero' => $request->numero[$i],
                    'numero_documento' => $request->numero_documento[$i],
                    'vencimento' => $request->vencimento[$i],
                    'valor' => __convert_value_bd($request->valor[$i]),
                    'carteira' => $request->carteira,
                    'convenio' => $request->convenio,
                    'linha_digitavel' => '',
                    'juros' => __convert_value_bd($request->juros[$i]),
                    'multa' => __convert_value_bd($request->multa[$i]),
                    'juros_apos' => $request->juros_apos[$i],
                    'instrucoes' => $request->instrucoes[$i] ?? '', 
                    'usar_logo' => $request->usar_logo,
                    'tipo' => $request->tipo,
                    // 'codigo_cliente' => null,
                    'codigo_cliente' => $request->convenio,
                    'posto' => isset($request->posto) ? $request->posto: null,
                    'empresa_id' => $request->empresa_id,
                    'cliente_id' => $contaReceber->cliente_id
                ];
                // dd($dataBoleto);

                $boleto = DB::transaction(function () use ($beneficiario, $pagador, $dataBoleto, $contaBoleto) {
                    $boleto = $this->util->gerarBoleto($beneficiario, $pagador, $dataBoleto, $contaBoleto);
                    // dd($boleto);
                    if(isset($boleto['erro'])){
                        session()->flash("flash_error", $boleto['mensagem']);
                        return $boleto;
                    }
                    $dataBoleto['nome_arquivo'] = $boleto['fileName'];
                    $dataBoleto['linha_digitavel'] = $boleto['linhaDigitavel']; 
                    $boleto = Boleto::create($dataBoleto);
                    return $boleto;
                });


                // $pdf = file_get_contents(public_path('boletos_pdf/'.$boleto->nome_arquivo));
                // return response($pdf)
                // ->header('Content-Type', 'application/pdf');

                if(isset($boleto['erro'])){
                    session()->flash("flash_error", $boleto['mensagem']);
                    return redirect()->back();
                }

            }
            if(sizeof($request->conta_id) > 1){
                session()->flash("flash_success", 'Boletos gerados!');
                return redirect()->route('boleto.index');
            }
            session()->flash("flash_success", 'Boleto gerado!');
            return redirect()->route('boleto.show', [$boleto->id]);
        }catch(\Exception $e){
            // echo $e->getMessage();
            // die;
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
            return redirect()->back();
        }
    }

    public function destroy($id){
        $item = Boleto::findOrFail($id);
        
        $item->delete();
        session()->flash("flash_success", 'Boleto removido!');

        return redirect()->back();
    }
}
