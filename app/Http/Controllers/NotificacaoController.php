<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificacao;

class NotificacaoController extends Controller
{
	public function clearAll(Request $request){
		Notificacao::where('empresa_id', $request->empresa_id)
		->where('visualizada', 0)
		->update([
			'visualizada' => 1
		]);
		return redirect()->back();
	}

	public function show($id){
		$item = Notificacao::findOrFail($id);
		$item->visualizada = 1;
		$item->save();
		return view('notificacao.show', compact('item'));
	}
}
