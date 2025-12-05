<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VariacaoModelo;
use App\Models\VendiZapConfig;
use App\Models\VariacaoModeloItem;
use Illuminate\Support\Facades\DB;

class VariacaoController extends Controller
{
    protected $url = "https://app.vendizap.com/api";
    public function __construct()
    {
        $this->middleware('permission:variacao_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:variacao_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:variacao_view', ['only' => ['show', 'index']]);
        $this->middleware('permission:variacao_delete', ['only' => ['destroy']]);
    }

    public function index(Request $request){

        $configVendizap = VendiZapConfig::where('empresa_id', $request->empresa_id)->first();
        if($configVendizap != null){
            $ch = curl_init();
            $headers = [
                "X-Auth-Id: " . $configVendizap->auth_id,
                "X-Auth-Secret: " . $configVendizap->auth_secret,
            ];

            curl_setopt($ch, CURLOPT_URL, $this->url . '/variacoes');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            $data = json_decode(curl_exec($ch));
            curl_close($ch);

            $this->cadastrarVariacoesVendiZap($data);
        }
        $data = VariacaoModelo::where('empresa_id', $request->empresa_id)
        ->get();

        return view('variacao_modelo.index', compact('data', 'configVendizap'));
    }

    private function cadastrarVariacoesVendiZap($data){
        if(is_array($data)){
            foreach($data as $variacao){
                $dataVariacao = [
                    'descricao' => $variacao->nome,
                    'status' => 1,
                    'empresa_id' => request()->empresa_id,
                    'vendizap_id' => $variacao->id
                ];

                $item = VariacaoModelo::updateOrCreate($dataVariacao);

                foreach($variacao->variaveis as $va){
                    $variacaoModeloItem = [
                        'variacao_modelo_id' => $item->id,
                        'nome' => $va->nome,
                        'vendizap_id' => $va->id
                    ];
                    VariacaoModeloItem::updateOrCreate($variacaoModeloItem);
                }
            }
        }
    }

    public function create(){
        return view('variacao_modelo.create');
    }

    public function edit($id){
        $item = VariacaoModelo::findOrFail($id);
        __validaObjetoEmpresa($item);
        return view('variacao_modelo.edit', compact('item'));
    }

