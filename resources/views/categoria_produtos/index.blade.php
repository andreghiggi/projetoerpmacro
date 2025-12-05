@extends('layouts.app', ['title' => 'Categorias de Produto'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('categoria_produtos_create')
                    <a href="{{ route('categoria-produtos.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Categoria
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3 col-6">
                            {!!Form::text('nome', 'Pesquisar por nome')
                            !!}
                        </div>
                        <div class="col-md-3 text-left col-6">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('categoria-produtos.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    @can('categoria_produtos_delete')
                                    <th>
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                                        </div>
                                    </th>
                                    @endcan
                                    <th>Nome</th>
                                    <th>Status</th>
                                    @if(__isActivePlan(Auth::user()->empresa, 'Cardapio'))
                                    <th>Cardápio</th>
                                    @endif
                                    @if(__isActivePlan(Auth::user()->empresa, 'Delivery'))
                                    <th>Delivery</th>
                                    <th>Tipo pizza</th>
                                    @endif
                                    @if(__isActivePlan(Auth::user()->empresa, 'Ecommerce'))
                                    <th>Ecommerce</th>
                                    @endif
                                    @if(__isActivePlan(Auth::user()->empresa, 'Reservas'))
                                    <th>Reserva</th>
                                    @endif
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    @can('categoria_produtos_delete')
                                    <td data-label="Selecionar">
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input check-delete" type="checkbox" name="item_delete[]" value="{{ $item->id }}">
                                        </div>
                                    </td>
                                    @endcan

                                    <td data-label="Nome">{{ $item->nome }}</td>

                                    <td data-label="Status">
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>

                                    @if(__isActivePlan(Auth::user()->empresa, 'Cardapio'))
                                    <td data-label="Cardápio">
                                        @if($item->cardapio)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    @endif

                                    @if(__isActivePlan(Auth::user()->empresa, 'Delivery'))
                                    <td data-label="Delivery">
                                        @if($item->delivery)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td data-label="Tipo pizza">
                                        @if($item->tipo_pizza)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    @endif

                                    @if(__isActivePlan(Auth::user()->empresa, 'Ecommerce'))
                                    <td data-label="Ecommerce">
                                        @if($item->ecommerce)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    @endif

                                    @if(__isActivePlan(Auth::user()->empresa, 'Reservas'))
                                    <td data-label="Reserva">
                                        @if($item->reserva)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    @endif

                                    <td>
                                        <form action="{{ route('categoria-produtos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 150px">
                                            @method('delete')
                                            @can('categoria_produtos_edit')
                                            <a class="btn btn-warning btn-sm text-white" href="{{ route('categoria-produtos.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('categoria_produtos_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>

                                {{-- Subcategorias --}}
                                @if($item->subCategorias->count() > 0)
                                @foreach($item->subCategorias as $sub)
                                <tr>
                                    <td data-label=""></td>
                                    <td data-label="Subcategoria">{{ $sub->nome }}</td>
                                    <td data-label="Status">
                                        @if($sub->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td colspan="7">
                                        <form action="{{ route('categoria-produtos.destroy', $sub->id) }}" method="post" id="form-{{$sub->id}}" style="width: 150px">
                                            @method('delete')
                                            @can('categoria_produtos_edit')
                                            <a class="btn btn-warning btn-sm text-white" href="{{ route('categoria-produtos.edit', [$sub->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('categoria_produtos_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                                @endif

                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <br>
                        @can('categoria_produtos_delete')
                        <form action="{{ route('categoria-produtos.destroy-select') }}" method="post" id="form-delete-select">
                            @method('delete')
                            @csrf
                            <div></div>
                            <button type="button" class="btn btn-danger btn-sm btn-delete-all" disabled>
                                <i class="ri-close-circle-line"></i> Remover selecionados
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
                <br>
                {!! $data->appends(request()->all())->links() !!}

            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script type="text/javascript" src="/js/delete_selecionados.js"></script>
@endsection