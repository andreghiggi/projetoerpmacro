<div class="row g-2">
    <div class="col-md-4">
        {!!Form::text('url', 'URL')
        ->required()
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::text('consumer_key', 'Consumer Key')
        ->required()
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::text('consumer_secret', 'Consumer Secret')->required()
        !!}
    </div>

  
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>