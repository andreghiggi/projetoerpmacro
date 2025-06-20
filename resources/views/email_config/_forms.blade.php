<div class="row g-2">
    <div class="col-md-2">
        {!!Form::text('nome', 'Nome')
        ->required()
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::text('host', 'Host')
        ->required()
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::text('email', 'Email')
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('senha', 'Senha')
        ->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('porta', 'Porta')
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('cripitografia', 'Cripitografia', ['tls' => 'TLS', 'ssl' => 'SSL'])
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('smtp_auth', 'Autenticação SMTP', ['0' => 'Não', '1' => 'Sim'])
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('smtp_debug', 'SMTP Debug', ['0' => 'Não', '1' => 'Sim'])
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::select('status', 'Status', ['0' => 'Desativado', '1' => 'Ativado'])
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>


