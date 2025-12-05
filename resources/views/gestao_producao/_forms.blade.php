<div class="row">
    <div class="col-md-4">
        {!!Form::select('produto_composto_id', 'Produto', ['' => 'Selecione'] + $produtos->pluck('nome', 'id')->all())
        ->attrs(['class' => 'select2'])->required()->disabled(isset($item) ? true : false)
        ->id('produto_composto_id')
        ->value(isset($item) ? $item->produto_id : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('quantidade', 'Quantidade Produção')
        ->attrs(['data-mask' => '00000000'])
        ->value(isset($item) ? number_format($item->quantidade, 0) : '')
        !!}

    </div>

    <div class="col-md-2">
        <br>
        <button type="button" class="btn btn-primary btn-calcular">
            <i class="ri-search-line"></i>
        </button>
    </div>
    <div class="col-md-12 mt-3">

        <ul class="nav nav-tabs nav-primary" role="tablist">

            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#produtos" role="tab" aria-selected="false">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-shopping-cart me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-box-2-line"></i>
                            Produtos
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
                            <i class="ri-settings-2-fill"></i>
                            Serviços
                        </div>
                    </div>
                </a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#outros" role="tab" aria-selected="false">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-shopping-cart me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-file-edit-fill"></i>
                            Outros Custos
                        </div>
                    </div>
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="produtos" role="tabpanel">

                <div class="row m-3">

                    <div class="table-responsive">
                        <table class="table table-dynamic table-produtos">
                            <thead class="table-dark">
                                <tr>
                                    <th class="sticky-col first-col">Produto</th>
                                    <th>Quantidade</th>
                                    <th>Valor Unit.</th>
                                    <th>Subtotal</th>
                                    <th>Observação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @isset($item)
                                @foreach($item->produtos as $p)
                                <tr>
                                    <td>
                                        {{ $p->produto->nome }}
                                        <input type="hidden" value="{{ $p->produto_id }}" class="produto_id" name="produto_id[]">
                                    </td>
                                    <td>
                                        <input type="tel" readonly class="form-control moeda" name="quantidade_produto[]" value="{{ !$p->produto->unidadeDecimal() ? number_format($p->quantidade, 0, '.', '') : number_format($p->quantidade, 3, '.', '') }}">
                                    </td>

                                    <td>
                                        <input type="tel" class="form-control moeda valor_unitario_produto" name="valor_unitario_produto[]" value="{{ __moedaInput($p->valor_unitario) }}">
                                    </td>
                                    <td>
                                        <input type="tel" class="form-control moeda sub_total_produto" name="sub_total_produto[]" value="{{ __moedaInput($p->sub_total) }}">
                                    </td>

                                    <td>
                                        <input type="text" class="form-control" name="observacao_produto[]" value="{{ $p->observacao }}">
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td class="text-danger">Disponibilidade em estoque</td>
                                    <td class="text-danger">{{ $disponibilidade }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="servicos" role="tabpanel">

                <div class="row m-3">

                    <div class="table-responsive">
                        <table class="table table-dynamic table-servicos">
                            <thead class="table-dark">
                                <tr>
                                    <th class="sticky-col first-col">Serviço</th>
                                    <th>Quantidade</th>
                                    <th>Valor Unit.</th>
                                    <th>Subtotal</th>
                                    <th>Observação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if(isset($item) && sizeof($item->servicos) > 0)
                                @foreach($item->servicos as $s)
                                <tr class="dynamic-form">
                                    <td class="sticky-col">
                                        <select required class="form-control select2 servico_id" name="servico_id[]">
                                            <option value="{{ $s->servico_id }}">{{ $s->servico->nome }}</option>
                                        </select>
                                        <div style="width: 400px;"></div>
                                    </td>
                                    <td>
                                        <input style="width: 150px" class="form-control quantidade_servico moeda" type="tel" name="quantidade_servico[]" value="{{ number_format($s->quantidade, 0, '.', '') }}">
                                    </td>
                                    <td>
                                        <input style="width: 150px" class="form-control moeda valor_unitario_servico moeda" type="tel" name="valor_unitario_servico[]" value="{{ __moedaInput($s->valor_unitario) }}">
                                    </td>
                                    <td>
                                        <input style="width: 150px" readonly class="form-control moeda sub_total_servico moeda" type="tel" name="sub_total_servico[]" value="{{ __moedaInput($s->sub_total) }}">
                                    </td>

                                    <td>
                                        <input style="width: 300px" class="form-control ignore" type="text" name="observacao_servico[]" value="{{ $s->observacao }}">
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-remove-tr">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr class="dynamic-form">
                                    <td class="sticky-col">
                                        <select required class="form-control select2 servico_id" name="servico_id[]">
                                        </select>
                                        <div style="width: 400px;"></div>
                                    </td>
                                    <td>
                                        <input style="width: 150px" class="form-control quantidade_servico moeda" type="tel" name="quantidade_servico[]">
                                    </td>
                                    <td>
                                        <input style="width: 150px" class="form-control moeda valor_unitario_servico moeda" type="tel" name="valor_unitario_servico[]">
                                    </td>
                                    <td>
                                        <input style="width: 150px" readonly class="form-control moeda sub_total_servico moeda" type="tel" name="sub_total_servico[]">
                                    </td>

                                    <td>
                                        <input style="width: 300px" class="form-control ignore" type="text" name="observacao_servico[]">
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-remove-tr">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="row col-12 col-lg-2 mt-3">
                        <br>
                        <button type="button" class="btn btn-dark btn-add-tr-servico px-2">
                            <i class="ri-add-fill"></i>
                            Adicionar Serviço
                        </button>
                    </div>
                </div>
            </div>

            <!-- outros -->

            <div class="tab-pane fade" id="outros" role="tabpanel">

                <div class="row m-3">

                    <div class="table-responsive">
                        <table class="table table-dynamic table-outros">
                            <thead class="table-dark">
                                <tr>
                                    <th class="sticky-col first-col">Descrição</th>
                                    <th>Quantidade</th>
                                    <th>Valor Unit.</th>
                                    <th>Subtotal</th>
                                    <th>Observação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if(isset($item) && sizeof($item->outros) > 0)
                                @foreach($item->outros as $s)
                                <tr class="dynamic-form">
                                    <td class="sticky-col">
                                        <input style="width: 400px" class="form-control" type="text" name="descricao_outros[]" value="{{ $s->descricao }}">
                                    </td>
                                    <td>
                                        <input style="width: 150px" class="form-control quantidade_outros moeda" type="tel" name="quantidade_outros[]" value="{{ number_format($s->quantidade, 0, '.', '') }}">
                                    </td>
                                    <td>
                                        <input style="width: 150px" class="form-control moeda valor_unitario_outros moeda" type="tel" name="valor_unitario_outros[]" value="{{ __moedaInput($s->valor_unitario) }}">
                                    </td>
                                    <td>
                                        <input style="width: 150px" readonly class="form-control moeda sub_total_outros moeda" type="tel" name="sub_total_outros[]" value="{{ __moedaInput($s->sub_total) }}">
                                    </td>

                                    <td>
                                        <input style="width: 300px" class="form-control ignore" type="text" name="observacao_outros[]" value="{{ $s->observacao }}">
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-remove-tr">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr class="dynamic-form">
                                    <td class="sticky-col">
                                        <input style="width: 400px" class="form-control" type="text" name="descricao_outros[]">
                                    </td>
                                    <td>
                                        <input style="width: 150px" class="form-control quantidade_outros moeda" type="tel" name="quantidade_outros[]">
                                    </td>
                                    <td>
                                        <input style="width: 150px" class="form-control moeda valor_unitario_outros moeda" type="tel" name="valor_unitario_outros[]">
                                    </td>
                                    <td>
                                        <input style="width: 150px" readonly class="form-control moeda sub_total_outros moeda" type="tel" name="sub_total_outros[]">
                                    </td>

                                    <td>
                                        <input style="width: 300px" class="form-control ignore" type="text" name="observacao_outros[]">
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-remove-tr">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="row col-12 col-lg-2 mt-3">
                        <br>
                        <button type="button" class="btn btn-dark btn-add-tr px-2">
                            <i class="ri-add-fill"></i>
                            Adicionar Custo
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-md-2">
        {!!Form::text('total_custo_produtos', 'Total custo produtos')
        ->attrs(['class' => 'moeda'])->readonly()->required()
        ->value(isset($item) ? __moeda($item->total_custo_produtos) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('total_custo_servicos', 'Total custo serviços')
        ->attrs(['class' => 'moeda'])->readonly()
        ->value(isset($item) ? __moeda($item->total_custo_servicos) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('total_custo_outros', 'Total custo outros')
        ->attrs(['class' => 'moeda'])->readonly()
        ->value(isset($item) ? __moeda($item->total_custo_outros) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('desconto', 'Desconto')
        ->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->desconto) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('frete', 'Frete')
        ->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->frete) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('total_final', 'Total')
        ->attrs(['class' => 'moeda'])->readonly()->required()
        ->value(isset($item) ? __moeda($item->total_final) : '')
        !!}
    </div>


    <div class="col-md-4 mt-2">
        {!!Form::select('cliente_id', 'Cliente')
        ->options(isset($item) && $item->cliente ? [$item->cliente_id => $item->cliente->info] : [])
        !!}
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="button" class="btn btn-success btn-salvar px-5 m-3">Salvar</button>
    </div>
</div>