    public function store(Request $request){
        try{
            DB::transaction(function () use ($request) {
                $item = VariacaoModelo::create($request->all());
                $variaveis = [];
                for($i=0; $i<sizeof($request->nome); $i++){
                    VariacaoModeloItem::create([
                        'variacao_modelo_id' => $item->id,
                        'nome' => $request->nome[$i]
                    ]);
                    $variaveis[] = [
                        'nome' => $request->nome[$i]
                    ];
                }
                $config = VendiZapConfig::where('empresa_id', $request->empresa_id)->first();
                if($config != null){

                    $ch = curl_init();
                    $headers = [
                        "X-Auth-Id: " . $config->auth_id,
                        "X-Auth-Secret: " . $config->auth_secret,
                        'Content-Type: application/json'
                    ];

                    $item = VariacaoModelo::findOrFail($item->id);
                    $data = [
                        'nome' => $item->descricao,
                        'variaveis' => $variaveis
                    ];

                    curl_setopt($ch, CURLOPT_URL, $this->url . '/variacoes');
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_ENCODING, '');
                    curl_setopt($ch, CURLOPT_POST, true);
                    $data = json_decode(curl_exec($ch));
                    curl_close($ch);

                    if($data){
                        $item->vendizap_id = $data->id;
                        $item->save();
                        foreach($data->variaveis as $va){
                            VariacaoModeloItem::where('variacao_modelo_id', $item->id)
                            ->where('nome', $va->nome)->update(['vendizap_id' => $va->id]);
                        }

                    }
                    // dd($data);
                }
                return 1;
            });

            __createLog($request->empresa_id, 'Variação  de Produto', 'cadastrar', $request->descricao);
            session()->flash("flash_success", "Cadastrado com Sucesso");
        }catch(\Exception $e){
            __createLog($request->empresa_id, 'Variação de Produto', 'erro', $e->getMessage());
            session()->flash("flash_error", "Não foi possivel fazer o cadastro" . $e->getMessage());
        }
        return redirect()->route('variacoes.index');
    }

    public function update(Request $request, $id){
        try{
            DB::transaction(function () use ($request, $id) {
                $item = VariacaoModelo::findOrFail($id);
                __validaObjetoEmpresa($item);
                $item->fill($request->all())->save();
                $item->itens()->delete();
                $variaveis = [];

                for($i=0; $i<sizeof($request->nome); $i++){
                    VariacaoModeloItem::create([
                        'variacao_modelo_id' => $item->id,
                        'nome' => $request->nome[$i]
                    ]);
                    $variaveis[] = [
                        'nome' => $request->nome[$i]
                    ];
                }

                $config = VendiZapConfig::where('empresa_id', $request->empresa_id)->first();
                if($config != null && $item->vendizap_id){

                    $ch = curl_init();
                    $headers = [
                        "X-Auth-Id: " . $config->auth_id,
                        "X-Auth-Secret: " . $config->auth_secret,
                        'Content-Type: application/json'
                    ];

                    $data = [
                        'nome' => $item->descricao,
                        'variaveis' => $variaveis
                    ];

                    curl_setopt($ch, CURLOPT_URL, $this->url . '/variacoes/'.$item->vendizap_id);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_ENCODING, '');
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    $data = json_decode(curl_exec($ch));
                    curl_close($ch);

                    if(isset($data->id)){
                        $item->vendizap_id = $data->id;
                        $item->save();
                        foreach($data->variaveis as $va){
                            VariacaoModeloItem::where('variacao_modelo_id', $item->id)
                            ->where('nome', $va->nome)->update(['vendizap_id' => $va->id]);
                        }

                    }else{

                        $ch = curl_init();
                        $headers = [
                            "X-Auth-Id: " . $config->auth_id,
                            "X-Auth-Secret: " . $config->auth_secret,
                            'Content-Type: application/json'
                        ];

                        $data = [
                            'nome' => $item->descricao,
                            'variaveis' => $variaveis
                        ];
                        curl_setopt($ch, CURLOPT_URL, $this->url . '/variacoes');
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HEADER, false);
                        curl_setopt($ch, CURLOPT_ENCODING, '');
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                        $data = json_decode(curl_exec($ch));
                        curl_close($ch);
                        // dd($data);

                        if(isset($data->id)){

                            $item->vendizap_id = $data->id;
                            $item->save();
                            foreach($data->variaveis as $va){
                                VariacaoModeloItem::where('variacao_modelo_id', $item->id)
                                ->where('nome', $va->nome)->update(['vendizap_id' => $va->id]);
                            }

                        }
                    }
                    // dd($data);
                }

                return 1;
            });
            __createLog($request->empresa_id, 'Variação  de Produto', 'editar', $request->descricao);
            session()->flash("flash_success", "Atualizado com Sucesso");
        }catch(\Exception $e){
            dd($e->getLine());
            __createLog($request->empresa_id, 'Variação de Produto', 'erro', $e->getMessage());
            session()->flash("flash_error", "Não foi possivel fazer o cadastro" . $e->getMessage());
        }
        return redirect()->route('variacoes.index');
    }

    public function destroy(string $id)
    {
        $item = VariacaoModelo::findOrFail($id);
        __validaObjetoEmpresa($item);
        try {
            $descricaoLog = $item->descricao;
            $item->itens()->delete();
            $item->delete();
            __createLog(request()->empresa_id, 'Variação de Produto', 'excluir', $descricaoLog);
            session()->flash("flash_success", "Removido com sucesso!");
        } catch (\Exception $e) {
            __createLog(request()->empresa_id, 'Variação de Produto', 'erro', $e->getMessage());
            session()->flash("flash_error", 'Algo deu errado: ' . $e->getMessage());
        }
        return redirect()->route('variacoes.index');
    }

    public function destroySelecet(Request $request)
    {
        $removidos = 0;
        for($i=0; $i<sizeof($request->item_delete); $i++){
            $item = VariacaoModelo::findOrFail($request->item_delete[$i]);
            try {
                $descricaoLog = $item->nome;
                $item->itens()->delete();
                $item->delete();
                $removidos++;
                __createLog(request()->empresa_id, 'Variação de Produto', 'excluir', $descricaoLog);
            } catch (\Exception $e) {
                __createLog(request()->empresa_id, 'Variação de Produto', 'erro', $e->getMessage());
                session()->flash("flash_error", 'Algo deu errado: '. $e->getMessage());
                return redirect()->route('variacoes.index');
            }
        }

        session()->flash("flash_success", "Total de itens removidos: $removidos!");
        return redirect()->route('variacoes.index');
    }
}
