@extends('layouts.app', ['title' => 'Produtos'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">

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
                            {!!Form::tel('codigo_barras', 'Pesquisar por Código de barras')
                            !!}
                        </div>
                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('contador-empresa.produtos') }}"><i class="ri-eraser-fill"></i>Limpar</a>
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
                                    <th>Código de barras</th>
                                    <th>NCM</th>
                                    <th>Unidade</th>
                                    <th>CFOP</th>
                                    <th>Gerenciar estoque</th>
                                    <th>Estoque</th>
                                    <th>Status</th>
                                    <th>Cardápio</th>
                                    <th>Valor de venda</th>
                                    <th>Valor de compra</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td><img class="img-60" src="{{ $item->img }}"></td>
                                    <td width="300">{{ $item->nome }}</td>
                                    <td width="200">{{ $item->codigo_barras ?? '--' }}</td>
                                    <td>{{ $item->ncm }}</td>
                                    <td>{{ $item->unidade }}</td>
                                    <td>{{ $item->cfop_estadual }}/{{ $item->cfop_outro_estado }}</td>
                                    <td>
                                        @if($item->gerenciar_estoque)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>{{ $item->estoqueAtual() }}</td>

                                    <td>
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->cardapio)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>{{ __moeda($item->valor_unitario) }}</td>
                                    <td>{{ __moeda($item->valor_compra) }}</td>
                                    <td style="width: 120px;">
                                        <a class="btn btn-warning btn-sm" href="{{ route('contador-empresa-produtos.edit', [$item->id]) }}">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <a class="btn btn-dark btn-sm" href="{{ route('contador-empresa-produtos.show', [$item->id]) }}">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="13" class="text-center">Nada encontrado</td>
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

@include('modals._info_vencimento', ['not_submit' => true])

@endsection

@section('js')
<script type="text/javascript">
    function infoVencimento(id) {
        $.get(path_url + 'api/produtos/info-vencimento/' + id)
        .done((res) => {
            $('.table-infoValidade tbody').html(res)
        })
        .fail((e) => {
            console.log(e)
        })
    }

</script>
@endsection
