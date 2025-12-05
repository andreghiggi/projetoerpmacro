$('body').on('change', '.cliente_id', function () {

    let cliente = $('.cliente_id').val()
    if (cliente != '') {
        getClient(cliente)
    } else {

    }
})

$('body').on('change', '.fornecedor_id', function () {
    let fornecedor = $('.fornecedor_id').val()
    if (fornecedor != '') {
        getFornecedor(fornecedor)
    } else {

    }
})

$('body').on('change', '.transportadora_id', function () {
    let transportadora = $('.transportadora_id').val()
    if (transportadora != '') {
        getTransp(transportadora)
    } else {

    }
})

$('.btn-add-tr-nfe').on("click", function () {
    console.clear()
    var $table = $(this)
    .closest(".row")
    .prev()
    .find(".table-dynamic");

    var hasEmpty = false;

    $table.find("input, select").each(function () {
        if (($(this).val() == "" || $(this).val() == null) && $(this).attr("type") != "hidden" && $(this).attr("type") != "file" && !$(this).hasClass("ignore")) {
            hasEmpty = true;
        }
    });

    if (hasEmpty) {
        swal(
            "Atenção",
            "Preencha todos os campos antes de adicionar novos.",
            "warning"
            );
        return;
    }
    // $table.find("select.select2").select2("destroy");
    var $tr = $table.find(".dynamic-form").first();
    $tr.find("select.select2").select2("destroy");
    var $clone = $tr.clone();
    $clone.show();

    $clone.find("input,select").val("");
    $clone.find("span").html("");
    
    $table.append($clone);
    setTimeout(function () {
        $("tbody select.select2").select2({
            language: "pt-BR",
            width: "100%",
            theme: "bootstrap4"
        });

        $("tbody #inp-produto_id").select2({
            minimumInputLength: 2,
            language: "pt-BR",
            placeholder: "Digite para buscar o produto",
            width: "100%",
            theme: "bootstrap4",
            ajax: {
                cache: true,
                url: path_url + "api/produtos",
                dataType: "json",
                data: function (params) {
                    let empresa_id = $('#empresa_id').val()
                    console.clear();
                    var query = {
                        pesquisa: params.term,
                        empresa_id: empresa_id
                    };
                    return query;
                },
                processResults: function (response) {
                    var results = [];
                    let compra = 0
                    if($('#is_compra') && $('#is_compra').val() == 1){
                        compra = 1
                    }
                    console.log(response)
                    $.each(response, function (i, v) {
                        var o = {};
                        o.id = v.id;
                        if(v.codigo_variacao){
                            o.codigo_variacao = v.codigo_variacao
                        }

                        o.text = v.nome;
                        if(compra == 0){
                            o.text += ' R$ ' + convertFloatToMoeda(v.valor_unitario);
                        }else{
                            o.text += ' R$ ' + convertFloatToMoeda(v.valor_compra);
                        }
                        if(v.codigo_barras){
                            o.text += ' [' + v.codigo_barras  + ']';
                        }
                        o.value = v.id;
                        results.push(o);
                    });
                    return {
                        results: results,
                    };
                },
            },
        });
    }, 100);

})

$('body').on('change', '#inp-tpNF', function () {
    let tpNF = $('#inp-tpNF').val()
    if (tpNF == 1) {
        $('.div-conta-receber').removeClass('d-none')
        $('.div-conta-pagar').addClass('d-none')
    } else {
        $('.div-conta-receber').addClass('d-none')
        $('.div-conta-pagar').removeClass('d-none')
    }
})

$(function () {
    setTimeout(() => {
        let compra = $('#is_compra').val()
        if (compra == 1) {
            $('.div-conta-pagar').removeClass('d-none')
            $('.div-conta-receber').addClass('d-none')
        }
    }, 300)
})

function adicionaZero(numero) {
    if (numero <= 9)
        return "0" + numero;
    else
        return numero;
}
$(function () {
    let data = new Date
    let dataFormatada = (data.getFullYear() + "-" + adicionaZero((data.getMonth() + 1)) + "-" + adicionaZero(data.getDate()));
    $('.date_atual').val(dataFormatada)

    $('#inp-gerar_conta_receber').val(0).change()
    $('#inp-gerar_conta_pagar').val(0).change()

    if ($('.fornecedor_id').val()) {
        getFornecedor($('.fornecedor_id').val())
    }
})


