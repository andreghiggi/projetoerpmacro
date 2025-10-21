@extends('layouts.app', ['title' => 'Estoque'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @can('estoque_create')
                    <div class="col-md-2">
                        <a href="{{ route('estoque.create') }}" class="btn btn-success">
                            <i class="ri-add-circle-fill"></i>
                            Adicionar estoque
                        </a>
                    </div>
                    <div class="col-md-10"  style="text-align: right;">
                        <a href="{{ route('apontamento.create') }}" class="btn btn-info">
                            <i class="ri-settings-3-line"></i>
                            Apontamento Produção
                        </a>
                    </div>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::text('produto', 'Pesquisar por produto')
                            !!}
                        </div>

                        @if(__countLocalAtivo() > 1)
                        <div class="col-md-2">
                            {!!Form::select('local_id', 'Local', ['' => 'Selecione'] + __getLocaisAtivoUsuario()->pluck('descricao', 'id')->all())
                            ->attrs(['class' => 'select2'])
                            !!}
                        </div>
                        @endif
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('estoque.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Valor de venda</th>
                                    <th>Unidade</th>
                                    @if(__countLocalAtivo() > 1)
                                    <th>Local</th>
                                    @endif
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td><img class="img-60" src="{{ $item->produto->img }}"></td>
                                    <td>{{ $item->produto->numero_sequencial }}</td>
                                    <td>
                                        {{ $item->descricao() }}
                                    </td>
                                    <td>
                                        @if(!$item->produto->unidadeDecimal())
                                        {{ number_format($item->quantidade, 0) }}
                                        @else
                                        {{ number_format($item->quantidade, 3, '.', '') }}
                                        @endif
                                        <!-- @if(__countLocalAtivo() == 1)

                                        @if(!$item->produto->unidadeDecimal())
                                        {{ number_format($item->quantidade, 0) }}
                                        @else
                                        {{ number_format($item->quantidade, 3, '.', '') }}
                                        @endif

                                        @else

                                        @foreach($item->produto->estoqueLocais as $e)
                                        @if($e->local)
                                        {{ $e->local->descricao }}:
                                        <strong class="text-success">
                                            @if(!$item->produto->unidadeDecimal())
                                            {{ number_format($e->quantidade, 0) }}
                                            @else
                                            {{ number_format($e->quantidade, 3) }}
                                            @endif
                                        </strong>
                                        @endif
                                        @if(!$loop->last) | @endif
                                        @endforeach

                                        @endif -->
                                    </td>
                                    <td>{{ __moeda($item->produto->valor_unitario) }}</td>
                                    <td>{{ $item->produto->unidade }}</td>
                                    @if(__countLocalAtivo() > 1)
                                    <th>{{ $item->local->descricao }}</th>
                                    @endif
                                    <td style="width: 300px">
                                        <form action="{{ route('estoque.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf
                                            @can('estoque_edit')
                                            <a title="Editar estoque" href="{{ route('estoque.edit', [$item->id]) }}" class="btn btn-dark btn-sm">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @can('produtos_edit')
                                            <a title="Editar produto" href="{{ route('produtos.edit', [$item->produto_id]) }}" class="btn btn-warning btn-sm">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan

                                            @can('estoque_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan

                                        </form>

                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                {!! $data->appends(request()->all())->links() !!}

            </div>
        </div>
    </div>
</div>
@endsection
