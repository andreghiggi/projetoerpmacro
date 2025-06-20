<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FaturaPreVenda;
use App\Models\ItemPreVenda;
use App\Models\PreVenda;
use App\Models\ConfigGeral;
use Illuminate\Http\Request;

class PreVendaController extends Controller
{
    public function finalizar($id)
    {
        $item = PreVenda::findOrFail($id);
        $config = ConfigGeral::where('empresa_id', $item->empresa_id)->first();

        return view('pre_venda.partials.modal_finalizar', compact('item', 'config'));
    }
}
