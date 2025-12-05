<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs nav-primary" role="tablist">
            <li class="nav-item" role="presentation" style="width: 200px">
                <a class="nav-link active" data-bs-toggle="tab" href="#dados" role="tab" aria-selected="true">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-user me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-file-user-fill"></i>
                            Identificação
                        </div>
                    </div>
                </a>
            </li>

            <li class="nav-item" role="presentation" style="width: 200px">
                <a class="nav-link" data-bs-toggle="tab" href="#tributacao" role="tab" aria-selected="true">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-user me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-percent-fill"></i>
                            Tributação
                        </div>
                    </div>
                </a>
            </li>

            <li class="nav-item" role="presentation" style="width: 200px">
                <a class="nav-link" data-bs-toggle="tab" href="#fatura" role="tab" aria-selected="true">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon"><i class='fa fa-user me-2'></i>
                        </div>
                        <div class="tab-title">
                            <i class="ri-coins-fill"></i>
                            Fatura
                        </div>
                    </div>
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="dados" role="tabpanel">
                <div class="card">
                    <div class="row m-2 g-2">
                        <div class="col-md-1">
                            {!!Form::text('numero_sequencial', 'Código', isset($item) ? $item->numero_sequencial : __getUltimoNumeroSequencial(request()->empresa_id, 'clientes')+1)->readonly()
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('cpf_cnpj', 'CPF/CNPJ')->attrs(['class' => 'cpf_cnpj'])
                            ->required()
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::text('razao_social', 'Razão Social')->required()
                            ->attrs(['data-contador' => true, 'maxlength' => 60])
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::text('nome_fantasia', 'Nome Fantasia')
                            ->attrs(['data-contador' => true, 'maxlength' => 60])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::text('ie', 'IE')->attrs(['class' => 'ie'])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('telefone', 'Telefone')->attrs(['class' => 'fone'])->required()
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('contribuinte', 'Contribuinte', [0 => 'Não', 1 => 'Sim'])->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('consumidor_final', 'Consumidor Final', [0 => 'Não', 1 => 'Sim'])->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('valor_cashback', 'Valor de cashback')->attrs(['class' => 'moeda'])
                            ->value(isset($item) ? __moeda($item->valor_cashback) : '')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('status', 'Ativo', [ 1 => 'Sim', 0 => 'Não'])->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('email', 'Email')->attrs(['class' => ''])->type('email') !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::text('cep', 'CEP')->attrs(['class' => 'cep'])->required()
                            !!}
                        </div>
                        
                        <div class="col-md-4">
                            {!!Form::text('rua', 'Rua')->required()
                            ->attrs(['maxlength' => '60'])
                            !!}
                        </div>
                        <div class="col-md-1">
                            {!!Form::text('numero', 'Número')->required()
                            !!}
                        </div>
                        
                        <div class="col-md-2">
                            {!!Form::text('bairro', 'Bairro')->attrs(['class' => ''])->required()
                            !!}
                        </div>
                        <div class="col-md-4">
                            {!!Form::text('complemento', 'Complemento')->attrs(['class' => ''])
                            !!}
                        </div>

                        <div class="col-md-3">
                            @isset($item)
                            {!!Form::select('cidade_id', 'Cidade')
                            ->attrs(['class' => 'select2'])->options(($item != null && $item->cidade) ? [$item->cidade_id => $item->cidade->info] : [])
                            ->required()
                            !!}
                            @else
                            {!!Form::select('cidade_id', 'Cidade')
                            ->attrs(['class' => 'select2'])
                            ->required()
                            !!}
                            @endisset
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('codigo_pais', 'País', ['' => 'Selecione'] + __getPaises())
                            ->attrs(['class' => 'select2'])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::text('id_estrangeiro', 'ID. estrangeiro')->attrs(['class' => ''])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::text('valor_credito', 'Valor crédito')->attrs(['class' => 'moeda'])
                            ->value(isset($item) ? __moeda($item->valor_credito) : '')
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::date('data_nascimento', 'Data de nascimento')
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::text('limite_credito', 'Limite de crédito')->attrs(['class' => 'moeda tooltipp'])
                            ->value(isset($item) ? __moeda($item->limite_credito) : '')
                            !!}
                            <div class="text-tooltip d-none">
                                Definir um valor de limite para vendas do cliente a prazo.
                            </div>
                        </div>

                        <div class="col-md-3">
                            {!!Form::select('lista_preco_id', 'Lista de preço', ['' => ''] + $listasPreco->pluck('nome', 'id')->all())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        @if(!isset($item))
                        <div class="col-md-3 mt-4">
                            {!!Form::checkbox('insere_fornecedor', 'Cadastrar também como fornecedor')
                            !!}
                        </div>
                        @endif

                        <div class="col-12"></div>

                        <div class="card col-md-3 mt-3 form-input" style="width: 210px">
                            <div class="preview">
                                <button type="button" id="btn-remove-imagem" class="btn btn-link-danger btn-sm btn-danger">x</button>
                                @isset($item)
                                <img id="file-ip-1-preview" src="{{ $item->img }}">
                                @else
                                <img id="file-ip-1-preview" src="/imgs/no-client.png">
                                @endif
                            </div>
                            <label for="file-ip-1">Foto</label>
                            @isset($item)
                            <a class="btn btn-danger btn-sm mt-2 mb-1" href="{{ route('clientes.remove-image', [$item->id])}}">
                                <i class="ri-close-line"></i>
                                Remover foto
                            </a>
                            @endif
                            <input type="file" id="file-ip-1" name="image" accept="image/*" onchange="showPreview(event);">
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade show" id="tributacao" role="tabpanel">
                <div class="card">
                    <div class="row m-2 g-2">
                        <div class="col-md-2">
                            {!!Form::text('perc_icms', '%ICMS')
                            ->attrs(['class' => 'percentual'])
                            ->value(isset($item) && isset($item->tributacao) ? $item->tributacao->perc_icms : null)
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::tel('perc_pis', '% PIS')
                            ->attrs(['class' => 'percentual'])
                            ->value(isset($item) && isset($item->tributacao) ? $item->tributacao->perc_pis : null)
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('perc_cofins', '% COFINS')
                            ->attrs(['class' => 'percentual'])
                            ->value(isset($item) && isset($item->tributacao) ? $item->tributacao->perc_cofins : null)
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('perc_ipi', '% IPI')
                            ->attrs(['class' => 'percentual'])
                            ->value(isset($item) && isset($item->tributacao) ? $item->tributacao->perc_ipi : null)
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::tel('perc_red_bc', '% Red BC')
                            ->attrs(['class' => 'percentual'])
                            ->value(isset($item) && isset($item->tributacao) ? $item->tributacao->perc_red_bc : null)
                            !!}
                        </div>

                        <div class="col-md-4">

                            {!!Form::select('ncm', 'NCM')
                            ->options(isset($item) && isset($item->tributacao) && isset($item->tributacao->_ncm) ? [$item->tributacao->ncm => $item->tributacao->_ncm->descricao] : [])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('cest', 'CEST')
                            ->attrs(['class' => 'cest'])
                            ->value(isset($item) && isset($item->tributacao) ? $item->tributacao->cest : null)
                            !!}
                        </div>
                        <div class="col-md-6">
                            {!!Form::select('cst_csosn', 'CSOSN', ['' => 'Selecione']+App\Models\Produto::listaCSTCSOSN())
                            ->attrs(['class' => 'form-select'])
                            ->value(isset($item) && isset($item->tributacao) ? $item->tributacao->cst_csosn : null)
                            !!}
                        </div>
                        <div class="col-md-4">
                            {!!Form::select('cst_pis', 'CST PIS', ['' => 'Selecione']+App\Models\Produto::listaCST_PIS_COFINS())
                            ->attrs(['class' => 'form-select'])
                            ->value(isset($item) && isset($item->tributacao) ? $item->tributacao->cst_pis : null)
                            !!}
                        </div>
                        <div class="col-md-4">
                            {!!Form::select('cst_cofins', 'CST COFINS', ['' => 'Selecione']+App\Models\Produto::listaCST_PIS_COFINS())
                            ->attrs(['class' => 'form-select'])
                            ->value(isset($item) && isset($item->tributacao) ? $item->tributacao->cst_cofins : null)
                            !!}
                        </div>
                        <div class="col-md-4">
                            {!!Form::select('cst_ipi', 'CST IPI', ['' => 'Selecione']+App\Models\Produto::listaCST_IPI())
                            ->attrs(['class' => 'form-select'])
                            ->value(isset($item) && isset($item->tributacao) ? $item->tributacao->cst_ipi : null)
                            !!}
                        </div>
                        
                        <div class="col-md-2">
                            {!!Form::tel('cfop_estadual', 'CFOP Estadual')
                            ->attrs(['class' => 'cfop'])
                            ->value(isset($item) && isset($item->tributacao) ? $item->tributacao->cfop_estadual : null)
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('cfop_outro_estado', 'CFOP Inter Estadual')
                            ->attrs(['class' => 'cfop'])
                            ->value(isset($item) && isset($item->tributacao) ? $item->tributacao->cfop_outro_estado : null)
                            !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade show" id="fatura" role="tabpanel">
                <div class="card">
                    <div class="col-md-4 m-2">
                        <div class="row">
                            <table class="table mb-0 table-striped table-dynamic">
                                <thead>
                                    <tr>
                                        <th>Tipo de pagamento</th>
                                        <th>Dias para vencimento</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($item) && sizeof($item->fatura) > 0)
                                    @foreach($item->fatura as $f)
                                    <tr class="dynamic-form">
                                        <td>
                                            <select name="tipo_pagamento[]" class="form-control form-select ignore">
                                                <option value="">Selecione</option>
                                                @foreach($tiposPagamento as $key => $t)
                                                <option @if($key == $f->tipo_pagamento) selected @endif value="{{ $key }}">{{ $t }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input value="{{ $f->dias_vencimento }}" type="tel" name="dias_vencimento[]" class="form-control" data-mask="000">
                                        </td>
                                        <td>
                                            <button class="btn btn-danger btn-sm btn-remove-tr">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr class="dynamic-form">
                                        <td>
                                            <select name="tipo_pagamento[]" class="form-control form-select ignore">
                                                <option value="">Selecione</option>
                                                @foreach($tiposPagamento as $key => $t)
                                                <option value="{{ $key }}">{{ $t }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="tel" name="dias_vencimento[]" class="form-control" data-mask="000">
                                        </td>
                                        <td>
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

        <hr class="mt-4">
        <div class="col-12" style="text-align: right;">
            <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
        </div>
    </div>

    @section('js')
    <script type="text/javascript" src="/js/busca_cep.js"></script>
    <script>

        $(document).on("blur", "#inp-cpf_cnpj", function () {

            let cpf_cnpj = $(this).val().replace(/[^0-9]/g,'')

            if(cpf_cnpj.length == 14){
                $.get('https://publica.cnpj.ws/cnpj/' + cpf_cnpj)
                .done((data) => {
                    if (data!= null) {
                        let ie = ''
                        if (data.estabelecimento.inscricoes_estaduais.length > 0) {
                            ie = data.estabelecimento.inscricoes_estaduais[0].inscricao_estadual
                        }

                        $('#inp-ie').val(ie)
                        if(ie != ""){
                            $('#inp-contribuinte').val(1).change()
                        }
                        $('#inp-razao_social').val(data.razao_social)
                        $('#inp-nome_fantasia').val(data.estabelecimento.nome_fantasia)
                        $("#inp-rua").val(data.estabelecimento.tipo_logradouro + " " + data.estabelecimento.logradouro)
                        $('#inp-numero').val(data.estabelecimento.numero)
                        $("#inp-bairro").val(data.estabelecimento.bairro);
                        let cep = data.estabelecimento.cep.replace(/[^\d]+/g, '');
                        $('#inp-cep').val(cep.substring(0, 5) + '-' + cep.substring(5, 9))
                        $('#inp-email').val(data.estabelecimento.email)
                        $('#inp-telefone').val(data.estabelecimento.telefone1)

                        findCidade(data.estabelecimento.cidade.ibge_id)

                    }
                })
                .fail((err) => {
                    console.log(err)
                })
            }
        })

        function findCidade(codigo_ibge){
            $('#inp-cidade_id').html('')
            $.get(path_url + "api/cidadePorCodigoIbge/" + codigo_ibge)
            .done((res) => {
                var newOption = new Option(res.info, res.id, false, false);
                $('#inp-cidade_id').append(newOption).trigger('change');
            })
            .fail((err) => {
                console.log(err)
            })
        }

        $('#inp-ie').blur(() => {
            if($('#inp-ie').val() != ""){
                $('#inp-contribuinte').val(1).change()
            }
        })

    </script>
    @endsection


