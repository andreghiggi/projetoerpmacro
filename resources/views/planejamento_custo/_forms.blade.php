@section('css')
<style type="text/css">
    input[type="file"] {
        display: none;
    }

    .file label {
        padding: 8px 8px;
        width: 100%;
        background-color: #8833FF;
        color: #FFF;
        text-transform: uppercase;
        text-align: center;
        display: block;
        margin-top: 20px;
        cursor: pointer;
        border-radius: 5px;
    }

    .card-body strong{
        color: #8833FF;
    }

</style>
@endsection

@if(__countLocalAtivo() > 1 && __escolheLocalidade())
<div class="row mb-2">
    <div class="col-md-3">
        <label for="">Local</label>
        <select id="inp-local_id" required class="select2 class-required" data-toggle="select2" name="local_id">
            <option value="">Selecione</option>
            @foreach(__getLocaisAtivoUsuario() as $local)
            <option @isset($item) @if($item->local_id == $local->id) selected @endif @endif value="{{ $local->id }}">{{ $local->descricao }}</option>
            @endforeach
        </select>
    </div>
</div>
@endif


<div class="row">
    <div class="col-md-12">

        <ul class="nav nav-tabs nav-primary" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#info-basica" role="tab" aria-selected="true">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-user me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-settings-line"></i>
                            Informações Básicas
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
                            <i class="ri-box-2-line"></i>
                            Produtos
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
            
        </ul>
        <hr>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="info-basica" role="tabpanel">
                <div class="card">
                    <div class="row m-3 g-2">


                        <div class="col-md-6">
                            {!!Form::select('projeto_id', 'Projeto', ['' => 'Selecione'] + $projetos->pluck('info', 'id')->all())
                            ->attrs(['class' => 'select2'])->required()
                            !!}
                        </div>

                        <div class="col-md-3">
                            {!!Form::text('codigo_material', 'Código do material')
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::text('equipamento', 'Equipamento')
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::text('desenho', 'Desenho')
                            !!}
                        </div>
                        
                        <div class="col-md-4">
                            {!!Form::text('material', 'Material')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::text('quantidade', 'Quantidade')
                            ->attrs(['data-mask' => '00000000'])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('unidade', 'Unidade', $unidades->pluck('nome', 'nome')->all())
                            ->attrs(['class' => 'select2'])
                            ->value(isset($item) ? $item->unidade : 'UN')
                            !!}
                        </div>

                        <div class="col-md-3 file">
                            {!! Form::file('file', 'Arquivo')
                            ->attrs(['accept' => '']) !!}
                            <span class="text-danger" id="filename"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="produtos" role="tabpanel">
                <div class="card">
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
                                        <th>Ações</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @isset($item)
                                    @foreach($item->produtos as $produto)
                                    <tr class="dynamic-form">
                                        <td class="sticky-col">
                                            <div class="input-group flex-nowrap">

                                                <select required class="form-control select2 produto_id" name="produto_id[]">
                                                    <option value="{{ $produto->produto_id }}">{{ $produto->produto->nome }}</option>
                                                </select>

                                                <button type="button" class="btn btn-primary btn-sm btn-dimensao">
                                                    <i class="ri-ruler-2-line"></i>
                                                </button>
                                            </div>
                                            <input type="hidden" class="largura" name="largura[]" value="{{ __moeda($produto->largura) }}">
                                            <input type="hidden" class="espessura" name="espessura[]" value="{{ __moeda($produto->espessura) }}">
                                            <input type="hidden" class="comprimento" name="comprimento[]" value="{{ __moeda($produto->comprimento) }}">
                                            <input type="hidden" class="peso_especifico" name="peso_especifico[]" value="{{ __moeda($produto->peso_especifico) }}">

                                            <input type="hidden" class="peso_bruto" name="peso_bruto[]" value="{{ __moeda($produto->peso_bruto) }}">
                                            
                                            <input type="hidden" class="calculo" name="calculo[]" value="{{ __moeda($produto->caculo) }}">

                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control quantidade_produto moeda" value="{{ number_format($produto->quantidade, 2, ',', '') }}" type="tel" name="quantidade_produto[]">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control moeda valor_unitario_produto moeda" type="tel" name="valor_unitario_produto[]" value="{{ __moeda($produto->valor_unitario) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" readonly class="form-control moeda sub_total_produto moeda" type="tel" name="sub_total_produto[]" value="{{ __moeda($produto->sub_total) }}">
                                        </td>
                                        <td>
                                            <input style="width: 300px" class="form-control ignore" type="text" name="observacao_produto[]" value="{{ $produto->observacao }}">
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
                                            <div class="input-group flex-nowrap">

                                                <select required class="form-control select2 produto_id" name="produto_id[]">
                                                </select>

                                                <button type="button" class="btn btn-primary btn-sm btn-dimensao">
                                                    <i class="ri-ruler-2-line"></i>
                                                </button>
                                            </div>

                                            <input type="hidden" class="largura" name="largura[]">
                                            <input type="hidden" class="espessura" name="espessura[]">
                                            <input type="hidden" class="comprimento" name="comprimento[]">
                                            <input type="hidden" class="peso_especifico" name="peso_especifico[]">
                                            <input type="hidden" class="peso_bruto" name="peso_bruto[]">
                                            <input type="hidden" class="calculo" name="calculo[]">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control quantidade_produto moeda" type="tel" name="quantidade_produto[]">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control moeda valor_unitario_produto moeda" type="tel" name="valor_unitario_produto[]">
                                        </td>
                                        <td>
                                            <input style="width: 150px" readonly class="form-control moeda sub_total_produto moeda" type="tel" name="sub_total_produto[]">
                                        </td>
                                        <td>
                                            <input style="width: 300px" class="form-control ignore" type="text" name="observacao_produto[]">
                                        </td>
                                        <td>
                                            <button class="btn btn-danger btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>

                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        @isset($item)
                                        <td class="text-muted">Soma <strong class="total-produto">R$ {{ __moeda($item->produtos->sum('sub_total')) }}</strong></td>
                                        @else
                                        <td class="text-muted">Soma <strong class="total-produto">R$ 0,00</strong></td>
                                        @endif
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row col-12 col-lg-2 mt-3">
                            <br>
                            <button type="button" class="btn btn-dark btn-add-tr-produto px-2">
                                <i class="ri-add-fill"></i>
                                Adicionar Produto
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="servicos" role="tabpanel">
                <div class="card">
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
                                    @foreach($item->servicos as $servico)

                                    <tr class="dynamic-form">
                                        <td class="sticky-col">
                                            <select required class="form-control select2 servico_id" name="servico_id[]">
                                                <option value="{{ $servico->servico_id }}">{{ $servico->servico->nome }}</option>
                                            </select>
                                            <div style="width: 400px;"></div>
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control quantidade_servico moeda" type="tel" name="quantidade_servico[]" value="{{ __moeda($servico->quantidade) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control moeda valor_unitario_servico moeda" type="tel" name="valor_unitario_servico[]" value="{{ __moeda($servico->valor_unitario) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" readonly class="form-control moeda sub_total_servico moeda" type="tel" name="sub_total_servico[]" value="{{ __moeda($servico->sub_total) }}">
                                        </td>
                                        
                                        <td>
                                            <input style="width: 300px" class="form-control ignore" type="text" name="observacao_servico[]" value="{{ $servico->observacao }}">
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
                                <tfoot>
                                    <tr>
                                        @isset($item)
                                        <td class="text-muted">Soma <strong class="total-servico">R$ {{ __moeda($item->servicos->sum('sub_total')) }}</strong></td>
                                        @else
                                        <td class="text-muted">Soma <strong class="total-servico">R$ 0,00</strong></td>
                                        @endif
                                    </tr>
                                </tfoot>
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
            </div>

            <div class="tab-pane fade" id="servicos-terceiro" role="tabpanel">
                <div class="card">
                    <div class="row m-3">
                        <div class="table-responsive">
                            <table class="table table-dynamic table-servicos-terceiro">
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
                                    @if(isset($item) && sizeof($item->servicosTerceiro) > 0)
                                    @foreach($item->servicosTerceiro as $servico)

                                    <tr class="dynamic-form">
                                        <td class="sticky-col">
                                            <select required class="form-control select2 servico_id" name="servico_terceiro_id[]">
                                                <option value="{{ $servico->servico_id }}">{{ $servico->servico->nome }}</option>
                                            </select>
                                            <div style="width: 400px;"></div>
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control quantidade_servico_terceiro moeda" type="tel" name="quantidade_servico_terceiro[]" value="{{ __moeda($servico->quantidade) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control moeda valor_unitario_servico_terceiro moeda" type="tel" name="valor_unitario_servico_terceiro[]" value="{{ __moeda($servico->valor_unitario) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" readonly class="form-control moeda sub_total_servico_terceiro moeda" type="tel" name="sub_total_servico_terceiro[]" value="{{ __moeda($servico->sub_total) }}">
                                        </td>
                                        <td>
                                            <input style="width: 300px" class="form-control ignore" type="text" name="observacao_servico_terceiro[]" value="{{ $servico->observacao }}">
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
                                            <select required class="form-control select2 servico_id" name="servico_terceiro_id[]">
                                            </select>
                                            <div style="width: 400px;"></div>
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control quantidade_servico_terceiro moeda" type="tel" name="quantidade_servico_terceiro[]">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control moeda valor_unitario_servico_terceiro moeda" type="tel" name="valor_unitario_servico_terceiro[]">
                                        </td>
                                        <td>
                                            <input style="width: 150px" readonly class="form-control moeda sub_total_servico_terceiro moeda" type="tel" name="sub_total_servico_terceiro[]">
                                        </td>

                                        <td>
                                            <input style="width: 300px" class="form-control ignore" type="text" name="observacao_servico_terceiro[]">
                                        </td>
                                        <td>
                                            <button class="btn btn-danger btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        @isset($item)
                                        <td class="text-muted">Soma <strong class="total-servico-terceiro">R$ {{ __moeda($item->servicosTerceiro->sum('sub_total')) }}</strong></td>
                                        @else
                                        <td class="text-muted">Soma <strong class="total-servico-terceiro">R$ 0,00</strong></td>
                                        @endif
                                    </tr>
                                </tfoot>
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
            </div>

            <div class="tab-pane fade" id="custos-adm" role="tabpanel">
                <div class="card">
                    <div class="row m-3">
                        <div class="table-responsive">
                            <table class="table table-dynamic table-custos-adm">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="sticky-col first-col">Descricão</th>
                                        <th>Quantidade</th>
                                        <th>Valor Unit.</th>
                                        <th>Subtotal</th>
                                        <th>Observação</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @if(isset($item) && sizeof($item->custosAdm) > 0)
                                    @foreach($item->custosAdm as $c)


                                    <tr class="dynamic-form">
                                        <td class="sticky-col">
                                            <input type="" value="{{ $c->descricao }}" class="form-control" name="descricao_custo_adm[]">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control quantidade_custo_adm moeda" type="tel" name="quantidade_custo_adm[]" value="{{ __moeda($c->quantidade) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control moeda valor_unitario_custo_adm moeda" type="tel" name="valor_unitario_custo_adm[]" value="{{ __moeda($c->valor_unitario) }}">
                                        </td>
                                        <td>
                                            <input style="width: 150px" readonly class="form-control moeda sub_total_custo_adm moeda" type="tel" name="sub_total_custo_adm[]" value="{{ __moeda($c->sub_total) }}">
                                        </td>

                                        <td>
                                            <input style="width: 300px" class="form-control ignore" type="text" name="observacao_custo_adm[]" value="{{ $c->observacao }}">
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
                                            <input type="" class="form-control" name="descricao_custo_adm[]">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control quantidade_custo_adm moeda" type="tel" name="quantidade_custo_adm[]">
                                        </td>
                                        <td>
                                            <input style="width: 150px" class="form-control moeda valor_unitario_custo_adm moeda" type="tel" name="valor_unitario_custo_adm[]">
                                        </td>
                                        <td>
                                            <input style="width: 150px" readonly class="form-control moeda sub_total_custo_adm moeda" type="tel" name="sub_total_custo_adm[]">
                                        </td>

                                        <td>
                                            <input style="width: 300px" class="form-control ignore" type="text" name="observacao_custo_adm[]">
                                        </td>
                                        <td>
                                            <button class="btn btn-danger btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        @isset($item)
                                        <td class="text-muted">Soma <strong class="total-custos-adm">R$ {{ __moeda($item->custosAdm->sum('sub_total')) }}</strong></td>
                                        @else
                                        <td class="text-muted">Soma <strong class="total-custos-adm">R$ 0,00</strong></td>
                                        @endif
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row col-12 col-lg-2 mt-3">
                            <br>
                            <button type="button" class="btn btn-dark btn-add-tr-servico px-2">
                                <i class="ri-add-fill"></i>
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
        <button type="button" class="btn btn-success btn-salvar px-5 m-3">Salvar</button>
    </div>
</div>