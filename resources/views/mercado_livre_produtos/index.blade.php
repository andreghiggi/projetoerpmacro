@extends('layouts.app', ['title' => 'Produtos'])
@section('css')
<style type="text/css">
    .div-overflow {
        width: 180px;
        overflow-x: auto;
        white-space: nowrap;
    }

    tr.active{
        background: #a7ffeb;
    }
    tr.disabled{
    }
</style>
@endsection
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <a href="{{ route('produtos.create', ['mercadolivre=1']) }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo Produto
                    </a>

                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::text('nome', 'Pesquisar por nome')
                            !!}
                        </div>
                        
                        <div class="col-md-2">
                            {!!Form::tel('codigo_barras', 'Pesquisar por Código de barras')
                            !!}
                        </div>

                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('mercado-livre-produtos.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                                        </div>
                                    </th>
                                    <th>Ações</th>
                                    <th></th>
                                    <th>Nome</th>
                                    <th>Código</th>
                                    <th>Valor de venda</th>
                                    <th>Categoria</th>
                                    <th>Código de barras</th>
                                    <th>NCM</th>
                                    <th>Unidade</th>
                                    <th>Data de cadastro</th>
                                    <th>CFOP</th>
                                    <th>Gerenciar estoque</th>
                                    <th>Estoque</th>
                                    <th>Valor de compra</th>
                                    <th>Com variação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr class="{{ $item->statusMercadoLivre() }}">
                                    <td>
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input check-delete" type="checkbox" name="item_delete[]" value="{{ $item->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <form style="width: 250px" action="{{ route('produtos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            <a class="btn btn-warning btn-sm" href="{{ route('mercado-livre-produtos.edit', [$item->id, 'mercadolivre=1']) }}">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            @csrf
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>

                                            <a title="Galeria" href="{{ route('mercado-livre-produtos.galery', [$item->id]) }}" class="btn btn-dark btn-sm"><i class="ri-image-line"></i></a>

                                            <a class="btn btn-primary btn-sm" href="{{ route('produtos.duplicar', [$item->id]) }}" title="Duplicar produto">
                                                <i class="ri-file-copy-line"></i>
                                            </a>
                                        </form>
                                    </td>
                                    <td><img class="img-60" src="{{ $item->img }}"></td>
                                    <td width="300">{{ $item->nome }}</td>
                                    <td width="150">{{ $item->mercado_livre_id }}</td>

                                    @if(sizeof($item->variacoesMercadoLivre) > 0)
                                    <td width="400">
                                        @foreach($item->variacoesMercadoLivre as $v)
                                        {{ $v->valor_nome }} - {{ __moeda($v->valor) }}<br>
                                        @endforeach
                                    </td>
                                    @else
                                    <td>
                                        {{ __moeda($item->valor_unitario) }}
                                    </td>
                                    @endif


                                    <td width="150">{{ $item->categoria ? $item->categoria->nome : '--' }}</td>
                                    <td width="200">{{ $item->codigo_barras ?? '--' }}</td>
                                    <td>{{ $item->ncm }}</td>
                                    <td>{{ $item->unidade }}</td>
                                    <td>{{ __data_pt($item->created_at) }}</td>
                                    <td>{{ $item->cfop_estadual }}/{{ $item->cfop_outro_estado }}</td>
                                    <td>
                                        @if($item->gerenciar_estoque)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>{{ $item->estoqueAtual() }}</td>

                                    <td width="100">{{ __moeda($item->valor_compra) }}</td>
                                    <td width="100">
                                        @if(sizeof($item->variacoesMercadoLivre) > 0)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="18" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                <form action="{{ route('produtos.destroy-select') }}" method="post" id="form-delete-select">
                    @method('delete')
                    @csrf
                    <div></div>
                    <button type="button" class="btn btn-danger btn-sm btn-delete-all" disabled>
                        <i class="ri-close-circle-line"></i> Remover selecionados
                    </button>
                </form>
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
