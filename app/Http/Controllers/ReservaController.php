<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Acomodacao;
use App\Models\HospedeReserva;
use App\Models\ConsumoReserva;
use App\Models\NotasReserva;
use App\Models\FaturaReserva;
use App\Models\ServicoReserva;
use App\Models\Servico;
use App\Models\Empresa;
use App\Models\Cliente;
use App\Models\Cidade;
use App\Models\ContaReceber;
use App\Models\Nfe;
use App\Models\PadraoFrigobar;
use App\Models\Transportadora;
use App\Models\NaturezaOperacao;
use Illuminate\Support\Str;
use Dompdf\Dompdf;

class ReservaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reserva_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:reserva_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:reserva_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:reserva_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');
        $estado = $request->get('estado');

        $data = Reserva::where('empresa_id', request()->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when($estado != "", function ($query) use ($estado) {
            return $query->where('estado', $estado);
        })
        ->orderBy('id', 'desc')
        ->paginate(__itensPagina());

        return view('reservas.index', compact('data'));
    }

    public function create(){
        return view('reservas.create');
    }

    public function store(Request $request){
        $acomodacao = Acomodacao::findOrFail($request->acomodacao_id);
        try{

            $last = Reserva::where('empresa_id', $request->empresa_id)
            ->orderBy('numero_sequencial', 'desc')->first();
            $codigo = Str::random(25);
            $reserva = Reserva::create([
                'empresa_id' => $request->empresa_id,
                'cliente_id' => $request->cliente_id,
                'acomodacao_id' => $request->acomodacao_id,
                'data_checkin' => $request->data_checkin,
                'data_checkout' => $request->data_checkout,
                'valor_estadia' => __convert_value_bd($request->valor_estadia),
                'observacao' => $request->observacao ?? '',
                'estado' => 'pendente',
                'valor_total' => __convert_value_bd($request->valor_estadia),
                'codigo_reseva' => $codigo,
                'link_externo' => env("APP_URL") . "/reserva-cliente/$codigo",
                'total_hospedes' => $request->qtd_hospedes,
                'numero_sequencial' => $last != null ? ($last->numero_sequencial+1) : 1
            ]);

            for($i=0; $i<$request->qtd_hospedes; $i++){
                $hospede = HospedeReserva::create([
                    'reserva_id' => $reserva->id,
                    'descricao' => 'Hóspede #'.$i+1
                ]);

                if($i == 0){
                    $cliente = Cliente::find($request->cliente_id);
                    $hospede->nome_completo = $cliente->razao_social;
                    $hospede->cpf = $cliente->cpf_cnpj;
                    $hospede->rua = $cliente->rua;
                    $hospede->numero = $cliente->numero;
                    $hospede->bairro = $cliente->bairro;
                    $hospede->cidade_id = $cliente->cidade_id;
                    $hospede->telefone = $cliente->telefone;
                    $hospede->cep = $cliente->cep;
                    $hospede->email = $cliente->email;

                    $hospede->save();
                }
            }
            session()->flash("flash_success", "Reserva criada com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('reservas.index');

    }

    public function imprimir($id){
        $item = Reserva::findOrFail($id);
        __validaObjetoEmpresa($item);
        $config = Empresa::where('id', $item->empresa_id)->first();

        $p = view('reservas.imprimir', compact('config', 'item'));

        $domPdf = new Dompdf(["enable_remote" => true]);
        $domPdf->loadHtml($p);
        $pdf = ob_get_clean();
        $domPdf->setPaper("A4");
        $domPdf->render();
        $domPdf->stream("Reserva #$item->numero_sequencial.pdf", array("Attachment" => false));
    }

    public function show($id){
        $item = Reserva::findOrFail($id);
        __validaObjetoEmpresa($item);

        return view('reservas.show', compact('item'));
    }

    public function checkin($id){
        $item = Reserva::findOrFail($id);
        __validaObjetoEmpresa($item);

        return view('reservas.checkin', compact('item'));
    }

    public function checkinStart(Request $request, $id){
        $item = Reserva::findOrFail($id);
        __validaObjetoEmpresa($item);

        try{

            for($i=0; $i<sizeof($request->nome_completo); $i++){
                $hospede = HospedeReserva::findOrFail($request->hospede_id[$i]);

                $hospede->nome_completo = $request->nome_completo[$i];
                $hospede->cpf = $request->cpf[$i];
                $hospede->cep = $request->cep[$i];
                $hospede->rua = $request->rua[$i];
                $hospede->numero = $request->numero[$i];
                $hospede->bairro = $request->bairro[$i];
                $hospede->cidade_id = $request->cidade_id[$i];
                $hospede->telefone = $request->telefone[$i];
                $hospede->email = $request->email[$i];

                $hospede->save();

            }

            $item->data_checkin_realizado = date('Y-m-d H:i:s');
            $item->estado = 'iniciado';
            $item->save();
            session()->flash("flash_success", "Checkin realizado com sucesso!");

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('reservas.show', $id);
    }

    public function storeProduto(Request $request, $id){
        $item = Reserva::findOrFail($id);
        try{
            ConsumoReserva::create([
                'reserva_id' => $item->id,
                'produto_id' => $request->produto_id,
                'quantidade' => __convert_value_bd($request->quantidade_produto),
                'valor_unitario' => __convert_value_bd($request->valor_unitario_produto),
                'sub_total' => __convert_value_bd($request->sub_total_produto),
                'observacao' => $request->observacao ?? '',
                'frigobar' => $request->frigobar
            ]);
            session()->flash("flash_success", "Produto adicionado com sucesso!");
            $this->atualizaTotais($item->id);

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('reservas.show', [$id]);
    }

    public function storeServico(Request $request, $id){
        $item = Reserva::findOrFail($id);
        try{
            ServicoReserva::create([
                'reserva_id' => $item->id,
                'servico_id' => $request->servico_id,
                'quantidade' => __convert_value_bd($request->quantidade_servico),
                'valor_unitario' => __convert_value_bd($request->valor_unitario_servico),
                'sub_total' => __convert_value_bd($request->sub_total_servico),
                'observacao' => $request->observacao ?? '',
            ]);
            session()->flash("flash_success", "Serviço adicionado com sucesso!");
            $this->atualizaTotais($item->id);

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('reservas.show', [$id . '#table-servicos']);
    }

    public function storeNota(Request $request, $id){
        $item = Reserva::findOrFail($id);
        try{
            NotasReserva::create([
                'reserva_id' => $item->id,
                'texto' => $request->texto,
            ]);
            session()->flash("flash_success", "Nota adicionada com sucesso!");
            $this->atualizaTotais($item->id);

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('reservas.show', [$id . '#table-notas']);
    }

    public function cancelamento(Request $request, $id){
        $item = Reserva::findOrFail($id);
        try{
            $item->estado = 'cancelado';
            $item->valor_total = __convert_value_bd($request->valor_total);
            $item->motivo_cancelamento = $request->motivo_cancelamento;
            $item->save();
            session()->flash("flash_success", "Reserva cancelada com sucesso!");

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function destroy($id)
    {
        $item = Reserva::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->consumoProdutos()->delete();
            $item->consumoServicos()->delete();
            $item->notas()->delete();
            $item->hospedes()->delete();
            $item->delete();

            session()->flash("flash_success", "Reserva removida com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('reservas.index');
    }

    public function destroyProduto($id)
    {
        $item = ConsumoReserva::findOrFail($id);
        $reservaId = $item->reserva_id;
        try {

            $item->delete();
            session()->flash("flash_success", "Produto removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        $this->atualizaTotais($reservaId);
        return redirect()->back();
    }

    public function destroyNota($id)
    {
        $item = NotasReserva::findOrFail($id);
        $reservaId = $item->reserva_id;
        try {

            $item->delete();
            session()->flash("flash_success", "Nota removida com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('reservas.show', [$reservaId . '#table-notas']);
    }

    public function destroyServico($id)
    {
        $item = ServicoReserva::findOrFail($id);
        $reservaId = $item->reserva_id;
        try {

            $item->delete();
            session()->flash("flash_success", "Serviço removido com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        $this->atualizaTotais($reservaId);
        return redirect()->route('reservas.show', [$reservaId . '#table-servicos']);

    }

    private function atualizaTotais($reservaId){
        $item = Reserva::findOrFail($reservaId);

        $item->valor_total = $item->valor_estadia + $item->consumoProdutos->sum('sub_total') 
        + $item->consumoServicos->sum('sub_total') + $item->valor_outros - $item->desconto;
        $item->save();
    }

    public function updateHospedes(Request $request, $id){
        $item = Reserva::findOrFail($id);
        try{

            for($i=0; $i<sizeof($request->nome_completo); $i++){
                $hospede = HospedeReserva::findOrFail($request->hospede_id[$i]);

                $hospede->nome_completo = $request->nome_completo[$i];
                $hospede->cpf = $request->cpf[$i];
                $hospede->cep = $request->cep[$i];
                $hospede->rua = $request->rua[$i];
                $hospede->numero = $request->numero[$i];
                $hospede->bairro = $request->bairro[$i];
                $hospede->cidade_id = $request->cidade_id[$i];
                $hospede->telefone = $request->telefone[$i];
                $hospede->email = $request->email[$i];

                $hospede->save();
            }

            session()->flash("flash_success", "Dados dos hóspede(s) atualizados!");

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();

    }

    public function storeFatura(Request $request, $id){
        $item = Reserva::findOrFail($id);
        try{

            $caixa = __isCaixaAberto();
            if($caixa == null){
                session()->flash("flash_error", 'É necessário o caixa estar aberto para definir fatura!');
                return redirect()->back();
            }

            $item->desconto = __convert_value_bd($request->desconto);
            $item->valor_outros = __convert_value_bd($request->valor_outros);
            $item->save();
            foreach($item->fatura as $f){
                if($f->contaReceber){
                    $f->contaReceber->delete();
                }
                $f->delete();
            }

            for($i=0; $i<sizeof($request->tipo_pagamento); $i++){
                $fatura = FaturaReserva::create([
                    'reserva_id' => $item->id,
                    'tipo_pagamento' => $request->tipo_pagamento[$i],
                    'data_vencimento' => $request->data_vencimento[$i],
                    'valor' => __convert_value_bd($request->valor[$i])
                ]);
                if($request->gerar_conta_receber){
                    $descricao = "Reserva #".$item->numero_sequencial." -  Parcela " .($i+1)." de " . sizeof($request->tipo_pagamento)+1;

                    $data = [
                        'venda_id' => null,
                        'data_vencimento' => $request->data_vencimento[$i],
                        'valor_integral' => __convert_value_bd($request->valor[$i]),
                        'valor_recebido' => 0,
                        'descricao' => $descricao,
                        'status' => 0,
                        'empresa_id' => $request->empresa_id,
                        'cliente_id' => $item->cliente_id,
                        'tipo_pagamento' => $request->tipo_pagamento[$i],
                        'local_id' => $caixa->local_id,
                    ];
                    $conta = ContaReceber::create($data);

                    $fatura->conta_receber_id = $conta->id;
                    $fatura->save();
                }
            }

            $this->atualizaTotais($item->id);
            
            session()->flash("flash_success", "Fatura gerada!");
        } catch (\Exception $e) {
            echo $e->getMessage();
            die;
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function gerarNfe($id)
    {
        $item = Reserva::findOrFail($id);
        __validaObjetoEmpresa($item);

        $cliente = $item->cliente;

        $cidades = Cidade::all();
        $transportadoras = Transportadora::where('empresa_id', request()->empresa_id)->get();

        $naturezas = NaturezaOperacao::where('empresa_id', request()->empresa_id)->get();
        if (sizeof($naturezas) == 0) {
            session()->flash("flash_warning", "Primeiro cadastre um natureza de operação!");
            return redirect()->route('natureza-operacao.create');
        } 
        // $produtos = Produto::where('empresa_id', request()->empresa_id)->get();
        $empresa = Empresa::findOrFail(request()->empresa_id);

        $caixa = __isCaixaAberto();
        if (!__isCaixaAberto()) {
            session()->flash("flash_warning", "Abrir caixa antes de continuar!");
            return redirect()->route('caixa.create');
        }
        $empresa = __objetoParaEmissao($empresa, $caixa->local_id);
        
        $numeroNfe = Nfe::lastNumero($empresa);

        $item->itens = $item->consumoProdutos;

        $item->fatura = [];
        $isReserva = 1;
        return view('nfe.create', compact('item', 'cidades', 'transportadoras', 'naturezas', 'isReserva', 'numeroNfe', 
            'caixa'));
    }

    public function gerarNfse($id){

        $reserva = Reserva::findOrFail($id);
        __validaObjetoEmpresa($reserva);

        $servicoPadrao = Servico::where('empresa_id', $reserva->empresa_id)
        ->where('padrao_reserva_nfse', 1)->first();

        if($servicoPadrao == null){
            session()->flash("flash_warning", 'Cadastre um serviço Padrão reserva NFSe!');
            return redirect()->route('servicos.create');
        }

        $total = $reserva->valor_estadia + $reserva->consumoServicos->sum('sub_total');
        $descricaoServico = "Reserva de acomodação " . $reserva->acomodacao->info . ", ";
        foreach($reserva->consumoServicos as $s){
            $descricaoServico .= " " . $s->servico->nome . ", ";
        }
        $descricaoServico = substr($descricaoServico, 0, strlen($descricaoServico)-2);
        return view('nota_servico.create', compact('reserva', 'total', 'descricaoServico', 'servicoPadrao'));
    }

    public function conferirFrigobar(Request $request, $id){
        $item = Reserva::findOrFail($id);
        $frigobar = $item->acomodacao->frigobar;
        if($frigobar == null){
            session()->flash("flash_warning", 'Acomodação sem nenhum frigobar associado!');
            return redirect()->back();
        }
        return view('reservas.conferir_frigobar', compact('item', 'frigobar'));

    }

    public function updateEstado(Request $request, $id){
        $item = Reserva::findOrFail($id);
        __validaObjetoEmpresa($item);
        try{
            $item->estado = $request->estado;
            $item->save();
            session()->flash("flash_success", "Estado alterado!");

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function conferenciaFrigobarSave(Request $request, $id){
        $item = Reserva::findOrFail($id);
        try{
            $cont = 0;
            for($i=0; $i<sizeof($request->item_id); $i++){
                if(__convert_value_bd($request->quantidade[$i]) > 0){
                    $qtd = __convert_value_bd($request->quantidade[$i]);
                    $valor = __convert_value_bd($request->valor_unitario[$i]);
                    $itemFrigobar = PadraoFrigobar::findOrFail($request->item_id[$i]);

                    ConsumoReserva::create([
                        'reserva_id' => $item->id,
                        'produto_id' => $itemFrigobar->produto_id,
                        'quantidade' => $qtd,
                        'valor_unitario' => $valor,
                        'sub_total' => $valor * $qtd,
                        'observacao' => '',
                        'frigobar' => 1
                    ]);
                    $cont++;

                }
            }
            $item->conferencia_frigobar = 1;
            $item->save();
            session()->flash("flash_success", "Frigobar conferido, $cont itens adicionados no consumo da reserva!");
            return redirect()->route('reservas.show', [$item->id]);
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
            return redirect()->back();
        }

    }

}
