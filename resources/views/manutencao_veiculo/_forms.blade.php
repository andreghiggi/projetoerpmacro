<div class="row">
    <div class="col-md-12">

        <div class="row m-3">
            <div class="col-md-5">
                <label class="required">Fornecedor</label>
                <div class="input-group flex-nowrap">
                    <select required id="inp-fornecedor_id" name="fornecedor_id">
                        @if(isset($item) && $item->fornecedor)
                        <option value="{{ $item->fornecedor_id }}">{{ $item->fornecedor->info }}</option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                {!! Form::select('veiculo_id', 'Veículo', ['' => 'Selecione'] + $veiculos->pluck('info', 'id')->all())
                ->id('veiculo')
                ->attrs(['class' => 'select2'])
                ->required() !!}
            </div>

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
                    <a class="nav-link" data-bs-toggle="tab" href="#servicos" role="tab" aria-selected="false">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='fa fa-shopping-cart me-2'></i>
                            </div>
                            <div class="tab-title">
                                <i class="ri-tools-fill"></i>
                                Serviços
                            </div>
                        </div>
                    </a>
                </li>

                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#produtos" role="tab" aria-selected="false">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='fa fa-shopping-cart me-2'></i>
                            </div>
                            <div class="tab-title">
                                <i class="ri-product-hunt-fill"></i>
                                Produtos
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
                            'aguardando' => 'Aguardando', 'em_manutencao' => 'Em manutenção', 'finalizado' => 'Finalizado'])
                            ->attrs(['class' => 'form-select'])
                            ->required()
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::date('data_inicio', 'Data início')->required()
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::date('data_fim', 'Data fim')
                            !!}
                        </div>

                        <div class="col-md-12">
                            {!!Form::text('observacao', 'Observação')
                            !!}
                        </div>

                    </div>
                </div>

                <div class="tab-pane fade" id="servicos" role="tabpanel">

                    <div class="table-responsive mt-4">
                        <table class="table table-dynamic table-servicos">
                            <thead class="table-dark">
                                <tr>
                                    <th>Serviço</th>
                                    <th>Quantidade</th>
                                    <th>Valor unitário</th>
                                    <th>Subtotal</th>
                                    <th>Observação</th>
                                    <th>Ação</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($item) && sizeof($item->servicos) > 0)
                                @foreach($item->servicos as $d)
                                <tr class="dynamic-form">

                                    <td style="width: 250px">
                                        <select class="servico_id" name="servico_id[]">
                                            <option selected value="{{ $d->servico_id }}">
                                                {{ $d->servico->nome }}
                                            </option>
                                        </select>
                                    </td>
                                    <td style="width: 130px">
                                        <input type="tel" class="form-control qtd" name="quantidade_servico[]"
                                        value="{{ __moeda($d->quantidade) }}">
                                    </td>
                                    <td style="width: 130px">
                                        <input type="tel" class="form-control valor_unitario moeda" name="valor_unitario_servico[]" value="{{ __moeda($d->valor_unitario) }}">
                                    </td>
                                    <td style="width: 130px">
                                        <input type="tel" readonly class="form-control sub_total moeda" name="sub_total_servico[]" value="{{ __moeda($d->sub_total) }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control ignore" name="observacao_servico[]" value="{{ $d->observacao }}">
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
                                    <td style="width: 250px">
                                        <select class="servico_id" name="servico_id[]">

                                        </select>
                                    </td>
                                    <td style="width: 130px">
                                        <input type="tel" class="form-control qtd" name="quantidade_servico[]">
                                    </td>
                                    <td style="width: 130px">
                                        <input type="tel" class="form-control valor_unitario moeda" name="valor_unitario_servico[]">
                                    </td>
                                    <td style="width: 130px">
                                        <input type="tel" readonly class="form-control sub_total moeda" name="sub_total_servico[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control ignore" name="observacao_servico[]">
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
                                    <td colspan="3">Soma</td>
                                    <td class="total-servico text-primary" colspan="3">
                                        @isset($item)
                                        R$ {{ __moeda($item->servicos->sum('sub_total')) }}
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
                            <button type="button" class="btn btn-dark btn-add-line-servico">
                                <i class="ri-add-line"></i>
                                Adicionar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="produtos" role="tabpanel">

                    <div class="table-responsive mt-4">
                        <table class="table table-dynamic table-produtos">
                            <thead class="table-dark">
                                <tr>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Valor unitário</th>
                                    <th>Subtotal</th>
                                    <th>Observação</th>
                                    <th>Ação</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($item) && sizeof($item->produtos) > 0)
                                @foreach($item->produtos as $d)
                                <tr class="dynamic-form">

                                    <td style="width: 250px">
                                        <select class="produto_id" name="produto_id[]">
                                            <option selected value="{{ $d->produto_id }}">
                                                {{ $d->produto->nome }}
                                            </option>
                                        </select>
                                    </td>
                                    <td style="width: 130px">
                                        <input type="tel" class="form-control qtd" name="quantidade_produto[]"
                                        value="{{ __moeda($d->quantidade) }}">
                                    </td>
                                    <td style="width: 130px">
                                        <input type="tel" class="form-control valor_unitario moeda" name="valor_unitario_produto[]" value="{{ __moeda($d->valor_unitario) }}">
                                    </td>
                                    <td style="width: 130px">
                                        <input type="tel" readonly class="form-control sub_total moeda" name="sub_total_produto[]" value="{{ __moeda($d->sub_total) }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control ignore" name="observacao_produto[]" value="{{ $d->observacao }}">
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
                                    <td style="width: 250px">
                                        <select class="produto_id" name="produto_id[]">

                                        </select>
                                    </td>
                                    <td style="width: 130px">
                                        <input type="tel" class="form-control qtd" name="quantidade_produto[]">
                                    </td>
                                    <td style="width: 130px">
                                        <input type="tel" class="form-control valor_unitario moeda" name="valor_unitario_produto[]">
                                    </td>
                                    <td style="width: 130px">
                                        <input type="tel" readonly class="form-control sub_total moeda" name="sub_total_produto[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control ignore" name="observacao_produto[]">
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
                                    <td colspan="3">Soma</td>
                                    <td class="total-produto text-primary" colspan="3">
                                        @isset($item)
                                        R$ {{ __moeda($item->produtos->sum('sub_total')) }}
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
                            <button type="button" class="btn btn-dark btn-add-line-produto">
                                <i class="ri-add-line"></i>
                                Adicionar
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            <hr>
            <div class="row">
                <div class="col-md-2">
                    {!!Form::tel('total', 'Valor da manutenção')
                    ->attrs(['class' => 'moeda'])
                    ->value(isset($item) ? __moeda($item->total) : '')
                    ->readonly()
                    !!}
                </div>
            </div>

        </div>
    </div>
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success btn-salvar-nfe px-5 m-3">Salvar</button>
    </div>
</div>