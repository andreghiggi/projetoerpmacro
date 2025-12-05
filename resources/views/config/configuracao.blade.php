@section('css')
<style type="text/css">
    input[type="file"] {
        display: none;
    }

    .file-certificado label {
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

    .card-body strong {
        color: #8833FF;
    }

</style>
@endsection

<div class="">

    <div class="row">
        <div class="card">
            <div class="col-md-12 mt-3">
                <ul class="nav nav-tabs nav-primary" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#empresa" role="tab" aria-selected="true">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-home font-18 me-2'></i>
                                </div>
                                <div class="tab-title">
                                    <i class="ri-briefcase-fill"></i>
                                    Empresa
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#endereco" role="tab" aria-selected="false">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-search-location font-18 me-2'></i>
                                </div>
                                <div class="tab-title">
                                    <i class="ri-map-pin-2-line"></i>
                                    Endereço
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#nota_fiscal" role="tab" aria-selected="false">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-file-alt font-18 me-2'></i>
                                </div>
                                <div class="tab-title">
                                    <i class="ri-file-edit-line"></i>
                                    Emissão
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#certificado" role="tab" aria-selected="false">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-certificate font-18 me-2'></i>
                                </div>
                                <div class="tab-title">
                                    <i class="ri-fingerprint-line"></i>
                                    Certificado A1
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
                <hr>
                <div class="tab-content py-3">
                    <div class="tab-pane fade show active" id="empresa" role="tabpanel">
                        <div class="card m-3">
                            <div class="row m-3">
                                <div class="col-md-4">
                                    {!!Form::select('tributacao', 'Tipo de tributação', App\Models\Empresa::tiposTributacao())
                                    ->attrs(['class' => 'form-select'])
                                    ->required()
                                    !!}
                                </div>
                                <div class="col-md-3">
                                    {!!Form::tel('cpf_cnpj', 'CPF/CNPJ')
                                    ->attrs(['class' => 'cpf_cnpj'])
                                    ->value(isset($item) ? __setMask($item->cpf_cnpj) : '')
                                    ->required()
                                    !!}
                                </div>
                                <div class="col-md-5">
                                    {!!Form::tel('nome', 'Razão social')
                                    ->attrs(['data-contador' => true, 'maxlength' => 60])
                                    ->required()
                                    !!}
                                </div>
                                <div class="col-md-4 mt-3">
                                    {!!Form::tel('nome_fantasia', 'Nome fantasia')
                                    ->attrs(['data-contador' => true, 'maxlength' => 60])
                                    ->required()
                                    !!}
                                </div>
                                <div class="col-md-2 mt-3">
                                    {!!Form::tel('ie', 'Inscrição estadual')
                                    ->attrs(['data-mask' => '000000000000000000'])
                                    ->required()
                                    !!}
                                </div>

                                <div class="col-md-3 mt-3">
                                    {!!Form::tel('email', 'Email')
                                    ->attrs(['class' => ''])

                                    !!}
                                </div>
                                <div class="col-md-3 mt-3">
                                    {!!Form::tel('celular', 'Telefone de atendimento')
                                    ->attrs(['class' => 'fone'])
                                    !!}
                                </div>

                                <div class="col-md-3 mt-3">
                                    {!!Form::tel('aut_xml', 'Autorizador XML')
                                    ->attrs(['class' => 'cnpj'])
                                    !!}
                                </div>
                                <hr class="mt-3">

                                <div class="card col-md-3 mt-3 form-input" style="width: 210px">
                                    <div class="preview">
                                        <button type="button" id="btn-remove-imagem" class="btn btn-link-danger btn-sm btn-danger">x</button>
                                        @isset($item)
                                        <img id="file-ip-1-preview" src="{{ $item->img }}">
                                        <a href="{{ route('config.delete-logo') }}">remover imagem</a>
                                        @else
                                        <img id="file-ip-1-preview" src="/imgs/no-image.png">
                                        @endif
                                    </div>
                                    <label for="file-ip-1">Imagem</label>
                                    <input type="file" id="file-ip-1" name="image" accept="image/*" onchange="showPreview(event);">
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="endereco" role="tabpanel">
                        <div class="card m-3">
                            <div class="row m-3">
                                <div class="col-md-2">
                                    {!!Form::tel('cep', 'CEP')
                                    ->attrs(['class' => 'cep'])
                                    ->required()
                                    !!}
                                </div>
                                <div class="col-md-5">
                                    {!!Form::tel('rua', 'Endereço')
                                    ->attrs(['class' => ''])
                                    ->required()
                                    !!}
                                </div>
                                <div class="col-md-1">
                                    {!!Form::text('numero', 'Número')
                                    ->required()
                                    !!}
                                </div>
                                <div class="col-md-4">
                                    {!!Form::tel('complemento', 'Complemento')
                                    ->attrs(['class' => ''])
                                    !!}
                                </div>
                                <div class="col-md-3 mt-3">
                                    {!!Form::tel('bairro', 'Bairro')
                                    ->attrs(['class' => ''])
                                    ->required()
                                    !!}
                                </div>
                                <div class="col-md-4 mt-3 cidade">
                                    @isset($item)
                                    {!!Form::select('cidade_id', 'Cidade')
                                    ->options($item != null ? [$item->cidade_id => $item->cidade->info] : [])
                                    ->required()
                                    !!}
                                    @else
                                    {!!Form::select('cidade_id', 'Cidade')
                                    ->required()
                                    !!}
                                    @endisset
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nota_fiscal" role="tabpanel">
                        <div class="card m-3">
                            <div class="row m-3">

                                <div class="col-md-3">
                                    {!!Form::text('csc', 'CSC')
                                    ->attrs(['class' => 'form-control'])
                                    !!}
                                </div>
                                <div class="col-md-2">
                                    {!!Form::text('csc_id', 'CSC ID')
                                    ->attrs(['data-mask' => '0000000000'])
                                    !!}
                                </div>

                                <div class="col-md-3">
                                    <label for="">Token</label>
                                    <div class="input-group">
                                        @if (!isset($not_submit))
                                        @endif
                                        <input readonly type="text" class="form-control" id="api_token" name="token" value="{{ isset($item) ? $item->token : '' }}">
                                        <button type="button" class="btn btn-info" id="btn_token"><a class="ri-eye-line text-white"></a></button>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    {!!Form::select('ambiente', 'Ambiente', [2 => 'Homologação', 1 => 'Produção'])
                                    ->attrs(['class' => 'form-select'])
                                    !!}
                                </div>
                                <div class="col-md-2">
                                    {!!Form::select('substituto_tributario', 'Substituto tributário', [0 => 'Não', 1 => 'Sim'])
                                    ->attrs(['class' => 'form-select'])
                                    !!}
                                </div>
                                <div class="col-md-3 mt-3">
                                    {!!Form::select('exclusao_icms_pis_cofins', 'Permite exclusão icms de pis e cofins', [0 => 'Não', 1 => 'Sim'])
                                    ->attrs(['class' => 'form-select'])
                                    !!}
                                </div>

                                <div class="col-md-9 mt-3">
                                    {!!Form::text('token_nfse', 'Token NFSe')
                                    ->attrs(['class' => ''])
                                    !!}
                                </div>

                                <div class="col-md-3 mt-3">
                                    {!!Form::tel('numero_ultima_nfse', 'Número da última NFSe')
                                    ->attrs(['class' => ''])
                                    !!}
                                </div>

                                <div class="col-md-2 mt-3">
                                    {!!Form::tel('numero_serie_nfse', 'Número de série NFSe')
                                    ->attrs(['class' => ''])
                                    !!}
                                </div>

                                <div class="col-md-7 mt-3">
                                    {!!Form::text('token_ibpt', 'Token IBPT')
                                    ->attrs(['class' => ''])
                                    !!}
                                </div>

                                <div class="card bg-card">
                                    <div class="card-body">
                                        <h5 class="mt-3">NFe</h5>
                                        <div class="row g-2">

                                            <div class="col-md-3">
                                                {!!Form::tel('numero_serie_nfe', 'Número de série')
                                                ->attrs(['class' => ''])
                                                !!}
                                            </div>
                                            <div class="col-md-3">
                                                {!!Form::tel('numero_ultima_nfe_producao', 'Número da última (Produção)')
                                                ->attrs(['class' => ''])
                                                !!}
                                            </div>
                                            <div class="col-md-3">
                                                {!!Form::tel('numero_ultima_nfe_homologacao', 'Número da última (Homologação)')
                                                ->attrs(['class' => ''])
                                                !!}
                                            </div>
                                            <div class="col-md-3 div-simples">
                                                {!!Form::tel('perc_ap_cred', '% Aproveitamento crédito')
                                                ->attrs(['class' => 'percentual'])
                                                !!}
                                            </div>

                                            <div class="col-md-12">
                                                {!!Form::textarea('observacao_padrao_nfe', 'Observação padrão')
                                                ->attrs(['rows' => '5'])
                                                !!}
                                            </div>

                                            <div class="col-md-12 div-simples">
                                                {!!Form::textarea('mensagem_aproveitamento_credito', 'Mensagem de aproveitamento de crédito ICMS')
                                                ->attrs(['rows' => '5', 'class' => 'tooltipp'])
                                                !!}
                                                <div class="text-tooltip d-none">
                                                    Mensagem de aproveitamento de crédito ICMS, exemplo: Permite o aproveitamento de credito R$ correspondente ao %. Use R$ para calcular o valor
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="card bg-card">
                                    <div class="card-body">
                                        <h5 class="mt-3">NFCe</h5>
                                        <div class="row g-2">
                                            <div class="col-md-3">
                                                {!!Form::tel('numero_serie_nfce', 'Número de série')
                                                ->attrs(['class' => ''])
                                                !!}
                                            </div>
                                            <div class="col-md-3">
                                                {!!Form::tel('numero_ultima_nfce_producao', 'Número da última (Produção)')
                                                ->attrs(['class' => ''])
                                                !!}
                                            </div>
                                            <div class="col-md-3">
                                                {!!Form::tel('numero_ultima_nfce_homologacao', 'Número da última (Homologação)')
                                                ->attrs(['class' => ''])
                                                !!}
                                            </div>

                                            <div class="col-md-3">
                                                {!!Form::select('natureza_id_pdv', 'Natureza de Operação para PDV', ['' => 'Selecione'] +
                                                $naturezas->pluck('descricao', 'id')->all())
                                                ->attrs(['class' => 'form-select'])
                                                ->required()
                                                ->value(isset($item) ? $item->natureza_id_pdv : null)
                                                !!}
                                            </div>

                                            <div class="col-md-12">
                                                {!!Form::textarea('observacao_padrao_nfce', 'Observação padrão')
                                                ->attrs(['rows' => '5'])
                                                !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="card bg-card">
                                    <div class="card-body">
                                        <h5 class="mt-3">CTe</h5>
                                        <div class="row">
                                            <div class="col-md-3">
                                                {!!Form::tel('numero_serie_cte', 'Número de série')
                                                ->attrs(['class' => ''])
                                                !!}
                                            </div>
                                            <div class="col-md-3">
                                                {!!Form::tel('numero_ultima_cte_producao', 'Número da última (Produção)')
                                                ->attrs(['class' => ''])
                                                !!}
                                            </div>
                                            <div class="col-md-3">
                                                {!!Form::tel('numero_ultima_cte_homologacao', 'Número da última (Homologação)')
                                                ->attrs(['class' => ''])
                                                !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card bg-card">
                                    <div class="card-body">
                                        <h5 class="mt-3">MDFe</h5>
                                        <div class="row">
                                            <div class="col-md-3">
                                                {!!Form::tel('numero_serie_mdfe', 'Número de série')
                                                ->attrs(['class' => ''])
                                                !!}
                                            </div>
                                            <div class="col-md-3">
                                                {!!Form::tel('numero_ultima_mdfe_producao', 'Número da última (Produção)')
                                                ->attrs(['class' => ''])
                                                !!}
                                            </div>
                                            <div class="col-md-3">
                                                {!!Form::tel('numero_ultima_mdfe_homologacao', 'Número da última (Homologação)')
                                                ->attrs(['class' => ''])
                                                !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="certificado" role="tabpanel">
                        <div class="card m-3">
                            <p class="m-3">Arquivo do certificado A1 (Formato .pfx ou .p12)</p>

                            @if($dadosCertificado != null)
                            <div class="col-12">
                                <div class="card m-2">
                                    <div class="card-body">
                                        @isset($dadosCertificado['serial'])
                                        <h6>serial <strong>{{ $dadosCertificado['serial'] }}</strong></h6>
                                        <h6>inicio <strong>{{ $dadosCertificado['inicio'] }}</strong></h6>
                                        <h6>expiracao <strong>{{ $dadosCertificado['expiracao'] }}</strong></h6>
                                        <h6>id <strong>{{ $dadosCertificado['id'] }}</strong></h6>
                                        @else
                                        <h6><strong class="text-danger">{{ $dadosCertificado['mensagem'] }}</strong></h6>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="row m-2">
                                <div class="col-md-5 file-certificado">
                                    {!! Form::file('certificado', 'Certificado Digital')->value(isset($item) ? false : true) !!}
                                    <span class="text-danger" id="filename"></span>
                                </div>
                                <div class="col-md-2">
                                    {!! Form::tel('senha', 'Senha do certificado') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="mt-4">
            <div class="col-12" style="text-align: right;">
                <button type="submit" class="btn btn-success px-5 m-3">Salvar</button>
            </div>
        </div>
    </div>
</div>

@section('js')
<script type="text/javascript">
    $(function(){
        isRegimeSimples()
    })

    function isRegimeSimples(){
        let tributacao = $('#inp-tributacao').val()
        if(tributacao == 'Simples Nacional'){
            $('.div-simples').removeClass('d-none')
        }else{
            $('.div-simples').addClass('d-none')
            $('.div-simples').find('input').val('')
            $('.div-simples').find('textarea').val('')
        }
    }

    $('#btn_token').click(() => {

        let token = generate_token(25);
        swal({
            title: "Atenção"
            , text: "Esse token é o responsavel pela comunicação com a API, tenha atenção!!"
            , icon: "warning"
            , buttons: true
            , dangerMode: true
            , }).then((confirmed) => {
                if (confirmed) {
                    $('#api_token').val(token)
                }
            });
        })

    function generate_token(length) {
        var a = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890".split("");
        var b = [];
        for (var i = 0; i < length; i++) {
            var j = (Math.random() * (a.length - 1)).toFixed(0);
            b[i] = a[j];
        }
        return b.join("");
    }

    $('.btn-success').click(() => {
        addClassRequired()
    })


    function addClassRequired() {
        let infMsg = ""

        $("body").find('input, select').each(function() {
            if ($(this).prop('required')) {
                if ($(this).val() == "") {
                    try {
                        infMsg += $(this).prev()[0].textContent + "\n"
                    } catch {}
                    $(this).addClass('is-invalid')
                } else {
                    $(this).removeClass('is-invalid')
                }
            } else {
                $(this).removeClass('is-invalid')
            }
        })

        if (infMsg != "") {
            swal("Campos pendentes", infMsg, "warning")
        }
    }

    $(document).on("change", "#inp-tributacao", function () {
        isRegimeSimples()
    })

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
                    $('#inp-nome').val(data.razao_social)
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
            console.log(newOption)
            $('#inp-cidade_id').append(newOption).trigger('change');
        })
        .fail((err) => {
            console.log(err)
        })

    }

</script>
@endsection
