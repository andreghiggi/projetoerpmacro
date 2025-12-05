<div class="row g-2">
    <div class="col-md-2">
        {!!Form::text('documento', 'Documento')->attrs(['class' => 'cpf_cnpj'])->required()
        ->value($item != null ? $item->documento : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('nome', 'Nome')->attrs(['class' => ''])->required()
        ->value($item != null ? $item->nome : '')
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::text('razao_social', 'Razão social')->attrs(['class' => ''])->required()
        ->value($item != null ? $item->razao_social : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('ie', 'I.E')->attrs(['class' => ''])
        ->value($item != null ? $item->ie : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('im', 'I.M')->attrs(['class' => ''])
        ->value($item != null ? $item->im : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('cnae', 'CNAE')->attrs(['class' => ''])
        ->value($item != null ? $item->cnae : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('telefone', 'Telefone')->attrs(['class' => 'fone'])->required()
        ->value($item != null ? $item->telefone : '')
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::text('rua', 'Rua')->attrs(['class' => ''])
        ->value($item != null ? $item->rua : '')->required()
        !!}
    </div>

    <div class="col-md-1">
        {!!Form::text('numero', 'Número')->attrs(['class' => ''])
        ->value($item != null ? $item->numero : '')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('bairro', 'Bairro')->attrs(['class' => ''])
        ->value($item != null ? $item->bairro : '')->required()
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::text('complemento', 'Complemento')->attrs(['class' => ''])
        ->value($item != null ? $item->complemento : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('cep', 'CEP')->attrs(['class' => 'cep'])
        ->value($item != null ? $item->cep : '')->required()
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::text('email', 'Email')->attrs([])
        ->value($item != null ? $item->email : '')->required()
        ->type('email')
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::select('cidade_id', 'Cidade')->attrs([])->required()
        ->options($item != null ? [$item->cidade_id => $item->cidade->info] : [])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('login_prefeitura', 'Login prefeitura')->attrs([])
        ->value($item != null ? $item->login_prefeitura : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('senha_prefeitura', 'Senha prefeitura')->attrs([])
        ->value($item != null ? $item->senha_prefeitura : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('regime', 'Regime', ['simples' => 'Simples', 'normal' => 'Normal'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-12">
        {!!Form::text('token', 'Token do emitente')
        ->value($item != null ? $item->token : '')
        ->readonly($item == null)
        !!}
    </div>

    <div class="card col-md-3 mt-3 form-input" style="width: 210px">
        <div class="preview">
            <button type="button" id="btn-remove-imagem" class="btn btn-link-danger btn-sm btn-danger">x</button>
            @isset($item)
            <img id="file-ip-1-preview" src="{{ $item->img }}">
            @else
            <img id="file-ip-1-preview" src="/imgs/no-image.png">
            @endif
        </div>
        <label for="file-ip-1">Logo</label>
        <input type="file" id="file-ip-1" name="image" accept="image/*" onchange="showPreview(event);">
    </div>

    @if($item != null && $item->token != null)
    <div class="col-lg-12 col-12 mb-2">
        <a href="{{ route('nota-servico-config.certificado') }}" class="btn btn-danger">
            <i class="ri-upload-cloud-2-fill"></i>
            Upload de certificado
        </a>
    </div>
    @endif

    @if($tokenNfse == null)
    <h5 class="text-danger col-12 mt-2">Nenhum token configurado para NFSe</h5>
    @endif

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>