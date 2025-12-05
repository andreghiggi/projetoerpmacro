<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('clientId', 'Client ID')
        ->required()
        !!}
    </div>

    <div class="col-md-6">
        {!!Form::text('clientSecret', 'Client Secret')
        ->required()
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::text('merchantId', 'Merchant ID')
        ->required()
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::text('merchantUUID', 'Merchant UUID')
        ->required()
        !!}
    </div>

    @if($item != null && $item->userCode)
    <div class="col-md-3">
        {!!Form::text('authorizationCode', 'AuthorizationCode')
        !!}
    </div>
    @if($item->userCode != "")
    <div class="row">
        <br>
        <div class="form-group validated col-12">
            <span>userCode: <strong>{{ $item->userCode }}</strong></span><br>
            <span>authorizationCodeVerifier: <strong>{{ $item->authorizationCodeVerifier }}</strong></span><br>
            <span>verificationUrlComplete: <a href="{{ $item->verificationUrlComplete }}" target="_blank">{{ $item->verificationUrlComplete }}</a></span>
            <br>

            @if($item->authorizationCode != "")
            @if($item->accessToken == "")
            <a href="{{ route('ifood-config.get-token') }}" class="btn btn-success">
                Gerar Novo Token
            </a>
            @else
            <a href="{{ route('ifood-config.get-token') }}" class="btn btn-info">
                Atualizar Token
            </a>
            @endif
            @endif

            @if($item->accessToken != "")
            <h6 class="mt-2">accessToken: <strong>{{ $item->accessToken }}</strong></h6>
            @endif

        </div>
    </div>
    @endif
    @endif

    <div class="col-md-3">
        <a href="{{ route('ifood-config.user-code') }}" class="btn btn-warning">
            Gerar Novo Código de Usuário
        </a>
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>
