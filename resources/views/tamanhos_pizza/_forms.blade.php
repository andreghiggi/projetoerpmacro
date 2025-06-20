<div class="row g-2">
    <div class="col-md-2">
        {!!Form::text('nome', 'Nome')->required()
        !!}
    </div>
    @if(__isInternacionalizar(Auth::user()->empresa))
    <div class="col-md-2">
        {!!Form::text('nome_en', 'Nome (em inglês)')
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('nome_es', 'Nome (em espanhol)')
        !!}
    </div>
    @endif

    <div class="col-md-2">
        {!!Form::text('maximo_sabores', 'Max. sabores')->required()
        ->attrs(['data-mask' => '0'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('quantidade_pedacos', 'Qtd. fatias')->required()
        ->attrs(['data-mask' => '00'])
        !!}
    </div>


    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', ['1' => 'Sim', '0' => 'Não'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>