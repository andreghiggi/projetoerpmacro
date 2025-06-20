<div id="basicwizard">
    <ul class="nav nav-pills nav-justified form-wizard-header mb-4 m-2">
        @foreach($item->hospedes as $key => $hospede)
        <li class="nav-item">
            <a href="#tab-{{$key}}" data-bs-toggle="tab" data-toggle="tab"  class="nav-link rounded-0 py-1"> 
                <i class="ri-product-hunt-fill fw-normal fs-18 align-middle me-1"></i>
                <span class="d-none d-sm-inline">Hóspede {{ $key+1 }}</span>
            </a>
        </li>
        @endforeach
    </ul>

    @foreach($item->hospedes as $key => $hospede)
    <div class="tab-content b-0 mb-0">
        <div class="tab-pane" id="tab-{{$key}}">
            <div class="row g-2 m-2">
                <div class="col-md-12">
                    <h5 class="text-danger">Hóspede {{ $key+1 }}</h5>
                </div>
                <input type="hidden" name="hospede_id[]" value="{{ $hospede->id }}">
                <div class="col-md-4">
                    {!!Form::text('nome_completo[]', 'Nome completo')
                    ->required()
                    ->value($hospede->nome_completo)
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::tel('cpf[]', 'CPF')
                    ->required()
                    ->attrs(['class' => 'cpf'])
                    ->value($hospede->cpf)
                    !!}
                </div>
                <hr>
                <div class="col-md-2 col">
                    {!!Form::tel('cep[]', 'CEP')
                    ->required()
                    ->attrs(['class' => 'cep'])
                    ->value($hospede->cep)
                    !!}
                </div>
                <div class="col-md-4 col">
                    {!!Form::text('rua[]', 'Rua')
                    ->required()
                    ->value($hospede->rua)
                    !!}
                </div>
                <div class="col-md-2 col">
                    {!!Form::text('numero[]', 'Número')
                    ->required()
                    ->value($hospede->numero)
                    !!}
                </div>
                <div class="col-md-3 col">
                    {!!Form::text('bairro[]', 'Bairro')
                    ->required()
                    ->value($hospede->bairro)
                    !!}
                </div>

                <div class="col-md-3 col">
                    {!!Form::select('cidade_id[]', 'Cidade')
                    ->required()
                    ->id('cidade_'.$key)
                    ->attrs(['class' => 'cidade'])
                    ->options(($hospede != null && $hospede->cidade) ? [$hospede->cidade_id => $hospede->cidade->info] : [])
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::tel('telefone[]', 'Telefone')
                    ->required()
                    ->attrs(['class' => 'fone'])
                    ->value($hospede->telefone)
                    !!}
                </div>

                <div class="col-md-3">
                    {!!Form::text('email[]', 'Email')
                    ->type('email')
                    ->value($hospede->email)
                    !!}
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success btn-action px-5" id="btn-store">Salvar</button>
    </div>

</div>
