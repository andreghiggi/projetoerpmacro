@extends('layouts.app', ['title' => 'Apontamento de Produção'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Apontamento de Produção</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('estoque.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
        <hr>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('apontamento.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            <div class="row">
                <div class="col-md-4">
                    {!!Form::select('produto_composto_id', 'Produto')
                    ->attrs(['class' => 'form-select'])->required()
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::text('quantidade', 'Quantidade')
                    ->attrs(['class' => 'qtd'])->required()
                    !!}
                </div>
                <div class="col-1 mt-3" style="text-align: right;">
                    <button type="submit" class="btn btn-success px-5">Salvar</button>
                </div>
            </div>
        </div>
        {!!Form::close()!!}

        <div class="col-md-12 mt-3">
            <div class="table-responsive-sm">
                <table class="table table-centered">
                    <thead class="table-dark">
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Data Apontamento</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                        <tr>
                            <td>{{ $item->produto->nome }}</td>

                            <td>
                                @if(!$item->produto->unidadeDecimal())
                                {{ number_format($item->quantidade, 0, '.', '') }}
                                @else
                                {{ number_format($item->quantidade, 3, '.', '') }}
                                @endif
                            </td>

                            <td>{{ __data_pt($item->created_at, 0) }}</td>
                            <td>
                                <a target="_blank" class="btn btn-info btn-sm" href="{{ route('apontamento.imprimir', $item->id) }}"><i class="ri-printer-line"></i></a>
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
        </div>
    </div>
</div>
@endsection
