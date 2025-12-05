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
        {!!Form::tel('cpf_cnpj', 'CPF/CNPJ')
        ->attrs(['class' => 'form-control cpf_cnpj', 'o'])
        ->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('tributacao', 'Tipo de tributação', App\Models\Empresa::tiposTributacao())
        ->attrs(['class' => 'form-select'])
        ->required()
        !!}
    </div>
    <div class="col-md-4">
        {!!Form::text('nome', 'Nome')
        ->attrs(['class' => 'form-control'])
        ->required()
        !!}
    </div>
    <div class="col-md-4">
        {!!Form::text('nome_fantasia', 'Nome Fantasia')
        ->attrs(['class' => 'form-control'])
        ->required()
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::tel('ie', 'IE')
        ->attrs(['data-mask' => '000000000000000000'])
        ->required()
        !!}
    </div>
    <br>
    <hr>
    <div class="col-md-4">
        {!!Form::text('rua', 'Rua')
        ->attrs(['class' => ''])
        ->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('numero', 'Número')
        ->attrs(['class' => ''])
        ->required()
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::text('bairro', 'Bairro')
        ->attrs(['class' => ''])
        ->required()
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::text('complemento', 'Complemento')
        ->attrs(['class' => ''])
        !!}
    </div>
    <div class="col-md-3">
        @isset($item)
        {!!Form::select('cidade_id', 'Cidade')
        ->attrs(['class' => 'select2'])
        ->options($item != null ? [$item->cidade_id => $item->cidade->info] : [])
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
        {!!Form::tel('cep', 'CEP')
        ->attrs(['class' => 'cep'])
        ->required()
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::text('email', 'Email')
        ->attrs(['class' => ''])
        ->value(isset($item) ? $item->email : '')
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('celular', 'Telefone')
        ->attrs(['class' => 'fone'])
        ->required()
        !!}
    </div>
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
    <div class="col-md-4">
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
        {!!Form::select('status', 'Status da empresa', [1 => 'Ativo', 0 => 'Desativado'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::select('ambiente', 'Ambiente', [2 => 'Homologação', 1 => 'Produção'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    @isset($segmentossegmentos)
    <div class="col-md-2">
        {!!Form::select('segmento_id', 'Segmento', ['' => 'Selecione'] + $segmentos->pluck('nome', 'id')->all())
        ->attrs(['class' => 'form-select'])
        ->value(isset($item) ? (sizeof($item->segmentos) > 0 ? $item->segmentos[0]->segmento_id : '') : '')
        !!}
    </div>
    @endif

    <div class="col-md-4">
        <label for="">Token</label>
        <div class="input-group">
            @if (!isset($not_submit))
            @endif
            <input readonly type="text" class="form-control" id="api_token" name="token" value="{{ isset($item) ? $item->token : '' }}">
            <button type="button" class="btn btn-info" id="btn_token"><a class="ri-refresh-line text-white"></a></button>
        </div>
    </div>

    @isset($config)
    @if($config && $config->receber_com_boleto)
    <hr>
    <h4>Recebimento com boleto</h4>
    <div class="col-md-2">
        {!!Form::tel('dia_vencimento_boleto', 'Dia venc. boleto')
        ->attrs(['data-mask' => '00'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('receber_com_boleto', 'Receber com boleto', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    @endif
    @endif

    @if(__isMaster() || __isContador())
    @if(!isset($edit))

    <hr class="mt-4">
    <h5>Dados do Usuário</h5>
    <div class="col-md-2">
        {!!Form::text('usuario', 'Nome')
        ->attrs(['class' => ''])
        ->required()
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::text('email', 'Email')
        ->attrs(['class' => ''])
        ->required()
        !!}
    </div>
    <div class="col-md-2">
        <div class="col-md-12">
            <label class="required" for="">Senha</label>
            <div class="input-group" id="show_hide_password">
                <input required type="password" class="form-control" name="password" autocomplete="off" @if(isset($senhaCookie)) value="{{$senhaCookie}}" @endif>
                <a class="input-group-text"><i class='ri-eye-line'></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="col-md-12">
            <label class="required" for="">Repetir Senha</label>
            <div class="input-group" id="show_hide_password_r">
                <input required type="password" class="form-control" name="password_confirmation" autocomplete="off">
                <a class="input-group-text"><i class='ri-eye-line'></i></a>
            </div>
        </div>
    </div>

    @endif
    @endif


    <hr class="mt-4">
    <div class="col-md-3 file-certificado">
        {!! Form::file('certificado', 'Certificado Digital')->value(isset($item) ? false : true) !!}
        <span class="text-danger" id="filename"></span>
    </div>
    <div class="col-md-2">
        {!! Form::text('senha_certificado', 'Senha do certificado') !!}
    </div>
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script>

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
            text: "Esse token é o responsavel pela comunicação com a API!!", 
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
            $('#inp-cidade_id').append(newOption).trigger('change');
        })
        .fail((err) => {
            console.log(err)
        })

    }

</script>
@endsection
