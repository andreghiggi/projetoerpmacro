<div class="row g-2">
    @isset($dias)
    <div class="col-md-2">
        {!!Form::select('dia', 'Dia', ['' => 'Selecione'] + $dias)->attrs(['class' => 'form-select'])->required()
        !!}
    </div>
    @else
    <div class="col-md-2">
        {!!Form::text('', 'Dia')
        ->value($item->getDiaStr())->readonly()
        !!}
    </div>
    @endif
    <div class="col-md-2">
        {!!Form::text('inicio', 'Inicio')->attrs(['class' => 'timer'])->required()
        ->value(isset($item) ? $item->inicioParse : '')
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('fim', 'Fim')->attrs(['class' => 'timer'])->required()
        ->value(isset($item) ? $item->finalParse : '')
        !!}
    </div>
    
    <hr>
    <div class="text-end">
        <button type="submit" class="btn btn-success px-5">Salvar</button>
    </div>
</div>

