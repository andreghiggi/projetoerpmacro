@extends('layouts.app', ['title' => 'Conecta Vendas - Produtos'])

@section('css')
    <style type="text/css">
        .div-overflow {
            width: 180px;
            overflow-x: auto;
            white-space: nowrap;
        }

        tr.active {
            background: #a7ffeb;
        }

        tr.disabled {
        }
    </style>
@endsection

@section('content')
    <div class="mt-3">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <hr class="mt-3">
                    <div class="col-lg-12">
                        {!! Form::open()->fill(request()->all())->get() !!}
                        <div class="row mt-3">
                            <div class="col-md-3">
                                {!! Form::text('nome', 'Pesquisar por nome') !!}
                            </div>
                            <div class="col-md-2">
                                {!! Form::tel('codigo_barras', 'Pesquisar por Código de barras') !!}
                            </div>
                            <div class="col-md-3 text-left">
                                <br>
                                <button class="btn btn-primary" type="submit">
                                    <i class="ri-search-line"></i>Pesquisar
                                </button>
                                <a id="clear-filter" class="btn btn-danger" href="{{ route('conecta-venda-produtos.index') }}">
                                    <i class="ri-eraser-fill"></i>Limpar
                                </a>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>

                    <div class="col-md-12 mt-3 table-responsive">
                        <div class="table-responsive-sm">
                            <table class="table table-striped table-centered mb-0">
                                <thead class="table-dark">
                                <tr>
                                    <th>
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                                        </div>
                                    </th>
                                    <th>Ações</th>
                                    <th>Código</th>
                                    <th>Imagem</th>
                                    <th>Nome</th>
                                    <th>Valor de venda</th>
                                    <th>Código de barras</th>
                                    <th>Estoque</th>
                                    <th>Data de cadastro</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($data as $item)
                                    <tr>
                                        <td>
                                            <div class="form-check form-checkbox-danger mb-2">
                                                <input class="form-check-input check-delete" type="checkbox" name="item_delete[]" value="{{ $item['id'] }}">
                                            </div>
                                        </td>
                                        <td>
                                            <form style="width: 250px" action="{{ route('conecta-venda-produtos.destroy', $item['id']) }}" method="post" id="form-{{$item['id']}}">
                                                @method('delete')
                                                @csrf
                                                <a class="btn btn-warning btn-sm" href="{{ route('produtos.edit', $item['id']) }}">
                                                    <i class="ri-edit-line"></i>
                                                </a>
                                                <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                                <a class="btn btn-primary btn-sm" href="{{ route('produtos.show', $item['id']) }}" title="Ver detalhes">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                            </form>
                                        </td>
                                        <td>{{ $item['id'] }}</td>

                                        <td><img class="img-60" src="{{ $item['img'] ?? '/img/sem-imagem.png' }}"></td>
                                        <td  width="280">{{ $item['nome'] }}</td>
                                        @if(!$item['variacoes'])
                                            <td>{{'--'}}</tdwidth>
                                        @else
                                            <td>{{ __moeda($item['variacoes'][0]['valor']) }}</tdwidth>
                                        @endif
                                        <td>{{ $item['ean'] ?? '--' }}</td>
                                        @if (($item['variacoes']))
                                            <td>{{ __qtd($item['variacoes'][0]['estoque']['quantidade']) }}</td>
                                        @else
                                            <td> -- </td>
                                        @endif
                                        <td>{{ __data_pt($item['conecta_venda_data_publicacao']) }}</td>


{{--                                        <td>--}}
{{--                                            @if($item['gerenciar_estoque'])--}}
{{--                                                <i class="ri-checkbox-circle-fill text-success"></i>--}}
{{--                                            @else--}}
{{--                                                <i class="ri-close-circle-fill text-danger"></i>--}}
{{--                                            @endif--}}
{{--                                        </td>--}}
{{--                                        <td>{{ $item->estoqueAtual() }}</td>--}}
{{--                                        <td>{{ __moeda($item['valor_compra']) }}</td>--}}
{{--                                    </tr>--}}
                                @empty
                                    <tr>
                                        <td colspan="15" class="text-center">Nenhum produto encontrado</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <br>
{{--                    <form action="{{ route('conecta-venda-produtos.destroy-select') }}" method="post" id="form-delete-select">--}}
{{--                        @method('delete')--}}
{{--                        @csrf--}}
{{--                        <button type="button" class="btn btn-danger btn-sm btn-delete-all" disabled>--}}
{{--                            <i class="ri-close-circle-line"></i> Remover selecionados--}}
{{--                        </button>--}}
{{--                    </form>--}}
                    <br>
{{--                    {!! $data->appends(request()->all())->links() !!}--}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="/js/delete_selecionados.js"></script>
@endsection