$(document).on("change", ".produto_id", function () {
    let product_id = $(this).val()

    let codigo_variacao = $(this).select2('data')[0].codigo_variacao
    if (product_id) {

        $qtd = $(this).closest('td').next().find('input');
        $vlUnit = $qtd.closest('td').next().find('input');
        $sub = $vlUnit.closest('td').next().find('input');
        $perc_icms = $sub.closest('td').next().find('input');
        $perc_pis = $perc_icms.closest('td').next().find('input');
        $perc_cofins = $perc_pis.closest('td').next().find('input');
        $perc_ipi = $perc_cofins.closest('td').next().find('input');
        $perc_red_bc = $perc_ipi.closest('td').next().find('input');
        $cfop_estadual = $perc_red_bc.closest('td').next().find('input');
        $ncm = $cfop_estadual.closest('td').next().find('input');
        $codben = $ncm.closest('td').next().find('input');
        $cst_csosn = $codben.closest('td').next().find('select');
        $cst_pis = $cst_csosn.closest('td').next().find('select');
        $cst_cofins = $cst_pis.closest('td').next().find('select');
        $cst_ipi = $cst_cofins.closest('td').next().find('select');

        $.get(path_url + "api/produtos/find", 
        { 
            produto_id: product_id, 
            cliente_id: $('#inp-cliente_id').val(),
            fornecedor_id: $('#inp-fornecedor_id').val(),
            entrada: $('#is_compra') ? $('#is_compra').val() : 0
        })
        .done((e) => {
            // console.clear()
            let cfop = e.cfop_atual

            $qtd.val('1,00')
            let value_unit = 0
            let compra = $('#is_compra').val()
            if (compra == '1') {
                if (e.valor_compra) {
                    value_unit = e.valor_compra
                } else {
                    value_unit = 0
                }
            } else {
                value_unit = e.valor_unitario
            }
            $vlUnit.val(convertFloatToMoeda(value_unit))
            $sub.val(convertFloatToMoeda(value_unit))
            $perc_icms.val(e.perc_icms)
            $perc_pis.val(e.perc_pis)
            $perc_cofins.val(e.perc_cofins)
            $perc_ipi.val(e.perc_ipi)
            $perc_red_bc.val(e.perc_red_bc)
            $cfop_estadual.val(cfop)
            $ncm.val(e.ncm)
            $cst_csosn.val(e.cst_csosn).change()
            $cst_pis.val(e.cst_pis).change()
            $cst_cofins.val(e.cst_cofins).change()
            $cst_ipi.val(e.cst_ipi).change()
            calcTotal()
            calTotalNfe()
            limpaFatura()

            if(e.variacao_modelo_id && !codigo_variacao){
                buscarVariacoes(product_id)
            }

            if(codigo_variacao > 0){
                setarVariacao(codigo_variacao)
            }
        })
        .fail((e) => {
            console.log(e)
        })
    }
})

function setarVariacao(codigo_variacao){
    $varicao = $('.table-produtos').find("tr").last().find('input')[0]
    $varicao.value = codigo_variacao
}

function buscarVariacoes(produto_id){
    $.get(path_url + "api/variacoes/find", { produto_id: produto_id })
    .done((res) => {
        $('#modal_variacao .modal-body').html(res)
        $('#modal_variacao').modal('show')
    })
    .fail((err) => {
        console.log(err)
        swal("Algo deu errado", "Erro ao buscar variações", "error")
    })
}

function selecionarVariacao(id, descricao, valor){
    $varicao = $('.table-produtos').find("tr").last().find('input')[0]
    $qtd = $('.table-produtos').find("tr").last().find('input')[1]
    $vlUnit = $('.table-produtos').find("tr").last().find('input')[2]
    $sub = $('.table-produtos').find("tr").last().find('input')[3]
    $select = $('.table-produtos').find("tr").last().find('select').first()
    $varicao.value = id
    $qtd.value = '1,00'
    $vlUnit.value = (convertFloatToMoeda(valor))
    $sub.value = (convertFloatToMoeda(valor))
    $select.closest('td').append('<span>variação: <strong>'+descricao+'</strong></span>')
    $('#modal_variacao').modal('hide')
    calcTotal()
    calTotalNfe()
    limpaFatura()
}

$('.valor_frete, .desconto, .acrescimo').blur(() => {
    limpaFatura()
})


