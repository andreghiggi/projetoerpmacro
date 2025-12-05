<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RemessaBoleto;
use App\Models\RemessaBoletoItem;
use App\Models\ContaBoleto;
use App\Models\ContaReceber;
use App\Models\Boleto;
use App\Models\Cliente;
use App\Utils\BoletoUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RemessaBoletoController extends Controller
{

    protected $util;

    public function __construct(BoletoUtil $util)
    {
        $this->util = $util;
        if (!is_dir(public_path('remessas_boleto'))) {
            mkdir(public_path('remessas_boleto'), 0777, true);
        }
    }

    public function index(Request $request){

        $contasBoleto = ContaBoleto::where('empresa_id', $request->empresa_id)
        ->get();

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $conta_boleto_id = $request->get('conta_boleto_id');

        $data = RemessaBoleto::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($conta_boleto_id), function ($query) use ($conta_boleto_id) {
            return $query->where('conta_boleto_id', $conta_boleto_id);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());

        return view('remessa_boletos.index', compact('data', 'contasBoleto'));
    }

    public function create(Request $request){
        $contasBoleto = ContaBoleto::where('empresa_id', request()->empresa_id)
        ->get();

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $conta_boleto_id = $request->get('conta_boleto_id');

        $boletos = RemessaBoletoItem::join('boletos', 'boletos.id', '=', 'remessa_boleto_items.boleto_id')
        ->where('boletos.empresa_id', request()->empresa_id)
        ->pluck('remessa_boleto_items.boleto_id')
        ->all();

        $data = Boleto::where('empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('vencimento', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date,) {
            return $query->whereDate('vencimento', '<=', $end_date);
        })->when(!empty($conta_boleto_id), function ($query) use ($conta_boleto_id) {
            return $query->where('conta_boleto_id', $conta_boleto_id);
        })
        ->orderBy('id', 'desc')
        ->whereNotIn('id', $boletos)->get();

        return view('remessa_boletos.create', compact('data', 'contasBoleto'));

    }

    public function store(Request $request){
        if(!$request->boleto_id){
            session()->flash('flash_error', 'Selecione ao menos um boleto para gerar');
            return redirect()->back();
        }
        try{

            $tipo = null;
            $banco = null;
            DB::transaction(function () use ($request) {
                $conta = null;
                $boleto = Boleto::findOrFail($request->boleto_id[0]);
                $tipo = $boleto->tipo;
                $contaBoleto = ContaBoleto::findOrFail($boleto->conta_boleto_id);
                $beneficiario = new \Eduardokum\LaravelBoleto\Pessoa([
                    'nome' => $contaBoleto->titular,
                    'endereco' => "$contaBoleto->rua, $contaBoleto->numero",
                    'bairro' => $contaBoleto->bairro,
                    'cep' => $contaBoleto->cep,
                    'uf' => $contaBoleto->cidade->uf,
                    'cidade' => $contaBoleto->cidade->nome,
                    'documento' => $contaBoleto->documento,
                ]);

                $boletos = [];
                for($i=0; $i<sizeof($request->boleto_id); $i++){
                    $boleto = Boleto::findOrFail($request->boleto_id[$i]);
                    if($conta == null){
                        $conta = $boleto->conta_boleto_id;
                    }

                    if($conta != $boleto->conta_boleto_id){
                        session()->flash('flash_error', 'Selecione um ou mais boletos do mesmo banco!');
                        return redirect()->back();
                    }

                    $contaReceber = $boleto->contaReceber;

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
                        'conta_boleto_id' => $boleto->conta_boleto_id,
                        'numero' => $boleto->numero,
                        'numero_documento' => $boleto->numero_documento,
                        'vencimento' => $boleto->vencimento,
                        'valor' => $boleto->valor,
                        'carteira' => $boleto->carteira,
                        'convenio' => $boleto->convenio,
                        'juros' => $boleto->juros,
                        'multa' => $boleto->multa,
                        'juros_apos' => $boleto->juros_apos,
                        'instrucoes' => $boleto->instrucoes ?? '', 
                        'usar_logo' => $boleto->usar_logo,
                        'tipo' => $request->tipo,
                        'codigo_cliente' => null,
                        'posto' => null,
                        'empresa_id' => $boleto->empresa_id,
                        'cliente_id' => $contaReceber->cliente_id
                    ];

                    $boletos = DB::transaction(function () use ($beneficiario, $pagador, $dataBoleto, $contaBoleto, $boletos) {
                        $boleto = $this->util->gerarBoletoParaRemessa($beneficiario, $pagador, $dataBoleto, $contaBoleto);
                        if($boleto){
                            array_push($boletos, $boleto);
                        }
                        return $boletos;
                    });

                }

                $dataRemessa = [
                    'agencia' => $contaBoleto->agencia,
                    'carteira' => $boleto->carteira,
                    'conta' => $contaBoleto->conta,
                    'convenio' => $boleto->convenio,
                    'variacaoCarteira' => '1',
                    'idremessa' => Str::random(30),
                    'beneficiario' => $beneficiario,
                    'codigoCliente' => $contaReceber->cliente_id
                ];

                $remessaFileName = $this->util->geraRemessa($boletos, $tipo, $contaBoleto->banco, $dataRemessa);
                $file = file_get_contents(public_path('remessas_boleto/'.$remessaFileName));
                // dd($file);
                // die;
                $remessaBoleto = RemessaBoleto::create([
                    'nome_arquivo' => $remessaFileName,
                    'empresa_id' => $request->empresa_id,
                    'conta_boleto_id' => $contaBoleto->id
                ]);

                for($i=0; $i<sizeof($request->boleto_id); $i++){
                    RemessaBoletoItem::create([
                        'remessa_id' => $remessaBoleto->id,
                        'boleto_id' => $request->boleto_id[$i]
                    ]);
                }

            });

session()->flash("flash_success", 'Remessa gerada!');
return redirect()->route('remessa-boleto.index');
}catch(\Exception $e){
    // echo $e->getMessage();
    // die;
    session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
    return redirect()->back();
}
}

