@extends('layouts.app', ['title' => 'Composição do Produto'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Composição do Produto:<strong style="color: royalblue"> {{$item->nome}}</strong></h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('produtos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('produto-composto.store', [$item->id])
        !!}
        <div class="pl-lg-4">
            <div class="row">
                <input type="hidden" name="produto_id" id="" value="{{$item->id}}">
                <div class="col-md-4">
                    {!!Form::select('ingrediente_id', 'Selecionar Produto')
                    ->attrs(['class' => 'select2'])->required()
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::text('quantidade', 'Quantidade')->attrs(['class' => 'qtd'])->required()
                    !!}
                </div>
                <div class="row col-12 col-lg-2 mt-3">
                    <br>
                    <button type="submit" class="btn btn-info px-3">
                        <i class="ri-add-line"></i> Adicionar
                    </button>
                </div>
            </div>
        </div>
        {!!Form::close()!!}
        <div class="table-responsive-sm mt-3">
            <table class="table table-striped table-centered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Produto insumo/ingrediente</th>
                        <th>Quantidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                    <tr>
                        <td>{{ $item->ingrediente->nome }}</td>
                        <td>{{ $item->quantidade }}</td>
                    <td>
                        <form action="{{ route('produto-composto.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                            @csrf
                            @method('delete')
                            <button type="submit" title="Deletar" class="btn btn-danger btn-sm btn-delete"><i class="ri-delete-bin-2-line"></i></button>
                        </form>
                    </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Nada encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="col-12 mt-4" style="text-align: right;">
            <a href="{{ route('produtos.index') }}" class="btn btn-success px-5" id="btn-store">Finalizar Produto</a>
        </div>
    </div>
</div>
@endsection
