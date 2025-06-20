<div class="row g-2">

    <div class="col-md-2">
        {!!Form::tel('cpf_cnpj', 'CPF/CNPJ')
        ->required()
        ->attrs(['class' => 'cpf_cnpj'])
        ->value($cpfCnpj)
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::text('razao_social', 'Razão social')
        ->required()
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::text('rua', 'Rua')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('numero', 'Número')->required()
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::text('bairro', 'Bairro')->required()
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::select('cidade_id', 'Cidade')
        ->required()
        ->options($item != null ? [$item->cidade_id => $item->cidade->info] : [])
        !!}
    </div>

    <div class="col-md-6">
        {!!Form::tel('complemento', 'Complemento')
        ->attrs(['class' => ''])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('cep', 'CEP')
        ->attrs(['class' => 'cep'])
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('telefone', 'Telefone')
        ->attrs(['class' => 'fone'])
        ->required()
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::tel('email', 'Email')
        ->required()
        ->type('email')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('horario_checkin', 'Horário checkin')
        ->required()
        ->attrs(['class' => 'timer'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('horario_checkout', 'Horário checkout')
        ->required()
        ->attrs(['class' => 'timer'])
        !!}
    </div>

    <hr>

    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')

<script type="text/javascript">
    $(function(){
        setTimeout(() => {
            @if($item == null)
            buscaDadosCnpj()
            @endif
        }, 100)
    })

    $(document).on("blur", "#inp-cpf_cnpj", function () {
        buscaDadosCnpj()
    })

    function buscaDadosCnpj(){
        let cpf_cnpj = $('#inp-cpf_cnpj').val().replace(/[^0-9]/g,'')

        if(cpf_cnpj.length == 14){
            $.get('https://publica.cnpj.ws/cnpj/' + cpf_cnpj)
            .done((data) => {
                if (data!= null) {

                    $('#inp-razao_social').val(data.razao_social)

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
    }

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
