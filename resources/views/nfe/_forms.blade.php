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
        @isset($item)
        @if($item->fornecedor)
        @php
        $isCompra = 1;
        @endphp
        @endif
        @endif
        @isset($isCompra)
        <input type="hidden" id="is_compra" name="is_compra" value="1">
        @endif

        @isset($isOrdemServico)
        <input type="hidden" name="ordem_servico_id" value="{{$item->id}}">
        @endif

        @isset($isPedidoEcommerce)
        <input type="hidden" name="pedido_ecommerce_id" value="{{$item->id}}">
        @endif

        @isset($isPedidoMercadoLivre)
        <input type="hidden" name="pedido_mercado_livre_id" value="{{$item->id}}">
        @endif

        @isset($isPedidoVendiZap)
        <input type="hidden" name="pedido_vendizap_id" value="{{$item->id}}">
        @endif

        @isset($isPedidoNuvemShop)
        <input type="hidden" name="pedido_nuvem_shop_id" value="{{$item->id}}">
        @endif

        @isset($cotacao)
        <input type="hidden" name="cotacao_id" value="{{$cotacao->id}}">
        @endif

        @isset($isReserva)
        <input type="hidden" name="reserva_id" value="{{$item->id}}">
        @endif

        @isset($isPedidoWoocommerce)
        <input type="hidden" name="pedido_woocommerce_id" value="{{$item->id}}">
        @endif

        @isset($orcamentosId)
        @foreach($orcamentosId as $i)
        <input type="hidden" name="orcamento_id[]" value="{{ $i }}">
        @endforeach
        @endif
        <input type="hidden" id="lista_id" value="" name="lista_id">

        <ul class="nav nav-tabs nav-primary" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#cliente" role="tab" aria-selected="true">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-user me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-file-user-fill"></i>
                            @isset($isCompra) Fornecedor @else Cliente @endif
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
                <a class="nav-link" data-bs-toggle="tab" href="#transportadora" role="tab" aria-selected="false">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-truck me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-truck-line"></i>
                            Frete
                        </div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#fatura" role="tab" aria-selected="false">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-money-bill me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-coins-line"></i>
                            Fatura
                        </div>
                    </div>
                </a>
            </li>
        </ul>
        <hr>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="cliente" role="tabpanel">
                <div class="card">
                    @if(!isset($isCompra))
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
                        <hr class="mt-3">
                        <div class="row d-cliente">
                            <div class="col-md-3">
                                {!!Form::text('cliente_nome', 'Razão Social')->attrs(['class' => ''])->required()
                                ->value(isset($item) ? $item->cliente->razao_social : '')
                                !!}
                            </div>
                            <div class="col-md-3">
                                {!!Form::text('nome_fantasia', 'Nome Fantasia')->attrs(['class' => ''])
                                ->value(isset($item) ? $item->cliente->nome_fantasia : '')
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('cliente_cpf_cnpj', 'CPF/CNPJ')->attrs(['class' => 'cpf_cnpj'])->required()
                                ->value(isset($item) ? $item->cliente->cpf_cnpj : '')
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::text('ie', 'IE')->attrs(['class' => ''])
                                ->value(isset($item) ? $item->cliente->ie : '')
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('telefone', 'Fone')->attrs(['class' => 'fone'])
                                ->value(isset($item) ? $item->cliente->telefone : '')
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::select('contribuinte', 'Contribuinte', [0 => 'Não', 1 => 'Sim'])->attrs(['class' => 'form-select'])
                                ->value(isset($item) ? $item->cliente->contribuinte : '')
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::select('consumidor_final', 'Consumidor Final', [0 => 'Não', 1 => 'Sim'])->attrs(['class' => 'form-select'])->required()
                                ->value(isset($item) ? $item->cliente->consumidor_final : '')
                                !!}
                            </div>
                            <div class="col-md-4 mt-3">
                                {!!Form::text('email', 'E-mail')->attrs(['class' => ''])
                                ->value(isset($item) ? $item->cliente->email : '')
                                !!}
                            </div>
                            <div class="col-md-4 mt-3">
                                <label for="">Cidade</label>
                                <select required class="form-control select2 cidade_id" name="cliente_cidade" id="inp-cidade_cliente">
                                    <option value="">Selecione..</option>
                                    @foreach ($cidades as $c)
                                    <option @isset($item) @if($item->cliente->cidade_id == $c->id) selected @endif @endisset value="{{$c->id}}">{{$c->nome}} - {{$c->uf}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-3">
                                {!!Form::text('cliente_rua', 'Rua')->attrs(['class' => ''])->required()
                                ->value(isset($item) ? $item->cliente->rua : '')
                                !!}
                            </div>
                            <div class="col-md-1 mt-3">
                                {!!Form::text('cliente_numero', 'Número')->attrs(['class' => ''])->required()
                                ->value(isset($item) ? $item->cliente->numero : '')
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::text('cep', 'CEP')->attrs(['class' => 'cep'])->required()
                                ->value(isset($item) ? $item->cliente->cep : '')
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::text('cliente_bairro', 'Bairro')->attrs(['class' => ''])->required()
                                ->value(isset($item) ? $item->cliente->bairro : '')
                                !!}
                            </div>
                            <div class="col-md-4 mt-3">
                                {!!Form::text('complemento', 'Complemento')->attrs(['class' => ''])
                                ->value(isset($item) ? $item->cliente->complemento : '')
                                !!}
                            </div>
                        </div>
                    </div>

                    @else

                    <div class="row m-3">

                        <div class="col-md-5">
                            <label>Fornecedor</label>
                            <div class="input-group flex-nowrap">
                                <select id="inp-fornecedor_id" name="fornecedor_id" class="fornecedor_id">
                                    @isset($cotacao)
                                    <option value="{{ $cotacao->fornecedor_id }}">{{ $cotacao->fornecedor->razao_social }}</option>

                                    @else
                                    @isset($item)
                                    <option value="{{ $item->fornecedor_id }}">{{ $item->fornecedor->razao_social }}</option>
                                    @endif
                                    @endif

                                </select>
                                @can('fornecedores_create')
                                <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modal_novo_fornecedor" type="button">
                                    <i class="ri-add-circle-fill"></i>
                                </button>
                                @endcan
                            </div>
                        </div>
                        
                        <hr class="mt-3">
                        <div class="row d-cliente">
                            <div class="col-md-3">
                                {!!Form::text('fornecedor_nome', 'Razão Social')->attrs(['class' => ''])->required()
                                ->value(isset($item) ? $item->fornecedor->razao_social : '')
                                !!}
                            </div>
                            <div class="col-md-3">
                                {!!Form::text('nome_fantasia', 'Nome Fantasia')->attrs(['class' => ''])->required()
                                ->value(isset($item) ? $item->fornecedor->nome_fantasia : '')
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('fornecedor_cpf_cnpj', 'CPF/CNPJ')->attrs(['class' => 'cpf_cnpj'])->required()
                                ->value(isset($item) ? $item->fornecedor->cpf_cnpj : '')
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::text('ie', 'IE')->attrs(['class' => ''])
                                ->value(isset($item) ? $item->fornecedor->ie : '')
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('telefone', 'Fone')->attrs(['class' => 'fone'])
                                ->value(isset($item) ? $item->fornecedor->telefone : '')
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::select('contribuinte', 'Contribuinte', [0 => 'Não', 1 => 'Sim'])->attrs(['class' => 'form-select'])
                                ->value(isset($item) ? $item->fornecedor->contribuinte : '')
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::select('consumidor_final', 'Consumidor Final', [0 => 'Não', 1 => 'Sim'])->attrs(['class' => 'form-select'])->required()
                                ->value(isset($item) ? $item->fornecedor->consumidor_final : '')
                                !!}
                            </div>
                            <div class="col-md-4 mt-3">
                                {!!Form::text('email', 'E-mail')->attrs(['class' => ''])
                                ->value(isset($item) ? $item->fornecedor->email : '')
                                !!}
                            </div>
                            <div class="col-md-4 mt-3">
                                <label for="">Cidade</label>
                                <select required class="form-control select2 cidade_id" name="fornecedor_cidade" id="inp-fornecedor_cidade">
                                    <option value="">Selecione..</option>
                                    @foreach ($cidades as $c)
                                    <option @isset($item) @if($item->fornecedor->cidade_id == $c->id) selected @endif @endisset value="{{$c->id}}">{{$c->nome}} - {{$c->uf}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-3">
                                {!!Form::text('fornecedor_rua', 'Rua')->attrs(['class' => ''])->required()
                                ->value(isset($item) ? $item->fornecedor->rua : '')
                                !!}
                            </div>
                            <div class="col-md-1 mt-3">
                                {!!Form::text('fornecedor_numero', 'Número')->attrs(['class' => ''])->required()
                                ->value(isset($item) ? $item->fornecedor->numero : '')
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::text('cep', 'CEP')->attrs(['class' => 'cep'])->required()
                                ->value(isset($item) ? $item->fornecedor->cep : '')
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::text('fornecedor_bairro', 'Bairro')->attrs(['class' => ''])->required()
                                ->value(isset($item) ? $item->fornecedor->bairro : '')
                                !!}
                            </div>
                            <div class="col-md-4 mt-3">
                                {!!Form::text('complemento', 'Complemento')->attrs(['class' => ''])
                                ->value(isset($item) ? $item->fornecedor->complemento : '')
                                !!}
                            </div>
                        </div>
                    </div>

                    @endif
                </div>
            </div>
            <div class="tab-pane fade" id="produtos" role="tabpanel">
                <div class="card">
                    <div class="row m-3">
                        @isset($funcionarios)
                        <div class="col-md-3 col-12">
                            <button type="button" class="btn btn-sm btn-light w-100 mt-1" data-bs-toggle="modal" data-bs-target="#lista_precos"><i class="ri-cash-line"></i> Lista de preços</button>
                        </div>
                        <h5 class="mt-3 text-muted lista_selecionada"></h5>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-dynamic table-produtos" style="width: 2800px">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="sticky-col first-col">Produto</th>
                                        <th>Quantidade</th>
                                        <th>Valor Unit.</th>
                                        <th>Subtotal</th>
                                        <th>%ICMS</th>
                                        <th>%PIS</th>
                                        <th>%COFINS</th>
                                        <th>%IPI</th>
                                        <th>%RED BC</th>
                                        <th>CFOP</th>
                                        <th>NCM</th>
                                        <th>Código benefício</th>
                                        <th>CST CSOSN</th>
                                        <th>CST PIS</th>
                                        <th>CST COFINS</th>
                                        <th>CST IPI</th>
                                        <th>Nº do pedido</th>
                                        <th>Nº item do pedido</th>
                                        <th>Informação adicional do item</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @if(isset($item))

                                    @foreach ($item->itens as $key => $prod)

                                    @isset($isOrdemServico)
                                    @include('ordem_servico.partials.itens', ['prod' => $prod])
                                    @elseif(isset($isPedidoEcommerce))
                                    @include('pedido_ecommerce.partials.itens', ['prod' => $prod, 'cfop_estadual' => $item->cliente->cidade->uf])
                                    @elseif(isset($isPedidoMercadoLivre))
                                    @include('mercado_livre_pedidos.partials.itens', ['prod' => $prod, 'cfop_estadual' => $item->cliente->cidade->uf])
                                    @elseif(isset($isPedidoVendiZap))
                                    @include('vendizap_pedidos.partials.itens', ['prod' => $prod])

                                    @elseif(isset($isReserva))
                                    @include('mercado_livre_pedidos.partials.itens', ['prod' => $prod, 'cfop_estadual' => $item->cliente->cidade->uf])
                                    @elseif(isset($isPedidoWoocommerce))
                                    @include('woocommerce_pedidos.partials.itens', ['prod' => $prod, 'cfop_estadual' => $item->cliente->cidade->uf])
                                    @elseif(isset($isPedidoNuvemShop))
                                    @include('woocommerce_pedidos.partials.itens', ['prod' => $prod, 'cfop_estadual' => $item->cliente->cidade->uf])
                                    @else

                                    <tr class="dynamic-form">
                                        <td class="sticky-col first-col">
                                            <input type="hidden" class="_key" name="_key[]" value="{{ $key }}">
                                            <div class="dimensoes-hidden dh_{{ $key }}">
                                                @if(isset($prod->itensDimensao) && sizeof($prod->itensDimensao) > 0)
                                                @foreach($prod->itensDimensao as $it)
                                                <input type='hidden' name='dimensao_altura[]' value='{{ $it->altura }}' />
                                                <input type='hidden' name='dimensao_espessura[]' value='{{ $it->espessura }}' />
                                                <input type='hidden' name='dimensao_largura[]' value='{{ $it->largura }}' />
                                                <input type='hidden' name='dimensao_m2_total[]' value='{{ $it->m2_total }}' />
                                                <input type='hidden' name='dimensao_observacao[]' value='{{ $it->observacao }}' />
                                                <input type='hidden' name='dimensao_quantidade[]' value='{{ $it->quantidade }}' />
                                                <input type='hidden' name='dimensao_sub_total[]' value='{{ $it->sub_total }}' />
                                                <input type='hidden' name='dimensao_valor_unitario_m2[]' value='{{ $it->valor_unitario_m2 }}' />
                                                @endforeach
                                                @endif

                                                <input type='hidden' name='_line[]' value='{{ $key }}' />

                                            </div>
                                            <select class="form-control select2 produto_id" name="produto_id[]" id="inp-produto_id">
                                                <option value="{{ $prod->produto_id }}">{{ $prod->produto->nome }}</option>
                                            </select>
                                            @if($prod->variacao_id)
                                            <span>variação: <strong>{{ $prod->produtoVariacao->descricao }}</strong></span>
                                            @endif
                                            <input name="variacao_id[]" type="hidden" value="{{ $prod->variacao_id }}">
                                            <div style="width: 500px;"></div>
                                            @if(isset($prod->itensDimensao) && sizeof($prod->itensDimensao) > 0)
                                            <a onclick="alterarDimensoesItem('{{ $prod->id }}', '{{ $key }}')" href="#!">Alterar dimensões</a>
                                            @endif

                                            @isset($isCompra)
                                            <input type="hidden" value="{{ $prod->dadosImportacaoEdit() }}" name="dados_importacao[]" class="dados_importacao">
                                            <a href="#!" class="mt-1 adiciona-di">Definir dados de importação</a>
                                            @endif

                                            @if(!isset($isCompra))
                                            <input type="hidden" value="{{ $prod->descricao }}" name="descricao[]" class="descricao_item">
                                            <a href="#!" class="mt-1 alterar-descricao">Alterar descrição do item</a>
                                            <p class="text-danger nova_descricao">{{ $prod->descricao }}</p>
                                            @endif
                                        </td>
                                        <td width="80">
                                            <input style="width: 150px" value="{{ __qtd($prod->quantidade) }}" class="form-control qtd next" type="tel" name="quantidade[]" id="inp-quantidade">
                                        </td>
                                        <td width="100">
                                            <input style="width: 150px" value="{{ __moeda($prod->valor_unitario) }}" class="form-control moeda valor_unit next" type="tel" name="valor_unitario[]" id="inp-valor_unitario">
                                        </td>
                                        <td width="150">
                                            <input style="width: 150px" value="{{ __moeda($prod->sub_total) }}" class="form-control moeda sub_total next" type="tel" name="sub_total[]" id="inp-subtotal">
                                        </td>
                                        <td width="120">
                                            <input style="width: 120px" value="{{ $prod->perc_icms }}" class="form-control percentual" type="tel" name="perc_icms[]" id="inp-perc_icms">
                                        </td>
                                        <td width="120">
                                            <input style="width: 120px" value="{{ $prod->perc_pis }}" class="form-control percentual" type="tel" name="perc_pis[]" id="inp-perc_pis">
                                        </td>
                                        <td width="120">
                                            <input style="width: 120px" value="{{ $prod->perc_cofins }}" class="form-control percentual" type="tel" name="perc_cofins[]" id="inp-perc_cofins">
                                        </td>
                                        <td width="120">
                                            <input style="width: 120px" value="{{ $prod->perc_ipi }}" class="form-control percentual perc_ipi" type="tel" name="perc_ipi[]" id="inp-perc_ipi">
                                        </td>
                                        <td width="120">
                                            <input style="width: 120px" value="{{ $prod->perc_red_bc }}" class="form-control percentual ignore" type="tel" name="perc_red_bc[]" id="inp-perc_red_bc">
                                        </td>
                                        <td width="120">
                                            <input style="width: 120px" required value="{{ $prod->cfop }}" class="form-control cfop" type="tel" name="cfop[]" id="inp-cfop_estadual">
                                        </td>

                                        <td width="150">
                                            <input style="width: 120px" required value="{{ $prod->ncm }}" class="form-control ncm" type="tel" name="ncm[]" id="inp-ncm2">
                                        </td>
                                        <td width="120">
                                            <input style="width: 120px" value="{{ $prod->codigo_beneficio_fiscal }}" class="form-control ignore codigo_beneficio_fiscal" type="text" name="codigo_beneficio_fiscal[]">
                                        </td>

                                        <td>
                                            <select name="cst_csosn[]" class="form-control select2">
                                                @foreach(App\Models\Produto::listaCSTCSOSN() as $key => $c)
                                                <option @if($prod->cst_csosn == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                            <div style="width: 400px"></div>
                                        </td>
                                        <td>
                                            <select name="cst_pis[]" class="form-control select2">
                                                @foreach(App\Models\Produto::listaCST_PIS_COFINS() as $key => $c)
                                                <option @if($prod->cst_pis == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                            <div style="width: 400px"></div>
                                        </td>
                                        <td>
                                            <select name="cst_cofins[]" class="form-control select2">
                                                @foreach(App\Models\Produto::listaCST_PIS_COFINS() as $key => $c)
                                                <option @if($prod->cst_cofins == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                            <div style="width: 400px"></div>
                                        </td>
                                        <td>
                                            <select name="cst_ipi[]" class="form-control select2">
                                                @foreach(App\Models\Produto::listaCST_IPI() as $key => $c)
                                                <option @if($prod->cst_ipi == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                            <div style="width: 400px"></div>
                                        </td>
                                        <td>
                                            <input class="form-control ignore" value="{{ $prod->xPed }}" maxlength="15" type="text" name="xPed[]">
                                            <div style="width: 200px"></div>
                                        </td>
                                        <td>
                                            <input class="form-control ignore" value="{{ $prod->nItemPed }}" maxlength="6" type="text" name="nItemPed[]">
                                            <div style="width: 200px"></div>
                                        </td>
                                        <td>
                                            <input class="form-control ignore" value="{{ $prod->infAdProd }}" maxlength="200" type="text" name="infAdProd[]">
                                            <div style="width: 300px"></div>
                                        </td>
                                        <td width="30">
                                            <button class="btn btn-danger btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endisset
                                    @endforeach

                                    @elseif(isset($cotacao))
                                    @foreach ($cotacao->itens as $prod)
                                    @include('cotacoes.partials.itens', ['prod' => $prod, 'mesmo_estado' => $cotacao->fornecedor->cidade->uf == $empresa->cidade->uf])
                                    @endforeach

                                    @else
                                    <tr class="dynamic-form">
                                        <td class="sticky-col first-col">
                                            <input type="hidden" class="_key" name="_key[]" value="0">
                                            <div class="dimensoes-hidden">

                                            </div>
                                            <select required class="form-control select2 produto_id" name="produto_id[]" id="inp-produto_id">
                                            </select>
                                            <div style="width: 400px;"></div>
                                            <input name="variacao_id[]" type="hidden" value="">

                                            @isset($isCompra)
                                            <input type="hidden" name="dados_importacao[]" class="dados_importacao">
                                            <a href="#!" class="mt-1 adiciona-di">Definir dados de importação</a>
                                            @endif

                                            @if(!isset($isCompra))
                                            <input type="hidden" value="" name="descricao[]" class="descricao_item">
                                            <a href="#!" class="mt-1 alterar-descricao">Alterar descrição do item</a>
                                            <p class="text-danger nova_descricao"></p>
                                            @endif
                                        </td>
                                        <td width="120">
                                            <input style="width: 120px" class="form-control qtd next" type="tel" name="quantidade[]" id="inp-quantidade">
                                        </td>
                                        <td width="150">
                                            <input style="width: 120px" class="form-control moeda valor_unit next" type="tel" name="valor_unitario[]" id="inp-valor_unitario">
                                        </td>
                                        <td width="150">
                                            <input style="width: 120px" readonly class="form-control moeda sub_total sub_total_new next" type="tel" name="sub_total[]" id="inp-subtotal">
                                        </td>
                                        <td width="120">
                                            <input style="width: 120px" class="form-control percentual" type="tel" name="perc_icms[]" id="inp-perc_icms">
                                        </td>
                                        <td width="120">
                                            <input style="width: 120px" class="form-control percentual" type="tel" name="perc_pis[]" id="inp-perc_pis">
                                        </td>
                                        <td width="120">
                                            <input style="width: 120px" class="form-control percentual" type="tel" name="perc_cofins[]" id="inp-perc_cofins">
                                        </td>
                                        <td width="120">
                                            <input style="width: 120px" class="form-control percentual perc_ipi" type="tel" name="perc_ipi[]" id="inp-perc_ipi">
                                        </td>
                                        <td width="120">
                                            <input style="width: 120px" class="form-control percentual ignore" type="tel" name="perc_red_bc[]" id="inp-perc_red_bc">
                                        </td>
                                        <td width="150">
                                            <input style="width: 120px" required class="form-control cfop" type="tel" name="cfop[]" id="inp-cfop_estadual">
                                        </td>

                                        <td width="150">
                                            <input style="width: 120px" required class="form-control ncm" type="tel" name="ncm[]" id="inp-ncm2">
                                        </td>

                                        <td width="120">
                                            <input style="width: 120px" class="form-control codigo_beneficio_fiscal ignore" type="text" name="codigo_beneficio_fiscal[]">
                                        </td>

                                        <td>
                                            <select name="cst_csosn[]" class="form-control select2">
                                                @foreach(App\Models\Produto::listaCSTCSOSN() as $key => $c)
                                                <option value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                            <div style="width: 400px"></div>
                                        </td>
                                        <td>
                                            <select name="cst_pis[]" class="form-control select2">
                                                @foreach(App\Models\Produto::listaCST_PIS_COFINS() as $key => $c)
                                                <option value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                            <div style="width: 400px"></div>
                                        </td>
                                        <td>
                                            <select name="cst_cofins[]" class="form-control select2">
                                                @foreach(App\Models\Produto::listaCST_PIS_COFINS() as $key => $c)
                                                <option value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                            <div style="width: 400px"></div>
                                        </td>
                                        <td>
                                            <select name="cst_ipi[]" class="form-control select2">
                                                @foreach(App\Models\Produto::listaCST_IPI() as $key => $c)
                                                <option value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                            <div style="width: 400px"></div>
                                        </td>
                                        <td>
                                            <input class="form-control ignore" maxlength="15" type="text" name="xPed[]">
                                            <div style="width: 200px"></div>
                                        </td>
                                        <td>
                                            <input class="form-control ignore" maxlength="6" type="text" name="nItemPed[]">
                                            <div style="width: 200px"></div>
                                        </td>
                                        <td>
                                            <input class="form-control ignore" maxlength="200" type="text" name="infAdProd[]">
                                            <div style="width: 300px"></div>
                                        </td>
                                        <td width="30">
                                            <button class="btn btn-danger btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endisset
                                </tbody>
                            </table>
                        </div>
                        <div class="row col-12 col-lg-2 mt-3">
                            <br>
                            <button type="button" class="btn btn-dark btn-add-tr-nfe px-2">
                                <i class="ri-add-fill"></i>
                                Adicionar Produto
                            </button>
                        </div>
                        <div class="mt-3">
                            <h5>Total de Produtos: <strong class="total_prod">R$</strong></h5>
                            <h5 class="d-none h5-ipi">Total de IPI: <strong class="total_ipi">R$ 0,00</strong></h5>
                        </div>
                        <input type="hidden" class="total_prod" name="valor_produtos" id="" value="">

                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="transportadora" role="tabpanel">
                <div class="card">
                    <div class="row m-3">
                        <div class="col-md-5">
                            {!!Form::select('transportadora_id', 'Transportadora',['' => 'Selecione..'] + $transportadoras->pluck('razao_social', 'id')->all())
                            ->attrs(['class' => 'select2 transportadora_id'])
                            !!}
                        </div>
                        <hr class="mt-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                {!!Form::text('razao_social_transp', 'Razão Social')
                                ->value(isset($item->transportadora) ? $item->transportadora->razao_social : '')
                                !!}
                            </div>
                            <div class="col-md-3">
                                {!!Form::text('nome_fantasia_transp', 'Nome Fantasia')
                                ->value(isset($item->transportadora) ? $item->transportadora->nome : '')
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('cpf_cnpj_transp', 'CNPJ')
                                ->attrs(['class' => 'cpf_cnpj'])
                                ->value(isset($item->transportadora) ? $item->transportadora->cpf_cnpj : '')
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('ie_transp', 'Incrição Estadual')
                                ->value(isset($item->transportadora) ? $item->transportadora->ie : '')
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('antt', 'ANTT')
                                ->value(isset($item->transportadora) ? $item->transportadora->antt : '')
                                !!}
                            </div>
                            <div class="col-md-3">
                                {!!Form::tel('rua_transp', 'Rua')
                                ->value(isset($item->transportadora) ? $item->transportadora->rua : '')
                                !!}
                            </div>
                            <div class="col-md-1">
                                {!!Form::tel('numero_transp', 'Número')
                                ->value(isset($item->transportadora) ? $item->transportadora->numero : '')
                                !!}
                            </div>
                            <div class="col-md-3">
                                {!!Form::select('cidade_transp', 'Cidade')
                                ->attrs(['class' => 'select2 cidade_select2'])
                                ->options(isset($item->transportadora) && isset($item->transportadora->cidade) ? [$item->transportadora->cidade_id => $item->transportadora->cidade->nome] : [])
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('cep_transp', 'CEP')
                                ->attrs(['class' => 'cep'])
                                ->value(isset($item->transportadora) ? $item->transportadora->cep : '')
                                !!}
                            </div>
                            <div class="col-md-3">
                                {!!Form::text('email_transp', 'E-mail')
                                ->value(isset($item->transportadora) ? $item->transportadora->email : '')
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('telefone_transp', 'Telefone')
                                ->attrs(['class' => 'fone'])
                                ->value(isset($item->transportadora) ? $item->transportadora->telefone : '')
                                !!}
                            </div>
                            <div class="col-md-3">
                                {!!Form::text('bairro_transp', 'Bairro')
                                ->value(isset($item->transportadora) ? $item->transportadora->bairro : '')
                                !!}
                            </div>
                            <div class="col-md-3">
                                {!!Form::text('complemento_transp', 'Complemento')
                                ->value(isset($item->transportadora) ? $item->transportadora->complemento : '')
                                !!}
                            </div>
                            <hr class="mt-3">
                            <h4 class="mt-3">Informações do Frete</h4>
                            <div class="col-md-2">
                                {!!Form::tel('valor_frete', 'Valor do Frete')
                                ->attrs(['class' => 'moeda valor_frete'])
                                ->value(isset($item) ? __moeda($item->valor_frete) : (isset($cotacao) ? __moeda($cotacao->valor_frete) : ''))
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('qtd_volumes', 'Qtd de Volumes')
                                ->attrs(['class' => ''])
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('numeracao_volumes', 'Número de Volumes')
                                ->attrs(['class' => ''])
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('peso_bruto', 'Peso Bruto')
                                ->attrs(['class' => 'peso'])
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('peso_liquido', 'Peso Líquido')
                                ->attrs(['class' => 'peso'])
                                !!}
                            </div>
                            <div class="col-md-3">
                                {!!Form::text('especie', 'Espécie')
                                ->attrs(['class' => ''])
                                !!}
                            </div>
                            <div class="col-md-3">
                                {!!Form::text('marca', 'Marca')
                                ->attrs(['class' => ''])
                                !!}
                            </div>
                            <div class="col-md-3">
                                {!!Form::select('tipo', 'Tipo', App\Models\Nfe::tiposFrete())
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::text('placa', 'Placa')
                                ->attrs(['class' => 'placa'])
                                !!}
                            </div>
                            <div class="col-md-1">
                                {!!Form::select('uf', 'UF', App\Models\Cidade::estados())
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>

                            @if(!isset($isCompra))
                            <hr class="mt-3">
                            <h4 class="mt-3">Endereço de Entrega</h4>
                            <div class="col-md-12">
                                {!!Form::checkbox('endereco_entrega_dados_cliente', 'Usar dados do cliente')
                                ->attrs(['class' => ''])
                                !!}
                            </div>

                            <div class="col-md-3">
                                {!!Form::tel('nome_entrega', 'Nome')
                                ->attrs(['class' => ''])
                                !!}
                            </div>
                            <div class="col-md-3">
                                {!!Form::tel('documento_entrega', 'CPF\CNPJ')
                                ->attrs(['class' => 'cpf_cnpj'])
                                !!}
                            </div>
                            <div class="col-md-4">
                                {!!Form::tel('rua_entrega', 'Rua')
                                ->attrs(['class' => ''])
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('numero_entrega', 'Número')
                                ->attrs(['class' => ''])
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('bairro_entrega', 'Bairro')
                                ->attrs(['class' => ''])
                                !!}
                            </div>
                            <div class="col-md-3">
                                {!!Form::select('cidade_id_entrega', 'Cidade')
                                ->attrs(['class' => 'cidade_select2'])
                                ->options(isset($item) && $item->cidadeEntrega ? [ $item->cidadeEntrega->id => $item->cidadeEntrega->info ] : [])
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('cep_entrega', 'CEP')
                                ->attrs(['class' => 'cep'])
                                !!}
                            </div>

                            <div class="col-md-4">
                                {!!Form::tel('complemento_entrega', 'Complemento')
                                ->attrs(['class' => ''])
                                !!}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="fatura" role="tabpanel">
                <div class="card">
                    <div class="row g-2 m-2">
                        <div class="col-md-3">
                            {!!Form::select('natureza_id', 'Natureza de Operação', ['' => 'Selecione'] + $naturezas->pluck('descricao', 'id')->all())
                            ->attrs(['class' => 'form-select'])
                            ->value(isset($item) ? $item->natureza_id : (isset($naturezaPadrao) && $naturezaPadrao != null ? $naturezaPadrao->id : '') )
                            ->required()
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('acrescimo', 'Acréscimo')
                            ->attrs(['class' =>'acrescimo moeda'])
                            ->value(isset($item) ? __moeda($item->acrescimo) : '')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('desconto', 'Desconto')
                            ->attrs(['class' => 'desconto moeda'])
                            ->value(isset($item) ? __moeda($item->desconto) : '')
                            !!}
                        </div>
                        <div class="col-md-5">
                            {!!Form::text('observacao', 'Observação')
                            ->attrs(['class' => ''])
                            !!}
                        </div>

                        @if(__isPlanoFiscal())
                        @if(isset($isOrdemServico) || isset($isPedidoEcommerce) || isset($isPedidoMercadoLivre) || isset($isReserva) || isset($isPedidoVendiZap))
                        <div class="col-md-2 mt-3">
                            {!!Form::tel('numero_nfe', 'Número NFe')
                            ->required()
                            ->value($numeroNfe)
                            !!}
                        </div>
                        @else
                        <div class="col-md-2 mt-3">
                            {!!Form::tel('numero_nfe', 'Número NFe')
                            ->required()
                            ->value(isset($item) ? $item->numero : $numeroNfe)
                            !!}
                        </div>
                        @endif

                        <div class="col-md-10 mt-3">
                            {!!Form::tel('referencia', 'Referência NFe')
                            !!}
                        </div>
                        
                        <div class="col-md-2 mt-3">
                            {!!Form::date('data_emissao_saida', 'Data Emissão Saída')
                            !!}
                        </div>

                        <div class="col-md-2 mt-3">
                            {!!Form::date('data_emissao_retroativa', 'Data Emissão Retroativa')
                            !!}
                        </div>

                        @if(isset($isCompra) && isset($item) && $item->chave_importada)
                        <div class="col-md-2 mt-3">
                            {!!Form::tel('data_emissao', 'Data de emissão')
                            ->value(isset($item) ? __data_pt($item->data_emissao) : '')
                            ->attrs(['data-mask' => '00/00/0000 00:00'])
                            !!}
                        </div>
                        @endif

                        <div class="col-md-2 mt-3">
                            {!!Form::date('data_entrega', 'Data de Entrega')
                            !!}
                        </div>

                        @if(!isset($isCompra))
                        <div class="col-md-2 mt-3">
                            {!!Form::select('tpNF', 'Tipo NFe', ['1' => 'Saída', '0' => 'Entrada'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        @else
                        <div class="col-md-2 mt-3">
                            {!!Form::select('tpNF', 'Tipo NFe', ['0' => 'Entrada'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        @endif


                        <div class="col-md-2 mt-3">
                            {!!Form::select('finNFe', 'Finalidade NFe', [
                            '1' => 'NFe normal',
                            '2' => 'NFe complementar',
                            '3' => 'NFe de ajuste',
                            '4' => 'Devolução de mercadoria'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        @endif
                        
                        @if(!isset($isCompra))
                        @if(!isset($item) && $isOrcamento == 0)
                        <div class="col-md-2 mt-3 div-conta-receber">
                            {!!Form::select('gerar_conta_receber', 'Gerar conta a receber', [0 => 'Não', 1 => 'Sim'])
                            ->attrs(['class' => 'form-select'])
                            ->value($config ? $config->gerar_conta_receber_padrao : 1)
                            !!}
                        </div>
                        @else
                        @if(isset($item) && $item->orcamento == 0)
                        @can('conta_receber_create')
                        <div class="col-md-2 mt-3 div-conta-receber">
                            {!!Form::select('gerar_conta_receber', 'Gerar conta a receber', [0 => 'Não', 1 => 'Sim'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        @endcan

                        @endif
                        @endif

                        @else

                        @can('conta_pagar_create')
                        <div class="col-md-2 mt-3 div-conta-pagar d-none">
                            {!!Form::select('gerar_conta_pagar', 'Gerar conta a pagar', [0 => 'Não', 1 => 'Sim'])
                            ->attrs(['class' => 'form-select'])
                            ->value($config ? $config->gerar_conta_pagar_padrao : 1)
                            !!}
                        </div>
                        @endcan
                        @endif

                        @if(isset($isOrcamento) && $isOrcamento == 1)
                        <input type="hidden" value="1" name="orcamento">
                        @else
                        @if(!isset($isCompra))
                        @if(!isset($item))
                        @can('orcamento_create')
                        <div class="col-md-2 mt-3">
                            {!!Form::select('orcamento', 'Salvar como Orçamento', [
                            0 => 'Não',
                            1 => 'Sim'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        @endcan
                        @endif
                        @endif
                        @endif

                        @if(!isset($isCompra))
                        <div class="col-md-3 mt-3">
                            {!! Form::select('funcionario_id', 'Vendedor')
                            ->options(isset($item) && $item->funcionario ? [$item->funcionario->id => $item->funcionario->nome] : [])
                            !!}
                        </div>
                        @endif

                    </div>
                </div>
                <div class="card mt-1">
                    <div class="row m-3">
                        <div class="col-12">

                            <button type="button" class="btn btn-light px-5 btn-fatura-padrao d-none">
                                <i class="ri-booklet-line"></i>
                                Fatura Padrão do Cliente
                            </button>

                            <button type="button" class="btn btn-dark px-5 btn-gerar-fatura" data-bs-toggle="modal" data-bs-target="#modal_fatura_venda">
                                <i class="ri-list-indefinite"></i>
                                Gerar Fatura
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-dynamic table-fatura" style="width: 800px">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Tipo de Pagamento</th>
                                        <th>Data Vencimento</th>
                                        <th>Valor</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="body-pagamento" class="datatable-body">
                                    @isset($cotacao)
                                    @if(sizeof($cotacao->fatura) > 0)
                                    @foreach ($cotacao->fatura as $f)
                                    <tr class="dynamic-form">
                                        <td width="300">
                                            <select name="tipo_pagamento[]" class="form-control tipo_pagamento select2">
                                                <option value="">Selecione..</option>
                                                @foreach(App\Models\Nfe::tiposPagamento() as $key => $c)
                                                <option @if($f->tipo_pagamento == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="150">
                                            <input value="{{ $f->data_vencimento }}" type="date" class="form-control" name="data_vencimento[]" id="">
                                        </td>
                                        <td width="150">
                                            <input value="{{ __moeda($f->valor) }}" type="tel" class="form-control moeda valor_fatura" name="valor_fatura[]">
                                        </td>
                                        <td width="30">
                                            <button class="btn btn-danger btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr class="dynamic-form">
                                        <td width="300">
                                            <select name="tipo_pagamento[]" class="form-control tipo_pagamento select2">
                                                <option value="">Selecione..</option>
                                                @foreach(App\Models\Nfe::tiposPagamento() as $key => $c)
                                                <option value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="150">
                                            <input type="date" class="form-control date_atual" name="data_vencimento[]" id="" value="">
                                        </td>
                                        <td width="150">
                                            <input type="tel" class="form-control moeda valor_fatura" name="valor_fatura[]" id="valor">
                                        </td>
                                        <td width="30">
                                            <button class="btn btn-danger btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                    
                                    @else
                                    @if(isset($item) && isset($item->fatura) && sizeof($item->fatura) > 0 && !isset($isOrdemServico))
                                    @foreach ($item->fatura as $f)
                                    <tr class="dynamic-form">
                                        <td width="300">
                                            <select name="tipo_pagamento[]" class="form-control tipo_pagamento select2">
                                                <option value="">Selecione..</option>
                                                @foreach(App\Models\Nfe::tiposPagamento() as $key => $c)
                                                <option @if($f->tipo_pagamento == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="150">
                                            <input value="{{ $f->data_vencimento }}" type="date" class="form-control" name="data_vencimento[]" id="">
                                        </td>
                                        <td width="150">
                                            <input value="{{ __moeda($f->valor) }}" type="tel" class="form-control moeda valor_fatura" name="valor_fatura[]" id="valor">
                                        </td>
                                        <td width="30">
                                            <button class="btn btn-danger btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else

                                    <tr class="dynamic-form">
                                        <td width="300">
                                            <select name="tipo_pagamento[]" class="form-control tipo_pagamento select2">
                                                <option value="">Selecione..</option>
                                                @foreach(App\Models\Nfe::tiposPagamento() as $key => $c)
                                                <option value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="150">
                                            <input type="date" class="form-control date_atual" name="data_vencimento[]" id="" value="">
                                        </td>
                                        <td width="150">
                                            <input type="tel" class="form-control moeda valor_fatura" name="valor_fatura[]" id="valor">
                                        </td>
                                        <td width="30">
                                            <button class="btn btn-danger btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    @endif
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-info btn-add-tr px-5">
                                    <i class="ri-add-fill"></i>
                                    Adicionar Pagamento
                                </button>
                            </div>
                        </div>
                        <div class="col-3 mt-4">
                            <h5>Total da Fatura: <strong class="total_fatura">R$</strong></h5>
                        </div>
                        <div class="col-3 mt-4">
                            <h5>Total de Produtos: <strong class="total_prod">R$</strong></h5>
                        </div>
                        <div class="col-3 mt-4">
                            <h5>Total do Frete: <strong class="total_frete">R$</strong></h5>
                        </div>
                        <div class="col-3 mt-4">
                            <h5>Total da NFe: <strong class="total_nfe text-success">R$</strong></h5>
                        </div>
                        <input type="hidden" class="valor_total" name="valor_total" id="" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success btn-salvar-nfe px-5 m-3 btn-action-form">
            Salvar
        </button>
    </div>
</div>

@include('modals._cartao_credito', ['not_submit' => true])
@include('modals._variacao')
@include('modals._fatura_venda')
@include('modals._lista_precos')


