@extends('layouts.app', ['title' => 'Informação Validade e Lote'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h3>Inserir Lote e Vencimento nos Produtos da Compra: {{$compra->id}}</h3>
                {!!Form::open()
                ->post()
                ->route('compras.setar-info-validade')
                !!}
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Produto</th>
                                    <th>Lote</th>
                                    <th>Vencimento</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produtos as $item)
                                <tr>
                                    <td>
                                        <input type="hidden" name="produto_id[]" value="{{ $item->id }}">
                                        <input class="form-control" readonly type="text" name="produto_nome[]" value="{{ $item->produto->nome }}">
                                    </td>
                                    <td class="col-3">
                                        <input type="text" name="lote[]" required class="form-control" data-mask="AAAAAAAAAAAAAAAAAAAAAAAA" value="{{ isset($item) ? $item->lote : []}}">
                                    </td>
                                    <td class="col-2">
                                        <input type="date" name="data_vencimento[]" required class="form-control" value="{{ isset($item) ? $item->data_vencimento : []}}">
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12" style="text-align: right;">
                        <button type="submit" class="btn btn-success btn-salvar-nfe px-5 m-3">Salvar</button>
                    </div>
                </div>
                {!!Form::close()!!}

            </div>
        </div>
    </div>
</div>


@endsection
