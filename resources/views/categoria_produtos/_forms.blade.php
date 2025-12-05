<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('nome', 'Nome')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', ['1' => 'Sim', '0' => 'Não'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>
    @if(__isInternacionalizar(Auth::user()->empresa))
    <div class="col-md-3">
        {!!Form::text('nome_en', 'Nome (em inglês)')
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::text('nome_es', 'Nome (em espanhol)')
        !!}
    </div>
    @endif

    @if(__isActivePlan(Auth::user()->empresa, 'Cardapio'))
    <div class="col-md-2">
        {!!Form::select('cardapio', 'Cardápio', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select tooltipp'])
        !!}
        <div class="text-tooltip d-none">
            Marcar como sim se for usar esta categoria no cardápio
        </div>
    </div>
    @endif
    @if(__isActivePlan(Auth::user()->empresa, 'Delivery'))
    <div class="col-md-2">
        @if(isset($delivery) && $delivery == 1)
        {!!Form::select('delivery', 'Delivery', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select tooltipp2'])
        ->value(1)
        !!}
        @else
        {!!Form::select('delivery', 'Delivery', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select tooltipp2'])
        !!}
        @endif
        <div class="text-tooltip2 d-none">
            Marcar como sim se for usar esta categoria no Delivery/Marketplace
        </div>
    </div>

    <div class="col-md-2">
        {!!Form::select('tipo_pizza', 'Tipo pizza', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    @endif
    
    @if(__isActivePlan(Auth::user()->empresa, 'Ecommerce'))
    <div class="col-md-2">
        {!!Form::select('ecommerce', 'Ecommerce', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select tooltipp3'])
        !!}
        <div class="text-tooltip3 d-none">
            Marcar como sim se for usar esta categoria no Ecommerce
        </div>
    </div>
    @endif

    @if(__isActivePlan(Auth::user()->empresa, 'Reservas'))
    <div class="col-md-2">
        {!!Form::select('reserva', 'Reserva', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select tooltipp4'])
        !!}
        <div class="text-tooltip4 d-none">
            Marcar como sim se for usar esta categoria no Módulo de reserva
        </div>
    </div>
    @endif

    <div class="col-md-4">
        {!!Form::select('categoria_id', 'Categoria Principal')
        ->attrs(['class' => 'form-select'])
        ->options(isset($item) && $item->categoria ? [$item->categoria->id => $item->categoria->nome] : [])
        !!}
    </div>
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script type="text/javascript">
    $("#inp-categoria_id").select2({
        minimumInputLength: 2,
        language: "pt-BR",
        placeholder: "Digite para buscar a categoria",
        width: "100%",
        ajax: {
            cache: true,
            url: path_url + "api/categorias-produto-subcategoria",
            dataType: "json",
            data: function (params) {
                console.clear();
                var query = {
                    pesquisa: params.term,
                    empresa_id: $('#empresa_id').val()
                };
                return query;
            },
            processResults: function (response) {
                var results = [];

                $.each(response, function (i, v) {

                    var o = {};
                    o.id = v.id;
                    o.text = v.nome
                    results.push(o);
                });
                return {
                    results: results,
                };
            },
        },
    });
</script>
@endsection