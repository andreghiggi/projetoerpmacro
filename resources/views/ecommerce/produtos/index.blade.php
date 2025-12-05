@extends('layouts.app', ['title' => 'Produtos de Ecommerce'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    <a href="{{ route('produtos.create', ['ecommerce=1']) }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo Produto Ecommerce
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
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('produtos-ecommerce.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive-sm">
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
                                    <td><img class="img-60" src="{{ $item->img }}"></td>
                                    <td width="300">{{ $item->nome }}</td>
                                    <td>{{ $item->unidade }}</td>
                                    <td>{{ $item->categoria ? $item->categoria->nome : '--' }}</td>
                                    <td>
                                        @if($item->gerenciar_estoque)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    @if(sizeof($item->variacoes) > 0)
                                    <td>
                                        <div class="div-overflow">
                                            {{ $item->valoresVariacao() }}
                                        </div>
                                    </td>
                                    @else
                                    <td>

                                        {{ __moeda($item->valor_ecommerce) }}
                                    </td>
                                    @endif
                                    <td>
                                        <form action="{{ route('produtos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 300px;">
                                            @method('delete')
                                            <a class="btn btn-warning btn-sm" href="{{ route('produtos.edit', [$item->id, 'ecommerce=1']) }}" title="Editar">
                                                <i class="ri-edit-line"></i>
                                            </a>

                                            @csrf
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>

                                            <a title="Galeria" class="btn btn-dark btn-sm" href="{{ route('produtos.galeria', [$item->id, 'ecommerce=1']) }}">
                                                <i class="ri-image-2-fill"></i>
                                            </a>

                                            <a class="btn btn-primary btn-sm" href="{{ route('produtos.duplicar', [$item->id, 'ecommerce=1']) }}" title="Duplicar produto">
                                                <i class="ri-file-copy-line"></i>
                                            </a>
                                        </form>

                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Nada encontrado</td>
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
