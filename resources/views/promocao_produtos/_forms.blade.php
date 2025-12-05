<div class="row g-2">
    <div class="col-md-4">
        {!!Form::select('produto_id', 'Produto')
        ->options(isset($item) ? [$item->produto_id => $item->produto->nome] : [])
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('valor_original', 'Valor original')
        ->attrs(['class' => 'moeda'])->required()->readonly()
        ->value(isset($item) ? __moeda($item->valor_original) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('valor', 'Valor promoção')
        ->attrs(['class' => 'moeda'])->required()
        ->value(isset($item) ? __moeda($item->valor) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Status', ['1' => 'Ativo', '0' => 'Pendente'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::date('data_inicio', 'Data de Início')->required()
        !!}
    </div>

     <div class="col-md-2">
        {!!Form::date('data_fim', 'Data de Fim')->required()
        !!}
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script type="text/javascript">
    $(document).on("change", "#inp-produto_id", function () {
        $.get(path_url + "api/produtos/findId/"+$(this).val())
        .done((e) => {
            // console.log(e)
            $('#inp-valor_original').val(convertFloatToMoeda(e.valor_unitario))
            $('#inp-valor').val(convertFloatToMoeda(e.valor_unitario))
        })
        .fail((err) => {
            console.log(err)
        })
    })
</script>
@endsection
