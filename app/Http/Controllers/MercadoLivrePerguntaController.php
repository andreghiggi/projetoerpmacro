<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MercadoLivrePergunta;
use App\Utils\MercadoLivreUtil;
use App\Models\MercadoLivreConfig;

class MercadoLivrePerguntaController extends Controller
{

    protected $util;
    public function __construct(MercadoLivreUtil $util)
    {
        $this->util = $util;
    }

    private function __validaToken(){
        $retorno = $this->util->refreshToken(request()->empresa_id);
        if($retorno != 'token valido!'){
            if($retorno == 0){
                return 0;
            }
            if(!isset($retorno->access_token)){
                dd($retorno);
            }
        }

    }

    public function index(Request $request){
        $this->getQuestions();

        $status = $request->status;
        $data = MercadoLivrePergunta::where('empresa_id', $request->empresa_id)
        ->orderBy('id', 'desc')
        ->when($status, function ($q) use ($status) {
            return $q->where('status', $status);
        })
        ->when(!$status, function ($q) {
            return $q->where('status', 'UNANSWERED');
        })
        ->paginate(50);

        return view('mercado_livre_perguntas.index', compact('data'));
    }

    private function getQuestions()
    {
        $token = $this->__validaToken();
        if($token == 0){
            return redirect()->route('mercado-livre-config.index');
        }
        $config = MercadoLivreConfig::where('empresa_id', request()->empresa_id)
        ->first();

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/questions/search?seller_id=$config->user_id&api_version=4");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $config->access_token,
            'Content-Type: application/json'
        ]);

        $res = curl_exec($curl);
        $retorno = json_decode($res);
        if($retorno->questions){
            foreach($retorno->questions as $item){
                $pergunta = MercadoLivrePergunta::where('_id', $item->id)
                ->first();
                if($pergunta == null){
                    MercadoLivrePergunta::create([
                        'empresa_id' => request()->empresa_id,
                        '_id' => $item->id,
                        'item_id' => $item->item_id,
                        'status' => $item->status,
                        'texto' => $item->text,
                        'data' => substr($item->date_created, 0, 20)
                    ]);
                }
            }
        }
    }

    public function show($id){
        $item = MercadoLivrePergunta::findOrFail($id);

        return view('mercado_livre_perguntas.show', compact('item'));
    }

    public function update(Request $request, $id){
        $this->__validaToken();

        $item = MercadoLivrePergunta::findOrFail($id);
        $config = MercadoLivreConfig::where('empresa_id', $request->empresa_id)
        ->first();
        $curl = curl_init();

        $dataMercadoLivre = [
            'question_id' => $item->_id,
            'text' => $request->resposta
        ];
        // dd($dataMercadoLivre);

        curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/answers");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataMercadoLivre));

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $config->access_token,
            'Content-Type: application/json'
        ]);

        $res = curl_exec($curl);
        $retorno = json_decode($res);
        if($retorno->status == 'ANSWERED'){
            $item->status = $retorno->status;
            $item->resposta = $request->resposta;
            $item->save();
            session()->flash("flash_success", 'Pergunta respondida!');
        }else{
            session()->flash("flash_error", $retorno->message);
        }
        return redirect()->route('mercado-livre-perguntas.index');

    }

    public function destroy($id){

        $item = MercadoLivrePergunta::findOrFail($id);

        $this->__validaToken();

        $config = MercadoLivreConfig::where('empresa_id', $item->empresa_id)
        ->first();

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, "https://api.mercadolibre.com/questions/$item->_id");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $config->access_token,
            'Content-Type: application/json'
        ]);
        $item->delete();

        $res = curl_exec($curl);
        $retorno = json_decode($res);
        if(isset($retorno->message)){
            session()->flash("flash_error", $retorno->message);
        }else{
            session()->flash("flash_success", 'Pergunta removida!');
        }
        sleep(3);
        return redirect()->back();
    }
}
