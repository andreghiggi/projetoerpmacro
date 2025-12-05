<tr class="line-product">
    <input readonly type="hidden" name="key" class="form-control" value="{{ $product->key }}">
    <input class="produto_row" readonly type="hidden" name="produto_id[]" class="form-control" value="{{ $product->id }}">
    <td>
        <img src="{{ $product->img }}" style="width: 30px; height: 40px; border-radius: 10px;">
        <input class="variacao_id" type="hidden" name="variacao_id[]" class="form-control" value="{{ $variacao_id }}">
    </td>
    <td>
        <input style="width: 350px" readonly type="text" name="produto_nome[]" class="form-control" value="{{ $product->nome }}@if($variacao != null) - {{ $variacao->descricao }} @endif">

        @if($product->precoComPromocao())
        <p>Promoção: <strong class="text-primary">{{ __data_pt($product->precoComPromocao()->data_inicio, 0) }}</strong> até <strong class="text-primary">{{ __data_pt($product->precoComPromocao()->data_fim, 0) }}</strong></p>
        @endif

    </td>
    <td class="datatable-cell">
        <div class="form-group mb-2" style="width: 200px">
            <div class="input-group">
                <div class="input-group-prepend">
                    <button id="btn-subtrai" class="btn btn-danger btn-qtd" type="button">-</button>
                </div>
                <input type="tel" readonly class="form-control qtd_row qtd" name="quantidade[]" value="{{ $qtd }}">
                <div class="input-group-append">
                    <button class="btn btn-success btn-qtd" id="btn-incrementa" type="button">+</button>
                </div>
            </div>
        </div>
    </td>
    <td>
        <input style="width: 100px" readonly type="tel" name="valor_unitario[]" class="form-control value-unit" value="{{ __moeda($value_unit) }}">
    </td>
    <td>
        <input style="width: 100px" readonly type="tel" name="subtotal_item[]" class="form-control subtotal-item" value="{{ __moeda($sub_total) }}">
    </td>
    <td>
        <input type="hidden" class="adicionais" name="adicionais[]">
        <button type="button" class="btn btn-danger btn-sm btn-delete-row"><i class="ri-delete-bin-line"></i></button>
    </td>
</tr>
