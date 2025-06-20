var PRODUTO = null
var TIPODIVISAOPIZZA = null

$('.btn-novo-endereco').click(() => {
    $('#modal-enderecos').modal('hide')
    $('#modal-novo-endereco').modal('show')
})

$(function(){
    TIPODIVISAOPIZZA = $('#tipo_divisao_pizza').val()
    $('#pizzas-hidden').val('')
    $('#tamanho_id-hidden').val('')
})

$('#btn-save-sabores').click(() => {
    if(SABORESSELECIONADOS.length > 0){
        $('#pizzas-hidden').val(SABORESSELECIONADOS)
        $('#tamanho_id-hidden').val($('#inp-tamanho_id').val())
        $('#inp-valor_unitario').val($('#inp-subtotal_modal').val())
        $('#inp-sub_total').val($('#inp-subtotal_modal').val())
        $('#modal-pizza').modal('hide')
    }else{
        swal("Alerta", "Selecione ao menos um sabor", "warning")
    }
})

$("#inp-produto_delivery").select2({
    minimumInputLength: 2,
    language: "pt-BR",
    placeholder: "Digite para buscar o produto",
    width: "100%",
    theme: "bootstrap4",
    ajax: {
        cache: true,
        url: path_url + "api/produtos/delivery",
        dataType: "json",
        data: function (params) {
            let empresa_id = $('#empresa_id').val()
            console.clear();

            $('.div-tp-carne').addClass('d-none')
            $('.div-tp-carne').find('select').removeAttr('required')
            var query = {
                pesquisa: params.term,
                empresa_id: empresa_id
            };
            return query;

        },
        processResults: function (response) {
            var results = [];

            $.each(response, function (i, v) {
                var o = {};
                o.id = v.id;

                o.text = v.nome;
                o.value = v.id;
                results.push(o);
            });
            return {
                results: results,
            };
        },
    },
});

$('#inp-produto_delivery').on("change", function () {
    let id = $(this).val()
    $.get(path_url + "api/produtos/findId/"+id)
    .done((success) => {
        $("#inp-tamanho_id").val('').change()
        PRODUTO = success
        if(success.categoria && success.categoria.tipo_pizza){

            changePizza(success)
        }
        // if(success.tipo_carne){
        //     $('.div-tp-carne').removeClass('d-none')
        //     $('.div-tp-carne').find('select').attr('required', 1)
        // }
        if(success.tempo_preparo > 0){
            $('#inp-estado').val('pendente').change()
        }else{
            $('#inp-estado').val('novo').change()
        }
        $('#inp-quantidade').val('1,00')
        $('#inp-valor_unitario').val(convertFloatToMoeda(success.valor_delivery))
        calcSubtotal()
    })
    .fail((err) => {
        console.log(err)
    })
})

function changePizza(produto){
    $('#modal-pizza').modal('show')
    SABORESSELECIONADOS = []
    $('#modal-pizza .modal-body .pizzas').html('')
    $('#inp-subtotal_modal').val(convertFloatToMoeda(0))
}

var SABORESSELECIONADOS = []
$(document).on("change", "#inp-tamanho_id", function () {
    if($(this).val()){
        SABORESSELECIONADOS = []
        buscasPizzas($(this).val())
    }
})

function buscasPizzas(tamanho_id){
    SABORESSELECIONADOS.push(PRODUTO.id)
    $.get(path_url + "api/produtos/get-pizzas",
    { 
        empresa_id: $('#empresa_id').val(), 
        produto_id: PRODUTO.id,
        tamanho_id: tamanho_id
    })
    .done((success) => {
        $('#modal-pizza .modal-body .pizzas').html(success)
        calculaValorPizza()
    })
    .fail((err) => {
        console.log(err)
    })
}

function calculaValorPizza(){
    $.get(path_url + "api/produtos/calculo-pizza",
    { 
        sabores: SABORESSELECIONADOS,
        tamanho_id: $("#inp-tamanho_id").val(),
        empresa_id: $('#empresa_id').val()
    })
    .done((success) => {
        $('#inp-subtotal_modal').val(convertFloatToMoeda(success))
    })
    .fail((err) => {
        console.log(err)
    })
}

