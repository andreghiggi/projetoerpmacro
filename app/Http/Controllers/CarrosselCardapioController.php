<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarrosselCardapio;
use App\Models\Produto;
use App\Utils\UploadUtil;

class CarrosselCardapioController extends Controller
{
    protected $util;

    public function __construct(UploadUtil $util)
    {
        $this->util = $util;
    }

    public function index(Request $request){
        $data = CarrosselCardapio::where('empresa_id', $request->empresa_id)
        ->when(!empty($request->produto_id), function ($q) use ($request) {
            return $q->where('produto_id', $request->produto_id);
        })
        ->paginate(__itensPagina());

        return view('carrossel.index', compact('data'));
    }

    public function create()
    {
        $produtos = Produto::where('empresa_id', request()->empresa_id)
        ->where('cardapio', 1)->orderBy('nome', 'asc')->get();
        return view('carrossel.create', compact('produtos'));
    }

    public function edit($id)
    {
        $produtos = Produto::where('empresa_id', request()->empresa_id)
        ->where('cardapio', 1)->orderBy('nome', 'asc')->get();
        $item = CarrosselCardapio::findOrFail($id);
        return view('carrossel.edit', compact('item', 'produtos'));
    }

    public function store(Request $request)
    {
        $this->__validate($request);
        try {

            $file_name = '';
            if ($request->hasFile('image')) {
                $file_name = $this->util->uploadImage($request, '/carrossel');
            }

            $request->merge([
                'valor' => $request->valor ? __convert_value_bd($request->valor) : 0,
                'imagem' => $file_name
            ]);

            CarrosselCardapio::create($request->all());
            session()->flash("flash_success", "Carrossel criado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }
        return redirect()->route('carrossel.index');
    }

    public function update(Request $request, $id)
    {
        $item = CarrosselCardapio::findOrFail($id);
        try {
            $file_name = $item->imagem;

            if ($request->hasFile('image')) {
                $this->util->unlinkImage($item, '/carrossel');
                $file_name = $this->util->uploadImage($request, '/carrossel');
            }

            $request->merge([
                'valor' => $request->valor ? __convert_value_bd($request->valor) : 0,
                'imagem' => $file_name
            ]);
            $item->fill($request->all())->save();
            session()->flash("flash_success", "Carrossel alterado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }
        return redirect()->route('carrossel.index');
    }

    public function destroy($id)
    {
        $item = CarrosselCardapio::findOrFail($id);
        try {
            $this->util->unlinkImage($item, '/carrossel');
            $item->delete();
            session()->flash("flash_success", "Apagado com sucesso!");
        } catch (\Exception $e) {
            session()->flash("flash_error", 'Algo deu errado ' . $e->getMessage());
        }
        return redirect()->route('carrossel.index');
    }

    private function __validate(Request $request)
    {
        $rules = [
            'image' => 'required',
        ];

        $messages = [
            'image.required' => 'Imagem é obrigatória',
        ];
        $this->validate($request, $rules, $messages);
    }
}
