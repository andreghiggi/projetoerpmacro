<div class="row g-2">
    
    <div class="col-md-3">
        {!!Form::select('usuario_id', 'Usuário')->required()
        ->options(isset($item) ? [ $item->usuario_id => $item->usuario->name ] : [])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('numero_ultima_nfce', 'Número ultima NFCe')
        ->attrs(['class' => ''])->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('numero_serie_nfce', 'Nº de Série NFCe')
        ->attrs(['class' => ''])->required()
        !!}
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>