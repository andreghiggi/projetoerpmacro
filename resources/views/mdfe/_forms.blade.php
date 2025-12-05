<div class="row g-2">
    @if(__countLocalAtivo() > 1)
    <div class="col-md-2">
        <label for="">Local</label>

        <select id="inp-local_id" required class="select2 class-required" data-toggle="select2" name="local_id">
            <option value="">Selecione</option>
            @foreach(__getLocaisAtivoUsuario() as $local)
            <option @isset($item) @if($item->local_id == $local->id) selected @endif @endif value="{{ $local->id }}">{{ $local->descricao }}</option>
            @endforeach
        </select>
    </div>
    @else
    <input id="inp-local_id" type="hidden" value="{{ __getLocalAtivo() ? __getLocalAtivo()->id : '' }}" name="local_id">
    @endif
    <div class="col-md-2">
        {!!Form::tel('mdfe_numero', 'Número MDFe')
        ->required()
        ->value(isset($item) ? $item->numero : $numeroMDFe)
        !!}
    </div>
    <div class="col-md-2">
        {!! Form::select('uf_inicio', 'UF inicial', ['' => 'Selecione...'] + App\Models\Cidade::estados())
        ->attrs(['class' => 'form-select select2'])->required() !!}
    </div>
    <div class="col-md-2">
        {!! Form::select('uf_fim', 'UF final', ['' => 'Selecione...'] + App\Models\Cidade::estados())->attrs([
        'class' => 'form-select select2'])->required() !!}
    </div>
    <div class="col-md-2">
        {!! Form::date('data_inicio_viagem', 'Data início da viagem')->value(date('Y-m-d'))->required() !!}
    </div>
    <div class="col-md-2">
        {!! Form::select('carga_posterior', 'Carga posterior', [0 => 'Não', 1 => 'Sim'])->attrs(
        ['class' => 'form-select'],
        )->required() !!}
    </div>
    <div class="col-md-2">
        {!! Form::select(
        'tp_emit',
        'Tipo do emitente',
        ['' => 'Selecione...'] + [
        1 => '1 - Prestador de serviço de transporte',
        2 => '2 - Transportador de carga própria',
        3 => '3 - CT-e globalizado',
        4 => '4 - MDF-e vinculado a NF-e, sem CTe',
        ],
        )->attrs(['class' => 'form-select class-required'])->required() !!}
    </div>
    <div class="col-md-2">
        {!! Form::select(
        'tp_transp',
        'Tipo do transportador',
        ['' => 'Selecione...'] + [1 => '1 - ETC', 2 => '2 - TAC', 3 => '3 - CTC'],
        )->attrs(['class' => 'form-select'])->required() !!}
    </div>
    <div class="col-md-2">
        {!! Form::select('tipo_modal', 'Tipo modal', ['' => 'Selecione...'] + App\Models\Mdfe::tiposModal())->attrs([
        'class' => 'form-select select2'])->required() !!}
    </div>
    <div class="col-md-2">
        {!! Form::text('lac_rodo', 'Lacre rodoviário')->attrs(['data-mask' => '00000000'])->required() !!}
    </div>
    <div class="col-md-3">
        {!! Form::tel('cnpj_contratante', 'CNPJ do contratante')->attrs(['class' => 'cpf_cnpj'])->required() !!}
    </div>
    <div class="col-md-2">
        {!! Form::tel('quantidade_carga', 'Quantidade da carga')->attrs(['class' => 'qtd_carga', 'data-mask' => '00000000.000', 'data-mask-reverse' => 'true'])->required()
        ->value(isset($item) ? number_format($item->quantidade_carga, 3) : '')
        !!}
    </div>
    <div class="col-md-2">
        {!! Form::tel('valor_carga', 'Valor da carga')->attrs(['class' => 'moeda'])->required()
        ->value(isset($item) ? __moedaInput($item->valor_carga) : '') !!}
    </div>
    <div class="col-md-3">
        {!! Form::select(
        'veiculo_tracao_id',
        'Veículo de tração',
        ['' => 'Selecione...'] + $veiculos->pluck('placa', 'id')->all(),
        )->attrs([
        'class' => 'form-select class-required',
        ])->required() !!}
    </div>
    <div class="col-md-3">
        {!! Form::select(
        'veiculo_reboque_id',
        'Veículo de reboque 1 (opcional)',
        ['' => 'Selecione...'] + $veiculos->pluck('placa', 'id')->all(),
        )->attrs(['class' => 'form-select']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::select(
        'veiculo_reboque2_id',
        'Veículo de reboque 2 (opcional)',
        ['' => 'Selecione...'] + $veiculos->pluck('placa', 'id')->all(),
        )->attrs(['class' => 'form-select']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::select(
        'veiculo_reboque3_id',
        'Veículo de reboque 3 (opcional)',
        ['' => 'Selecione...'] + $veiculos->pluck('placa', 'id')->all(),
        )->attrs(['class' => 'form-select']) !!}
    </div>
    <hr>
    <div class="row card-body g-3 m-1">
        <h4>Produto predominante (opcional)</h4>
        <div class="col-md-3">
            {!! Form::text('produto_pred_nome', 'Nome')->attrs(['class' => '']) !!}
        </div>
        <div class="col-md-2">
            {!! Form::tel('produto_pred_ncm', 'NCM')->attrs(['class' => 'ncm']) !!}
        </div>
        <div class="col-md-2">
            {!! Form::tel('produto_pred_cod_barras', 'Código de barras')->attrs(['class' => '']) !!}
        </div>
        <div class="col-md-2">
            {!! Form::tel('cep_carrega', 'Cep carrega')->attrs(['data-mask' => '00000000']) !!}
        </div>
        <div class="col-md-2">
            {!! Form::tel('latitude_carregamento', 'Latitude carrega')->attrs(['class' => '']) !!}
        </div>
        <div class="col-md-2">
            {!! Form::tel('longitude_carregamento', 'Longitude carrega')->attrs(['class' => '']) !!}
        </div>
        <div class="col-md-2">
            {!! Form::tel('cep_descarrega', 'Cep descarrega')->attrs(['data-mask' => '00000000']) !!}
        </div>
        <div class="col-md-2">
            {!! Form::tel('latitude_descarregamento', 'Latitude descarrega')->attrs(['class' => '']) !!}
        </div>
        <div class="col-md-2">
            {!! Form::tel('longitude_descarregamento', 'Longitude descarrega')->attrs(['class' => '']) !!}
        </div>
        <div class="col-md-2">
            {!! Form::select('tp_carga', 'Tipo de carga', ['' => 'Selecione...'] + App\Models\Mdfe::tiposCarga())->attrs([
            'class' => 'form-select',
            ]) !!}
        </div>
    </div>
    <hr>
    <div class="row mt-4">
        <div class="col-md-3 row">
            <button type="button" class="btn btn-outline-primary btn-gerais active px-6" onclick="selectDiv2('gerais')">INFORMAÇÕES GERAIS</button>
        </div>
        <div class="col-md-3 row ms-auto">
            <button type="button" class="btn btn-outline-primary btn-transporte px-6" onclick="selectDiv2('transporte')">
            INFORMAÇÕES TRANSPORTE</button>
        </div>
        <div class="col-md-3 row ms-auto">
            <button type="button" class="btn btn-outline-primary btn-pagamento px-6" onclick="selectDiv2('pagamento')">
            INFORMAÇÕES PAGAMENTO</button>
        </div>
        <div class="col-md-3 row m-auto">
            <button type="button" class="btn btn-outline-primary btn-descarregamento px-6" onclick="selectDiv2('descarregamento')">
            INFORMAÇÕES DESCARREGAMENTO</button>
        </div>
    </div>
    <hr>

    <div class="div-gerais row">
        <div class="card">
            <div class="row m-3">
                <h4>Seguradora (opcional)</h4>
                <div class="col-md-4 mt-3">
                    {!! Form::text('seguradora_nome', 'Nome da seguradora')->attrs(['class' => '']) !!}
                </div>
                <div class="col-md-3 mt-3">
                    {!! Form::tel('seguradora_cnpj', 'CNPJ da seguradora')->attrs(['class' => 'cpf_cnpj']) !!}
                </div>
                <div class="col-md-2 mt-3">
                    {!! Form::tel('numero_apolice', 'Número da apólice')->attrs(['class' => '']) !!}
                </div>
                <div class="col-md-2 mt-3">
                    {!! Form::tel('numero_averbacao', 'Número da averbação')->attrs(['class' => '']) !!}
                </div>
            </div>
        </div>
        <div class="card col-12 col-md-8">
            <div class="row m-3">
                <div class="col-12 row">
                    <h4>Município(s) de carregamento</h4>
                    <div class="row">
                        <table class="table mb-0 table-striped table-dynamic">
                            <thead class="table-dark">
                                <tr>
                                    <th>Cidade</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="body" class="datatable-body">
                                @if (isset($item) && sizeof($item->municipiosCarregamento) > 0)
                                @foreach($item->municipiosCarregamento as $mun)
                                <tr class="dynamic-form">
                                    <td class="col-12 col-md-4">
                                        {!! Form::select('municipiosCarregamento[]', '', [null => 'Selecione...'] + $cidades->pluck('info', 'id')->all())
                                        ->attrs(['class' => 'select2'])->required()->value($mun->cidade_id) !!}
                                    </td>
                                    <td>
                                        <br>
                                        <button class="btn btn-danger btn-sm btn-remove-tr">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr class="dynamic-form">
                                    <td class="col-11">
                                        {!! Form::select('municipiosCarregamento[]', '', [null => 'Selecione...'] + $cidades->pluck('info', 'id')->all())
                                        ->attrs(['class' => 'select2 class-municipio class-required'])->required() !!}
                                    </td>
                                    <td>
                                        <br>
                                        <button class="btn btn-danger btn-sm btn-remove-tr">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <br>
                            <button type="button" class="btn btn-dark btn-add-tr">
                                <i class="ri-add-line"></i>
                                Adicionar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card col-12 col-md-4">
            <div class="row">
                <div class="col-12 mt-3">
                    <h4>Percurso</h4>
                    <div class="row">
                        <table class="table mb-0 table-striped table-dynamic">
                            <thead class="table-dark">
                                <tr>
                                    <th>UF</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody class="datatable-body" id="tbody">
                                @if (isset($item) && sizeof($item->percurso) > 0)
                                @foreach($item->percurso as $p)
                                <tr class="dynamic-form">
                                    <td class="col-10">
                                        <br>

                                        {!! Form::select('uf[]', '', ['' => 'Selecione...'] + App\Models\Cidade::estados())
                                        ->attrs(['class' => 'select2'])
                                        ->value($p->uf)
                                        !!}
                                    </td>
                                    <td>
                                        <br>
                                        <button class="btn btn-danger btn-sm btn-remove-tr">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr class="dynamic-form">
                                    <td class="col-10">
                                        <br>
                                        {!! Form::select('uf[]', '', ['' => 'Selecione...'] + App\Models\Cidade::estados())
                                        ->attrs(['class' => 'select2']) !!}
                                    </td>
                                    <td>
                                        <br>
                                        <button class="btn btn-danger btn-sm btn-remove-tr">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <br>
                            <button type="button" class="btn btn-dark btn-add-tr">
                                <i class="ri-add-line"></i>
                                Adicionar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="div-transporte d-none">
        <div class="row">
            <div class="col-md-5">
                <div class="card">
                    <div class="row m-3">
                        <h4>CIOT (opcional)</h4>
                        <div class="table-responsive mt-2">
                            <table class="table table-dynamic">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Código CIOT</th>
                                        <th>CPF/CNPJ</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($item) && sizeof($item->ciots) > 0)
                                    @foreach($item->ciots as $ciot)
                                    <tr class="dynamic-form">
                                        <td>
                                            <input type="tel" class="form-control codigo_ciot" name="codigo_ciot[]" value="{{$ciot->codigo}}">
                                        </td>
                                        <td>
                                            <input type="tel" class="form-control cpf_cnpj" name="cpf_cnpj[]" value="{{$ciot->cpf_cnpj}}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr class="dynamic-form">
                                        <td>
                                            <input type="tel" class="form-control codigo_ciot" name="codigo_ciot[]">
                                        </td>
                                        <td>
                                            <input type="tel" class="form-control cpf_cnpj" name="cpf_cnpj[]">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-dark btn-add-tr">
                                    <i class="ri-add-line"></i>
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card">
                    <div class="row m-3">
                        <h4>Vale pedágio (opcional)</h4>
                        <div class="table-responsive mt-2">
                            <table class="table table-dynamic">
                                <thead class="table-dark">
                                    <tr>
                                        <th>CNPJ</th>
                                        <th>CPF/CNPJ Pagador</th>
                                        <th>Número da compra</th>
                                        <th>Valor</th>
                                        <th>Ação</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($item) && sizeof($item->valesPedagio) > 0)
                                    @foreach($item->valesPedagio as $vale)
                                    <tr class="dynamic-form">
                                        <td>
                                            <input type="tel" class="form-control cnpj_fornecedor cpf_cnpj" name="cnpj_fornecedor[]" value="{{$vale->cnpj_fornecedor}}">
                                        </td>
                                        <td>
                                            <input type="tel" class="form-control cnpj_fornecedor_pagador" name="cnpj_fornecedor_pagador[]" value="{{$vale->cnpj_fornecedor_pagador}}">
                                        </td>
                                        <td>
                                            <input type="tel" class="form-control numero_compra" name="numero_compra[]" value="{{$vale->numero_compra}}">
                                        </td>
                                        <td>
                                            <input type="tel" class="form-control valor" name="valor_pedagio[]" value="{{ __moeda($vale->valor)}}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr class="dynamic-form">
                                        <td>
                                            <input type="tel" class="form-control cnpj_fornecedor cpf_cnpj" name="cnpj_fornecedor[]">
                                        </td>
                                        <td>
                                            <input type="tel" class="form-control cnpj_fornecedor_pagador cpf_cnpj" name="cnpj_fornecedor_pagador[]">
                                        </td>
                                        <td>
                                            <input type="tel" class="form-control numero_compra" name="numero_compra[]">
                                        </td>
                                        <td>
                                            <input type="tel" class="form-control valor moeda" name="valor_pedagio[]">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-dark btn-add-tr">
                                    <i class="ri-add-line"></i>
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="row m-3">
                    <h4>Condutor</h4>
                    <div class="col-md-3 mt-3">
                        {!! Form::text('condutor_nome', 'Nome')->attrs(['class' => 'class-condutor class-required'])->required() !!}
                    </div>
                    <div class="col-md-2 mt-3">
                        {!! Form::tel('condutor_cpf', 'CPF')->attrs(['class' => 'cpf class-condutor class-required'])->required() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="div-pagamento d-none">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="row m-3">
                        <h4>Grupo de Pagamento</h4>

                        <div class="col-md-3">
                            {!! Form::text('nome_pagador', 'Nome do pagador')->attrs(['class' => '']) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::tel('documento_pagador', 'Documento do pagador')->attrs(['class' => 'cpf_cnpj']) !!}
                        </div>
                        <div class="col-md-2">
                            {!! Form::tel('valor_transporte', 'Valor do transporte')->attrs(['class' => 'moeda'])->value(isset($item) ? __moeda($item->valor_transporte) : '') !!}
                        </div>

                        <div class="col-md-2">
                            {!! Form::select('ind_pag', 'Indicador de pagamento', App\Models\Mdfe::indicadoresDePagamento())->attrs(['class' => 'form-select']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card">
                    <div class="row m-3">
                        <h4>Componentes de Pagamento</h4>

                        <div class="table-responsive mt-2">
                            <table class="table table-striped table-dynamic table-componentes">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Valor</th>
                                        <th>Descrição</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody id="body" class="datatable-body">
                                    @if(isset($item) && sizeof($item->componentes) > 0)
                                    @foreach($item->componentes as $c)
                                    <tr class="dynamic-form">
                                        <td>
                                            {!! Form::select('tipo_componente[]', '', App\Models\Mdfe::tiposDeComponentes())
                                            ->attrs(['class' => 'form-select'])->value($c->tipo) !!}
                                        </td>
                                        <td>
                                            {!! Form::tel('valor_componente[]', '')->value(__moeda($c->valor))
                                            ->attrs(['class' => 'moeda']) !!}
                                        </td>
                                        <td>
                                            {!! Form::text('descricao_componente[]', '')->value($c->descricao)
                                            ->attrs(['class' => 'ignore']) !!}
                                        </td>
                                        <td>
                                            <br>
                                            <button class="btn btn-danger btn-sm btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr class="dynamic-form">
                                        <td>
                                            {!! Form::select('tipo_componente[]', '', App\Models\Mdfe::tiposDeComponentes())
                                            ->attrs(['class' => 'form-select']) !!}
                                        </td>
                                        <td>
                                            {!! Form::tel('valor_componente[]', '')
                                            ->attrs(['class' => 'moeda']) !!}
                                        </td>
                                        <td>
                                            {!! Form::text('descricao_componente[]', '')
                                            ->attrs(['class' => 'ignore']) !!}
                                        </td>
                                        <td>
                                            <br>
                                            <button class="btn btn-danger btn-sm btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-dark btn-add-tr">
                                    <i class="ri-add-line"></i>
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card">
                    <div class="row m-3">
                        <h4>Parcelamento</h4>

                        <div class="table-responsive mt-2">
                            <table class="table table-striped table-dynamic table-parcelamento">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Valor</th>
                                        <th>Vencimento</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody id="body" class="datatable-body">
                                    @if(isset($item) && sizeof($item->parcelamento) > 0)
                                    @foreach($item->parcelamento as $p)
                                    <tr class="dynamic-form">
                                        <td>
                                            {!! Form::tel('valor_parcelamento[]', '')->value(__moeda($p->valor))
                                            ->attrs(['class' => 'moeda']) !!}
                                        </td>
                                        <td>
                                            {!! Form::date('vencimento_parcelamento[]', '')->value($p->vencimento)
                                            ->attrs(['class' => '']) !!}
                                        </td>
                                        <td>
                                            <br>
                                            <button class="btn btn-danger btn-sm btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr class="dynamic-form">
                                        <td>
                                            {!! Form::tel('valor_parcelamento[]', '')
                                            ->attrs(['class' => 'moeda']) !!}
                                        </td>
                                        <td>
                                            {!! Form::date('vencimento_parcelamento[]', '')
                                            ->attrs(['class' => '']) !!}
                                        </td>
                                        <td>
                                            <br>
                                            <button class="btn btn-danger btn-sm btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-dark btn-add-tr">
                                    <i class="ri-add-line"></i>
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card">
                    <div class="row m-3">
                        <h4>Dados Bancários</h4>

                        <div class="table-responsive mt-2">
                            <table class="table table-striped table-dynamic table-componentes">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Código do banco</th>
                                        <th>Código da agência</th>
                                        <th>CNPJ do IPEF</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody id="body" class="datatable-body">
                                    @if(isset($item) && sizeof($item->infosBancaria) > 0)
                                    @foreach($item->infosBancaria as $i)
                                    <tr class="dynamic-form">
                                        <td>
                                            {!! Form::text('codigo_banco[]', '')->value($i->codigo_banco)
                                            ->attrs(['class' => '']) !!}
                                        </td>
                                        <td>
                                            {!! Form::text('codigo_agencia[]', '')->value($i->codigo_agencia)
                                            ->attrs(['class' => '']) !!}
                                        </td>
                                        <td>
                                            {!! Form::tel('cnpj_ipef[]', '')->value($i->cnpj_ipef)
                                            ->attrs(['class' => 'cnpj ignore']) !!}
                                        </td>
                                        <td>
                                            <br>
                                            <button class="btn btn-danger btn-sm btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr class="dynamic-form">
                                        <td>
                                            {!! Form::text('codigo_banco[]', '')
                                            ->attrs(['class' => '']) !!}
                                        </td>
                                        <td>
                                            {!! Form::text('codigo_agencia[]', '')
                                            ->attrs(['class' => '']) !!}
                                        </td>
                                        <td>
                                            {!! Form::tel('cnpj_ipef[]', '')
                                            ->attrs(['class' => 'cnpj ignore']) !!}
                                        </td>
                                        <td>
                                            <br>
                                            <button class="btn btn-danger btn-sm btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-dark btn-add-tr">
                                    <i class="ri-add-line"></i>
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- informações de descarregamento --}}
    <div class="div-descarregamento d-none">
        <div class="form-descarregamento">
            <div class="card">
                <div class="row m-3">
                    <h4>Informações da unidade de transporte/Documentos fiscais/Lacres</h4>
                    <div class="col-md-3 mt-3">
                        {!! Form::select(
                        'tp_unid_transp',
                        'Tipo unidade de transporte', ['' => 'Selecione...'] +
                        App\Models\Mdfe::tiposUnidadeTransporte(),
                        )->attrs(['class' => 'form-select']) !!}
                    </div>
                    <div class="col-md-2 mt-3">
                        {!! Form::tel('id_unid_transp', 'ID da Unidade de transporte (placa)')->attrs(['class' => 'placa']) !!}
                    </div>
                    <div class="col-md-2 mt-3">
                        {!! Form::tel('quantidade_rateio', 'Quantidade de rateio (transporte)')->attrs(['data-mask' => '000,00', 'class' => 'ignore']) !!}
                    </div>
                    <div class="col-md-2 mt-3">
                        {!! Form::tel('id_unidade_carga', 'ID unidade da carga')->attrs(['class' => 'ignore']) !!}
                    </div>
                    <div class="col-md-3 mt-3">
                        {!! Form::tel('quantidade_rateio_carga', 'Quantidade de rateio (unidade carga)')->attrs(['data-mask' => '000,00', 'class' => 'ignore']) !!}
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="row m-3">
                    <h4>NFe referência</h4>
                    <div class="col-md-6 mt-3">
                        {!! Form::tel('chave_nfe', 'NFe referência')->attrs(['class' => 'ignore chave_nfe']) !!}
                    </div>
                    <div class="col-md-6 mt-3">
                        {!! Form::tel('seg_cod_nfe', 'Segundo código de barra NFe (contingência)')->attrs(['class' => 'ignore']) !!}
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="row m-3">
                    <h4>CTe referência</h4>
                    <div class="col-md-6 mt-3">
                        {!! Form::tel('chave_cte', 'CTe referência')->attrs(['class' => 'ignore chave_nfe']) !!}
                    </div>
                    <div class="col-md-6 mt-3">
                        {!! Form::tel('seg_cod_cte', 'Segundo código de barra CTe (contingência)')->attrs(['class' => 'ignore']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="card col-6">
                    <div class="row m-2">
                        <h4>Lacres de transporte</h4>
                        <div class="table-responsive mt-2">
                            <table class="table table-striped table-dynamic table-lacres">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 70%">Número lacre</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody id="body" class="datatable-body">
                                    <tr class="dynamic-form">
                                        <td class="col-md-5">
                                            {!! Form::tel('numero_transporte[]', '')->attrs(['class' => 'numero_transporte input_lacres ignore']) !!}
                                        </td>
                                        <td>
                                            <br>
                                            <button class="btn btn-danger btn-sm btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-dark btn-numero_transporte btn-add-tr">
                                    <i class="ri-add-line"></i>
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card col-6">
                    <div class="row m-2">
                        <h4>Lacres da unidade da carga</h4>
                        <div class="table-responsive mt-2">
                            <table class="table table-striped table-dynamic table-lacres-carga">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 70%">Número lacre</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody id="body" class="datatable-body">
                                    <tr class="dynamic-form">
                                        <td class="col-md-5">
                                            {!! Form::tel('numero_carga[]', '')->attrs(['class' => 'numero_carga input_lacres ignore']) !!}
                                        </td>
                                        <td>
                                            <br>
                                            <button class="btn btn-danger btn-sm btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-dark btn-add-tr">
                                    <i class="ri-add-line"></i>
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="row m-3">
                    <h4>Município de descarregamento</h4>
                    <div class="col-md-6 mt-3">
                        {!! Form::select('municipio_descarregamento', 'Município', ['' => 'Selecione...'] + $cidades->pluck('info', 'id')->all())->attrs(['class' => 'select2']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="row m-2">
                <h4>Dados do descarregamento</h4>
                <div class="col-md-12 mt-3">
                    <button type="button" class="btn btn-info btn_info_desc">Adicionar informações do
                    descarregamento</button>
                </div>
            </div>
            <div class="table-responsive class-descarregamento mt-4">
                <table class="table table-centered table-descarregamento mb-2 mr-2">
                    <thead class="table-success">
                        <tr>
                            <th>Tipo transporte</th>
                            <th>Id unid transporte</th>
                            <th>Quantidade rateio</th>
                            <th>Quantidade rateio carga</th>
                            <th>NFe referência</th>
                            <th>CTe referência</th>
                            <th>Cidade descarregamento</th>
                            <th>Lacres de transp</th>
                            <th>Lacres unid. carga</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody class="" id="">
                        @isset($item)
                        @foreach ($item->infoDescarga as $i)
                        <tr>
                            <td>
                                <input type="hidden" name="tp_und_transp_row[]" class="form-control" value="{{ $i->tp_unid_transp }}">
                                <input style="width: 180px" readonly type="" class="form-control" value="{{ \App\Models\Mdfe::tiposUnidadeTransporte()[$i->tp_unid_transp] }}">

                            </td>
                            <td>
                                <input readonly style="width: 150px" type="text" name="id_und_transp_row[]" class="form-control" value="{{ $i->id_unid_transp }}">
                            </td>
                            <td>
                                <input style="width: 150px" readonly type="tel" name="quantidade_rateio_row[]" class="form-control" value="{{ $i->quantidade_rateio }}">
                            </td>
                            <td>
                                <input readonly style="width: 150px" type="tel" name="quantidade_rateio_carga_row[]" class="form-control" value="{{ $i->unidadeCarga->quantidade_rateio ?? '' }}">
                            </td>
                            <td>
                                <input readonly type="tel" name="chave_nfe_row[]" class="form-control" value="{{ isset($i->nfe->chave) ? $i->nfe->chave : '' }}">
                                <div style="width: 400px"></div>
                            </td>
                            <td>
                                <input readonly type="tel" name="chave_cte_row[]" class="form-control" value="{{ isset($i->cte->chave) ? $i->cte->chave : '' }}">
                                <div style="width: 400px"></div>

                            </td>
                            <td>
                                <input readonly style="width: 250px" type="text" name="municipio_descarregamento" class="form-control" value="{{ $i->cidade->info }}">
                                <input readonly type="hidden" name="municipio_descarregamento_row[]" class="form-control" value="{{ $i->cidade->id }}">
                            </td>
                            <td>
                                <input style="width: 150px" readonly type="tel" name="lacres_transporte_row[]" class="form-control" value="{{ json_encode($i->lacresTransp->pluck('numero')->toArray()) }}">
                            </td>
                            <td>
                                <input style="width: 150px" readonly type="tel" name="lacres_unidade_row[]" class="form-control" value="{{ json_encode($i->lacresUnidCarga->pluck('numero')->toArray()) }}">
                            </td>
                            <td>
                                <div style="width: 100px">
                                    <button class="btn btn-sm btn-warning btn-edit-row" type="button">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-delete-row" type="button">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('mdfe.partials.modal_edita_descarregamento')
    <hr>
    <div class="row mt-4 rodape">
        <div class="col-md-6">
            {!! Form::text('info_complementar', 'Informação complementar (opcional)') !!}
        </div>
        <div class="col-md-6">
            {!! Form::text('info_adicional_fisco', 'Informação fiscal (opcional)') !!}
        </div>
        <div class="col-12 alerts mt-4">
        </div>
    </div>

    <div class="col-12" style="text-align: right;">
        <button type="submit" disabled class="btn btn-success btn-salvarMdfe px-5">Salvar</button>
    </div>
</div>

@section('js')
<script src="/js/mdfe.js"></script>
@endsection
