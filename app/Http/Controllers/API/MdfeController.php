<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cidade;
use App\Models\Empresa;
use App\Models\Mdfe;
use App\Models\Nfe;
use App\Services\MDFeService;
use Illuminate\Http\Request;
use InvalidArgumentException;
use NFePHP\DA\MDFe\Daevento;

class MdfeController extends Controller
{
    public function linhaInfoDescarregamento(Request $request)
    {
        try {
            $tp_und_transp = $request->tp_und_transp;
            $id_und_transp = $request->id_und_transp;
            $quantidade_rateio = $request->quantidade_rateio;
            $quantidade_rateio_carga = $request->quantidade_rateio_carga;
            $chave_nfe = $request->chave_nfe;
            $chave_cte = $request->chave_cte;
            $municipio_descarregamento = $request->municipio_descarregamento;
            $lacres_transporte = $request->lacres_transporte;
            $lacres_unidade = $request->lacres_unidade;

            $cidade = Cidade::findOrFail($municipio_descarregamento);

            return view('mdfe.partials.row_info', compact(
                'tp_und_transp',
                'id_und_transp',
                'quantidade_rateio',
                'quantidade_rateio_carga',
                'chave_nfe',
                'chave_cte',
                'municipio_descarregamento',
                'lacres_transporte',
                'lacres_unidade',
                'cidade'
            ));
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 401);
        }
    }

    public function vendasAprovadas(Request $request)
    {
        try {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $tipo = $request->get('tipo');
            $data = [];
            if($tipo == 1){
                $data = Nfe::orderBy('created_at', 'desc')
                ->where('empresa_id', $request->empresa_id)
                ->where('tpNF', 1)
                ->where('estado', 'aprovado')
                ->when(!empty($start_date), function ($query) use ($start_date) {
                    return $query->whereDate('created_at', '>=', $start_date);
                })
                ->when(!empty($end_date), function ($query) use ($end_date) {
                    return $query->whereDate('created_at', '<=', $end_date);
                })
                ->get();
                return view('mdfe.lista_vendas', compact('data'));
            }

            if($tipo == 0){
                $data = Nfe::orderBy('created_at', 'desc')
                ->where('empresa_id', $request->empresa_id)
                ->where('tpNF', 0)
                ->when(!empty($start_date), function ($query) use ($start_date) {
                    return $query->whereDate('created_at', '>=', $start_date);
                })
                ->where(function($q){
                    $q->where('estado', 'aprovado')
                    ->orWhere('chave_importada', '!=', '');
                })

                ->when(!empty($end_date), function ($query) use ($end_date) {
                    return $query->whereDate('created_at', '<=', $end_date);
                })
                ->get();
                return view('mdfe.lista_compras', compact('data'));

            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 401);
        }
    }

    public function cancelar(Request $request)
    {
        $item = Mdfe::findOrFail($request->id);
        $empresa = Empresa::findOrFail($item->empresa_id);

        if ($item != null) {
            $cnpj = preg_replace('/[^0-9]/', '', $empresa->cpf_cnpj);
            $mdfe_service = new MDFeService([
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => (int)$empresa->ambiente,
                "razaosocial" => $empresa->nome,
                "siglaUF" => $empresa->cidade->uf,
                "cnpj" => $empresa->cpf_cnpj,
                "schemes" => "PL_MDFe_300a",
                "versao" => "3.00",
            ], $empresa);
            $result = $mdfe_service->cancelar($item->chave, $item->protocolo, $request->motivo);

            if ($result->infEvento->cStat == '101' || $result->infEvento->cStat == '135' || $result->infEvento->cStat == '155') {
                $item->estado_emissao = 'cancelado';
                $item->save();
                return response()->json($result, 200);
            } else {

                return response()->json($result, 401);
            }
        } else {
            return response()->json("Erro a MDF-e precisa estar atutorizada para cancelar", 404);
        }
    }


}
