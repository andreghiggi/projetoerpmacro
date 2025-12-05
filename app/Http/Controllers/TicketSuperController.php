<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketMensagem;
use App\Models\TicketMensagemAnexo;
use App\Models\Empresa;
use App\Models\Notificacao;
use App\Utils\UploadUtil;
use Illuminate\Support\Facades\DB;
use App\Models\ConfigGeral;

class TicketSuperController extends Controller
{
    protected $util;

    public function __construct(UploadUtil $util)
    {
        $this->util = $util;
    }
    
    public function index(Request $request){
        $status = $request->get('status');
        $created_at = $request->get('created_at');
        $empresa_id = $request->get('empresa');
        $departamento = $request->get('departamento');

        $empresa = null;
        if($empresa_id){
            $empresa = Empresa::findOrFail($empresa_id);
        }
        $data = Ticket::when(!empty($status), function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->when(!empty($created_at), function ($query) use ($created_at) {
            return $query->whereDate('created_at', "$created_at");
        })
        ->when($empresa != null, function ($query) use ($empresa) {
            return $query->where('empresa_id', $empresa->id);
        })
        ->when($departamento, function ($query) use ($departamento) {
            return $query->where('departamento', $departamento);
        })
        ->orderBy('updated_at', 'desc')
        ->paginate(__itensPagina());

        return view('ticket_super.index', compact('data', 'empresa'));
    }

    public function create(){
        return view('ticket_super.create');
    }

    public function store(Request $request){

        try {
            $item = DB::transaction(function () use ($request) {
                $ticket = Ticket::create([
                    'assunto' => $request->assunto,
                    'departamento' => $request->departamento,
                    'status' => 'respondida',
                    'empresa_id' => $request->empresa
                ]);

                $ticketMensagem = TicketMensagem::create([
                    'ticket_id' => $ticket->id,
                    'descricao' => $request->descricao,
                    'resposta' => 1
                ]);

                if ($request->hasFile('anexo')) {
                    $file_name = $this->util->uploadImage($request, '/ticket', 'anexo');
                    TicketMensagemAnexo::create([
                        'ticket_mensagem_id' => $ticketMensagem->id,
                        'anexo' => $file_name
                    ]);
                }

                return $ticket;
            });
            $empresa = Empresa::findOrFail($request->empresa);
            $descricaoCurta = 'Abertura de chamado';
            $this->createTicket($item, $empresa, $descricaoCurta);
            session()->flash("flash_success", "Solicitação aberta para empresa: " . $empresa->nome);

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('ticket-super.index');
    }

    private function createTicket($ticket, $empresa, $descricaoCurta){
        $config = ConfigGeral::where('empresa_id', $empresa->id)->first();
        $alertasAtivos = null;
        if($config != null){
            $alertasAtivos = json_decode($config->notificacoes);
        }
        if($alertasAtivos == null || in_array('Ticket', $alertasAtivos)){
            $descricao = view('notificacao.partials.ticket', compact('ticket'));
            Notificacao::create([
                'empresa_id' => $empresa->id,
                'tabela' => 'tickets',
                'descricao' => $descricao,
                'descricao_curta' => $descricaoCurta,
                'referencia' => $ticket->id,
                'status' => 1,
                'por_sistema' => 1,
                'prioridade' => 'baixa', 
                'visualizada' => 0,
                'titulo' => 'Ticket #'.$ticket->id
            ]);
        }
    }

    public function edit($id){
        $item = Ticket::findOrFail($id);
        return view('ticket_super.edit', compact('item'));
    }

    public function update(Request $request, $id){

        try {
            $item = Ticket::findOrFail($id);

            $item->departamento = $request->departamento;
            $item->assunto = $request->assunto;
            $item->empresa_id = $request->empresa;
            $item->save();

            $empresa = Empresa::findOrFail($request->empresa);

            session()->flash("flash_success", "Solicitação alterada para empresa: " . $empresa->nome);

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('ticket-super.index');
    }

    public function updateStatus(Request $request, $id){

        try {
            $item = Ticket::findOrFail($id);

            $item->status = $request->status;
            $item->save();

            session()->flash("flash_success", "Status alterado");

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    public function destroy($id)
    {
        $item = Ticket::findOrFail($id);
        try {
            foreach($item->mensagens as $m){
                foreach($m->anexos as $a){
                    $this->util->unlinkImage($a, '/ticket', 'anexo');
                    $a->delete();
                }
            }
            $item->mensagens()->delete();
            $item->delete();
            session()->flash('flash_success', 'Removido com sucesso');
        } catch (\Exception $e) {
            echo $e->getMessage();
            die;
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('ticket-super.index');
    }

    public function destroySelecet(Request $request)
    {
        $removidos = 0;
        for($i=0; $i<sizeof($request->item_delete); $i++){
            $item = Ticket::findOrFail($request->item_delete[$i]);
            try {
                foreach($item->mensagens as $m){
                    foreach($m->anexos as $a){
                        $this->util->unlinkImage($a, '/ticket', 'anexo');
                        $a->delete();
                    }
                }
                $item->mensagens()->delete();
                $item->delete();
                $removidos++;
            } catch (\Exception $e) {
                session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
                return redirect()->back();
            }
        }

        session()->flash("flash_success", "Total de itens removidos: $removidos!");
        return redirect()->back();
    }

    public function destroyMensagem($id)
    {
        $item = TicketMensagem::findOrFail($id);
        try {

            foreach($item->anexos as $a){
                $this->util->unlinkImage($a, '/ticket', 'anexo');
                $a->delete();
            }
            $item->delete();
            session()->flash('flash_success', 'Mensagem removida com sucesso');
        } catch (\Exception $e) {
            echo $e->getMessage();
            die;
            session()->flash('flash_error', 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->back();
    }

    public function show($id){
        $item = Ticket::findOrFail($id);
        return view('ticket_super.show', compact('item'));
    }

    public function addMensagem(Request $request, $id){
        $this->__validate($request);

        try {
            $item = Ticket::findOrFail($id);
            $item->status = 'respondida';
            $item->save();
            $ticketMensagem = TicketMensagem::create([
                'ticket_id' => $item->id,
                'descricao' => $request->descricao,
                'resposta' => 1
            ]);

            if ($request->file('anexos')) {
                foreach($request->file('anexos') as $key => $file){
                    $file_name = $this->util->uploadFile($file, '/ticket');
                    TicketMensagemAnexo::create([
                        'ticket_mensagem_id' => $ticketMensagem->id,
                        'anexo' => $file_name
                    ]);
                }
            }
            $descricaoCurta = 'Resposta de chamado';
            $this->createTicket($item, $item->empresa, $descricaoCurta);

            session()->flash("flash_success", "Mensagem adicionada");

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

    private function __validate(Request $request)
    {
        $rules = [
            'descricao' => 'required',
        ];

        $messages = [
            'descricao.required' => 'Campo Obrigatório',
        ];
        $this->validate($request, $rules, $messages);
    }

}
