@section('css')
<style type="text/css">

    h4{
        color: #558FF2;
    }
    h6 strong{
        color: #558FF2;
    }
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

</style>
@endsection

<div class="row g-2">

    <div class="col-md-2">
        {!!Form::text('descricao', 'Descrição do local')
        ->attrs(['class' => 'form-control'])
        ->value(isset($item) ? $item->descricao : 'BL000' . $count)
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('cpf_cnpj', 'CPF/CNPJ')
        ->attrs(['class' => 'form-control cpf_cnpj', 'o'])
        ->required()
        ->readonly(isset($firstLocation) && $firstLocation ? true : false)
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('tributacao', 'Tipo de tributação', App\Models\Empresa::tiposTributacao())
        ->attrs(['class' => 'form-select'])
        ->required()
        ->disabled(isset($firstLocation) && $firstLocation ? true : false)
        !!}
    </div>
    <div class="col-md-4">
        {!!Form::text('nome', 'Nome')
        ->attrs(['class' => 'form-control'])
        ->required()
        ->readonly(isset($firstLocation) && $firstLocation ? true : false)
        !!}
    </div>
    <div class="col-md-4">
        {!!Form::text('nome_fantasia', 'Nome Fantasia')
        ->attrs(['class' => 'form-control'])
        ->required()
        ->readonly(isset($firstLocation) && $firstLocation ? true : false)
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::tel('ie', 'IE')
        ->attrs(['data-mask' => '00000000000000'])
        ->required()
        ->readonly(isset($firstLocation) && $firstLocation ? true : false)
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', [1 => 'Sim', 0 => 'Não'])
        ->required()
        ->attrs(['class' => 'form-select'])
        ->disabled($naoDesativar)
        !!}
    </div>

    <br>
    <hr>
    <div class="col-md-4">
        {!!Form::text('rua', 'Rua')
        ->attrs(['class' => ''])
        ->required()
        ->readonly(isset($firstLocation) && $firstLocation ? true : false)
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('numero', 'Número')
        ->attrs(['class' => ''])
        ->required()
        ->readonly(isset($firstLocation) && $firstLocation ? true : false)
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::text('bairro', 'Bairro')
        ->attrs(['class' => ''])
        ->required()
        ->readonly(isset($firstLocation) && $firstLocation ? true : false)
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::text('complemento', 'Complemento')
        ->attrs(['class' => ''])
        ->readonly(isset($firstLocation) && $firstLocation ? true : false)
        !!}
    </div>
    <div class="col-md-3">

        {!!Form::select('cidade_id', 'Cidade')
        ->options(isset($item) && $item->cidade != null ? [$item->cidade_id => $item->cidade->info] : [])
        ->required()
        ->disabled(isset($firstLocation) && $firstLocation ? true : false)
        !!}
        
    </div>
    <div class="col-md-2">
        {!!Form::tel('cep', 'CEP')
        ->attrs(['class' => 'cep'])
        ->required()
        ->readonly(isset($firstLocation) && $firstLocation ? true : false)
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::text('email', 'Email')
        ->attrs(['class' => ''])
        ->value(isset($item) ? $item->email : '')
        ->readonly(isset($firstLocation) && $firstLocation ? true : false)
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('celular', 'Telefone')
        ->attrs(['class' => 'fone'])
        ->required()
        ->readonly(isset($firstLocation) && $firstLocation ? true : false)
        !!}
    </div>

    <div class="col-md-6">
        {!!Form::text('token_whatsapp', 'Token WhatsApp')
        !!}
        <h5 class="text-success mt-1">Para enviar mensagens de alertas de agendamento https://criarwhats.com</h5>
    </div>

    @if(!isset($firstLocation) || (isset($firstLocation) && !$firstLocation))
    <hr class="mt-4">
    <h4>NFe</h4>
    <div class="col-md-2">
        {!!Form::tel('numero_ultima_nfe_producao', 'Última Produção')
        ->attrs(['class' => ''])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('numero_ultima_nfe_homologacao', 'Última Homologação')
        ->attrs(['class' => ''])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('numero_serie_nfe', 'Nº de Série')
        ->attrs(['class' => ''])
        !!}
    </div>

    <div class="col-md-2 div-simples">
        {!!Form::tel('perc_ap_cred', '% Aproveitamento crédito')
        ->attrs(['class' => 'percentual'])
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
    <hr>
    <h4>NFCe</h4>
    <div class="col-md-2">
        {!!Form::tel('numero_ultima_nfce_producao', 'Última Produção')
        ->attrs(['class' => ''])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('numero_ultima_nfce_homologacao', 'Última Homologação')
        ->attrs(['class' => ''])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('numero_serie_nfce', 'Nº de Série')
        ->attrs(['class' => ''])
        !!}
    </div>
    <hr>
    <h4>CTe</h4>
    <div class="col-md-2">
        {!!Form::tel('numero_ultima_cte_producao', 'Última Produção')
        ->attrs(['class' => ''])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('numero_ultima_cte_homologacao', 'Última Homologação')
        ->attrs(['class' => ''])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('numero_serie_cte', 'Nº de Série')
        ->attrs(['class' => ''])
        !!}
    </div>
    <hr>
    <h4>MDFe</h4>
    <div class="col-md-2">
        {!!Form::tel('numero_ultima_mdfe_producao', 'Última Produção')
        ->attrs(['class' => ''])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('numero_ultima_mdfe_homologacao', 'Última Homologação')
        ->attrs(['class' => ''])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('numero_serie_mdfe', 'Nº de Série')
        ->attrs(['class' => ''])
        !!}
    </div>
    <hr>
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

    <div class="col-md-3">
        {!!Form::tel('aut_xml', 'Autorizador XML')
        ->attrs(['class' => 'cnpj'])
        !!}
    </div>
    <div class="col-12"></div>
    <div class="card col-md-3 mt-3 form-input" style="width: 210px">
        <h5>Selecionar imagem</h5>
        <div class="preview">
            <button type="button" id="btn-remove-imagem" class="btn btn-link-danger btn-sm btn-danger">x</button>
            @isset($item)
            <img id="file-ip-1-preview" src="{{ $item->img }}">
            <a href="{{ route('localizacao.delete-logo', [$item->id]) }}">remover imagem</a>
            @else
            <img id="file-ip-1-preview" src="/imgs/no-image.png">
            @endif
        </div>
        <label for="file-ip-1">Imagem</label>
        <input type="file" id="file-ip-1" name="image" accept="image/*" onchange="showPreview(event);">
    </div>

    <hr class="mt-4">
    <p class="m-3">Arquivo do certificado A1 (Formato .pfx ou .p12)</p>
    @if($dadosCertificado != null)
    <div class="col-12">
        <div class="card m-2">
            <div class="card-body">

                <h6>serial <strong>{{ $dadosCertificado['serial'] }}</strong></h6>
                <h6>inicio <strong>{{ $dadosCertificado['inicio'] }}</strong></h6>
                <h6>expiracao <strong>{{ $dadosCertificado['expiracao'] }}</strong></h6>
                <h6>id <strong>{{ $dadosCertificado['id'] }}</strong></h6>
            </div>
        </div>
    </div>
    @endif
    <div class="col-md-3 file-certificado">
        {!! Form::file('certificado', 'Certificado Digital')->value(isset($item) ? false : true) !!}
        <span class="text-danger" id="filename"></span>
    </div>
    <div class="col-md-2">
        {!! Form::text('senha_certificado', 'Senha do certificado') !!}
    </div>
    <hr class="mt-4">
    @endif
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script>

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

    $(document).on("change", "#inp-tributacao", function () {
        isRegimeSimples()
    })

    $(document).ready(function() {
        $("#show_hide_password a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password input').attr("type") == "text") {
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass("bx-hide");
                $('#show_hide_password i').removeClass("bx-show");
            } else if ($('#show_hide_password input').attr("type") == "password") {
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass("bx-hide");
                $('#show_hide_password i').addClass("bx-show");
            }
        });

        $("#show_hide_password_r a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password_r input').attr("type") == "text") {
                $('#show_hide_password_r input').attr('type', 'password');
                $('#show_hide_password_r i').addClass("bx-hide");
                $('#show_hide_password_r i').removeClass("bx-show");
            } else if ($('#show_hide_password_r input').attr("type") == "password") {
                $('#show_hide_password_r input').attr('type', 'text');
                $('#show_hide_password_r i').removeClass("bx-hide");
                $('#show_hide_password_r i').addClass("bx-show");
            }
        });
    });

    $('#btn_token').click(() => {

        let token = generate_token(25);
        swal({
            title: "Atenção", 
            text: "Esse token é o responsavel pela comunicação com a API, tenha atenção!!", 
            icon: "warning", 
            buttons: true,
            dangerMode: true
        }).then((confirmed) => {
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
                    $('#inp-nome').val(data.razao_social)
                    $('#inp-nome_fantasia').val(data.estabelecimento.nome_fantasia)
                    $("#inp-rua").val(data.estabelecimento.tipo_logradouro + " " + data.estabelecimento.logradouro)
                    $('#inp-numero').val(data.estabelecimento.numero)
                    $("#inp-bairro").val(data.estabelecimento.bairro);
                    let cep = data.estabelecimento.cep.replace(/[^\d]+/g, '');
                    $('#inp-cep').val(cep.substring(0, 5) + '-' + cep.substring(5, 9))
                    $('#inp-email').val(data.estabelecimento.email)
                    $('#inp-celular').val(data.estabelecimento.telefone1)

                    findCidade(data.estabelecimento.cidade.ibge_id)

                }
            })
            .fail((err) => {
                console.log(err)
                // swal("Algo errado", err.responseJSON['detalhes'], "warning")
            })
        }
    })

    function findCidade(codigo_ibge){

        $.get(path_url + "api/cidadePorCodigoIbge/" + codigo_ibge)
        .done((res) => {
            var newOption = new Option(res.info, res.id, false, false);
            $('#inp-cidade_id').html('')
            $('#inp-cidade_id').append(newOption).trigger('change');

        })
        .fail((err) => {
            console.log(err)
        })

    }

</script>
@endsection