function limpaFatura() {

    $('#body-pagamento tr').each(function (e, x) {
        if (e == 0) {
            setTimeout(() => {
                let total = convertMoedaToFloat($('.total_prod').val())
                let valor_frete = convertMoedaToFloat($('.valor_frete').val())
                let desconto = convertMoedaToFloat($('.desconto').val())
                let acrescimo = convertMoedaToFloat($('.acrescimo').val())
                // $('.valor_fatura').first().val(convertFloatToMoeda(total + valor_frete - desconto + acrescimo))
                $('.tipo_pagamento').first().val('').change()
                let data = new Date
                let dataFormatada = (data.getFullYear() + "-" + adicionaZero((data.getMonth() + 1)) + "-" + adicionaZero(data.getDate()));
                $('.date_atual').first().val(dataFormatada)
            }, 500)

        } else {
            console.log('sim')
            x.remove();
        }
    })
}

$('body').on('blur', '.valor_unit', function () {
    $qtd = $(this).closest('td').prev().find('input');
    $sub = $(this).closest('td').next().find('input');
    let value_unit = $(this).val();
    value_unit = convertMoedaToFloat(value_unit)
    let qtd = convertMoedaToFloat($qtd.val())
    $sub.val(convertFloatToMoeda(qtd * value_unit))
})


$('.btn-add-tr').on("click", function () {

    var $table = $(this)
    .closest(".row")
    .prev()
    .find(".table-dynamic");
    var hasEmpty = false;
    $table.find("input, select").each(function () {
        if (($(this).val() == "" || $(this).val() == null) && $(this).attr("type") != "hidden" && $(this).attr("type") != "file" && !$(this).hasClass("ignore")) {
            hasEmpty = true;
        }
    });
    if (hasEmpty) {
        swal(
            "Atenção",
            "Preencha todos os campos antes de adicionar novos.",
            "warning"
            );
        return;
    }
    // $table.find("select.select2").select2("destroy");
    var $tr = $table.find(".dynamic-form").first();
    $tr.find("select.select2").select2("destroy");
    var $clone = $tr.clone();
    $clone.show();
    $clone.find("input,select").val("");
    $table.append($clone);
    setTimeout(function () {
        $("tbody select.select2").select2({
            language: "pt-BR",
            width: "100%",
            theme: "bootstrap4"
        });
    }, 100);
})

$(document).delegate(".btn-remove-tr", "click", function (e) {
    e.preventDefault();
    swal({
        title: "Você esta certo?",
        text: "Deseja remover esse item mesmo?",
        icon: "warning",
        buttons: true
    }).then(willDelete => {
        if (willDelete) {
            var trLength = $(this)
            .closest("tr")
            .closest("tbody")
            .find("tr")
            .not(".dynamic-form-document").length;
            if (!trLength || trLength > 1) {
                $(this)
                .closest("tr")
                .remove();
                calcTotal()
                calTotalNfe()
                limpaFatura()
            } else {
                swal(
                    "Atenção",
                    "Você deve ter ao menos um item na lista",
                    "warning"
                    );
            }
        }
    });
});


function getClient(cliente) {
    $.get(path_url + "api/clientes/find/" + cliente)
    .done((res) => {
        $('#inp-cliente_nome').val(res.razao_social)
        $('#inp-nome_fantasia').val(res.nome_fantasia)
        $('#inp-cliente_cpf_cnpj').val(res.cpf_cnpj)
        $('#inp-ie').val(res.ie)
        $('#inp-telefone').val(res.telefone)
        $('#inp-contribuinte').val(res.contribuinte).change()
        $('#inp-consumidor_final').val(res.consumidor_final).change()
        $('#inp-email').val(res.email)
        $('#inp-cidade_cliente').val(res.cidade_id).change()
        $('#inp-cliente_rua').val(res.rua)
        $('#inp-cliente_numero').val(res.numero)
        $('#inp-cep').val(res.cep)
        $('#inp-cliente_bairro').val(res.bairro)
        $('#inp-complemento').val(res.complemento)
    })
    .fail((err) => {
        console.error(err)
    })
}

