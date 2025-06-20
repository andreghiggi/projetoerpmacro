
<div class="row g-2">
    <div class="col-md-2">
        {!!Form::tel('cnpj', 'CNPJ')
        ->attrs(['class' => 'cnpj'])
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::text('razao_social', 'Razão social')
        ->required()
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::text('nome_fantasia', 'Nome fantasia')
        ->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('cpf', 'CPF')
        ->attrs(['class' => 'cpf'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('ie', 'IE')
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('crc', 'CRC')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('envio_xml_automatico', 'Enviar XML automático', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select tooltipp'])
        !!}
        <div class="text-tooltip d-none">
            Se marcar como sim todo documento transmitido será enviado para o email do escritório configurado
        </div>
    </div>

    <div class="col-md-3">
        {!!Form::text('email', 'Email')
        ->required()
        ->type('email')
        !!}
    </div>

    <hr>
    <div class="col-md-2">
        {!!Form::tel('cep', 'CEP')
        ->attrs(['class' => 'cep'])
        ->required()
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::text('rua', 'Rua')
        ->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('numero', 'Número')
        ->required()
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::text('bairro', 'Bairro')
        ->required()
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::select('cidade_id', 'Cidade')
        ->required()
        ->options($item != null ? [$item->cidade->id => $item->cidade->info] : [])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('telefone', 'Telefone')
        ->attrs(['class' => 'fone'])
        ->required()
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::tel('token_sieg', 'Token SIEG')
        !!}
    </div>

    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script type="text/javascript">

    $(document).on("blur", "#inp-cnpj", function () {

        let cnpj = $(this).val().replace(/[^0-9]/g,'')

        if(cnpj.length == 14){
            $.get('https://publica.cnpj.ws/cnpj/' + cnpj)
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
                swal("Algo deu errado", err.responseJSON.detalhes, "error")
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

    $(document).on("blur", ".cep", function () {
        let cep = $(".cep").val().replace(/[^0-9]/g,'')
        if(cep.length == 8){
            $.get('https://viacep.com.br/ws/'+cep+'/json')
            .done((res) => {
                console.log(res)
                findCidade(res.ibge)
                $('#inp-rua').val(res.logradouro)
                $('#inp-bairro').val(res.bairro)
            })
            .fail((err) => {
                console.log(err)
            })
        }else{
            swal("Erro", "Informe o CEP corretamente", "error")
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
</script>
@endsection

