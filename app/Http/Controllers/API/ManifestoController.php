<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\ManifestoDfe;
use App\Services\DFeService;
use Illuminate\Http\Request;

class ManifestoController extends Controller
{
    public function novosDocumentos(Request $request)
    {
        $config = Empresa::where('id', $request->empresa_id)
        ->first();

        $cnpj = preg_replace('/[^0-9]/', '', $config->cpf_cnpj);

        $dfe_service = new DFeService([
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => 1,
            "razaosocial" => $config->nome,
            "siglaUF" => $config->cidade->uf,
            "cnpj" => $cnpj,
            "schemes" => "PL_009_V4",
            "versao" => "4.00",
            "tokenIBPT" => "AAAAAAA",
            "CSC" => $config->csc,
            "CSCid" => $config->csc_id
        ], $config);

        $manifesto = ManifestoDfe::where('empresa_id', $request->empresa_id)
        ->orderBy('nsu', 'desc')->first();

        if ($manifesto == null) $nsu = 0;
        else $nsu = $manifesto->nsu;
        $docs = $dfe_service->novaConsulta($nsu);
        $novos = [];

        if (!isset($docs['erro'])) {

            $novos = [];
            foreach ($docs as $d) {
                if ($this->validaNaoInserido($d['chave'])) {
                    if ($d['valor'] > 0 && $d['nome']) {
                        ManifestoDfe::create($d);
                        array_push($novos, $d);
                    }
                }
            }

            return response()->json($novos, 200);
        } else {
            return response()->json($docs, 401);
        }
    }

    private function validaNaoInserido($chave)
    {
        $m = ManifestoDfe::where('empresa_id', request()->empresa_id)
        ->where('chave', $chave)->first();
        if ($m == null) return true;
        else return false;
    }
}
