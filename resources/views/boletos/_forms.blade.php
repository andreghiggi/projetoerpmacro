<div class="row g-2">
    <div class="col-md-3">
        {!!Form::select('conta_boleto', 'Conta', ['' => 'Selecione'] + $contasBoleto->pluck('info', 'id')->all())->required()
        ->attrs(['class' => 'form-select'])
        ->value($contaPadrao != null ? $contaPadrao->id : null)
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('carteira', 'Carteira')->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('convenio', 'Convênio')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('tipo', 'Tipo', ['Cnab400' => 'Cnab400', 'Cnab240' => 'Cnab240'])->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('usar_logo', 'Usar logo', [0 => 'Não', 1 => 'Sim'])->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    @if(sizeof($contas) > 0)
    <hr>
    @foreach($contas as $conta)
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary">{{ $conta->cliente->info }}</h5>
                </div>
                <div class="col-md-6 text-end">
                    <h5>Valor <strong class="text-danger">R$ {{ __moeda($conta->valor_integral) }}</strong></h5>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <input type="hidden" name="conta_id[]" value="{{ $conta->id }}">
                <div class="col-md-2">
                    {!!Form::tel('numero[]', 'Número boleto')->required()
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::tel('numero_documento[]', 'Número documento')->required()
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::tel('juros[]', 'Juros')->required()
                    ->attrs(['class' => 'moeda juros'])
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::tel('juros_apos[]', 'Juros após (dias) ')->required()
                    ->attrs(['class' => 'juros_apos', 'data-mask' => '000'])
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::tel('multa[]', 'Multa')->required()
                    ->attrs(['class' => 'moeda multa'])
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::tel('valor[]', 'Valor')->required()
                    ->value(__moeda($conta->valor_integral))
                    ->attrs(['class' => 'moeda'])
                    !!}
                </div>
                <div class="col-md-2 mt-2">
                    {!!Form::date('vencimento[]', 'Vencimento')->required()
                    ->value($conta->data_vencimento)
                    !!}
                </div>

                <div class="col-md-5 mt-2">
                    {!!Form::tel('instrucoes[]', 'Instruções')
                    !!}
                </div>

                <div class="col-md-2 mt-2 div-sicredi">
                    {!!Form::text('posto', 'Posto')->required()
                    ->attrs(['class' => 'posto'])
                    !!}
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @else

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ $conta->cliente->info }}</h5>
                </div>
                <div class="col-md-6 text-end">
                    <h5>Valor <strong class="text-danger">R$ {{ __moeda($conta->valor_integral) }}</strong></h5>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <input type="hidden" name="conta_id[]" value="{{ $conta->id }}">
                <div class="col-md-2">
                    {!!Form::tel('numero[]', 'Número boleto')->required()
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::tel('numero_documento[]', 'Número documento')->required()
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::tel('juros[]', 'Juros')->required()
                    ->attrs(['class' => 'moeda juros'])
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::tel('juros_apos[]', 'Juros após (dias) ')->required()
                    ->attrs(['class' => 'juros_apos', 'data-mask' => '000'])
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::tel('multa[]', 'Multa')->required()
                    ->attrs(['class' => 'moeda multa'])
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::tel('valor[]', 'Valor')->required()
                    ->value(__moeda($conta->valor_integral))
                    ->attrs(['class' => 'moeda'])
                    !!}
                </div>
                <div class="col-md-2 mt-2">
                    {!!Form::date('vencimento[]', 'Vencimento')->required()
                    ->value($conta->data_vencimento)
                    !!}
                </div>

                <div class="col-md-5 mt-2">
                    {!!Form::tel('instrucoes[]', 'Instruções')
                    !!}
                </div>

                <div class="col-md-2 mt-2 div-sicredi">
                    {!!Form::text('posto', 'Posto')->required()
                    ->attrs(['class' => 'posto'])
                    !!}
                </div>
            </div>
        </div>
    </div>
    @endif

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script type="text/javascript">
    $(function(){
        setTimeout(() => {
            $('#inp-conta_boleto').change()
        })
    })

    $('body').on('change', '#inp-conta_boleto', function () {
        let conta_boleto = $(this).val()
        if(conta_boleto){
            $.get(path_url + 'api/conta-boleto', {conta_boleto_id: conta_boleto})
            .done((res) => {

                $('#inp-carteira').val(res.carteira)
                $('#inp-convenio').val(res.convenio)
                $('#inp-tipo').val(res.tipo).change()

                $('.juros').val(convertFloatToMoeda(res.juros))
                $('.multa').val(convertFloatToMoeda(res.multa))
                $('.juros_apos').val(res.juros_apos)

                if(res.banco == 'Sicredi'){
                    $('.div-sicredi').removeClass('d-none')
                    $('.posto').attr('required', 1)
                }else{
                    $('.div-sicredi').addClass('d-none')
                    $('.posto').removeAttr('required')
                }

            })
            .fail((err) => {
                console.log(err)
            })
        }
    })
</script>
@endsection