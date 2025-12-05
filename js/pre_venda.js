var DESCONTO = 0;
var VALORACRESCIMO = 0;
var PERCENTUALMAXDESCONTO = false;
var CONFIRMAITENS = false
var total_venda = 0;

$(function () {
    validaButtonSave()

    if (typeof senhaAcao !== 'undefined' && senhaAcao) {
        $('#inp-valor_unitario').attr('readonly', 1)
    }

    $("#lista_id").val('')
    setTimeout(() => {
        total_venda = convertMoedaToFloat($('.total-venda').text())
    }, 100)
})

$("#inp-tipo_pagamento").change(() => {
    validaButtonSave()
})

$("#salvar_pre_venda").on('click', function(e){
    $('#form-prevenda').submit()
});

$("#inp-produto_id").select2({
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
                lista_id: $('#lista_id').val(),
                empresa_id: empresa_id,
                usuario_id: $('#usuario_id').val(),
            };
            return query;
        },
        processResults: function (response) {
            var results = [];
            let compra = 0
            if($('#is_compra') && $('#is_compra').val() == 1){
                compra = 1
            }

            $.each(response, function (i, v) {
                var o = {};
                o.id = v.id;
                if(v.codigo_variacao){
                    o.codigo_variacao = v.codigo_variacao
                }

                o.text = v.nome

                if(parseFloat(v.valor_unitario) > 0){
                    o.text += ' R$ ' + convertFloatToMoeda(v.valor_unitario);
                }

                if(v.codigo_barras){
                    o.text += ' [' + v.codigo_barras  + ']';
                }
                o.value = v.id;
                // console.log(o)
                results.push(o);
            });
            return {
                results: results,
            };
        },
    },
});

function validaButtonSave() {
    $('#salvar_pre_venda').attr("disabled", 1)

    var tipo = $('#inp-tipo_pagamento').val()
    var tipo_row = $('#inp-tipo_pagamento_row').val()

    setTimeout(() => {
        var total = parseFloat($('.total-venda').text())

        if(total <= 0){
            return;
        }
        if (tipo != null) {

            if (tipo != '01') {
                $('#salvar_pre_venda').removeAttr("disabled")
            }
            else {
                $('#salvar_pre_venda').removeAttr("disabled")
            }
        }
    }, 500)

}

function finalizar(id) {
    $('#finalizar_pre_venda').modal('show')
    $.get(path_url + 'api/pre-venda/finalizar/' + id)
    .done((res) => {
        $('#finalizar_pre_venda .modal-body').html(res)
        setTimeout(() => {
            calcTotalFatura()
            CONFIRMAITENS = $('#confirma-itens').val()
            validaItens()
        }, 200)
    })
    .fail((e) => {
        console.log(e)
    })
}

function validaItens(){
    if(CONFIRMAITENS == 1){
        $('.btn-sbm').attr('disabled', 1)
        $('.mensagem-itens').html('* Confirme todos os itens para finalizar')
    }
    setTimeout(() => {
        $('.btn-sbm').removeAttr('disabled')

        $('.line_status').each(function () {
            if($(this).val() == 0){
                $('.btn-sbm').attr('disabled', 1)
            }
        })
    }, 20)

    // setTimeout(() => {
    //     $('#inp-codigo_barras').focus()
    // }, 500)
}


$('body').on('blur', '.valor_parcela', function () {
    calcTotalFatura()
})

// $(document).on("click", ".btn-delete-linha", function () {
//     $(this).closest("tr").remove();
//     swal("Sucesso", "Parcela removida!", "success");
//     calcTotalFatura()
// });

function calcTotalFatura() {
    var total = 0
    $(".valor_parcela").each(function () {
        total += convertMoedaToFloat($(this).val())
    })
    setTimeout(() => {
        total_fatura = total
        $('.total_parcelas').html("R$ " + convertFloatToMoeda(total))
    }, 100)
}

$(document).on("keyup", "#inp-codigo_barras", function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        encontraItemCodigoBarras($(this).val())
    }
})

$(document).on("blur", "#inp-codigo_barras", function (e) {

    encontraItemCodigoBarras($(this).val())
})