public function download($id){
    $item = RemessaBoleto::findOrFail($id);
    if(!file_exists(public_path('remessas_boleto/'.$item->nome_arquivo))){
        session()->flash("flash_error", 'Arquivo não existe');
        return redirect()->back();
    }

    return response()->download(public_path('remessas_boleto/'.$item->nome_arquivo));
}

public function destroy($id){
    $item = RemessaBoleto::findOrFail($id);
    if(file_exists(public_path('remessas_boleto/'.$item->nome_arquivo))){
        unlink(public_path('remessas_boleto/'.$item->nome_arquivo));
    }
    $item->itens()->delete();
    $item->delete();
    session()->flash("flash_success", 'Remessa removida!');

    return redirect()->back();
}

public function import(Request $request){
    $contasBoleto = ContaBoleto::where('empresa_id', $request->empresa_id)
    ->get();

    return view('remessa_boletos.import', compact('contasBoleto'));
}

public function importStore(Request $request){
    if ($request->hasFile('file')) {
        $retorno = \Eduardokum\LaravelBoleto\Cnab\Retorno\Factory::make($request->file);
        // dd($retorno);

        $retorno->processar();
        $banco = $retorno->getBancoNome();
        $data = [];

        $contasPendentes = ContaReceber::where('empresa_id', $request->empresa_id)
        ->get();

        foreach($retorno->getDetalhes() as $item){
            // dd($item);
            $r = [
                'ocorrencia' => $item->ocorrenciaDescricao,
                'carteira' => $item->carteira,
                'valor_integral' => $item->valor,
                'valor_recebido' => $item->valorRecebido,
                'valor_tarifa' => $item->valorTarifa,
                'vencimento' => $item->dataVencimento,
                'pagador' => $item->getPagador() ? $item->getPagador()->getNome() : '',
                'documento' => $item->getPagador() ? $item->getPagador()->getDocumento() : '',
                'conta_id' => null
            ];
            $conta = $this->getContaReceber((object)$r);
            if($conta != null){
                $r['conta_id'] = $conta->id;
            }
            array_push($data, (object)$r);

        }

        return view('remessa_boletos.import_view', compact('data', 'banco', 'contasPendentes'));
    } else {
        session()->flash('flash_error', 'Nenhum Arquivo!!');
        return redirect()->back();
    }
}

private function getContaReceber($r){

    $cliente = Cliente::where('empresa_id', request()->empresa_id)
    ->where('cpf_cnpj', $r->documento)->first();
    if($cliente && $r->documento){

        $vencimento = str_replace("/", "-", $r->vencimento);
        $conta = ContaReceber::where('cliente_id', $cliente->id)
        ->where('valor_integral', $r->valor_integral)
        ->whereDate('data_vencimento', \Carbon\Carbon::parse($vencimento)->format('Y-m-d'))
        ->first();
        return $conta;
    }
    return null;
}

public function importSave(Request $request){
    try{
        $cont = 0;
        for($i=0; $i<sizeof($request->conta_id); $i++){
            $conta = ContaReceber::findOrFail($request->conta_id[$i]);
            if(!$conta->status){
                $cont++;
            }
            $conta->valor_recebido = __convert_value_bd($request->valor_recebido[$i]);
            $conta->data_recebimento = $request->data_recebimento[$i];
            $conta->status = true;
            $conta->save();

        }
        session()->flash("flash_success", 'Total de contas recebidas: ' . $cont);
        return redirect()->route('conta-receber.index');    
    }catch(\Exception $e){
            // echo $e->getMessage();
            // die;
        session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        return redirect()->back();
    }
}

}
