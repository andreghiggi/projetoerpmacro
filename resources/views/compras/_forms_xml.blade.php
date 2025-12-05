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
        @isset($isCompra)
        <input type="hidden" id="is_compra" name="is_compra" value="1">
        @endif

        <input type="hidden" id="is_xml" value="1">
        <ul class="nav nav-tabs nav-primary" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#fornecedor" role="tab" aria-selected="true">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-user me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-file-user-fill"></i>
                            Fornecedor
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
                            Produtos({{ sizeof($dadosXml['itens']) }})
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

        @isset($dadosXml)
        <input type="hidden" name="chave_dfe" value="{{ $dadosXml['chave'] }}">

        @isset($dadosXml['tpNF'])
        <input type="hidden" name="tpNF_importada" value="{{ $dadosXml['tpNF'] }}">
        @endif
        @endif
        <div class="tab-content">
            <div class="tab-pane fade show active" id="fornecedor" role="tabpanel">
                <div class="card">

                    <div class="row m-3">
                        <div class="col-md-5">
                            {!!Form::select('fornecedor_id', 'Fornecedor')->attrs(['class' => 'select2 fornecedor_id'])
                            ->options([$fornecedor->id => $fornecedor->info])
                            !!}
                        </div>
                        <hr class="mt-3">
                        <div class="row">
                            <div class="col-md-3">
                                {!!Form::text('fornecedor_nome', 'Razão Social')->attrs(['class' => ''])->required()
                                ->value(isset($item) ? $item->cliente->razao_social : '')
                                !!}
                            </div>
                            <div class="col-md-3">
                                {!!Form::text('nome_fantasia', 'Nome Fantasia')->attrs(['class' => ''])
                                ->value(isset($item) ? $item->cliente->nome_fantasia : '')
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('fornecedor_cpf_cnpj', 'CPF/CNPJ')->attrs(['class' => 'cpf_cnpj'])->required()
                                ->value(isset($item) ? $item->cliente->cpf_cnpj : '')
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::text('ie', 'IE')->attrs(['class' => ''])->required()
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
                                <select required class="form-control select2 cidade_id" name="fornecedor_cidade" id="inp-fornecedor_cidade">
                                    <option value="">Selecione..</option>
                                    @foreach ($cidades as $c)
                                    <option @isset($item) @if($item->cliente->cidade_id == $c->id) selected @endif @endisset value="{{$c->id}}">{{$c->nome}} - {{$c->uf}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-3">
                                {!!Form::text('fornecedor_rua', 'Rua')->attrs(['class' => ''])->required()
                                ->value(isset($item) ? $item->cliente->rua : '')
                                !!}
                            </div>
                            <div class="col-md-1 mt-3">
                                {!!Form::text('fornecedor_numero', 'Número')->attrs(['class' => ''])->required()
                                ->value(isset($item) ? $item->cliente->numero : '')
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::text('cep', 'CEP')->attrs(['class' => 'cep'])->required()
                                ->value(isset($item) ? $item->cliente->cep : '')
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::text('fornecedor_bairro', 'Bairro')->attrs(['class' => ''])->required()
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

                </div>
            </div>
            <div class="tab-pane fade" id="produtos" role="tabpanel">
                <div class="card">

                    <div class="row m-3">

                        <div class="col-md-3 mt-3">
                            {!!Form::select('padrao_id', 'Padrão de tributação', ['' => 'Selecione'] + $padroes->pluck('descricao', 'id')->all())
                            ->attrs(['class' => 'form-select tooltipp'])
                            !!}
                            <div class="text-tooltip d-none">
                                Se selecionar irá sobrescrever os percentuais e CST do xml
                            </div>
                        </div>

                        <div class="col-md-2 mt-3">
                            <br>
                            <button type="button" class="btn btn-primary btn-padrao-item">Alter padrão por item</button>
                        </div>

                        <div class="col-md-2 mt-3">
                            {!!Form::select('gerenciar_estoque', 'Gerenciar estoque', [1 => 'Sim', 0 => 'Não'])->attrs(['class' => 'form-select'])
                            ->value($configGerenciaEstoque)
                            !!}
                        </div>

                        <div class="col-md-2 mt-3">
                            <br>
                            <button type="button" class="btn btn-dark btn-desvincular-todos">Desvincular todos</button>
                        </div>

                        <div class="table-responsive">
                            <div class="tabela-scroll" style="overflow-x:auto;">
                                <table class="table table-dynamic table-produtos">
                                    <thead class="table-dark">
                                        <tr>
                                            <th class="sticky-col first-col">Produto</th>
                                            <th class="td-padrao d-none">Padrão de tributação</th>
                                            <th>Unidade</th>
                                            <th>Quantidade</th>
                                            <th>Valor de compra</th>
                                            <th>Subtotal</th>
                                            <th>Valor de venda</th>
                                            <th>Conversão para estoque
                                                <button type="button" tabindex="0" style="padding: 1px;" class="btn" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="Utilize se for vender em unidade diferente de compra, exemplo caixa com 12 e vender em unidade" title="Conversão para estoque e valor">
                                                    <i class="ri-file-info-fill text-info" style="font-size: 15px;"></i>
                                                </button>
                                            </th>
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
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($dadosXml['itens'] as $key => $prod)
                                        <tr class="dynamic-form line_{{$key}}">

                                            <td class="sticky-col first-col">
                                                <input class="cadastrar_produto" type="hidden" name="cadastrar_produto[]" value="{{ $prod->id == 0 ? 1 : 0 }}"> 
                                                <input class="produto_nome" type="hidden" name="nome_produto[]" value="{{ $prod->xProd }}">
                                                <input type="hidden" class="_codigo_barras" name="codigo_barras[]" value="{{ $prod->codigo_barras }}">
                                                <input type="hidden" class="_codigo_barras2" name="_codigo_barras2[]" value="{{ $prod->codigo_barras2 }}">
                                                <input type="hidden" name="cProd[]" value="{{ $prod->codigo }}">
                                                <input type="hidden" name="cest[]" value="{{ $prod->cest }}">

                                                <input type="hidden" class="_produto_id" name="_produto_id[]" value="{{ $prod->id }}">

                                                <input type="hidden" class="_categoria_id" name="_categoria_id[]" value="{{ $prod->categoria_id }}">
                                                <input type="hidden" class="_sub_categoria_id" name="_sub_categoria_id[]" value="{{ $prod->sub_categoria_id }}">
                                                <input type="hidden" class="_sub_categoria_nome" value="{{ $prod->sub_categoria_nome }}">
                                                <input type="hidden" class="_margem" name="_margem[]" value="{{ $prod->margem }}">

                                                <input type="hidden" class="_referencia" name="_referencia[]" value="{{ $prod->referencia }}">
                                                <input type="hidden" class="_referencia_balanca" name="_referencia_balanca[]" value="{{ $prod->referencia_balanca }}">

                                                <input type="hidden" class="_exportar_balanca" name="_exportar_balanca[]" value="{{ $prod->exportar_balanca }}">

                                                <input type="hidden" class="_observacao" name="_observacao[]" value="{{ $prod->observacao }}">
                                                <input type="hidden" class="_observacao2" name="_observacao2[]" value="{{ $prod->observacao2 }}">
                                                <input type="hidden" class="_observacao3" name="_observacao3[]" value="{{ $prod->observacao3 }}">
                                                <input type="hidden" class="_observacao4" name="_observacao4[]" value="{{ $prod->observacao4 }}">
                                                <input type="hidden" class="_disponibilidade" name="_disponibilidade[]" value="{{ $prod->disponibilidade }}">

                                                <input type="hidden" class="_valor_atacado" name="_valor_atacado[]" value="{{ $prod->valor_atacado }}">
                                                <input type="hidden" class="_marca_id" name="_marca_id[]" value="{{ $prod->marca_id }}">

                                                <input type="hidden" class="_estoque_minimo" name="_estoque_minimo[]" value="{{ $prod->estoque_minimo }}">

                                                <input type="hidden" class="_quantidade_atacado" name="_quantidade_atacado[]" value="{{ $prod->quantidade_atacado }}">
                                                <input type="hidden" class="_valor_minimo_venda" name="_valor_minimo_venda[]" value="{{ $prod->valor_minimo_venda }}">

                                                <input type="hidden" class="_gerenciar_estoque" name="_gerenciar_estoque[]" value="{{ $prod->gerenciar_estoque }}">

                                                <input type="hidden" class="_qtd_original" value="{{ $prod->quantidade }}">
                                                <input type="hidden" class="_valor_unitario_original" value="{{ $prod->valor_unitario }}">
                                                <input type="hidden" class="_valor_sub_total_original" value="{{ $prod->sub_total }}">

                                                <input type="hidden" class="_check" value="0">


                                                <div class="d-flex align-items-center grupo-produto">
                                                    <div class="flex-grow-1" style="max-width: 350px;">
                                                        <select class="form-select produto_id select2-produto" name="produto_id[]">
                                                            <option value="{{ $prod->id }}">{{ $prod->xProd }}</option>
                                                        </select>
                                                        <input type="hidden" value="{{ $key }}" class="_key">
                                                    </div>

                                                    <button data-key="{{ $key }}" title="Alterar dados do produto" type="button"class="btn btn-primary ms-2 btn-modal-altera btn-produto-icone">
                                                        <i class="ri-box-1-fill"></i>
                                                    </button>
                                                </div>

                                                @if($prod->id == 0)
                                                <span class="text-danger">*Produto será cadastrado no sistema</span>
                                                @else
                                                <span class="text-primary">*Produto já esta cadastrado no sistema</span>
                                                @endif
                                                <i class="ri-information-line" style="font-size: 18px" onclick="modalXml('{{ $prod->nomeXml }}', '{{ $prod->valorXml }}', '{{ $prod->cfopXml }}')"></i>
                                                <div style="width: 450px"></div>
                                            </td>

                                            <td class="td-padrao d-none">
                                                <select style="width: 200px" class="form-select form-select padrao_item_id" name="padrao_item_id[]">
                                                    <option value="">Selecione</option>
                                                    @foreach($padroes as $padrao)
                                                    <option value="{{ $padrao->id }}">{{ $padrao->descricao }}</option>
                                                    @endforeach
                                                </select>
                                            </td> 

                                            <td>
                                                <input readonly style="width: 120px;" value="{{ $prod->unidade }}" class="form-control unidade" type="text" name="unidade[]">
                                            </td> 
                                            <td>
                                                <input readonly style="width: 120px" value="{{ __moedaInput($prod->quantidade) }}" class="form-control qtd" type="tel" name="quantidade[]" id="inp-quantidade">
                                            </td>
                                            <td>
                                                <input readonly style="width: 120px" value="{{ __moedaInput($prod->valor_unitario) }}" class="form-control moeda valor_unit valor_compra" type="tel" name="valor_unitario[]" id="inp-valor_unitario">
                                            </td>
                                            <td>
                                                <input readonly style="width: 120px" value="{{ __moedaInput($prod->sub_total) }}" class="form-control moeda sub_total" type="tel" name="sub_total[]" id="inp-subtotal">
                                            </td>
                                            <td>
                                                <input style="width: 120px" value="{{ __moedaInput($prod->valor_venda) }}" class="form-control moeda valor_venda" type="tel" name="valor_venda[]" id="inp-valor_unitario">
                                            </td>
                                            <td>
                                                <input required style="width: 150px" value="1" class="form-control" data-mask="00000" type="tel" name="conversao_estoque[]">
                                            </td>
                                            <td>
                                                <input style="width: 120px" value="{{ $prod->perc_icms }}" class="form-control perc_icms percentual" type="tel" name="perc_icms[]" id="inp-perc_icms">
                                            </td>
                                            <td>
                                                <input style="width: 120px" value="{{ $prod->perc_pis }}" class="form-control perc_pis percentual" type="tel" name="perc_pis[]" id="inp-perc_pis">
                                            </td>
                                            <td>
                                                <input style="width: 120px" value="{{ $prod->perc_cofins }}" class="form-control perc_cofins percentual" type="tel" name="perc_cofins[]" id="inp-perc_cofins">
                                            </td>
                                            <td width="120">
                                                <input style="width: 120px" value="{{ $prod->perc_ipi }}" class="form-control perc_ipi percentual" type="tel" name="perc_ipi[]" id="inp-perc_ipi">
                                            </td>
                                            <td>
                                                <input style="width: 120px" value="{{ $prod->perc_red_bc }}" class="form-control perc_red_bc percentual ignore" type="tel" name="perc_red_bc[]" id="inp-perc_red_bc">
                                            </td>
                                            <td>
                                                <input style="width: 120px" value="{{ $prod->cfop }}" class="form-control cfop" type="tel" name="cfop[]" id="inp-cfop_estadual">
                                            </td>

                                            <td>
                                                <input style="width: 120px" value="{{ $prod->ncm }}" class="form-control ncm" type="tel" name="ncm[]" id="inp-ncm2">
                                            </td>
                                            <td>
                                                <input style="width: 120px" value="{{ $prod->codigo_beneficio_fiscal }}" class="form-control codigo_beneficio_fiscal" type="text" name="codigo_beneficio_fiscal[]">
                                            </td>

                                            <td>
                                                <select style="width: 400px" name="cst_csosn[]" class="form-select cst_csosn">
                                                    @foreach(App\Models\Produto::listaCSTCSOSN() as $key => $c)
                                                    <option @if($prod->cst_csosn == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select style="width: 300px" name="cst_pis[]" class="form-select cst_pis">
                                                    @foreach(App\Models\Produto::listaCST_PIS_COFINS() as $key => $c)
                                                    <option @if($prod->cst_pis == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select style="width: 300px" name="cst_cofins[]" class="form-select cst_cofins">
                                                    @foreach(App\Models\Produto::listaCST_PIS_COFINS() as $key => $c)
                                                    <option @if($prod->cst_cofins == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select style="width: 300px" name="cst_ipi[]" class="form-select cst_ipi">
                                                    @foreach(App\Models\Produto::listaCST_IPI() as $key => $c)
                                                    <option @if($prod->cst_ipi == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td width="30">
                                                <button class="btn btn-danger btn-remove-tr">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <button type="button" id="scrollToggle2" class="scroll-btn-jidox hidden">
                            <i class="ri-arrow-right-circle-line"></i>
                        </button>
                        <!-- <div class="row col-12 col-lg-2 mt-3">
                            <br>
                            <button type="button" class="btn btn-dark btn-add-tr px-2">
                                <i class="ri-add-fill"></i>
                                Adicionar Produto
                            </button>
                        </div> -->
                        <div class="mt-3">
                            <h5>Valor Total de Produtos: <strong class="total_prod">R$</strong></h5>
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
                        <div class="row">
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
                            <div class="col-md-3 mt-3">
                                {!!Form::tel('rua_transp', 'Rua')
                                ->value(isset($item->transportadora) ? $item->transportadora->rua : '')
                                !!}
                            </div>
                            <div class="col-md-1 mt-3">
                                {!!Form::tel('numero_transp', 'Número')
                                ->value(isset($item->transportadora) ? $item->transportadora->numero : '')
                                !!}
                            </div>
                            <div class="col-md-3 mt-3">
                                {!!Form::select('cidade_id', 'Cidade')
                                ->attrs(['class' => 'select2'])
                                ->options(isset($item->transportadora) ? [$item->transportadora->cidade->nome] : [])
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::tel('cep_transp', 'CEP')
                                ->attrs(['class' => 'cep'])
                                ->value(isset($item->transportadora) ? $item->transportadora->cep : '')
                                !!}
                            </div>
                            <div class="col-md-3 mt-3">
                                {!!Form::text('email_transp', 'E-mail')
                                ->value(isset($item->transportadora) ? $item->transportadora->email : '')
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::tel('telefone_transp', 'Telefone')
                                ->attrs(['class' => 'fone'])
                                ->value(isset($item->transportadora) ? $item->transportadora->telefone : '')
                                !!}
                            </div>
                            <div class="col-md-3 mt-3">
                                {!!Form::text('bairro_transp', 'Bairro')
                                ->value(isset($item->transportadora) ? $item->transportadora->bairro : '')
                                !!}
                            </div>
                            <div class="col-md-3 mt-3">
                                {!!Form::text('complemento_transp', 'Complemento')
                                ->value(isset($item->transportadora) ? $item->transportadora->complemento : '')
                                !!}
                            </div>
                            <hr class="mt-3">
                            <h4 class="mt-3">Informações do Frete</h4>
                            <div class="col-md-2 mt-2">
                                {!!Form::tel('valor_frete', 'Valor do Frete')
                                ->attrs(['class' => 'moeda valor_frete'])
                                ->value(isset($item) ? __moeda($item->valor_frete) : '')
                                !!}
                            </div>
                            <div class="col-md-2 mt-2">
                                {!!Form::tel('qtd_volumes', 'Qtd de Volumes')
                                ->attrs(['class' => ''])
                                !!}
                            </div>
                            <div class="col-md-3 mt-2">
                                {!!Form::tel('numeracao_volumes', 'Número de Volumes')
                                ->attrs(['class' => ''])
                                !!}
                            </div>
                            <div class="col-md-2 mt-2">
                                {!!Form::tel('peso_bruto', 'Peso Bruto')
                                ->attrs(['class' => 'peso'])
                                !!}
                            </div>
                            <div class="col-md-2 mt-2">
                                {!!Form::tel('peso_liquido', 'Peso Líquido')
                                ->attrs(['class' => 'peso'])
                                !!}
                            </div>
                            <div class="col-md-3 mt-3">
                                {!!Form::text('especie', 'Espécie')
                                ->attrs(['class' => ''])
                                !!}
                            </div>
                            <div class="col-md-3 mt-3">
                                {!!Form::select('tipo', 'Tipo', ['' => 'Selecione..'] + App\Models\Nfe::tiposFrete())
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::text('placa', 'Placa')
                                ->attrs(['class' => 'placa'])
                                !!}
                            </div>
                            <div class="col-md-1 mt-3">
                                {!!Form::select('uf', 'UF', App\Models\Cidade::estados())
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="fatura" role="tabpanel">
                <div class="card">
                    <div class="row m-3">
                        <div class="col-md-3">
                            {!!Form::select('natureza_id', 'Natureza de Operação', ['' => 'Selecione'] + $naturezas->pluck('descricao', 'id')->all())
                            ->attrs(['class' => 'form-select'])
                            ->value(isset($item) ? $item->natureza_id : '')
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

                        <div class="col-md-2 mt-3">
                            {!!Form::tel('numero_nfe', 'Número NFe')
                            ->required()
                            ->value(isset($item) ? $item->numero : $dadosXml['nNf'])
                            !!}
                        </div>

                        <div class="col-md-10 mt-3">
                            {!!Form::tel('referencia', 'Referência NFe')
                            !!}
                        </div>

                        
                        <div class="col-md-2 mt-3">
                            {!!Form::select('tpNF', 'Tipo NFe', ['0' => 'Entrada'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-md-2 mt-3">
                            {!!Form::select('finNFe', 'Finalidade NFe', [
                            '1' => 'NFe normal',
                            '2' => 'NFe complementar',
                            '3' => 'NFe de ajuste',
                            '4' => 'Devolução de mercadoria'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-md-2 mt-3">
                            {!!Form::select('gerar_conta_pagar', 'Gerar conta a pagar', [
                            0 => 'Não',
                            1 => 'Sim'])
                            ->attrs(['class' => 'form-select'])
                            ->value($config ? $config->gerar_conta_receber_padrao : 1)
                            !!}
                        </div>

                        <div class="col-md-5 mt-3">
                            {!!Form::text('chave_importada', 'Chave')
                            ->attrs(['class' => ''])
                            ->readonly()
                            ->value($dadosXml['chave'])
                            !!}
                        </div>

                        <div class="col-md-2 mt-3">
                            {!!Form::text('', 'Data de emissão')
                            ->attrs(['class' => ''])
                            ->readonly()
                            ->value(__data_pt($dadosXml['data_emissao']))
                            !!}
                        </div>
                        <input type="hidden" name="data_emissao" value="{{ $dadosXml['data_emissao'] }}">
                    </div>
                </div>
                <div class="card mt-1">
                    <div class="row m-3">
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
                                    @if(isset($item) && sizeof($item->fatura) > 0)
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

                                    @elseif(isset($dadosXml) && isset($dadosXml['fatura']))

                                    @foreach ($dadosXml['fatura'] as $f)
                                    <tr class="dynamic-form">
                                        <td width="300">
                                            <select name="tipo_pagamento[]" class="form-control tipo_pagamento select2">
                                                <option value="">Selecione..</option>
                                                @foreach(App\Models\Nfe::tiposPagamento() as $key => $c)
                                                <option @if($f['tipo_pagamento'] == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="150">
                                            <input value="{{ $f['vencimento'] }}" type="date" class="form-control" name="data_vencimento[]" id="">
                                        </td>
                                        <td width="150">
                                            <input value="{{ __moeda($f['valor_parcela']) }}" type="tel" class="form-control moeda valor_fatura" name="valor_fatura[]" id="valor">
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
                                            <input type="tel" class="form-control moeda valor_faturas" name="valor_fatura[]" id="valor">
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
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-info btn-add-tr px-5">
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
        <button type="button" class="btn btn-success btn-salvar-nfe px-5 m-3">Salvar</button>
    </div>
</div>
