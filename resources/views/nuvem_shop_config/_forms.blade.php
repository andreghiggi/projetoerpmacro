<div class="row g-2">
    <div class="col-md-2">
        {!!Form::text('client_id', 'APP ID')
        ->required()
        !!}
    </div>

    <div class="col-md-5">
        {!!Form::text('client_secret', 'Client Secret')
        ->required()
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::text('email', 'Email')->required()
        !!}
    </div>

  
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>