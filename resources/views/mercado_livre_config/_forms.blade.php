<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('client_id', 'Client ID')
        ->required()
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::text('client_secret', 'Client Secret')
        ->required()
        !!}
    </div>

    <div class="col-md-5">
        {!!Form::text('url', 'Url redirecionamento')->required()
        !!}
    </div>

    @if($item)
    <div class="col-6">
        Access Token: <strong>{{ $item->access_token }}</strong>
    </div>
    <div class="col-6">
        Refresh Token: <strong>{{ $item->refresh_token }}</strong>
    </div>
    <div class="col-2">
        User ID: <strong>{{ $item->user_id }}</strong>
    </div>
    <div class="col-3">
        Code: <strong>{{ $item->code }}</strong>
    </div>
    @endif
  
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>