<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs nav-primary" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#cliente" role="tab" aria-selected="true">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-user me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-file-user-fill"></i>
                            Cliente
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
                    <div class="row m-3">
                        <h5>CPF na Nota:</h5>
                        <div class="col-md-3">
                            {!!Form::text('cliente_nome', 'Nome')->attrs(['class' => ''])
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::tel('cliente_cpf_cnpj', 'CPF/CNPJ')->attrs(['class' => 'cpf_cnpj'])
                            !!}
                        </div>
                    </div>
                </div>
                <div class="card mt-1">
                    <div class="row m-3">
                        <div class="col-md-5">
                            <label>Cliente</label>
                            <div class="input-group flex-nowrap">
                                <select id="inp-cliente_id" class="cliente_id" name="cliente_id">
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
                                {!!Form::text('nome', 'Razão Social')->attrs(['class' => ''])
                                ->value(isset($item) && $item->cliente ? $item->cliente->razao_social : '')
                                !!}
                            </div>
                            <div class="col-md-3">
                                {!!Form::text('nome_fantasia', 'Nome Fantasia')->attrs(['class' => ''])
                                ->value(isset($item) && $item->cliente ? $item->cliente->nome_fantasia : '')
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('cpf_cnpj', 'CPF/CNPJ')->attrs(['class' => 'cpf_cnpj'])
                                ->value(isset($item) && $item->cliente ? $item->cliente->cpf_cnpj : '')
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::text('ie', 'IE')->attrs(['class' => ''])
                                ->value(isset($item) && $item->cliente ? $item->cliente->ie : '')
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('telefone', 'Fone')->attrs(['class' => 'fone'])
                                ->value(isset($item) && $item->cliente ? $item->cliente->telefone : '')
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::select('contribuinte', 'Contribuinte', [0 => 'Não', 1 => 'Sim'])->attrs(['class' => 'form-select'])
                                ->value(isset($item) && $item->cliente ? $item->cliente->contribuinte : '')
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::select('consumidor_final', 'Consumidor Final', [0 => 'Não', 1 => 'Sim'])->attrs(['class' => 'form-select'])
                                ->value(isset($item) && $item->cliente ? $item->cliente->consumidor_final : '')
                                !!}
                            </div>
                            <div class="col-md-4 mt-3">
                                {!!Form::text('email', 'E-mail')->attrs(['class' => ''])
                                ->value(isset($item) && $item->cliente ? $item->cliente->email : '')
                                !!}
                            </div>
                            <div class="col-md-4 mt-3">
                                <label for="">Cidade</label>
                                <select class="form-control select2 cidade_id" name="cliente_cidade" id="inp-cidade_cliente">
                                    <option value="">Selecione..</option>
                                    @foreach ($cidades as $c)
                                    <option @isset($item) && @isset($item->cliente) @if($item->cliente->cidade_id == $c->id) selected @endif @endisset @endisset value="{{$c->id}}">{{$c->nome}} - {{$c->uf}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-3">
                                {!!Form::text('cliente_rua', 'Rua')->attrs(['class' => ''])
                                ->value(isset($item) && $item->cliente ? $item->cliente->rua : '')
                                !!}
                            </div>
                            <div class="col-md-1 mt-3">
                                {!!Form::text('cliente_numero', 'Número')->attrs(['class' => ''])
                                ->value(isset($item) && $item->cliente ? $item->cliente->numero : '')
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::text('cep', 'CEP')->attrs(['class' => 'cep'])
                                ->value(isset($item) && $item->cliente ? $item->cliente->cep : '')
                                !!}
                            </div>
                            <div class="col-md-2 mt-3">
                                {!!Form::text('cliente_bairro', 'Bairro')->attrs(['class' => ''])
                                ->value(isset($item) && $item->cliente ? $item->cliente->bairro : '')
                                !!}
                            </div>
                            <div class="col-md-4 mt-3">
                                {!!Form::text('complemento', 'Complemento')->attrs(['class' => ''])
                                ->value(isset($item) && $item->cliente ? $item->cliente->complemento : '')
                                !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="produtos" role="tabpanel">
                <div class="card">
                    <div class="row m-3">
                        <div class="table-responsive">
                            <table class="table table-dynamic table-produtos" style="width: 2800px">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
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
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @isset($item)
                                    @foreach ($item->itens as $prod)
                                    <tr class="dynamic-form">
                                        <td width="250">
                                            <select class="form-control select2 produto_id" name="produto_id[]" id="inp-produto_id">
                                                <option value="{{ $prod->produto_id }}">{{ $prod->produto->nome }}</option>
                                            </select>
                                            @if($prod->variacao_id)
                                            <span>variação: <strong>{{ $prod->produtoVariacao->descricao }}</strong></span>
                                            @endif
                                            <input name="variacao_id[]" type="hidden" value="{{ $prod->variacao_id }}">

                                        </td>
                                        <td width="80">
                                            <input value="{{ __moeda($prod->quantidade) }}" class="form-control qtd" type="tel" name="quantidade[]" id="inp-quantidade">
                                        </td>
                                        <td width="100">
                                            <input value="{{ __moeda($prod->valor_unitario) }}" class="form-control moeda valor_unit" type="tel" name="valor_unitario[]" id="inp-valor_unitario">
                                        </td>
                                        <td width="150">
                                            <input value="{{ __moeda($prod->sub_total) }}" class="form-control moeda sub_total" type="tel" name="sub_total[]" id="inp-subtotal">
                                        </td>
                                        <td width="80">
                                            <input value="{{ $prod->perc_icms }}" class="form-control" type="tel" name="perc_icms[]" id="inp-perc_icms">
                                        </td>
                                        <td width="100">
                                            <input value="{{ $prod->perc_pis }}" class="form-control" type="tel" name="perc_pis[]" id="inp-perc_pis">
                                        </td>
                                        <td width="100">
                                            <input value="{{ $prod->perc_cofins }}" class="form-control" type="tel" name="perc_cofins[]" id="inp-perc_cofins">
                                        </td>
                                        <td width="100">
                                            <input value="{{ $prod->perc_ipi }}" class="form-control" type="tel" name="perc_ipi[]" id="inp-perc_ipi">
                                        </td>
                                        <td width="100">
                                            <input value="{{ $prod->perc_red_bc }}" class="form-control percentual ignore" type="tel" name="perc_red_bc[]" id="inp-perc_red_bc">
                                        </td>
                                        <td width="120">
                                            <input value="{{ $prod->cfop }}" class="form-control ignore" type="tel" name="cfop[]" id="inp-cfop_estadual">
                                        </td>
                                        <td width="150">
                                            <input value="{{ $prod->ncm }}" class="form-control ignore" type="tel" name="ncm[]" id="inp-ncm2">
                                        </td>
                                        <td width="120">
                                            <input value="{{ $prod->codigo_beneficio_fiscal }}" class="form-control codigo_beneficio_fiscal ignore" type="text" name="codigo_beneficio_fiscal[]">
                                        </td>
                                        <td width="250">
                                            <select name="cst_csosn[]" class="form-control select2">
                                                @foreach(App\Models\Produto::listaCSTCSOSN() as $key => $c)
                                                <option @if($prod->cst_csosn == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="250">
                                            <select name="cst_pis[]" class="form-control select2">
                                                @foreach(App\Models\Produto::listaCST_PIS_COFINS() as $key => $c)
                                                <option @if($prod->cst_pis == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="250">
                                            <select name="cst_cofins[]" class="form-control select2">
                                                @foreach(App\Models\Produto::listaCST_PIS_COFINS() as $key => $c)
                                                <option @if($prod->cst_cofins == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="250">
                                            <select name="cst_ipi[]" class="form-control select2">
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
                                    @else
                                    <tr class="dynamic-form">
                                        <td>
                                            <select required class="form-control select2 produto_id" name="produto_id[]" id="inp-produto_id">
                                            </select>

                                            <input name="variacao_id[]" type="hidden" value="">
                                            <div style="width: 400px;"></div>
                                        </td>
                                        <td>
                                            <input style="width: 120px;" class="form-control qtd" type="tel" name="quantidade[]" id="inp-quantidade">
                                        </td>
                                        <td>
                                            <input style="width: 120px;" class="form-control moeda valor_unit" type="tel" name="valor_unitario[]" id="inp-valor_unitario">
                                        </td>
                                        <td>
                                            <input style="width: 120px;" class="form-control moeda sub_total" type="tel" name="sub_total[]" id="inp-subtotal">
                                        </td>
                                        <td>
                                            <input style="width: 120px;" class="form-control" type="tel" name="perc_icms[]" id="inp-perc_icms">
                                        </td>
                                        <td>
                                            <input style="width: 120px;" class="form-control" type="tel" name="perc_pis[]" id="inp-perc_pis">
                                        </td>
                                        <td>
                                            <input style="width: 120px;" class="form-control" type="tel" name="perc_cofins[]" id="inp-perc_cofins">
                                        </td>
                                        <td>
                                            <input style="width: 120px;" class="form-control" type="tel" name="perc_ipi[]" id="inp-perc_ipi">
                                        </td>
                                        <td>
                                            <input style="width: 120px;" class="form-control percentual ignore" type="tel" name="perc_red_bc[]" id="inp-perc_red_bc">
                                        </td>
                                        <td>
                                            <input style="width: 120px;" class="form-control ignore" type="tel" name="cfop[]" id="inp-cfop_estadual">
                                        </td>
                                        <td>
                                            <input style="width: 150px;" class="form-control ignore" type="tel" name="ncm[]" id="inp-cfop_outro_estado">
                                        </td>
                                        <td>
                                            <input style="width: 120px;" class="form-control codigo_beneficio_fiscal ignore" type="text" name="codigo_beneficio_fiscal[]">
                                        </td>
                                        <td>
                                            <select name="cst_csosn[]" class="form-control select2">
                                                @foreach(App\Models\Produto::listaCSTCSOSN() as $key => $c)
                                                <option value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                            <div style="width: 350px;"></div>
                                        </td>
                                        <td>
                                            <select name="cst_pis[]" class="form-control select2">
                                                @foreach(App\Models\Produto::listaCST_PIS_COFINS() as $key => $c)
                                                <option value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                            <div style="width: 250px;"></div>
                                        </td>
                                        <td>
                                            <select name="cst_cofins[]" class="form-control select2">
                                                @foreach(App\Models\Produto::listaCST_PIS_COFINS() as $key => $c)
                                                <option value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                            <div style="width: 250px;"></div>
                                        </td>
                                        <td>
                                            <select name="cst_ipi[]" class="form-control select2">
                                                @foreach(App\Models\Produto::listaCST_IPI() as $key => $c)
                                                <option value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                            <div style="width: 250px;"></div>
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
                            <button type="button" class="btn btn-dark btn-add-tr-nfce px-2">
                                <i class="ri-add-fill"></i>
                                Adicionar Produto
                            </button>
                        </div>
                        <div class="mt-3">
                            <h5>Total de Produtos: <strong class="total_prod">R$ 0,00</strong></h5>
                        </div>

                        <input type="hidden" class="total_prod" name="valor_total" value="">

                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="fatura" role="tabpanel">
                <div class="card">
                    <div class="row m-3">
                        <div class="col-md-3">
                            {!!Form::select('natureza_id', 'Natureza de Operação', ['' => 'Selecione'] + $naturezas->pluck('descricao', 'id')->all())
                            ->attrs(['class' => 'form-select'])
                            ->value(isset($item) ? $item->natureza_id : (isset($naturezaPadrao) && $naturezaPadrao != null ? $naturezaPadrao->id : '') )
                            ->required()
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('acrescimo', 'Acréscimo')
                            ->attrs(['class' => 'moeda acrescimo'])
                            ->value(isset($item) ? __moeda($item->acrescimo) : '')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('desconto', 'Desconto')
                            ->attrs(['class' => 'moeda desconto'])
                            ->value(isset($item) ? __moeda($item->desconto) : '')
                            !!}
                        </div>
                        <div class="col-md-5">
                            {!!Form::text('observacao', 'Observação')
                            ->attrs(['class' => ''])
                            !!}
                        </div>

                        <div class="col-md-2 mt-3">
                            {!!Form::tel('numero_nfce', 'Número NFCe')
                            ->required()
                            ->value(isset($item) ? $item->numero : $numeroNfce)
                            !!}
                        </div>

                        <div class="col-md-2 mt-3">
                            {!!Form::select('gerar_conta_receber', 'Gerar conta a receber', [
                            0 => 'Não',
                            1 => 'Sim'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                    </div>
                </div>
                <div class="card mt-1">
                    <div class="row m-3">
                        <div class="table-responsive">
                            <table class="table table-dynamic table-fatura" style="width: 800px">
                                <thead>
                                    <tr>
                                        <th>Tipo de Pagamento</th>
                                        <th>Data Vencimento</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody id="body-pagamento" class="datatable-body">
                                    @if(isset($item) && sizeof($item->fatura) > 0)
                                    @foreach ($item->fatura as $f)
                                    <tr class="dynamic-form">
                                        <td width="300">
                                            <select required name="tipo_pagamento[]" class="form-control select2">
                                                <option value="">Selecione..</option>
                                                @foreach(App\Models\Nfce::tiposPagamento() as $key => $c)
                                                <option @if($f->tipo_pagamento == $key) selected @endif value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="150">
                                            <input required value="{{ $f->data_vencimento }}" type="date" class="form-control date_atual" name="data_vencimento[]" id="">
                                        </td>
                                        <td width="150">
                                            <input required value="{{ __moeda($f->valor) }}" type="tel" class="form-control moeda valor_fatura" name="valor_fatura[]" id="valor">
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
                                            <select required name="tipo_pagamento[]" class="form-control select2">
                                                <option value="">Selecione..</option>
                                                @foreach(App\Models\Nfce::tiposPagamento() as $key => $c)
                                                <option value="{{$key}}">{{$c}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="150">
                                            <input required type="date" class="form-control date_atual" name="data_vencimento[]" id="">
                                        </td>
                                        <td width="150">
                                            <input required type="tel" class="form-control moeda valor_fatura" name="valor_fatura[]" id="valor">
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
                        <div class="col-4 mt-4">
                            <h5>Total da Fatura: <strong class="total_fatura">R$</strong></h5>
                        </div>
                        <div class="col-4 mt-4">
                            <h5>Total de Produtos: <strong class="total_prod">R$</strong></h5>
                        </div>
                        <div class="col-4 mt-4">
                            <h5>Total da NFCe: <strong class="total_nfe text-success">R$ 0,00</strong></h5>
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
@include('modals._variacao')