function selectPizza(id){
    id = parseInt(id)
    let tempArr = []
    if(SABORESSELECIONADOS.includes(id)){
        $('.bg-'+id).removeClass('bg-info')
        SABORESSELECIONADOS = SABORESSELECIONADOS.filter((x) => { return x != id})
    }else{
        $('.bg-'+id).addClass('bg-info')
        SABORESSELECIONADOS.push(id)
    }

    setTimeout(() => {
        calculaValorPizza()
    }, 50)
}

$('#inp-valor_unitario').blur(() => {
    calcSubtotal()
})

$('#inp-quantidade').blur(() => {
    calcSubtotal()
})

function calcSubtotal(){
    let qtd = convertMoedaToFloat($('#inp-quantidade').val())
    let vl_unit = convertMoedaToFloat($('#inp-valor_unitario').val())
    $('#inp-sub_total').val(convertFloatToMoeda(qtd*vl_unit))
}

function noteSwal(m){
    swal("Observação", m, "info")
}

$('#btn-adicionais').click(() => {
    if(PRODUTO != null){
        $('#modal-adicionais').modal('show')
        calcSubtotalModal()
        let html = ''
        PRODUTO.adicionais.map((x) => {
            html += '<div class="col-lg-4 col-12 card-hover" onclick="clickAdicional('+x.adicional.id+')">'
            html += '<div class="card card-'+x.adicional.id+'">'
            html += '<div class="card-body">'
            html += '<h4>'+x.adicional.nome+' - R$ '+convertFloatToMoeda(x.adicional.valor)+'</h4>'
            html += '</div>'
            html += '</div>'
            html += '</div>'
        })

        setTimeout(() => {
            $('.adicionais').html(html)
        }, 100)
    }else{
        swal("Ops", "Selecione o produto primeiro!", "error")
    }
})

ADICIONAIS = []
function clickAdicional(id){
    let f = ADICIONAIS.find((x) => {
        return x.id == id
    })

    if(!f){
        let a = PRODUTO.adicionais.find((x) => {
            return x.adicional.id == id
        })
        ADICIONAIS.push({
            id: a.adicional.id,
            nome: a.adicional.nome,
            valor: a.adicional.valor,
        })
        $('.card-'+id).addClass('bg-success')
        $('.card-'+id).addClass('text-white')
    }else{
        ADICIONAIS = ADICIONAIS.filter((x) => {
            return x.id != id
        })
        $('.card-'+id).removeClass('bg-success')
        $('.card-'+id).removeClass('text-white')
    }
    calcSubtotalModal()
}

function calcSubtotalModal(){
    let qtd = convertMoedaToFloat($('#inp-quantidade').val())
    let vl_unit = convertMoedaToFloat($('#inp-valor_unitario').val())
    let vl_add = 0
    ADICIONAIS.map((x) => {
        vl_add += x.valor*qtd
    })
    setTimeout(() => {
        $('.subtotal_modal').html('R$ ' + convertFloatToMoeda((qtd*vl_unit)+vl_add))
    }, 50)

}

$('#btn-save-modal').click(() => {

    let qtd = convertMoedaToFloat($('#inp-quantidade').val())
    let vl_unit = convertMoedaToFloat($('#inp-valor_unitario').val())
    let vl_add = 0
    let ids = []
    ADICIONAIS.map((x) => {
        vl_add += x.valor*qtd
        ids.push(x.id)
    })
    setTimeout(() => {
        $('#inp-sub_total').val(convertFloatToMoeda((qtd*vl_unit)+vl_add))
    }, 50)

    $('#adicionais-hidden').val(ids)

})

$(document).on("change", "#inp-motoboy_id", function () {
    if($(this).val()){
        $.get(path_url + "api/motoboys/calc-comissao/", { motoboy_id: $(this).val(), pedido_id: $('#pedido_id').val() })
        .done((success) => {
            console.log(success)
            $('#inp-valor_comissao').val(convertFloatToMoeda(success))
        })
        .fail((err) => {
            console.log(err)
        })
    }
});

$(document).on("click", "#btn-finalizar", function () {
    $('#motoboy_id').val($('#inp-motoboy_id').val())
    $('#valor_comissao').val($('#inp-valor_comissao').val())
    $('#form-finish').submit()
});


