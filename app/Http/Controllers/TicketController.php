<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketMensagem;
use App\Models\Notificacao;
use App\Models\TicketMensagemAnexo;
use Illuminate\Support\Facades\DB;
use App\Utils\UploadUtil;

class TicketController extends Controller
{
    protected $util;

    public function __construct(UploadUtil $util)
    {
        $this->util = $util;
    }

    public function index(Request $request){
        $status = $request->get('status');
        $assunto = $request->get('assunto');
        $created_at = $request->get('created_at');
        $departamento = $request->get('departamento');

        $data = Ticket::where('empresa_id', $request->empresa_id)
        ->when(!empty($created_at), function ($query) use ($created_at) {
            return $query->whereDate('created_at', "$created_at");
        })
        ->when($assunto, function ($query) use ($assunto) {
            return $query->where('assunto', 'like', "%$assunto%");
        })
        ->when($departamento, function ($query) use ($departamento) {
            return $query->where('departamento', $departamento);
        })
        ->orderBy('updated_at', 'desc')
        ->paginate(__itensPagina());

        return view('ticket.index', compact('data'));
    }

    public function create(){
        return view('ticket.create');
    }

    public function store(Request $request){
        $this->__validate($request);
        try {
            $item = DB::transaction(function () use ($request) {
                $ticket = Ticket::create([
                    'assunto' => $request->assunto,
                    'departamento' => $request->departamento,
                    'status' => 'aberto',
                    'empresa_id' => $request->empresa_id
                ]);

                $ticketMensagem = TicketMensagem::create([
                    'ticket_id' => $ticket->id,
                    'descricao' => $request->descricao,
                    'resposta' => 0
                ]);

                if ($request->hasFile('anexo')) {
                    $file_name = $this->util->uploadImage($request, '/ticket', 'anexo');
                    TicketMensagemAnexo::create([
                        'ticket_mensagem_id' => $ticketMensagem->id,
                        'anexo' => $file_name
                    ]);
                }

                $this->criaNotificacao('tickets', $ticket);

                return $ticket;
            });
            session()->flash("flash_success", "Solicitação aberta!");

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->route('ticket.index');
    }

    private function criaNotificacao($tabela, $ticket, $prioridade = 'baixa'){

        $descricao = view('notificacao.partials.ticket_super', compact('ticket'));

        Notificacao::create([
            'empresa_id' => null,
            'tabela' => $tabela,
            'descricao' => $descricao,
            'descricao_curta' => 'Resposta de ticket',
            'referencia' => $ticket->id,
            'status' => 1,
            'por_sistema' => 0,
            'super' => 1,
            'prioridade' => $prioridade, 
            'visualizada' => 0,
            'titulo' => 'Ticket'
        ]);
    }

    public function show($id){
        $item = Ticket::findOrFail($id);
        return view('ticket.show', compact('item'));
    }

    public function download($id){
        $item = TicketMensagemAnexo::findOrFail($id);
        echo $item->file;
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

    public function addMensagem(Request $request, $id){
        $this->__validate($request);

        try {
            $item = Ticket::findOrFail($id);

            $item->status = 'aguardando';
            $item->save();

            $ticketMensagem = TicketMensagem::create([
                'ticket_id' => $item->id,
                'descricao' => $request->descricao,
                'resposta' => 0
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

            $this->criaNotificacao('tickets', $item);

            session()->flash("flash_success", "Mensagem adicionada");

        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
        }
        return redirect()->back();
    }

}
