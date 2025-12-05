<div class="row">
    <div class="col-md-12">

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
                            Mão de obra
                        </div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#servicos-terceiro" role="tab" aria-selected="false">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-shopping-cart me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-list-settings-line"></i>
                            Serviços de Terceiros
                        </div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#custos-adm" role="tab" aria-selected="false">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-shopping-cart me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-refund-2-line"></i>
                            Custos Administrativos
                        </div>
                    </div>
                </a>
            </li>
        </ul>

        <hr>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="produtos" role="tabpanel">
                <div class="card">
                    <div class="row m-3">

                        <div class="col-md-2">
                            {!!Form::tel('percentual_produtos', '% Sobre Produtos')
                            ->value('0')
                            ->attrs(['class' => 'percentual'])
                            !!}
                        </div>

                        <div class="table-responsive mt-2">
                            <table class="table table-dynamic table-produtos">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="sticky-col first-col">Produto</th>
                                        <th>Quantidade</th>
                                        <th>Valor Unit. Custo</th>
                                        <th>Subtotal Custo</th>
                                        <th>Observação</th>
                                        <th>Valor Unit. Final</th>
                                        <th>Subtotal Final</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($item->produtos as $produto)
                                    <tr class="dynamic-form">

                                        <td class="sticky-col first-col">
                                            <input type="hidden" name="produto_id[]" value="{{ $produto->produto_id }}">
                                            <input style="width: 350px" class="form-control" value="{{ $produto->descricao() }}" type="text" name="descricao_produto[]">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control qtd moeda" value="{{ __moeda($produto->quantidade) }}" type="tel" name="quantidade_produto[]">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control moeda valor_unitario moeda" type="tel" name="valor_unitario_produto[]" value="{{ __moeda($produto->valor_unitario) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" readonly class="form-control moeda sub_total moeda" type="tel" name="sub_total_produto[]" value="{{ __moeda($produto->sub_total) }}">
                                        </td>
                                        <td>
                                            <input style="width: 300px" class="form-control ignore" type="text" name="observacao_produto[]" value="{{ $produto->observacao }}">
                                        </td>

                                        <td>
                                            <input style="width: 150px" class="form-control moeda valor_unitario_final moeda" type="tel" name="valor_unitario_final_produto[]" value="{{ __moeda($produto->valor_unitario) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" readonly class="form-control moeda sub_total_final moeda" type="tel" name="sub_total_final_produto[]" value="{{ __moeda($produto->sub_total) }}">
                                        </td>
                                        <td>
                                            <button class="btn btn-danger btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>

                                    </tr>
                                    @endforeach
                                    
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3">
                                            Soma
                                        </td>
                                        <td colspan="3" class="text-muted">
                                            <strong class="text-danger soma-custo">R$ {{ __moeda($item->produtos->sum('sub_total')) }}</strong>
                                        </td>
                                        <td class="text-muted">
                                            <strong class="text-success soma-final">R$ {{ __moeda($item->produtos->sum('sub_total')) }}</strong>
                                        </td>
                                    </tr>
                                    
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="servicos" role="tabpanel">
                <div class="card">
                    <div class="row m-3">

                        <div class="col-md-2">
                            {!!Form::tel('percentual_servicos', '% Sobre Mão de Obra')
                            ->value('0')
                            ->attrs(['class' => 'percentual'])
                            !!}
                        </div>
                        <div class="table-responsive mt-2">
                            <table class="table table-dynamic table-servicos">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="sticky-col first-col">Serviço</th>
                                        <th>Quantidade</th>
                                        <th>Valor Unit.</th>
                                        <th>Subtotal</th>
                                        <th>Observação</th>
                                        <th>Valor Unit. Final</th>
                                        <th>Subtotal Final</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($item->servicos as $servico)

                                    <tr class="dynamic-form">

                                        <td class="sticky-col first-col">
                                            <input type="hidden" name="servico_id[]" value="{{ $servico->servico_id }}">
                                            <input style="width: 350px" class="form-control" value="{{ $servico->servico->nome }}" type="text" name="descricao_servico[]">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control qtd moeda" type="tel" name="quantidade_servico[]" value="{{ __moeda($servico->quantidade) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control moeda valor_unitario moeda" type="tel" name="valor_unitario_servico[]" value="{{ __moeda($servico->valor_unitario) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" readonly class="form-control moeda sub_total moeda" type="tel" name="sub_total_servico[]" value="{{ __moeda($servico->sub_total) }}">
                                        </td>
                                        
                                        <td>
                                            <input style="width: 300px" class="form-control ignore" type="text" name="observacao_servico[]" value="{{ $servico->observacao }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control moeda valor_unitario_final moeda" type="tel" name="valor_unitario_final_servico[]" value="{{ __moeda($servico->valor_unitario) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" readonly class="form-control moeda sub_total_final moeda" type="tel" name="sub_total_final_servico[]" value="{{ __moeda($servico->sub_total) }}">
                                        </td>
                                        <td>
                                            <button class="btn btn-danger btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3">
                                            Soma
                                        </td>
                                        <td colspan="3" class="text-muted">
                                            <strong class="text-danger soma-custo">R$ {{ __moeda($item->servicos->sum('sub_total')) }}</strong>
                                        </td>
                                        <td class="text-muted">
                                            <strong class="text-success soma-final">R$ {{ __moeda($item->servicos->sum('sub_total')) }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="servicos-terceiro" role="tabpanel">
                <div class="card">
                    <div class="row m-3">
                        <div class="col-md-2">
                            {!!Form::tel('percentual_servicos_terceiro', '% Sobre Serviços Terceiro')
                            ->value('0')
                            ->attrs(['class' => 'percentual'])
                            !!}
                        </div>
                        <div class="table-responsive mt-2">
                            <table class="table table-dynamic table-servicos-terceiro">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="sticky-col first-col">Serviço</th>
                                        <th>Quantidade</th>
                                        <th>Valor Unit.</th>
                                        <th>Subtotal</th>
                                        <th>Observação</th>
                                        <th>Valor Unit. Final</th>
                                        <th>Subtotal Final</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($item->servicosTerceiro as $servico)

                                    <tr class="dynamic-form">
                                        <td class="sticky-col first-col">
                                            <input type="hidden" name="servico_id[]" value="{{ $servico->servico_id }}">
                                            <input style="width: 350px" class="form-control" value="{{ $servico->servico->nome }}" type="text" name="descricao_servico_terceiro[]">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control qtd moeda" type="tel" name="quantidade_servico_terceiro[]" value="{{ __moeda($servico->quantidade) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control moeda valor_unitario moeda" type="tel" name="valor_unitario_servico_terceiro[]" value="{{ __moeda($servico->valor_unitario) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" readonly class="form-control moeda sub_total moeda" type="tel" name="sub_total_servico_terceiro[]" value="{{ __moeda($servico->sub_total) }}">
                                        </td>
                                        <td>
                                            <input style="width: 300px" class="form-control ignore" type="text" name="observacao_servico_terceiro[]" value="{{ $servico->observacao }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control moeda valor_unitario_final moeda" type="tel" name="valor_unitario_final_servico_terceiro[]" value="{{ __moeda($servico->valor_unitario) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" readonly class="form-control moeda sub_total_final moeda" type="tel" name="sub_total_final_servico_terceiro[]" value="{{ __moeda($servico->sub_total) }}">
                                        </td>
                                        <td>
                                            <button class="btn btn-danger btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3">
                                            Soma
                                        </td>
                                        <td colspan="3" class="text-muted">
                                            <strong class="text-danger soma-custo">R$ {{ __moeda($item->servicosTerceiro->sum('sub_total')) }}</strong>
                                        </td>
                                        <td class="text-muted">
                                            <strong class="text-success soma-final">R$ {{ __moeda($item->servicosTerceiro->sum('sub_total')) }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="custos-adm" role="tabpanel">
                <div class="card">
                    <div class="row m-3">
                        <div class="col-md-2">
                            {!!Form::tel('percentual_custos_adm', '% Sobre Custos Administrativos')
                            ->value('0')
                            ->attrs(['class' => 'percentual'])
                            !!}
                        </div>
                        <div class="table-responsive mt-2">
                            <table class="table table-dynamic table-custos-adm">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="sticky-col first-col">Descricão</th>
                                        <th>Quantidade</th>
                                        <th>Valor Unit.</th>
                                        <th>Subtotal</th>
                                        <th>Observação</th>
                                        <th>Valor Unit. Final</th>
                                        <th>Subtotal Final</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($item->custosAdm as $c)

                                    <tr class="dynamic-form">
                                        <td class="sticky-col">
                                            <input type="" value="{{ $c->descricao }}" class="form-control" name="descricao_custo_adm[]">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control qtd moeda" type="tel" name="quantidade_custo_adm[]" value="{{ __moeda($c->quantidade) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control moeda valor_unitario moeda" type="tel" name="valor_unitario_custo_adm[]" value="{{ __moeda($c->valor_unitario) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" readonly class="form-control moeda sub_total moeda" type="tel" name="sub_total_custo_adm[]" value="{{ __moeda($c->sub_total) }}">
                                        </td>

                                        <td>
                                            <input style="width: 300px" class="form-control ignore" type="text" name="observacao_custo_adm[]" value="{{ $c->observacao }}">
                                        </td>

                                        <td>
                                            <input style="width: 150px" class="form-control moeda valor_unitario_final moeda" type="tel" name="valor_unitario_final_custo_adm[]" value="{{ __moeda($c->valor_unitario) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" readonly class="form-control moeda sub_total_final moeda" type="tel" name="sub_total_final_custo_adm[]" value="{{ __moeda($c->sub_total) }}">
                                        </td>
                                        <td>
                                            <button class="btn btn-danger btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3">
                                            Soma
                                        </td>
                                        <td colspan="3" class="text-muted">
                                            <strong class="text-danger soma-custo">R$ {{ __moeda($item->custosAdm->sum('sub_total')) }}</strong>
                                        </td>
                                        <td class="text-muted">
                                            <strong class="text-success soma-final">R$ {{ __moeda($item->custosAdm->sum('sub_total')) }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row m-2">
            <div class="col-md-12">

                <div class="float-end">
                    <p><b>TOTAL CUSTO</b> <strong class="total-custo-geral text-danger"></strong></p>
                    <p><b>TOTAL FINAL</b> <strong class="total-final-geral text-success"></strong></p>


                </div>
            </div>
        </div>
        <div class="row m-2">

            <div class="col-md-2">
                {!!Form::tel('desconto', 'Desconto')
                ->attrs(['class' => 'moeda'])
                !!}
            </div>

            <div class="col-md-2">
                {!!Form::tel('frete', 'Frete')
                ->attrs(['class' => 'moeda'])
                !!}
            </div>
        </div>

        <hr class="mt-4">
        <div class="col-12" style="text-align: right;">
            <button type="submit" class="btn btn-success btn-salvar px-5 m-3">Salvar</button>
        </div>
    </div>
</div>