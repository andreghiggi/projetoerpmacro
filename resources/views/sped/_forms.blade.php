<div class="row g-2">

    <div class="col-md-2">
        {!!Form::date('data_inicial', 'Data inicial')
        ->value($firstDate)
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::date('data_final', 'Data final')
        ->value($lastDate)
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('inventario', 'Inventário', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::date('data_inventario', 'Data de inventário')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('motivo_inventario', 'Motivo de inventário', App\Models\Sped::motivosInventario())
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        <br>
        <button type="submit" class="btn btn-success px-5" id="btn-store">
            Gerar
        </button>
    </div>
</div>

