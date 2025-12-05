<div class="row g-2">
    
    <div class="col-md-2">
        {!!Form::text('nome', 'Nome')->attrs(['class' => ''])->required()
        ->value($nome)
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('sobre_nome', 'Sobre nome')->attrs(['class' => ''])->required()
        ->value($sobreNome)
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::tel('telefone', 'Telefone')->attrs(['class' => ''])->required()
        ->attrs(['class' => 'fone'])
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', [ 1 => 'Sim', 0 => 'Não'])->attrs(['class' => 'form-select'])->required()
        !!}
    </div>
    <div class="col-md-3">
        {!! Form::text('email', 'Email')->attrs(['class' => ''])->type('email') !!}
    </div>

    <!-- <div class="col-md-2">
        {!! Form::text('senha', 'Senha')->attrs(['class' => ''])->type('password')->value('') !!}
    </div> -->
   
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
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


