@extends('layouts.app', ['title' => 'Produtos de Cardápio'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    <a href="{{ route('produtos.create', ['cardapio=1']) }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo Produto Cardápio
                    </a>
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-4">
                            {!!Form::text('nome', 'Pesquisar por nome')
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('status', 'Status', ['' => 'Todos', '1' => 'Ativos', '0' => 'Desativados'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('produtos-cardapio.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th></th>
                                    <th>Nome</th>
                                    <th>Unidade</th>
                                    <th>Categoria</th>
                                    <th>Gerenciar estoque</th>
                                    <th>Status</th>
                                    <th>Valor</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label=""><img class="img-60" src="{{ $item->img }}"></td>
                                    <td data-label="Nome">{{ $item->nome }}</td>
                                    <td data-label="Unidade">{{ $item->unidade }}</td>
                                    <td data-label="Categoria">{{ $item->categoria ? $item->categoria->nome : '--' }}</td>
                                    <td data-label="Gerenciar estoque">
                                        @if($item->gerenciar_estoque)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td data-label="Status">
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td data-label="Valor">
                                        @if($item->categoria && $item->categoria->tipo_pizza)
                                        {!! $item->valoresPizza() !!}
                                        @else
                                        {{ __moeda($item->valor_cardapio) }}
                                        @endif
                                    </td>
                                    <td>
                                        <form style="width: 200px;" action="{{ route('produtos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            <a class="btn btn-warning btn-sm" href="{{ route('produtos.edit', [$item->id, 'cardapio=1']) }}" title="Editar">
                                                <i class="ri-edit-line"></i>
                                            </a>

                                            <a class="btn btn-info btn-sm" href="{{ route('produtos-cardapio.show', [$item->id]) }}" title="Adicionais">
                                                <i class="ri-play-list-add-line"></i>
                                            </a>

                                            <a class="btn btn-dark btn-sm" href="{{ route('produtos-cardapio.ingredientes', [$item->id]) }}" title="Ingredientes">
                                                <i class="ri-draft-line"></i>
                                            </a>

                                            @if($item->categoria && $item->categoria->tipo_pizza)
                                            <a class="btn btn-primary btn-sm" href="{{ route('produtos.tamanho-pizza', [$item->id]) }}" title="Tamanhos de pizza">
                                                <i class="ri-restaurant-fill"></i>
                                            </a>
                                            @endif

                                            @csrf
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
