<div class="row g-2">

    <div class="col-md-2">
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" onclick="openModal()">
        Campos disponiveis para o contrato</button>
    </div>
    <div class="col-md-12">
        {!!Form::textarea('texto', 'Texto')
        ->attrs(['rows' => '10', 'class' => 'tiny'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Status', [1 => 'Sim', 0 => 'Não'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('limite_dias_assinar', 'Limite de dias para assinar')
        ->required()
        ->attrs(['data-mask' => '00'])
        !!}
    </div>

    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>



