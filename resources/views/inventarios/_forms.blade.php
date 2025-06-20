<div class="row g-2">
    <div class="col-md-2">
        {!!Form::text('referencia', 'Referência')->attrs(['class' => ''])->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::date('inicio', 'Início')->required()
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::date('fim', 'Fim')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('tipo', 'Tipo', [ '' => 'Selecione' ] + App\Models\Inventario::tipos())->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', ['1' => 'Sim', '0' => 'Não'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-12">
        {!!Form::textarea('observacao', 'Observação')->attrs(['rows' => '5'])
        !!}
    </div>
    
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>


