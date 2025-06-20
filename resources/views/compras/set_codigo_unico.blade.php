@extends('layouts.app', ['title' => 'Código Único dos Produtos'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h3>Inserir Código de Identificação Única de Produtos Compra</h3>
                {!!Form::open()
                ->post()
                ->route('compras.setar-codigo-unico')
                !!}

                <input type="hidden" value="{{ $compra->id }}" name="nfe_id">
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Produto</th>
                                    <th style="width: 200px">Código</th>
                                    <th>Observação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produtos as $item)
                                <tr>
                                    <td>
                                        <input type="hidden" name="produto_id[]" value="{{ $item->produto->id }}">
                                        <input class="form-control" readonly type="text" name="produto_nome[]" value="{{ $item->produto->nome }}">
                                    </td>
                                    <td>
                                        <input type="text" name="codigo[]" required class="form-control">
                                    </td>
                                    <td>
                                        <input type="text" name="observacao[]" class="form-control">
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
@section('js')
<script type="text/javascript">
    $('form input').on('keypress', function(e) {
        return e.which !== 13;
    });
</script>
@endsection

    