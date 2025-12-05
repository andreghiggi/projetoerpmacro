@extends('layouts.app', ['title' => 'Produtos com validade'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">

                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('produto_id', 'Pesquisar por produto')
                            ->options($produto != null ? [$produto->id => $produto->nome] : [])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('tipo_pesquisa', 'Tipo de pesquisa', ['created_at' => 'Data de cadastro', 'data_vencimento' => 'Data de vencimento'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data inicial')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data final')
                            !!}
                        </div>
                        
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('compras.validade') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Produto</th>
                                    <th>Lote</th>
                                    <th>Quantidade</th>
                                    <th>Data de validade</th>
                                    <th>Data de cadastro</th>
                                    <th>Status</th>

                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Nome">{{ $item->produto->nome }}</td>
                                    <td data-label="Lote">{{ $item->lote }}</td>
                                    <td data-label="Quantidade">
                                        @if(!$item->produto->unidadeDecimal())
                                        {{ number_format($item->quantidade, 0, '.', '') }}
                                        @else
                                        {{ number_format($item->quantidade, 3, '.', '') }}
                                        @endif
                                    </td>
                                    <td data-label="Data de vencimento">{{ __data_pt($item->data_vencimento, 0) }}</td>
                                    <td data-label="Data de cadastro">{{ __data_pt($item->created_at) }}</td>
                                    <td data-label="Status">
                                        {!! $item->__statusVencimento() !!}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <br>
                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection


