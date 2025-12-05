<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfiguracaoAgendamento;
use App\Models\MensagemAgendamentoLog;
use App\Utils\WhatsAppUtil;

class ConfiguracaoAgendamentoController extends Controller
{

    protected $util;
    public function __construct(WhatsAppUtil $util)
    {
        $this->util = $util;
    }

    public function index(Request $request)
    {
        $item = ConfiguracaoAgendamento::where('empresa_id', $request->empresa_id)
        ->first();

        return view('config_agendamento.index', compact('item'));
    }

    public function store(Request $request)
    {
        $item = ConfiguracaoAgendamento::where('empresa_id', $request->empresa_id)
        ->first();

        $request->merge([
            'mensagem_manha' => $request->mensagem_manha ?? '',
            'mensagem_alerta' => $request->mensagem_alerta ?? '',
        ]);

        if ($item != null) {

            $item->fill($request->all())->save();
            session()->flash("flash_success", "Configuração atualizada!");
        } else {

            ConfiguracaoAgendamento::create($request->all());
            session()->flash("flash_success", "Configuração cadastrada!");
        }
        return redirect()->back();
    }

    public function testeWpp(Request $request){
        $item = ConfiguracaoAgendamento::where('empresa_id', $request->empresa_id)
        ->first();

        $telefone = preg_replace('/[^0-9]/', '', $request->telefone);
        $mensagem = $request->mensagem;
        $retorno = $this->util->sendMessageWithToken('55'.$telefone, $mensagem, $request->empresa_id, $item->token_whatsapp);
        $retorno = json_decode($retorno, true);
        dd($retorno);
    }

    public function logs(Request $request){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');

        $data = MensagemAgendamentoLog::where('empresa_id', $request->empresa_id)
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->orderBy('id', 'desc')
        ->paginate(__itensPagina());

        return view('config_agendamento.logs', compact('data'));

    }

}
