<div class="row g-2">
    <div class="col-md-12 table-responsible">
        <button type="button" class="btn btn-dark btn-add px-2 mb-2">
            <i class="ri-add-fill"></i>
            Adicionar Produto
        </button>
        <table class="table">
            <thead class="table-dark">
                <tr>
                    <th>
                        <div class="form-check form-checkbox-danger mb-2">
                            <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                        </div>
                    </th>
                    <th>Produto</th>
                    <th>Cliente</th>
                    <th>Quantidade</th>
                    <th>Observação do item</th>
                    <th>Nº Pedido</th>
                </tr>
            </thead>
            <tbody class="tbody-produtos">
                @foreach($data as $i)
                @if($i->itemNfe)
                <tr>
                    <td>
                        <div class="form-check form-checkbox-danger mb-2">
                            <input class="form-check-input check-button" type="checkbox" name="item_select[]" value="{{ $i->id }}">
                        </div>
                    </td>
                    <td>{{ $i->produto->nome }} {{ $i->dimensao }}</td>
                    <td>{{ $i->itemNfe->nfe ? $i->itemNfe->nfe->cliente->info : '--' }}</td>
                    <td>
                        <input type="tel" readonly class="form-control" name="qtd[]" value="{{ number_format($i->quantidade, 0) }}">
                    </td>
                    <td>
                        <input readonly type="text" class="form-control" name="observacao_item[]" value="{{ $i->observacao }}">
                    </td>
                    <td>{{ $i->itemNfe->nfe ? $i->itemNfe->nfe->numero_sequencial : '--' }}</td>

                </tr>
                @endif
                @endforeach
                @isset($item)
                @foreach($item->itens as $i)
                <tr>
                    @if($i->itemProducao)
                    <td>
                        <div class="form-check form-checkbox-danger mb-2">
                            <input checked class="form-check-input check-button" type="checkbox" name="item_select[]" value="{{ $i->itemProducao->id }}">
                        </div>
                    </td>
                    <td>{{ $i->produto->nome }} {{ $i->itemProducao->dimensao }}</td>
                    <td>{{ $i->itemProducao->itemNfe->nfe->cliente->info }}</td>

                    <td>
                        <input type="tel" readonly class="form-control" name="qtd[]" value="{{ number_format($i->quantidade, 0) }}">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="observacao_item[]" value="{{ $i->observacao }}">
                    </td>
                    <td>{{ $i->itemProducao->itemNfe->nfe->numero_sequencial }}</td>
                    @else

                    <td>
                        <div class="form-check form-checkbox-danger mb-2">
                            <input checked class="form-check-input check-button" type="checkbox" name="item_select[]">
                        </div>
                    </td>

                    <td>
                        <select name="produto_id[]" required class="produto_id">
                            <option value="{{ $i->produto_id }}">{{ $i->produto->nome }}</option>
                        </select>
                    </td>
                    <td>
                        <select name="cliente_id[]" required class="cliente_id">
                            <option value="{{ $i->cliente_id }}">{{ $i->cliente->info }}</option>
                        </select>
                    </td>
                    <td>
                        <input type="tel" required class="form-control qtd" name="qtd[]" value="{{ number_format($i->quantidade, 0) }}">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="observacao_item[]" value="{{ $i->observacao }}">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="numero_pedido[]" value="{{ $i->numero_pedido }}">
                    </td>

                    @endif

                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <div class="col-md-2">
        {!!Form::date('data_prevista_entrega', 'Data prevista de entrega')
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::select('funcionario_id', 'Funcionário')
        ->options(isset($item) && $item->funcionario_id ? [$item->funcionario_id => $item->funcionario->nome] : [])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('estado', 'Estado', App\Models\OrdemProducao::estados())
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-12">
        {!!Form::textarea('observacao', 'Observação')
        ->attrs(['rows' => 5])
        !!}
    </div>

    @isset($item)
    <input type="hidden" id="_edit" value="1">
    @endisset

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script type="text/javascript">
    $("#select-all-checkbox").on("click", function (e) {
        if($(this).is(':checked')){
            $('.check-button').prop('checked', 1)
        }else{
            $('.check-button').prop('checked', 0)
        }

        // validaButtonDelete()
    });
</script>
<script type="text/javascript" src="/js/ordemProducao.js"></script>
@endsection
