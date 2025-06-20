$("#inp-novo_cidade_id").select2({
    minimumInputLength: 2,
    language: "pt-BR",
    placeholder: "Digite para buscar a cidade",
    width: "100%",
    dropdownParent: $("#modal_novo_cliente"),
    ajax: {
        cache: true,
        url: path_url + "api/buscaCidades",
        dataType: "json",
        data: function (params) {
            console.clear();
            var query = {
                pesquisa: params.term,
            };
            return query;
        },
        processResults: function (response) {
            var results = [];

            $.each(response, function (i, v) {
                var o = {};
                o.id = v.id;

                o.text = v.info;
                o.value = v.id;
                results.push(o);
            });
            return {
                results: results,
            };
        },
    },
});

$(document).on("click", ".btn-store-cliente", function () {
    var json = {};
    var a = $("#modal_novo_cliente").serializeArray();
    let msg = ""
    $("#modal_novo_cliente").find('input, select').each(function () {
        if (($(this).val() == "" || $(this).val() == null) && $(this).attr("type") != "hidden" && $(this).attr("type") != "file" && !$(this).hasClass("ignore")) {
            if($(this).prev()[0].textContent){
                msg += "Informe o campo " + $(this).prev()[0].textContent + "\n"
            }
        }
        if($(this)[0].name){
            let name = $(this)[0].name
            name = name.replace("novo_", "")
            json[name] = $(this).val()
        }
    })

    json['empresa_id'] = $('#empresa_id').val()

    setTimeout(() => {
        if(msg == ""){
            // console.log(json)
            $.post(path_url + "api/clientes/store", json)
            .done((res) => {
                $('#modal_novo_cliente .btn-close').trigger('click')

                // console.log(res)
                swal("Sucesso", "Cliente cadastrado!", "success")

                var newOption = new Option(res.info, res.id, false, false);
                $('#inp-cliente_id').append(newOption);

                $('.cliente_selecionado').text(res.razao_social);
                setTimeout(() => {
                    if(typeof getClient === 'function') {
                        getClient(res.id)
                    }
                }, 100)
                $("#modal_novo_cliente").find('input, select').each(function () {
                    $(this).val('')
                })

            })
            .fail((err) => {
                console.log(err)
                swal("Erro", "Erro ao cadastrar cliente: " + err.responseJSON, "error")
                .then(() => {
                    $('#modal_novo_cliente .btn-close').trigger('click')

                })
            })
        }else{
            swal("Alerta", msg, "warning")
        }
    }, 300)
})

$(document).on("blur", "#inp-novo_cpf_cnpj", function () {

    let cpf_cnpj = $(this).val().replace(/[^0-9]/g,'')

    if(cpf_cnpj.length == 14){
        $.get('https://publica.cnpj.ws/cnpj/' + cpf_cnpj)
        .done((data) => {
            if (data!= null) {
                let ie = ''
                if (data.estabelecimento.inscricoes_estaduais.length > 0) {
                    ie = data.estabelecimento.inscricoes_estaduais[0].inscricao_estadual
                }

                $('#inp-novo_ie').val(ie)
                if(ie != ""){
                    $('#inp-novo_contribuinte').val(1).change()
                }
                $('#inp-novo_razao_social').val(data.razao_social)
                $('#inp-novo_nome_fantasia').val(data.estabelecimento.nome_fantasia)
                $("#inp-novo_rua").val(data.estabelecimento.tipo_logradouro + " " + data.estabelecimento.logradouro)
                $('#inp-novo_numero').val(data.estabelecimento.numero)
                $("#inp-novo_bairro").val(data.estabelecimento.bairro);
                let cep = data.estabelecimento.cep.replace(/[^\d]+/g, '');
                $('#inp-novo_cep').val(cep.substring(0, 5) + '-' + cep.substring(5, 9))
                $('#inp-novo_email').val(data.estabelecimento.email)
                $('#inp-novo_telefone').val(data.estabelecimento.telefone1)

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
        $('#inp-novo_cidade_id').append(newOption).trigger('change');
    })
    .fail((err) => {
        console.log(err)
    })
}

$('#inp-novo_ie').blur(() => {
    if($('#inp-novo_ie').val() != ""){
        $('#inp-novo_contribuinte').val(1).change()
    }
})
