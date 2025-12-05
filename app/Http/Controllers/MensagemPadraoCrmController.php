<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MensagemPadraoCrm;

class MensagemPadraoCrmController extends Controller
{

    public function index(Request $request)
    {
        $data = MensagemPadraoCrm::where('empresa_id', $request->empresa_id)
        ->get();

        return view('mensagem_padrao_crm.index', compact('data'));
    }

    public function create()
    {
        return view('mensagem_padrao_crm.create');
    }

    public function edit($id)
    {
        $item = MensagemPadraoCrm::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('mensagem_padrao_crm.edit', compact('item'));
    }

    public function store(Request $request){
        try {

            $request->merge([
                'enviar_whatsapp' => $request->enviar_whatsapp ? 1 : 0,
                'enviar_email' => $request->enviar_email ? 1 : 0,
            ]);
            
            MensagemPadraoCrm::create($request->all());
            session()->flash('flash_success', 'Mensagem cadastrada com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível concluir o cadastro' . $e->getMessage());
        }
        return redirect()->route('mensagem-padrao-crm.index');
    }

    public function update(Request $request, $id){
        try {
            $item = MensagemPadraoCrm::findOrFail($id);
            
            $request->merge([
                'enviar_whatsapp' => $request->enviar_whatsapp ? 1 : 0,
                'enviar_email' => $request->enviar_email ? 1 : 0,
            ]);

            $item->fill($request->all())->save();
            session()->flash('flash_success', 'Mensagem atualizada com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível concluir ' . $e->getMessage());
        }
        return redirect()->route('mensagem-padrao-crm.index');
    }

    public function destroy($id)
    {
        $item = MensagemPadraoCrm::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $item->delete();
            session()->flash('flash_success', 'Mensagem removida com sucesso');
        } catch (\Exception $e) {
            session()->flash('flash_error', 'Não foi possível deletar: ' . $e->getMessage());
        }
        return redirect()->back();
    }
    
}