function encontraItemCodigoBarras(codigo){
    $('.line_codigo_barras').each(function () {
        if($(this).val() == codigo){
            $status = $(this).prev()
            $btn = $(this).next()
            $status.val(1)
            $btn.addClass('disabled')
            $nome = $(this).closest("tr").find('.produto_nome')
            $nome.addClass('text-success')
            
        }
    })
    setTimeout(() => {
        $('#inp-codigo_barras').val('')
        validaItens()
    }, 50)
}

$(document).on("click", ".confirma-item", function () {
    $codigoBarra = $(this).prev()
    $status = $(this).prev().prev()
    $id = $(this).prev().prev().prev()
    $status.val(1)
    $(this).addClass('disabled')
    $nome = $(this).closest("tr").find('.produto_nome')
    $nome.addClass('text-success')
    validaItens()
})

$(document).on("click", ".btn-add-tr", function () {
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

$(document).on("click", '#gerar_nfe', function () {
    let fatura = getFaturas()
    if(fatura){
        gerarNFe(fatura)
    }
})

$(document).on("click", '#gerar_nfce', function () {
    let fatura = getFaturas()
    if(fatura){
        gerarNFCe(fatura)
    }
})

$(document).on("click", '.finalizar_pre_venda', function () {
    let fatura = getFaturas()

    if(fatura){
        gerarVenda(fatura)
    }
})

function getFaturas() {
    let data = []
    let soma = 0
    $('.tipo_pagamento').each(function () {
        let tipo = $(this).val()
        let vencimento = $(this).closest('td').next().find('input').val()
        let valor = $(this).closest('td').next().next().find('input').val()
        let js = {
            tipo: tipo,
            vencimento: vencimento,
            valor: valor,
        }
        soma += convertMoedaToFloat(valor)
        data.push(js)
    })
    if(soma <= 0){
        swal("Erro", "Informe a fatura", "error")
        return null
    }
    return data
}

function gerarNFe(fatura) {

    $.post(path_url + "api/nfe/gerarNfe", {
        pre_venda_id: $('#pre_venda_id').val(),
        conta_receber: $('#inp-gerar_conta_receber').val(),
        fatura: fatura,
        empresa_id: $('#empresa_id').val(),
        usuario_id: $('#usuario_id').val()
    })
    .done((success) => {
        transmitir(success)
    })
    .fail((err) => {
        console.log(err)
    })
}

function gerarNFCe(fatura) {
    $.post(path_url + "api/nfce/gerarNfce", {
        pre_venda_id: $('#pre_venda_id').val(),
        conta_receber: $('#inp-gerar_conta_receber').val(),
        fatura: fatura,
        empresa_id: $('#empresa_id').val(),
        usuario_id: $('#usuario_id').val()
    })
    .done((success) => {
        transmitirNfce(success)
    })
    .fail((err) => {
        console.log(err)
    })
}

function gerarVenda(fatura) {

    $.post(path_url + "api/nfce/gerarVenda", {
        pre_venda_id: $('#pre_venda_id').val(),
        conta_receber: $('#inp-gerar_conta_receber').val(),
        fatura: fatura,
        empresa_id: $('#empresa_id').val(),
    })
    .done((success) => {
        swal({
            title: "Sucesso",
            text: "Venda finalizada com sucesso, deseja imprimir o comprovante?",
            icon: "success",
            buttons: true,
            buttons: ["Não", "Sim"],
            dangerMode: true,
        }).then((isConfirm) => {
            if (isConfirm) {
                window.open(path_url + 'frontbox/imprimir-nao-fiscal/' + success, "_blank")
                location.reload()
            } else {
                location.reload()
            }
        });
    })
    .fail((err) => {
        console.log(err)
        swal("Erro", err.responseJSON, "error")
    })
}

function transmitir(id) {
    console.clear()
    $.post(path_url + "api/nfe_painel/emitir", {
        id: id,
    })
    .done((success) => {
        swal("Sucesso", "NFe emitida " + success.recibo + " - chave: [" + success.chave + "]", "success")
        .then(() => {
            window.open(path_url + 'nfe/imprimir/' + id, "_blank")
            setTimeout(() => {
                location.reload()
            }, 100)
        })
    })
    .fail((err) => {
        try {
            if (err.responseJSON.error) {
                let o = err.responseJSON.error.protNFe.infProt
                swal("Algo deu errado", o.cStat + " - " + o.xMotivo, "error")
                .then(() => {
                    location.reload()
                })
            } else {
                swal("Algo deu errado", err[0], "error")
            }
        } catch {
            try {
                swal("Algo deu errado", err.responseJSON, "error")
                .then(() => {
                    location.reload()
                })
            } catch {
                swal("Algo deu errado", err.responseJSON[0], "error")
                .then(() => {
                    location.reload()
                })
            }
        }

    })
}

function transmitirNfce(id) {
    console.clear()
    $.post(path_url + "api/nfce_painel/emitir", {
        id: id,
    })
    .done((success) => {
        swal("Sucesso", "NFCe emitida " + success.recibo + " - chave: [" + success.chave + "]", "success")
        .then(() => {
            window.open(path_url + 'nfce/imprimir/' + id, "_blank")
            setTimeout(() => {
                location.reload()
            }, 100)
        })
    })
    .fail((err) => {
        console.log(err)

        swal("Algo deu errado", err.responseJSON, "error")

    })
}

$('#codBarras').keyup((v) => {
    setTimeout(() => {
        let barcode = v.target.value
        if (barcode.length > 7) {
            $('#codBarras').val('')
            $.get(path_url + "api/produtos/findByBarcode",
            {
                barcode: barcode,
                empresa_id: $('#empresa_id').val(),
                lista_id: $('#lista_id').val(),
                usuario_id: $('#usuario_id').val()
            })
            .done((e) => {
                if (e.valor_unitario) {
                    $("#inp-produto_id").append(new Option(e.nome, e.id));
                    $("#inp-quantidade").val("1,00");
                    $("#inp-valor_unitario").val(
                        convertFloatToMoeda(e.valor_unitario)
                        );
                    $("#inp-subtotal").val(
                        convertFloatToMoeda(e.valor_unitario)
                        );
                    setTimeout(() => {
                        $('.btn-add-item').trigger('click')
                    }, 20)
                } else {
                    buscarPorReferencia(barcode)
                }
                setTimeout(() => {
                    $('#codBarras').focus()
                }, 10)
            })
            .fail((err) => {
                console.log(err);
                buscarPorReferencia(barcode)
            });
        }
    }, 500)
})

function buscarPorReferencia(barcode) {
    $.get(path_url + "api/produtos/findByBarcodeReference",
    {
        barcode: barcode,
        empresa_id: $('#empresa_id').val(),
        usuario_id: $('#usuario_id').val()
    })
    .done((e) => {
        $(".table-itens tbody").append(e);
        calcTotal();
    })
    .fail((e) => {
        console.log(e);
        swal("Erro", "Produto não localizado!", "error")
    });
}


$(function () {
    setTimeout(() => {
        $('#cat_todos').first().trigger('click')
    }, 100)
})

function selectCat(id) {
    $('#cat_todos').removeClass('active')
    $('.btn-cat').removeClass('active')
    $('.btn_cat_' + id).addClass('active')
    $.get(path_url + "api/produtos/findByCategory",
    {
        lista_id: $('#lista_id').val(),
        usuario_id: $('#usuario_id').val(),
        id: id
    })
    .done((e) => {
        $('.cards-categorias').html(e)
    })
    .fail((e) => {
        console.log(e);
    });
}

function todos() {

    $('#cat_todos').addClass('active')
    $('.btn_cat').removeClass('active')

    $.get(path_url + "api/produtos/all", { 
        empresa_id: $('#empresa_id').val(),
        lista_id: $('#lista_id').val(),
        usuario_id: $('#usuario_id').val(),
    })
    .done((e) => {

        $('.cards-categorias').html(e)
    })
    .fail((e) => {
        console.log(e);
    });
}


$(function () {

    setTimeout(() => {
        $("#inp-produto_id").change(() => {

            let product_id = $("#inp-produto_id").val();

            if (product_id) {
                let codigo_variacao = $("#inp-produto_id").select2('data')[0].codigo_variacao
                $.get(path_url + "api/produtos/findWithLista",
                { 
                    produto_id: product_id,
                    lista_id: $('#lista_id').val(),
                    usuario_id: $('#usuario_id').val(),
                })
                .done((e) => {
                    if(e.variacao_modelo_id){
                        if(!codigo_variacao){
                            buscarVariacoes(product_id)
                        }else{

                            $.get(path_url + "api/variacoes/findById", {codigo_variacao: codigo_variacao})
                            .done((e) => {
                                $("#inp-variacao_id").val(codigo_variacao);
                                $("#inp-quantidade").val("1,00");
                                $("#inp-valor_unitario").val(convertFloatToMoeda(e.valor));
                                $("#inp-subtotal").val(convertFloatToMoeda(e.valor));
                            })
                            .fail((e) => {
                                console.log(e);
                            });
                        }
                    }else{
                        $("#inp-quantidade").val("1,00");
                        $("#inp-valor_unitario").val(convertFloatToMoeda(e.valor_unitario));
                        $("#inp-subtotal").val(convertFloatToMoeda(e.valor_unitario));
                    }

                    setTimeout(() => {
                        $("#inp-quantidade").focus()
                    }, 20)
                })
                .fail((e) => {
                    console.log(e);
                });
            }
        })
    }, 100)

    $("body").on("blur", ".value_unit", function () {
        let qtd = $("#inp-quantidade").val();
        let value_unit = $(this).val();
        value_unit = convertMoedaToFloat(value_unit);
        qtd = convertMoedaToFloat(qtd);
        $("#inp-subtotal").val(convertFloatToMoeda(qtd * value_unit));
    })
})

var PRODUTOID = null
function addProdutos(id) {

    $.get(path_url + "api/frenteCaixa/linhaProdutoVendaAdd", {
        id: id, 
        qtd: 0,
        lista_id: $('#lista_id').val(),
        usuario_id: $('#usuario_id').val(),
        local_id: $('#local_id').val(),
    })
    .done((e) => {
        if(!e){
            swal("Alerta", "Produto sem estoque", "warning")
        }
        $(".table-itens tbody").append(e);
        calcTotal();
    })
    .fail((e) => {
        console.log(e);
        if(e.responseJSON == 'Produto sem estoque'){
            swal("Alerta", "Produto sem estoque", "warning")
        }
        PRODUTOID = id
        if(e.status == 402){
            buscarVariacoes(id)
        }
    });
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
    $("#inp-quantidade").val("1,00");
    $("#inp-valor_unitario").val(convertFloatToMoeda(valor));
    $("#inp-subtotal").val(convertFloatToMoeda(valor));
    $("#inp-variacao_id").val(id);

    $('#modal_variacao').modal('hide')

    if(PRODUTOID != null){
        addItem()
    }
}

$("#inp-quantidade").on('keypress',function(e) {
    if(e.which == 13) {
        $("#inp-valor_unitario").focus()
    }
});

$("#inp-valor_unitario").on('keypress',function(e) {
    if(e.which == 13) {
        $('.btn-add-item').trigger('click')
    }
});

function addItem(){
    $.get(path_url + "api/produtos/findId/" + PRODUTOID)
    .done((res) => {
        // console.log(res)
        var newOption = new Option(res.nome, res.id, false, false);
        $('#inp-produto_id').html('')
        $('#inp-produto_id').append(newOption);
        setTimeout(() => {
            $('.btn-add-item').trigger('click')
        }, 10)
    })
    .fail((err) => {
        console.log(err)
    })
    PRODUTOID = null
}

$("#lista_precos select").each(function () {

    let id = $(this).prop("id");

    if (id == "inp-lista_preco_id") {

        $(this).select2({
            minimumInputLength: 2,
            language: "pt-BR",
            placeholder: "Digite para buscar a lista de preço",
            theme: "bootstrap4",
            dropdownParent: $(this).parent(),
            ajax: {
                cache: true,
                url: path_url + "api/lista-preco/pesquisa",
                dataType: "json",
                data: function (params) {
                    console.clear();

                    var query = {
                        pesquisa: params.term,
                        empresa_id: $("#empresa_id").val(),
                        usuario_id: $("#usuario_id").val(),
                        tipo_pagamento_lista: $("#inp-tipo_pagamento_lista").val(),
                        funcionario_lista_id: $("#inp-funcionario_lista_id").val(),
                    };
                    return query;
                },
                processResults: function (response) {
                    console.log(response)
                    var results = [];

                    $.each(response, function (i, v) {
                        var o = {};
                        o.id = v.id;

                        o.text = v.nome + " " + v.percentual_alteracao + "%";
                        o.value = v.id;
                        results.push(o);
                    });
                    return {
                        results: results,
                    };
                },
            },
        });
    }
});

function selecionaLista(){
    let tipo_pagamento_lista = $('#inp-tipo_pagamento_lista').val()
    let funcionario_lista_id = $('#inp-funcionario_lista_id').val()
    let lista_preco_id = $('#inp-lista_preco_id').val()

    if(!lista_preco_id){
        swal("Alerta", "Selecione a lista", "warning")
        return;
    }

    if(tipo_pagamento_lista){
        $('#inp-tipo_pagamento').val(tipo_pagamento_lista).change()
    }
    if(funcionario_lista_id){
        $.get(path_url + "api/funcionarios/find", {id: funcionario_lista_id})
        .done((res) => {
            console.log(res)
            var newOption = new Option(res.nome, res.id, true, false);
            $('#inp-funcionario_id').append(newOption);
            $('.funcionario_selecionado').text(res.nome)

        })
        .fail((err) => {
            console.log(err);
        });
    }

    $('#lista_id').val(lista_preco_id)
    setTimeout(() => {
        todos()
    }, 10)
    setTimeout(() => {
        $("#codBarras").focus();
    }, 500)
}

$(".btn-add-item").click(() => {
    let qtd = $("#inp-quantidade").val();
    let value_unit = $("#inp-valor_unitario").val();
    value_unit = convertMoedaToFloat(value_unit);
    qtd = convertMoedaToFloat(qtd);
    $("#inp-subtotal").val(convertFloatToMoeda(qtd * value_unit));
    setTimeout(() => {
        let abertura = $('#abertura').val()

        if (abertura) {
            let qtd = $("#inp-quantidade").val();
            let value_unit = $("#inp-valor_unitario").val();
            let sub_total = $("#inp-subtotal").val();
            let product_id = $("#inp-produto_id").val();
            // let key = $("#inp-key").val()
            let variacao_id = $("#inp-variacao_id").val();

            if (qtd && value_unit && product_id && sub_total) {
                let dataRequest = {
                    qtd: qtd,
                    value_unit: value_unit,
                    sub_total: sub_total,
                    product_id: product_id,
                    variacao_id: variacao_id,
                    local_id: $('#local_id').val(),
                };
                $.get(path_url + "api/frenteCaixa/linhaProdutoVenda", dataRequest)
                .done((e) => {
                    $(".table-itens tbody").append(e);
                    calcTotal();
                })
                .fail((e) => {
                    console.log(e);
                    swal("Erro", e.responseJSON, "error")
                });
            } else {
                swal(
                    "Atenção",
                    "Informe corretamente os campos para continuar!",
                    "warning"
                    );
            }
        } else {
            swal(
                "Atenção",
                "Abra o caixa para continuar!",
                "warning"
                ).then(() => {
                    validaCaixa()
                })
            }
        }, 100);
});

function validaCaixa() {
    let abertura = $('#abertura').val()
    if (!abertura) {
        $('#modal-abrir_caixa').modal('show')
        return
    }
}


$("body").on("click", "#btn-incrementa", function () {
    let inp = $(this).closest('div.input-group-append').prev()[0]
    if (inp.value) {
        let v = convertMoedaToFloat(inp.value)
        v += 1
        inp.value = convertFloatToMoeda(v)
        calcSubTotal()
    }
})

$("body").on("click", "#btn-subtrai", function () {
    let inp = $(this).closest('.input-group').find('input')[0]
    if (inp.value) {
        let v = convertMoedaToFloat(inp.value)
        v -= 1
        inp.value = convertFloatToMoeda(v)

        calcSubTotal()
    }
})

$(".table-itens").on('click', '.btn-delete-row', function () {
    $(this).closest('tr').remove();
    swal("Sucesso", "Produto removido!", "success")
    calcTotal()
});


function calcSubTotal(e) {

    $(".line-product").each(function () {
        $qtd = $(this).find('.qtd')[0]
        $value = $(this).find('.value-unit')[0]
        $sub = $(this).find('.subtotal-item')[0]

        let qtd = convertMoedaToFloat($qtd.value)
        let value = convertMoedaToFloat($value.value)
        if (qtd <= 0) {
            $(this).remove()
        } else {
            $sub.value = convertFloatToMoeda(qtd * value)
        }
    })
    setTimeout(() => {
        calcTotal()
    }, 10)
}

function setaDesconto() {
    if (total_venda == 0) {
        swal("Erro", "Total da venda é igual a zero", "warning");
    } else {
        if(senhaAcao != ''){
            swal({
                title: "Senha para desconto",
                text: "Informe a senha para continuar",
                content: {
                    element: "input",
                    attributes: {
                        type: "password",
                        placeholder: "Digite a senha"
                    }
                },
                button: {
                    text: "Ok",
                    closeModal: false,
                    type: "error",
                },
            }).then((v) => {
                if(v == senhaAcao){
                    modalDesconto()
                }else{
                    swal("Erro", "Senha incorreta!", "error")
                }
            })
        }else{
            modalDesconto()
        }
    }
}

function modalDesconto(){
    swal({
        title: "Valor desconto?",
        text: "Ultilize ponto(.) ao invés de virgula!",
        content: "input",
        button: {
            text: "Ok",
            closeModal: false,
            type: "error",
        },
    }).then((v) => {
        if (v) {
            let desconto = v;
            if (desconto.substring(0, 1) == "%") {
                let perc = desconto.substring(1, desconto.length);
                DESCONTO = TOTAL * (perc / 100);
                if (PERCENTUALMAXDESCONTO > 0) {
                    if (perc > PERCENTUALMAXDESCONTO) {
                        swal.close();
                        setTimeout(() => {
                            swal(
                                "Erro",
                                "Máximo de desconto permitido é de " +
                                PERCENTUALMAXDESCONTO +
                                "%",
                                "error"
                                );
                            $("#valor_desconto").html("0,00");
                        }, 500);
                    }
                }
                if (DESCONTO > 0) {
                    $("#valor_item").attr("disabled", "disabled");
                    $(".btn-mini-desconto").attr(
                        "disabled",
                        "disabled"
                        );
                } else {
                    $("#valor_item").removeAttr("disabled");
                    $(".btn-mini-desconto").removeAttr("disabled");
                }
            } else {
                desconto = desconto.replace(",", ".");
                DESCONTO = parseFloat(desconto);
                if (PERCENTUALMAXDESCONTO > 0) {
                    let tempDesc =
                    (TOTAL * PERCENTUALMAXDESCONTO) / 100;
                    if (tempDesc < DESCONTO) {
                        swal.close();

                        setTimeout(() => {
                            swal(
                                "Erro",
                                "Máximo de desconto permitido é de R$ " +
                                parseFloat(tempDesc),
                                "error"
                                );
                            $("#valor_desconto").html("0,00");
                        }, 500);
                    }
                }
                if (DESCONTO > 0) {
                    $("#valor_item").attr("disabled", "disabled");
                    $(".btn-mini-desconto").attr(
                        "disabled",
                        "disabled"
                        );
                } else {
                    $("#valor_item").removeAttr("disabled");
                    $(".btn-mini-desconto").removeAttr("disabled");
                }
            }
            if (desconto.length == 0) DESCONTO = 0;
            $("#valor_desconto").html(convertFloatToMoeda(DESCONTO));
            $("#inp-valor_desconto").val(convertFloatToMoeda(DESCONTO));
            calcTotal();
            validaButtonSave()
        }
        swal.close();
        $("#codBarras").focus();
    });
}


function setaAcrescimo() {

    if (total_venda == 0) {
        swal("Erro", "Total da venda é igual a zero", "warning");
    } else {
        if(senhaAcao != ''){
            swal({
                title: "Senha para acréscimo",
                text: "Informe a senha para continuar",
                content: {
                    element: "input",
                    attributes: {
                        type: "password",
                        placeholder: "Digite a senha"
                    }
                },
                button: {
                    text: "Ok",
                    closeModal: false,
                    type: "error",
                },
            }).then((v) => {
                if(v == senhaAcao){
                    modalAcrescimo()
                }else{
                    swal("Erro", "Senha incorreta!", "error")
                }
            })
        }else{
            modalAcrescimo()
        }
    }
}

function modalAcrescimo(){
    swal({
        title: "Valor acrescimo?",
        text: "Ultilize ponto(.) ao invés de virgula!",
        content: "input",
        button: {
            text: "Ok",
            closeModal: false,
            type: "error",
        },
    }).then((v) => {
        if (v) {
            let acrescimo = v;
            if (acrescimo > 0) {
                DESCONTO = 0;
                $("#valor_desconto").html(convertFloatToMoeda(DESCONTO));
            }
            let total = total_venda;
            if (acrescimo.substring(0, 1) == "%") {
                let perc = acrescimo.substring(1, acrescimo.length);
                VALORACRESCIMO = total * (perc / 100);
            } else {
                acrescimo = acrescimo.replace(",", ".");
                VALORACRESCIMO = parseFloat(acrescimo);
            }
            if (acrescimo.length == 0) VALORACRESCIMO = 0;
            VALORACRESCIMO = parseFloat(VALORACRESCIMO);
            $("#valor_acrescimo").html(convertFloatToMoeda(VALORACRESCIMO));
            $("#inp-valor_acrescimo").val(convertFloatToMoeda(VALORACRESCIMO));
            calcTotal();
            $("#codBarras").focus();
            validaButtonSave()

        }
        swal.close();
    });
}

$("#cliente select").each(function () {
    let id = $(this).prop("id");
    if (id == "inp-cliente_id") {
        $(this).select2({
            minimumInputLength: 2,
            language: "pt-BR",
            placeholder: "Digite para buscar o cliente",
            width: "100%",
            theme: "bootstrap4",
            dropdownParent: $(this).parent(),
            ajax: {
                cache: true,
                url: path_url + "api/clientes/pesquisa",
                dataType: "json",
                data: function (params) {
                    console.clear();
                    var query = {
                        pesquisa: params.term,
                        empresa_id: $("#empresa_id").val(),
                    };
                    return query;
                },
                processResults: function (response) {

                    var results = [];
                    $.each(response, function (i, v) {
                        var o = {};
                        o.id = v.id;

                        o.text = v.razao_social + " - " + v.cpf_cnpj;
                        o.value = v.id;
                        results.push(o);

                    });
                    return {
                        results: results,
                    };
                },
            },
        });
    }
});

$(document).on("change", "#inp-cliente_id", function () {
    let cliente_id = $(this).val()

    $.get(path_url + "api/clientes/find/" + cliente_id)
    .done((cliente) => {
        // console.log(cliente)
        $('.cliente_selecionado').text(cliente.razao_social);
        
    })
    .fail((err) => {
        console.log(err);
    });
})

function calcTotal() {
    var total = 0;
    $(".subtotal-item").each(function () {
        total += convertMoedaToFloat($(this).val());
    });
    setTimeout(() => {
        total_venda = total;
        $(".total-venda").html(
            convertFloatToMoeda(total + parseFloat(VALORACRESCIMO) - parseFloat(DESCONTO))
            );
        $('#inp-valor_total').val(
            convertFloatToMoeda(total + parseFloat(VALORACRESCIMO) - parseFloat(DESCONTO))
            );
        $(".total-venda-modal").html(
            convertFloatToMoeda(total + VALORACRESCIMO - DESCONTO)
            );
        $('#inp-valor_integral').val(convertFloatToMoeda(total_venda))

        $('#inp-quantidade').val('')
        $('#inp-valor_unitario').val('')
        $('#inp-produto_id').val('').change()
    }, 100);
}

$(function () {
    let data = new Date
    let dataFormatada = (data.getFullYear() + "-" + adicionaZero((data.getMonth() + 1)) + "-" + adicionaZero(data.getDate()));
    $('.data_atual').val(dataFormatada)
})
function adicionaZero(numero) {
    if (numero <= 9)
        return "0" + numero;
    else
        return numero;
}


$(".btn-add-payment").click(() => {
    let tipo_pagamento_row = $("#inp-tipo_pagamento_row").val();
    let vencimento = $("#inp-data_vencimento_row").val();
    let valor_integral_row = $("#inp-valor_row").val();
    let obs_row = $("#inp-observacao_row").val();

    validaButtonSave();
    let totalGeral = convertMoedaToFloat($('.total-venda').text())

    let v = convertMoedaToFloat(valor_integral_row);

    if (v + total_payment <= totalGeral) {
        if (vencimento && valor_integral_row && tipo_pagamento_row) {
            let dataRequest = {
                data_vencimento_row: vencimento,
                valor_integral_row: valor_integral_row,
                obs_row: obs_row,
                tipo_pagamento_row: tipo_pagamento_row,
            };

            $.get(path_url + "api/frenteCaixa/linhaParcelaVenda", dataRequest)
            .done((e) => {
                $(".table-payment tbody").append(e);
                calcTotalPayment();

            })
            .fail((e) => {
                console.log(e);
            });
        } else {
            swal(
                "Atenção",
                "Informe corretamente os campos para continuar!",
                "warning"
                );
        }
    } else {
        swal(
            "Atenção",
            "A soma das parcelas não corresponde com o valor total da venda",
            "warning"
            );
    }
});

var total_payment = 0;
function calcTotalPayment() {
    $('.btn-modal-multiplo').attr("disabled", true)
    $('#btn-pag_row').attr("disabled", true)

    var total = 0;
    $(".valor_integral").each(function () {
        total += convertMoedaToFloat($(this).val());
    });
    let totalGeral = convertMoedaToFloat($('.total-venda').text())

    setTimeout(() => {
        total_payment = total;
        $(".sum-payment").html("R$ " + convertFloatToMoeda(total));

        $(".sum-restante").html("R$ " + convertFloatToMoeda(totalGeral - total));
    }, 100);

    let dif = totalGeral - total;

    let diferenca = dif.toFixed(2);

    if (diferenca <= 10) {
        $("#btn-pag_row").removeAttr("disabled")
    }
    // console.log("dif", dif)
    if(dif == 0){
        $('.btn-modal-multiplo').removeAttr("disabled")
    }
}


// $(".table-payment").on("click", ".btn-delete-row", function () {
//     $(this).closest("tr").remove();
//     swal("Sucesso", "Parcela removida!", "success");
//     calcTotalPayment();
// });

$(document).delegate(".btn-delete-row", "click", function (e) {
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

$('.funcionario-venda').click(() => {
    let funcionario_id = $('#inp-funcionario_id').val()
    $.get(path_url + "api/funcionarios/find/", {id: funcionario_id})
    .done((e) => {
        $('.funcionario_selecionado').text(e.nome)
    })
    .fail((e) => {
        console.log(e);
    });
})

$(".modal-funcioario select").each(function () {

    let id = $(this).prop("id");

    if (id == "inp-funcionario_id") {

        $(this).select2({
            minimumInputLength: 2,
            language: "pt-BR",
            placeholder: "Digite para buscar o funcionário",
            theme: "bootstrap4",
            dropdownParent: $(this).parent(),
            ajax: {
                cache: true,
                url: path_url + "api/funcionarios/pesquisa",
                dataType: "json",
                data: function (params) {
                    console.clear();
                    var query = {
                        pesquisa: params.term,
                        empresa_id: $("#empresa_id").val(),
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
    }
});
