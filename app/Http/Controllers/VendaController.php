<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nfe;
use App\Models\Nfce;
use Illuminate\Support\Facades\DB;
use App\Models\ConfigGeral;

class VendaController extends Controller
{
    public function index(Request $request){

        $locais = __getLocaisAtivoUsuario();
        $locais = $locais->pluck(['id']);

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $cliente_id = $request->get('cliente_id');
        $estado = $request->get('estado');
        $local_id = $request->get('local_id');

        $nfe = Nfe::where('nves.empresa_id', $request->empresa_id)
        ->where('orcamento', 0)->where('tpNF', 1)
        ->select('nves.id', 'cliente_id', 'user_id', 'funcionario_id', 'total', 'desconto', 'acrescimo', 'numero_serie', 'nves.numero',
            'estado', 'nves.ambiente', 'nves.numero_sequencial', 'nves.created_at as created_at', DB::raw("'Nfe' as tipo"), 
            'clientes.razao_social as razao_social', 'clientes.cpf_cnpj as cpf_cnpj', 'localizacaos.descricao as descricao', 
            'users.name as user_name', 'data_emissao')
        ->join('clientes', 'clientes.id', '=', 'nves.cliente_id')
        ->join('localizacaos', 'localizacaos.id', '=', 'nves.local_id')
        ->join('users', 'users.id', '=', 'nves.user_id')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('nves.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('nves.created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when($estado != "", function ($query) use ($estado) {
            return $query->where('estado', $estado);
        });

        $nfce = Nfce::where('nfces.empresa_id', $request->empresa_id)
        ->select('nfces.id', 'cliente_id', 'user_id', 'funcionario_id', 'total', 'desconto', 'acrescimo', 'numero_serie', 'nfces.numero',
            'estado', 'nfces.ambiente', 'nfces.numero_sequencial', 'nfces.created_at as created_at', DB::raw("'Nfce' as tipo"), 
            'clientes.razao_social as razao_social', 'clientes.cpf_cnpj as cpf_cnpj', 'localizacaos.descricao as descricao',
            'users.name as user_name', 'data_emissao')
        ->leftJoin('clientes', 'clientes.id', '=', 'nfces.cliente_id')
        ->join('localizacaos', 'localizacaos.id', '=', 'nfces.local_id')
        ->join('users', 'users.id', '=', 'nfces.user_id')
        ->when(!empty($start_date), function ($query) use ($start_date) {
            return $query->whereDate('nfces.created_at', '>=', $start_date);
        })
        ->when(!empty($end_date), function ($query) use ($end_date) {
            return $query->whereDate('nfces.created_at', '<=', $end_date);
        })
        ->when(!empty($cliente_id), function ($query) use ($cliente_id) {
            return $query->where('cliente_id', $cliente_id);
        })
        ->when($estado != "", function ($query) use ($estado) {
            return $query->where('estado', $estado);
        });

        $unionQuery = $nfe->unionAll($nfce);

        $sub = DB::table(DB::raw("({$unionQuery->toSql()}) as unioned"))
        ->mergeBindings($unionQuery->getQuery());

        $somaGeral = DB::table(DB::raw("({$unionQuery->toSql()}) as s"))
        ->mergeBindings($unionQuery->getQuery())
        ->sum('total');

        $data = $sub->orderBy('created_at', 'desc')
        ->paginate(__itensPagina());

        $config = ConfigGeral::where('empresa_id', $request->empresa_id)->first();
        $envioWppLink = 0;
        if($config != null && $config->status_wpp_link){
            $envioWppLink = 1;
        }

        return view('vendas.index', compact('data', 'somaGeral', 'envioWppLink'));
    }
}
