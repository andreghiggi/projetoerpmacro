<div class="row">
    <div class="col-md-12">

        <div class="row m-3">
            <div class="col-md-5">
                <label class="required">Cliente</label>
                <div class="input-group flex-nowrap">
                    <select required id="inp-cliente_id" name="cliente_id" class="cliente_id">
                        @if(isset($item) && $item->cliente)
                        <option value="{{ $item->cliente_id }}">{{ $item->cliente->razao_social }}</option>
                        @endif
                    </select>
                    @can('clientes_create')
                    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modal_novo_cliente" type="button">
                        <i class="ri-add-circle-fill"></i>
                    </button>
                    @endcan
                </div>
            </div>

            <div class="col-md-3">
                {!! Form::select('veiculo_id', 'Veículo', ['' => 'Selecione'] + $veiculos->pluck('info', 'id')->all())
                ->id('veiculo')
                ->attrs(['class' => 'select2'])
                ->required() !!}
            </div>

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
        </div>

        <div class="m-3">
            <ul class="nav nav-tabs nav-primary" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#dados-iniciais" role="tab" aria-selected="true">
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
                    <a class="nav-link" data-bs-toggle="tab" href="#despesas" role="tab" aria-selected="false">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='fa fa-shopping-cart me-2'></i>
                            </div>
                            <div class="tab-title">
                                <i class="ri-coins-line"></i>
                                Despesas
                            </div>
                        </div>
                    </a>
                </li>
            </ul>

            <hr>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="dados-iniciais" role="tabpanel">
                    <div class="row g-2">

                        <div class="col-md-2">
                            {!!Form::tel('total', 'Valor total do frete')->required()
                            ->attrs(['class' => 'moeda'])
                            ->value(isset($item) ? __moeda($item->total) : '')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('desconto', 'Desconto')
                            ->attrs(['class' => 'moeda'])
                            ->value(isset($item) ? __moeda($item->desconto) : '')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('acrescimo', 'Acréscimo')
                            ->attrs(['class' => 'moeda'])
                            ->value(isset($item) ? __moeda($item->acrescimo) : '')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('estado', 'Estado', ['' => 'Selecione', 
                            'em_carregamento' => 'Em carregamento', 'em_viagem' => 'Em viagem', 'finalizado' => 'Finalizado'])
                            ->attrs(['class' => 'form-select'])
                            ->required()
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::date('data_inicio', 'Dt. início viagem')->required()
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::date('data_fim', 'Dt. final viagem')->required()
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::time('horario_inicio', 'Horário início')!!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::time('horario_fim', 'Horário fim')!!}
                        </div>

                        <div class="col-md-3">
                            {!!Form::select('cidade_carregamento', 'Cidade carregamento', 
                            ['' => 'Selecione a cidade'] + $cidades->pluck('info', 'id')->all())
                            ->attrs(['class' => 'select2'])
                            ->required()
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::select('cidade_descarregamento', 'Cidade descarregamento', 
                            ['' => 'Selecione a cidade'] + $cidades->pluck('info', 'id')->all())
                            ->attrs(['class' => 'select2'])
                            ->required()
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::tel('distancia_km', 'Distância KM')->required()
                            !!}
                        </div>
                        <div class="col-md-12">
                            {!!Form::text('observacao', 'Observação')
                            !!}
                        </div>

                    </div>
                </div>

                <div class="tab-pane fade " id="despesas" role="tabpanel">

                    <div class="table-responsive mt-4">
                        <table class="table table-dynamic">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tipo de Despesa</th>
                                    <th>Fornecedor</th>
                                    <th>Valor</th>
                                    <th>Observação</th>
                                    <th>Ação</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($item) && sizeof($item->despesas) > 0)
                                @foreach($item->despesas as $d)
                                <tr class="dynamic-form">
                                    <td>
                                        <select class="select2" name="tipo_despesa_id[]">
                                            <option value="">Selecione</option>
                                            @foreach($tiposDespesas as $t)
                                            <option @if($t->id == $d->tipo_despesa_id) selected @endif value="{{ $t->id }}">{{ $t->nome }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="max-width: 400px">
                                        <select class="fornecedor_id ignore" name="fornecedor_id[]">

                                            @if($d->fornecedor)
                                            <option value="{{ $d->fornecedor_id }}">
                                                {{ $d->fornecedor->info }}
                                            </option>
                                            @endif
                                        </select>
                                    </td>

                                    <td style="width: 130px">
                                        <input type="tel" class="form-control valor moeda" name="valor_despesa[]" value="{{ __moeda($d->valor) }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control ignore" name="observacao_despesa[]" value="{{ $d->observacao }}">
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
                                        <select class="select2" name="tipo_despesa_id[]">
                                            <option value="">Selecione</option>
                                            @foreach($tiposDespesas as $t)
                                            <option value="{{ $t->id }}">{{ $t->nome }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="max-width: 400px">
                                        <select class="fornecedor_id ignore" name="fornecedor_id[]">
                                        </select>
                                    </td>

                                    <td style="width: 130px">
                                        <input type="tel" class="form-control valor moeda" name="valor_despesa[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control ignore" name="observacao_despesa[]">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm btn-remove-tr">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">Soma</td>
                                    <td class="total-despesa text-primary" colspan="3">
                                        @isset($item)
                                        R$ {{ __moeda($item->total_despesa) }}
                                        @else
                                        R$ 0,00
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <br>
                            <button type="button" class="btn btn-dark btn-add-line">
                                <i class="ri-add-line"></i>
                                Adicionar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success btn-salvar-nfe px-5 m-3">Salvar</button>
    </div>
</div>