<div class="row g-2">
    
    <div class="col-md-4">
        {!!Form::text('nome', 'Nome')->attrs(['class' => ''])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('cnpj', 'CNPJ')->attrs(['class' => 'cnpj'])->required()
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::tel('telefone', 'Telefone')->attrs(['class' => 'fone'])->required()
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', [ 1 => 'Sim', 0 => 'Não'])->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-4">
        {!! Form::text('email', 'Email')->attrs(['class' => ''])->type('email') !!}
    </div>
    <div class="col-md-4">
        @isset($item)
        {!!Form::select('cidade_id', 'Cidade')
        ->attrs(['class' => 'select2'])->options(($item != null && $item->cidade) ? [$item->cidade_id => $item->cidade->info] : [])
        ->required()
        !!}
        @else
        {!!Form::select('cidade_id', 'Cidade')
        ->attrs(['class' => 'select2'])
        ->required()
        !!}
        @endisset
    </div>
    <div class="col-md-3">
        {!!Form::text('rua', 'Rua')->attrs(['class' => ''])->required()
        !!}
    </div>
    <div class="col-md-1">
        {!!Form::text('numero', 'Número')->attrs(['class' => ''])->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('cep', 'CEP')->attrs(['class' => 'cep'])->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('bairro', 'Bairro')->attrs(['class' => ''])->required()
        !!}
    </div>
    
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>



