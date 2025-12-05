<div class="row">
    <input type="hidden" id="clientes" value="{{json_encode($clientes)}}" name="">

    <div class="col-md-12">
        <ul class="nav nav-tabs nav-primary" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#dados_iniciais" role="tab" aria-selected="true">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-user me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-settings-fill"></i>
                            Dados Iniciais
                        </div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#referencia_cte" role="tab" aria-selected="false">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-shopping-cart me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-file-paper-fill"></i>
                            Referência de Documento para CTe
                        </div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#info_carga" role="tab" aria-selected="false">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-truck me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-truck-line"></i>
                            Informações da Carga
                        </div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#info_entrega" role="tab" aria-selected="false">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-money-bill me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-map-2-line"></i>
                            Informações de Entrega
                        </div>
                    </div>
                </a>
            </li>
        </ul>
        <hr>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="dados_iniciais" role="tabpanel">
                <div class="card">
                    <div class="row m-3 g-2">

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

                        <div class="col-md-3">
                            {!!Form::select('natureza_id', 'Natureza de Operação', ['' => 'Selecione'] + $naturezas->pluck('descricao', 'id')->all())->attrs(['class' => 'class-required form-select'])->required()
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('globalizado', 'Tipo Globalizado', [0 => 'Não', 1 => 'Sim'])->attrs(['class' => 'form-select'])->required()
                            !!}
                        </div>
                        <div class="col-md-4">
                            {!! Form::select('cst', 'CST', App\Models\Cte::getCsts())->attrs(['class' => 'form-select']) !!}
                        </div>

                        <div class="col-md-1">
                            {!!Form::tel('perc_icms', '%ICMS')->attrs(['class' => 'percentual class-required'])->required()
                            !!}
                        </div>

                        <div class="col-md-1">
                            {!!Form::tel('cfop', 'CFOP')->attrs(['class' => 'cfop class-required'])->required()
                            !!}
                        </div>

                        <div class="col-md-1">
                            {!!Form::tel('numero', 'Número CTe')
                            ->required()
                            ->value(isset($item) ? $item->numero : $numeroCte)
                            !!}
                        </div>
                        <div class="col-md-1 mb-2">
                            {!!Form::text('perc_red_bc', '%Red. BC')->attrs(['class' => 'percentual'])
                            !!}
                        </div>

                        <div class="col-md-2 mb-2">
                            {!!Form::select('tipo_servico', 'Tipo de serviço', App\Models\Cte::tiposServico())
                            ->attrs(['class' => 'form-select'])
                            ->required()
                            !!}
                        </div>

                        <div class="col-md-2 mb-2">
                            {!!Form::select('tipo_cte', 'Tipo de CTe', App\Models\Cte::tiposDeCte())
                            ->attrs(['class' => 'form-select'])
                            ->required()
                            !!}
                        </div>
                        <hr>
                        <div class="col-md-6">
                            {!! Form::select('remetente_id','Remetente', ['' => 'Selecione'] + $clientes->pluck('razao_social', 'id')->all(),
                            )->attrs(['class' => 'select2 class-required'])->required()
                            ->value(isset($item) ? $item->remetente_id : null) !!}
                            <div class="card border mt-3 div-remetente d-none">
                                <div class="m-3">
                                    <h5 class="text-center text-info">REMETENTE SELECIONADO</h5>
                                    <hr>
                                    <H6>Razão Social: <strong id="razao_social_remetente"></strong></H6>
                                    <H6>CNPJ: <strong id="cnpj_remetente"></strong></H6>
                                    <H6>Cidade: <strong id="cidade_remetente"></strong></H6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {!! Form::select('destinatario_id', 'Destinatário', ['' => 'Selecione'] + $clientes->pluck('razao_social', 'id')->all())->attrs(['class' => 'select2 class-required'])->required()
                            ->value(isset($item) ? $item->destinatario_id : null) !!}
                            <div class="card border mt-3 div-destinatario d-none">
                                <div class="m-3">
                                    <h5 class="text-center text-info">DESTINÁTARIO SELECIONADO</h5>
                                    <hr>
                                    <H6>Razão Social: <strong id="razao_social_destinatario"></strong></H6>
                                    <H6>CNPJ: <strong id="cnpj_destinatario"></strong></H6>
                                    <H6>Cidade: <strong id="cidade_destinatario"></strong></H6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {!! Form::select(
                            'expedidor_id',
                            'Expedidor',
                            ['' => 'Selecione'] + $clientes->pluck('razao_social', 'id')->all(),
                            )->attrs(['class' => 'select2'])
                            ->value(isset($item) ? $item->expedidor_id : null) !!}
                            <div class="card border mt-3 div-expedidor d-none">
                                <div class="m-3">
                                    <h5 class="text-center text-info">EXPEDIDOR SELECIONADO</h5>
                                    <hr>
                                    <H6>Razão Social: <strong id="razao_social_expedidor"></strong></H6>
                                    <H6>CNPJ: <strong id="cnpj_expedidor"></strong></H6>
                                    <H6>Cidade: <strong id="cidade_expedidor"></strong></H6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {!! Form::select(
                            'recebedor_id',
                            'Recebedor',
                            ['' => 'Selecione'] + $clientes->pluck('razao_social', 'id')->all(),
                            )->attrs(['class' => 'select2'])
                            ->value(isset($item) ? $item->recebedor_id : null) !!}
                            <div class="card border mt-3 div-recebedor d-none">
                                <div class="m-3">
                                    <h5 class="text-center text-info">RECEBEDOR SELECIONADO</h5>
                                    <hr>
                                    <H6>Razão Social: <strong id="razao_social_recebedor"></strong></H6>
                                    <H6>CNPJ: <strong id="cnpj_recebedor"></strong></H6>
                                    <H6>Cidade: <strong id="cidade_recebedor"></strong></H6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="referencia_cte" role="tabpanel">
                <div class="card">
                    <div class="col-12">
                        <ul class="nav nav-tabs nav-primary" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#referencia_nfe" role="tab" aria-selected="true">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-user me-2'></i>
                                        </div>
                                        <div class="tab-title">NFe</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#referencia_outros" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-shopping-cart me-2'></i>
                                        </div>
                                        <div class="tab-title">Outros</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="referencia_nfe" role="tabpanel">
                                <div class="table-responsive m-3">
                                    <div class="col-11">
                                        <table class="table table-dynamic table-chave">
                                            <thead>
                                                <tr>
                                                    <th>Chave</th>
                                                    <th>Ação</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($item) && sizeof($item->chaves_nfe) > 0)
                                                @foreach ($item->chaves_nfe as $i)
                                                <tr class="dynamic-form">
                                                    <td>
                                                        <input type="tel" id="chave_nfe" class="form-control class-required" name="chave_nfe[]" value="{{$i->chave}}">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm btn-remove-tr">
                                                            <i class=" ri-delete-bin-5-line"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @else
                                                <tr class="dynamic-form">
                                                    <td>
                                                        <input type="tel" id="chave_nfe" class="form-control class-required" name="chave_nfe[]">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm btn-remove-tr">
                                                            <i class=" ri-delete-bin-5-line"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row col-11">
                                        <div class="col-11">
                                            <button type="button" class="btn btn-success btn-add-tr">
                                                Adicionar chave
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="referencia_outros" role="tabpanel">
                                <div class="row m-3">
                                    <div class="col-md-3">
                                        {!! Form::select('tpDoc', 'Tipo', [null => 'Selecione'] + [
                                        '00' => 'Declaração',
                                        '10' => 'Dutoviário',
                                        '59' => 'Cf-e SAT',
                                        '65' => 'NFCe',
                                        '99' => 'Outros',
                                        ])->attrs(['class' => 'form-select class-outros class-required'])
                                        ->value(isset($item) ? $item->tpDoc : '') !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::text('descOutros', 'Descrição doc.')->attrs(['class' => 'class-outros class-required'])->value(isset($item) ? $item->descOutros : '' ) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::tel('nDoc', 'Número doc.')->attrs(['class' => 'class-outros class-required'])->value(isset($item) ? $item->nDoc : '' ) !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::tel('vDocFisc', 'Valor doc.')->attrs(['class' => 'moeda class-outros class-required'])->value(isset($item) ? __moeda($item->vDocFisc) : '' ) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card">
                    <div class="card-body">

                        <div class="row g-2">
                            <div class="col-md-6">
                                {!! Form::text('referencia_cte', 'Referência')->attrs(['class' => 'chave']) !!}
                            </div>
                            <div class="col-md-2">
                                {!! Form::text('n_doc_anterior', 'Nº doc. anterior')->attrs(['class' => '']) !!}
                            </div>
                            <div class="col-md-2">
                                {!! Form::text('serie_anterior', 'Série doc. anterior')->attrs(['class' => '']) !!}
                            </div>

                            <div class="col-md-2">
                                {!! Form::select('tp_doc_anterior', 'Tipo documento Anterior', App\Models\Cte::tiposDocumentoAnterior())
                                ->attrs(['class' => 'form-select']) !!}
                            </div>
                            <div class="col-md-4">
                                {!! Form::text('emitente_anterior', 'Emitente anterior nome')->attrs(['class' => '']) !!}
                            </div>

                            <div class="col-md-2">
                                {!! Form::text('doc_anterior', 'Emitente anterior CPF/CNPJ')->attrs(['class' => 'cpf_cnpj']) !!}
                            </div>

                            <div class="col-md-2">
                                {!! Form::text('ie_anterior', 'Emitente anterior I.E')->attrs(['class' => '']) !!}
                            </div>
                            
                            <div class="col-md-2">
                                {!! Form::select('uf_anterior', 'Emitente anterior UF', App\Models\Cidade::estados())
                                ->attrs(['class' => 'select2']) !!}
                            </div>
                            <div class="col-md-2">
                                {!! Form::date('data_emissao_anterior', 'Data de emissão anterior')->attrs(['class' => '']) !!}
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="info_carga" role="tabpanel">
                <div class="card">
                    <div class="row m-3">
                        <div class="col-md-2">
                            {!! Form::select('veiculo_id', 'Veículo', ['' => 'Selecione'] + $veiculos->pluck('placa', 'id')
                            ->all())->attrs(['class' => 'form-select'])->value(isset($item) ? $item->veiculo_id : '' )
                            ->required() !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('produto_predominante', 'Produto predominante')->required()->value(isset($item) ? $item->produto_predominante : '' )->attrs(['class' => 'class-cargas class-required']) !!}
                        </div>
                        <div class="col-md-2">
                            {!! Form::tel('valor_carga', 'Valor carga')->attrs(['class' => 'moeda class-cargas class-required'])->value(isset($item) ? __moeda($item->valor_carga) : '')
                            ->required() !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::select('modal', 'Modelo de transporte',
                            App\Models\Cte::modals())->attrs(['class' => 'form-select'])->required()->value(isset($item) ? $item->modal : '') !!}
                        </div>

                        <div class="col-md-2">
                            {!! Form::select('tomador', 'Tomador', App\Models\Cte::tiposTomador())->attrs(['class' => 'form-select'])->value(isset($item) ? $item->tomador : '')
                            ->required() !!}
                        </div>

                        <div class="dados-tomador d-none row g-2">
                            <br>
                            <hr>
                            <h5>Dados do tomador</h5>

                            <div class="col-md-3">
                                {!! Form::text('razao_social_tomador', 'Razão social')->value(isset($item) ? $item->razao_social_tomador : '' )->attrs(['class' => '']) !!}
                            </div>
                            <div class="col-md-3">
                                {!! Form::text('nome_fantasia_tomador', 'Nome fantasia')->value(isset($item) ? $item->nome_fantasia_tomador : '' )->attrs(['class' => '']) !!}
                            </div>


                            <div class="col-md-2">
                                {!! Form::text('cpf_cnpj_tomador', 'CPF/CNPJ')->value(isset($item) ? $item->cpf_cnpj_tomador : '' )->attrs(['class' => 'cpf_cnpj']) !!}
                            </div>

                            <div class="col-md-2">
                                {!! Form::text('ie_tomador', 'I.E')->attrs(['class' => '']) !!}
                            </div>

                            <div class="col-md-2">
                                {!! Form::text('telefone_tomador', 'Telefone')->attrs(['class' => 'fone']) !!}
                            </div>

                            <div class="col-md-3">
                                {!! Form::text('email_tomador', 'Email')->attrs(['class' => ''])->type('email') !!}
                            </div>
                        </div>
                        <hr class="mt-5">
                        <div class="table-responsive">
                            <h5>Informações de quantidade:</h5>
                            <div class="row mt-0">
                                <table class="table table-striped table-informacoes table-dynamic" id="prod">
                                    <thead>
                                        <tr>
                                            <th>Unidade</th>
                                            <th>Tipo de medida</th>
                                            <th>Quantidade</th>
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody id="body" class="datatable-body">
                                        @if(isset($item) && sizeof($item->medidas) > 0)
                                        @foreach($item->medidas as $med)
                                        <tr class="dynamic-form">
                                            <td class="col-md-3">
                                                {!! Form::select('cod_unidade[]', '', $unidadesMedida)->attrs(['class' => 'form-select'])
                                                ->required()->value($med->cod_unidade) !!}
                                            </td>
                                            <td class="col-md-4">
                                                {!! Form::select('tipo_medida[]', '', $tiposMedida)->attrs(['class' => 'form-select'])
                                                ->required()->value($med->tipo_medida) !!}
                                            </td>
                                            <td class="col-md-2">
                                                {!! Form::tel('quantidade_carga[]', '')->attrs(['class' => 'moeda'])
                                                ->required()->value(__moeda($med->quantidade)) !!}
                                            </td>
                                            <td>
                                                <br>
                                                <button class="btn btn-danger btn-sm btn-remove-tr">
                                                    <i class="ri-delete-bin-5-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr class="dynamic-form">
                                            <td class="col-md-2">
                                                {!! Form::select('cod_unidade[]', '', $unidadesMedida)->attrs(['class' => 'select2'])->required() !!}
                                            </td>
                                            <td class="col-md-3">
                                                {!! Form::select('tipo_medida[]', '', $tiposMedida)->attrs(['class' => 'select2'])->required() !!}
                                            </td>
                                            <td class="col-md-2">
                                                {!! Form::tel('quantidade_carga[]', '')->attrs(['class' => 'moeda class-cargas class-required'])->required() !!}
                                            </td>
                                            <td>
                                                <br>
                                                <button class="btn btn-danger btn-sm btn-remove-tr">
                                                    <i class="ri-delete-bin-5-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-success btn-add-tr">
                                        Adicionar
                                    </button>
                                </div>
                            </div>
                            <br>
                        </div>
                        <hr class="mt-4">
                        <div class="table-responsive">
                            <h5>Componentes de carga:</h5>
                            <p class="mt-1" style="color: crimson">*A soma dos valores dos componentes deve ser igual ao valor a receber!</p>
                            <div class="row">
                                <table class="table table-striped table-componentes table-dynamic" id="componentes">
                                    <thead>
                                        <tr>
                                            <th>Nome do componente</th>
                                            <th>Valor</th>
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody id="body" class="datatable-body">
                                        @if(isset($item) && sizeof($item->componentes) > 0)
                                        @foreach($item->componentes as $cp)
                                        <tr class="dynamic-form">
                                            <td class="col-md-5">
                                                {!! Form::text('nome_componente[]', '')->required()->value($cp->nome) !!}
                                            </td>
                                            <td class="col-md-4">
                                                {!! Form::text('valor_componente[]', '')->attrs(['class' => 'moeda'])->required()
                                                ->value(__moeda($cp->valor))
                                                !!}
                                            </td>
                                            <td>
                                                <br>
                                                <button class="btn btn-danger btn-sm btn-remove-tr">
                                                    <i class="ri-delete-bin-5-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr class="dynamic-form">
                                            <td class="col-md-3">
                                                {!! Form::text('nome_componente[]', '')->attrs(['class' => 'class-cargas class-required'])->required() !!}
                                            </td>
                                            <td class="col-md-2">
                                                {!! Form::text('valor_componente[]', '')->attrs(['class' => 'moeda class-cargas class-required'])->required() !!}
                                            </td>
                                            <td>
                                                <br>
                                                <button class="btn btn-danger btn-sm btn-remove-tr">
                                                    <i class="ri-delete-bin-5-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-success btn-add-tr">
                                        Adicionar
                                    </button>
                                </div>
                            </div>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="info_entrega" role="tabpanel">
                <div class="card">
                    <div class="row m-3">
                        <h5 class="" style="color: rgb(1, 3, 7)">Endereço do tomador</h5>
                        <div class="col-md-9">
                            {!! Form::radio('tipo', 'Endereço do destinatário')
                            ->value('destinatario')->checked(isset($item) ? $item->tomador == 3 : false) !!}
                        </div>
                        <div class="col-md-9 mt-1">
                            {!! Form::radio('tipo', 'Endereço do remetente')
                            ->value('remetente')->checked(isset($item) ? $item->tomador == 0 : false) !!}
                        </div>
                        <div class="col-md-6 mt-3">
                            {!! Form::text('logradouro_tomador', 'Rua')->required()->value(isset($item) ? $item->logradouro_tomador : '') !!}
                        </div>
                        <div class="col-md-1 mt-3">
                            {!! Form::text('numero_tomador', 'Número')->required()->value(isset($item) ? $item->numero_tomador : '') !!}
                        </div>
                        <div class="col-md-2 mt-3">
                            {!! Form::text('cep_tomador', 'CEP')->attrs(['class' => 'cep'])->required()->value(isset($item) ? $item->cep_tomador : '') !!}
                        </div>
                        <div class="col-md-3 mt-3">
                            {!! Form::text('bairro_tomador', 'Bairro')->required()->value(isset($item) ? $item->bairro_tomador : '') !!}
                        </div>
                        <div class="col-md-5 mt-3">
                            {!! Form::select('municipio_tomador', 'Cidade', ['' => 'Selecione'] + $cidades->pluck('info', 'id')->all())->attrs([
                            'class' => 'select2',
                            ])->required()->value(isset($item) ? $item->municipio_tomador : '') !!}
                        </div>
                        <div class="col-md-3 mt-3">
                            {!! Form::date('data_prevista_entrega', 'Data prevista de entrega')->required()->value(isset($item) ? $item->data_prevista_entrega : '') !!}
                        </div>

                        <div class="col-md-2 mt-3">
                            {!! Form::tel('valor_transporte', 'Valor da prestação de serviço')->required()->attrs(['class' => 'moeda'])->value(isset($item) ? __moeda($item->valor_transporte) : '') !!}
                        </div>
                        <div class="col-md-2 mt-3">
                            {!! Form::tel('valor_receber', 'Valor a receber')->attrs(['class' => 'moeda'])->required()->value(isset($item) ? __moeda($item->valor_receber) : '') !!}
                        </div>
                        <div class="col-md-4 mt-3">
                            {!! Form::select('municipio_envio', 'Município de envio', ['' => 'Selecione'] + $cidades->pluck('info', 'id')->all())->attrs([
                            'class' => 'select2',
                            ])->required()->value(isset($item) ? $item->municipio_envio : '') !!}
                        </div>
                        <div class="col-md-4 mt-3">
                            {!! Form::select('municipio_inicio', 'Município de início', ['' => 'Selecione'] + $cidades->pluck('info', 'id')->all())->attrs([
                            'class' => 'select2',
                            ])->required()->value(isset($item) ? $item->municipio_inicio : '') !!}
                        </div>
                        <div class="col-md-4 mt-3">
                            {!! Form::select('municipio_fim', 'Município final', ['' => 'Selecione'] + $cidades->pluck('info', 'id')->all())->attrs([
                            'class' => 'select2',
                            ])->required()->value(isset($item) ? $item->municipio_fim : '') !!}
                        </div>
                        <div class="col-md-2 mt-3">
                            {!! Form::select('retira', 'Retira', [0 => 'Não', 1 => 'Sim'])->attrs(['class' => 'select2'])->value(isset($item) ? $item->retira : '') !!}
                        </div>
                        <div class="col-md-10 mt-3">
                            {!! Form::text('detalhes_retira', 'Detalhes (opcional)')->value(isset($item) ? $item->detalhes_retira : '') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr class="mt-4">
    <div class="col-12 alerts mt-4">
    </div>
    <div class="col-md-12 alert alert-secondary mt-4">
        {!! Form::text('observacao', 'Informação adicional')->value(isset($item) ? $item->observacao : '') !!}
    </div>


    <hr class="mt-4">
    @isset($item)
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success btn-salvarCte px-5 m-3">Salvar</button>
    </div>
    @else
    <div class="col-12" style="text-align: right;">
        <button type="submit" disabled class="btn btn-success btn-salvarCte px-5 m-3">Salvar</button>
    </div>
    @endif

</div>
