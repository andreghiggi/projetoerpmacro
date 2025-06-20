

<div class="row g-2">

    <div class="col-md-2">
        <label class="required">Mês</label>

        <select class="form-select" name="mes" required>
            @foreach(\App\Models\FinanceiroContador::meses() as $key => $m)
            <option value="{{$m}}" @if($key==$mesAtual) selected @endif>{{ ($m) }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label class="required">Ano</label>
        <select class="form-select" name="ano" required>
            @foreach(\App\Models\FinanceiroContador::anos() as $key => $a)
            <option @if(date('Y') == $a) selected @endif value="{{$a}}">{{ $a }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        {!!Form::tel('total_venda', 'Total de vendas')
        ->attrs(['class' => 'form-control moeda'])
        ->required()
        ->value(__moeda($data->total))
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('percentual_comissao', '% Comissão')
        ->attrs(['class' => 'form-control percentual'])
        ->required()
        ->value($contador->percentual_comissao)
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('valor_comissao', 'Valor da comissão')
        ->attrs(['class' => 'form-control moeda'])
        ->required()
        ->value(__moeda($data->comissao))
        !!}
    </div>

    <div class="col-md-2 col-6">
        {!!Form::select('tipo_pagamento', 'Tipo de Pagamento', ['' => 'Selecione'] + App\Models\ApuracaoMensal::tiposPagamento())->attrs(['class' => 'form-select'])
        ->required()
        !!}
    </div>

    <div class="col-md-2 col-6">
        {!!Form::select('status_pagamento', 'Status de Pagamento', [0 => 'Pendente', 1 => 'Pago'])->attrs(['class' => 'form-select'])
        ->required()
        !!}
    </div>

    <div class="col-md-6 col-12">
        {!!Form::text('observacao', 'Observação')
        !!}
    </div>
    

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script>

    $(document).on("change", "#inp-tipo_pagamento", function () {
        if($(this).val()){
            $('#inp-status_pagamento').val(1).change()
        }else{
            $('#inp-status_pagamento').val(0).change()
        }
    })

</script>
@endsection
