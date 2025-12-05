$('body').on('change', '.cliente_id', function () {
    let cliente = $('.cliente_id').val()
    if (cliente != '') {
        getClient(cliente)
    } else {

    }
})

$(function(){
    validaContaReceber()
})

function validaContaReceber(){
    let selected = $('#inp-gerar_conta_receber').val()
    if(selected == 1){
        $('.div-data_vencimento').removeClass('d-none')
        $('#inp-data_vencimento').attr('required', 1)
    }else{
        $('.div-data_vencimento').addClass('d-none')
        $('#inp-data_vencimento').removeAttr('required')
    }
}

$('body').on('change', '#inp-gerar_conta_receber', function () {
    validaContaReceber()
})

function getClient(cliente) {

    $.get(path_url + "api/clientes/find/" + cliente)
    .done((res) => {
        $('#inp-razao_social').val(res.razao_social)
        $('#inp-documento').val(res.cpf_cnpj)
        $('#inp-ie').val(res.ie)
        $('#inp-telefone').val(res.telefone)
        $('#inp-email').val(res.email)
        $('#inp-rua').val(res.rua)
        $('#inp-numero').val(res.numero)
        $('#inp-cep').val(res.cep)
        $('#inp-bairro').val(res.bairro)
        $('#inp-complemento').val(res.complemento)
        findCidade(res.cidade_id)
    })
    .fail((err) => {
        console.error(err)
    })
}

function findCidade(codigo){
    $('#inp-cidade_id').html('')
    $.get(path_url + "api/cidadePorId/" + codigo)
    .done((res) => {
        var newOption = new Option(res.info, res.id, false, false);
        $('#inp-cidade_id').append(newOption).trigger('change');
    })
    .fail((err) => {
        console.log(err)
    })
}

$('body').on('change', '.servico_id', function () {
    let servico_id = $('.servico_id').val()
    if (servico_id != '') {
        getSerico(servico_id)
    } else {

    }
})

function getSerico(servico_id) {

    $.get(path_url + "api/servicos/find/" + servico_id)
    .done((res) => {
        console.log(res)

        $('#inp-discriminacao').val(res.nome + "")
        $('#inp-codigo_servico').val(res.codigo_servico)
        $('#inp-valor_servico').val(convertFloatToMoeda(res.valor))
        $('#inp-aliquota_cofins').val(res.aliquota_cofins)
        $('#inp-aliquota_pis').val(res.aliquota_pis)
        $('#inp-aliquota_inss').val(res.aliquota_inss)
        $('#inp-aliquota_iss').val(res.aliquota_iss)
        $('#inp-aliquota_ir').val(res.aliquota_ir)
        $('#inp-aliquota_csll').val(res.aliquota_csll)  
        $('#inp-codigo_tributacao_municipio').val(res.codigo_tributacao_municipio)
        $('#inp-estado_local_prestacao_servico').val(res.estado_local_prestacao_servico).change()

        $('#inp-valor_deducoes').val(convertFloatToMoeda(res.valor_deducoes))
        $('#inp-desconto_incondicional').val(convertFloatToMoeda(res.desconto_incondicional))
        $('#inp-desconto_condicional').val(convertFloatToMoeda(res.desconto_condicional))
        $('#inp-outras_retencoes').val(convertFloatToMoeda(res.outras_retencoes))
        $('#inp-natureza_operacao').val(res.natureza_operacao)
        $('#inp-codigo_cnae').val(res.codigo_cnae)
        
    })
    .fail((err) => {
        console.error(err)
    })
}