function getFornecedor(fornecedor) {
    $.get(path_url + "api/fornecedores/find/" + fornecedor)
    .done((res) => {
        $('#inp-fornecedor_nome').val(res.razao_social)
        $('#inp-nome_fantasia').val(res.nome_fantasia)
        $('#inp-fornecedor_cpf_cnpj').val(res.cpf_cnpj)
        $('#inp-ie').val(res.ie)
        $('#inp-telefone').val(res.telefone)
        $('#inp-contribuinte').val(res.contribuinte).change()
        $('#inp-consumidor_final').val(res.consumidor_final).change()
        $('#inp-email').val(res.email)
        $('#inp-fornecedor_cidade').val(res.cidade_id).change()
        $('#inp-fornecedor_rua').val(res.rua)
        $('#inp-fornecedor_numero').val(res.numero)
        $('#inp-cep').val(res.cep)
        $('#inp-fornecedor_bairro').val(res.bairro)
        $('#inp-complemento').val(res.complemento)
    })
    .fail((err) => {
        console.error(err)
    })
}

function getTransp(transportadora) {
    $.get(path_url + "api/transportadoras/find/" + transportadora)
    .done((res) => {
        $('#inp-razao_social_transp').val(res.razao_social)
        $('#inp-nome_fantasia_transp').val(res.nome_fantasia)
        $('#inp-cpf_cnpj_transp').val(res.cpf_cnpj)
        $('#inp-ie_transp').val(res.ie)
        $('#inp-antt').val(res.antt)
        $('#inp-telefone_transp').val(res.telefone)
        $('#inp-email_transp').val(res.email)
        $('#inp-cidade_id').val(res.cidade_id).change()
        $('#inp-rua_transp').val(res.rua)
        $('#inp-numero_transp').val(res.numero)
        $('#inp-cep_transp').val(res.cep)
        $('#inp-bairro_transp').val(res.bairro)
        $('#inp-complemento_transp').val(res.complemento)
    })
    .fail((err) => {
        console.error(err)
    })
}

$(function () {
    $('body').on('blur', '.acrescimo, .desconto', function () {
        calTotalNfe()
    })
})

$(function () {
    calcTotal()
    $('body').on('blur', '.produto_id', function () {
        calcTotal()
    })

    calcTotal()
    $('body').on('blur', '.sub_total', function () {
        calcTotal()
    })
})


// CÁLCULO TOTAL DE PRODUTOS
var total_venda = 0
function calcTotal() {
    var total = 0
    $(".sub_total").each(function () {
        total += convertMoedaToFloat($(this).val())
    })
    setTimeout(() => {
        total_venda = total
        $('.total_prod').html("R$ " + convertFloatToMoeda(total))
        $('.total_prod').val(total)
        calTotalNfe()
    }, 100)
}

$(function () {
    calcTotalFatura()
    $('body').on('blur', '.valor_fatura', function () {
        calcTotalFatura()
    })
})

function calcTotalFatura() {
    var total = 0
    $(".valor_fatura").each(function () {
        total += convertMoedaToFloat($(this).val())
    })

    setTimeout(() => {
        // let acrescimo = convertMoedaToFloat($('#inp-acrescimo').val())
        // let desconto = convertMoedaToFloat($('#inp-desconto').val())
        // let total_nfe = $('.total_nfe').val()
        total_fatura = total
        $('.total_fatura').html("R$ " + convertFloatToMoeda(total))
    }, 100)
}


// CALCULO TOTAL DA NFE
$(function () {
    $('body').on('blur', '.valor_frete', function () {
        calTotalNfe()
    })
})
var total_frete = 0
var total_nfe = 0
function calTotalNfe() {
    let acrescimo = convertMoedaToFloat($('#inp-acrescimo').val())
    let desconto = convertMoedaToFloat($('#inp-desconto').val())
    let total_fr = convertMoedaToFloat($("#inp-valor_frete").val())
    let total_prod = parseFloat($('.total_prod').val())

    setTimeout(() => {
        total_frete = total_fr
        total_nfe = total_prod + total_fr + acrescimo - desconto
        $('.total_frete').html("R$ " + convertFloatToMoeda(total_fr))
        $('.total_nfe').html("R$ " + convertFloatToMoeda(total_nfe))
        // $('.valor_fatura').val(convertFloatToMoeda(total_nfe))
        $('.valor_total').val(convertFloatToMoeda(total_nfe))
        calcTotalFatura()
    }, 100)
}


$('.btn-salvar-nfe').click(() => {
    addClassRequired()
})

function addClassRequired() {
    let infMsg = ""
    $("body").find('input, select').each(function () {
        if ($(this).prop('required')) {
            if ($(this).val() == "") {
                try {
                    infMsg += $(this).prev()[0].textContent + "\n"
                } catch { }
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

