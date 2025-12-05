var DESCONTO = 0;
var VALORCREDITO = 0;
var VALORFRETE = 0;
var VALORACRESCIMO = 0;
var PERCENTUALMAXDESCONTO = false;
function isTablet() {
  const ua = navigator.userAgent.toLowerCase();
  return /ipad|android(?!.*mobile)|tablet|kindle|playbook/.test(ua);
}

$('.btn-clinte').click(() => {
    $('#cpf_nota').modal('hide')
    $('#finalizar_venda').modal('hide')
    $('#cliente').modal('show')
})

$('.leitor_desativado').click(() => {
    $('.leitor_ativado').removeClass('d-none')
    $('.leitor_desativado').addClass('d-none')
    $('#codBarras').focus()
})

function ativaTef(){

    $.get(path_url + "api/tef/verifica-ativo",
    {
        empresa_id: $('#empresa_id').val(),
        usuario_id: $('#usuario_id').val(),
    })
    .done((data) => {
        console.log(data)
    })
    .fail((e) => {
        // console.log(e);
        toastr.error("TEF LOG: " + e.responseJSON);

        // alert('oi')
        $(".tp-pag option[value='30']").remove();
        $(".tp-pag option[value='31']").remove();
        $(".tp-pag option[value='32']").remove();
    });
}

$(function () {

    if (isTablet()) {
        $('.div-btns .widget-icon-box').css({'height': '240px'})
    }
    $('#codBarras').val('')
    let config_tef = $("#config_tef").val()

    if($("#definir_vendedor_pdv").val() == 1){
        $('#funcionario').modal('show')
    }

    if(config_tef == 1){
        ativaTef()
    }else{
        $(".tp-pag option[value='30']").remove();
        $(".tp-pag option[value='31']").remove();
        $(".tp-pag option[value='32']").remove();
    }
    $("#inp-variacao_id").val('')
    $("#lista_id").val('')

    if($('#pedido_desconto').length){
        DESCONTO = $('#pedido_desconto').val()
        // VALORACRESCIMO = $('#pedido_valor_entrega').val()
        VALORFRETE = $('#pedido_valor_entrega').val()
        if(VALORFRETE){
            $('#valor_frete').val(convertFloatToMoeda(VALORFRETE))
            $('.valor-frete').text("R$ " + convertFloatToMoeda(VALORFRETE))
        }
        $("#valor_desconto").text("R$ " + convertFloatToMoeda(DESCONTO));
        $("#valor_acrescimo").text("R$ " + convertFloatToMoeda(VALORACRESCIMO));
    }

    $('#mousetrapTitle').click(() => {
        $('#codBarras').focus()
    })

    $('#codBarras').focus(() => {
        $('#mousetrapTitle').css('display', 'none');
        $('.leitor_ativado').removeClass('d-none')
        $('.leitor_desativado').addClass('d-none')
    });
    $('#codBarras').focusout(() => {
        $('#mousetrapTitle').css('display', 'flex');
        $('.leitor_desativado').removeClass('d-none')
        $('.leitor_ativado').addClass('d-none')
    });

    validateButtonSave()
    calcTotal()

    setTimeout(() => {
        if(senhaAcao != ''){
            $('#inp-valor_unitario').attr('readonly', 1)
        }
    }, 100)

    if(!$('#venda_id').val()){
        $('#inp-tipo_pagamento').val('').change()
    }else{
        setTimeout(() => {
            DESCONTO = convertMoedaToFloat($('#valor_desconto').text())
            VALORACRESCIMO = convertMoedaToFloat($('#valor_acrescimo').text())
            VALORFRETE = convertMoedaToFloat($('.valor-frete').text())
            validateButtonSave()
            calcTotal()

        }, 300)
    }

    $('#inp-tipo_pagamento_row').val('').change()
    $('#inp-valor_row').val('')
    // $('#inp-data_vencimento_row').val('')
    $('#inp-valor_recebido').val('')
    $('#inp-troco').val('')
    $('#inp-valor_credito').val('')

    if($('#acrescimo_pedido').length){
        VALORACRESCIMO = $('#acrescimo_pedido').val()
        $("#valor_acrescimo").text("R$ " + convertFloatToMoeda(VALORACRESCIMO));
    }

    // consultaStatusTef(2075408)

})

$('.btn-gerar-fatura').click(() => {
    $('#pagamento_multiplo').modal('hide')
    $('#modal_fatura_venda').modal('show')
    $('.lbl-total_fatura').text("R$ " + convertFloatToMoeda(total_venda))
})

$('.btn-pagamento-multi').click(() => {
    calcTotalPayment()
});

$('.btn-store-fatura').click(() => {
    console.clear()

    if(!$('#inp-parcelas_fatura').val()){
        swal("Erro", "Informe a quantidade de parcelas!", "error")
        return;
    }
    if(!$('#inp-intervalo_fatura').val()){
        swal("Erro", "Informe o intervalo!", "error")
        return;
    }
    let data = {
        entrada_fatura: $('#inp-entrada_fatura').val(),
        parcelas_fatura: $('#inp-parcelas_fatura').val(),
        intervalo_fatura: $('#inp-intervalo_fatura').val(),
        primeiro_vencimento_fatura: $('#inp-primeiro_vencimento_fatura').val(),
        tipo_pagamento_fatura: $('#inp-tipo_pagamento_fatura').val(),
        total: total_venda
    }
    // console.log(data)
    $.get(path_url + "api/frenteCaixa/gerar-fatura-pdv", data)
    .done((success) => {
        // console.log(success)
        $('#pagamento_multiplo').modal('show')

        setTimeout(() => {
            $(".table-payment tbody").html(success)
            $('#modal_fatura_venda').modal('hide')
            calcTotalPayment()
            validateButtonSave()

        }, 100)
        

    })
    .fail((err) => {
        console.log(err);
    });
})

$('.btn-vendas-suspensas').click(() => {
    $.get(path_url + "api/frenteCaixa/venda-suspensas",
    {
        empresa_id: $('#empresa_id').val(),
    })
    .done((data) => {
        // console.log(data)
        $('.table-vendas-suspensas tbody').html(data)
    })
    .fail((e) => {
        console.log(e);
    });
})

$('.btn-orcamentos').click(() => {
    $.get(path_url + "api/frenteCaixa/orcamentos",
    {
        empresa_id: $('#empresa_id').val(),
    })
    .done((data) => {
        // console.log(data)
        $('.table-orcamentos tbody').html(data)
    })
    .fail((e) => {
        console.log(e);
    });
})

$("#inp-produto_id").select2({
    minimumInputLength: 2,
    language: "pt-BR",
    placeholder: "Digite para buscar o produto",
    width: "100%",
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
                usuario_id: $('#usuario_id').val(),
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

            $.each(response, function (i, v) {
                var o = {};
                o.id = v.id;
                if(v.codigo_variacao){
                    o.codigo_variacao = v.codigo_variacao
                }

                o.text = ""
                if(v.numero_sequencial){
                    o.text += "["+v.numero_sequencial+"] "
                }
                o.text += v.nome

                if(parseFloat(v.valor_unitario) > 0){
                    o.text += ' R$ ' + convertFloatToMoeda(v.valor_unitario);
                }

                if(v.estoque_atual > 0 && $('#estoque_view').val() == 1){
                    o.text += ' | Estoque: ' + v.estoque_atual;
                }

                if(v.codigo_barras){
                    o.text += ' [' + v.codigo_barras  + ']';
                }

                if(v.referencia){
                    o.text += ' #REF: ' + v.referencia;
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

$('#codBarras').keyup((v) => {
    setTimeout(() => {
        let barcode = v.target.value

        let bex = barcode.split('*')
        let qtd = 1
        if(bex[1]){
            qtd = bex[0]
            barcode = bex[1]
            // console.log(bex)

        }
        if(barcode.includes('*')){
            $('.leitor_ativado').text('Leitor Ativado x' + bex[0])
        }
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
                console.log(e)
                if(e.status == 0){
                    toastr.error('Produto inativo!');
                    return
                }
                if (e.valor_unitario) {

                    var newOption = new Option(e.nome, e.id, false, false);
                    $('#inp-produto_id').html('')
                    $('#inp-produto_id').append(newOption);

                    // $("#inp-produto_id").append(new Option(e.nome, e.id));
                    $("#inp-quantidade").val(qtd);
                    $("#inp-variacao_id").val(e.codigo_variacao);
                    $("#inp-valor_unitario").val(convertFloatToMoeda(e.valor_unitario));
                    $("#inp-subtotal").val(convertFloatToMoeda(qtd * e.valor_unitario));
                    $('.leitor_ativado').text('Leitor Ativado')
                    
                    setTimeout(() => {
                        $('.btn-add-item').trigger('click')
                    }, 100)
                } else {
                    buscarPorReferencia(barcode)
                }
                setTimeout(() => {
                    $('#codBarras').focus()
                }, 10)
            })
            .fail((err) => {
                console.log(err);
                // swal("Erro", "Produto não localizado!", "error")
                buscarPorReferencia(barcode)
            });
        }
    }, 500)
})

$('.cliente-venda').click(() => {
    let vl_cashback = convertMoedaToFloat($('#inp-valor_cashback').val())
    if(vl_cashback > 0){
        DESCONTO = vl_cashback
        $("#valor_desconto").html(convertFloatToMoeda(DESCONTO));
        calcTotal();
    }
})

$('.btn-selecionar_cliente').click(() => {
    $('#inp-valor_cashback').val('')
    $('#inp-permitir_credito').val('1').change()
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

var CashBackConfig = null
var valorCashBack = 0

$('.btn-fatura-padrao').on("click", function () {

    console.clear()
    let total = convertMoedaToFloat($(".total-venda").text())
    if(total <= 0){
        swal("Erro", "Valor precisa ser maior que zero!", "error")
        return;
    }
    
    let data = {
        total: total,
        cliente_id: $('#inp-cliente_id').val()
    }
    $.get(path_url + "api/frenteCaixa/fatura-padrao-cliente-pdv", data)
    .done((success) => {
        // console.log(success)
        $('#pagamento_multiplo').modal('show')

        $(".table-payment tbody").html(success)
        calcTotalPayment()
        validateButtonSave()
    })
    .fail((err) => {
        console.log(err);
    });
});

$(document).on("change", "#inp-cliente_id", function () {
    clienteCNPJ = false
    $('.btn-fatura-padrao').addClass('d-none')

    $('.p-cliente').html("")
    $('.cashback-div').addClass('d-none')
    $('#inp-valor_cashback').val('')
    $('#inp-permitir_credito').val('1').change()
    let cliente_id = $(this).val()
    $.get(path_url + "api/clientes/cashback/" + cliente_id)
    .done((e) => {
        if(e){
            CashBackConfig = e
            valorCashBack = e.valor_cashback

            $('.cashback-div').removeClass('d-none')
            $('.info_cash_back').text('*percentual de cashback para uso ' + e.percentual_maximo_venda + '%')

        }
        $('.valor-cashback-disponivel').text('R$ ' + convertFloatToMoeda(e.valor_cashback))
    })
    .fail((e) => {
        $('.cashback-div').addClass('d-none')
        // console.log(e);
    });

    console.clear()
    $.get(path_url + "api/clientes/find/" + cliente_id)
    .done((cliente) => {
        // console.log(cliente)
        $('.cliente_selecionado').text(cliente.razao_social);
        $('.p-cliente').html("<label>Cliente: <strong>"+cliente.info+"</strong></label>");
        if(cliente.cpf_cnpj.replace(/[^0-9]/g,'').length == 14 && $('#NFECNPJ').val() == '1'){
            $('.p-cliente').append("<br><strong class='text-danger'>Será emitida NFe cliente selecionado com CNPJ</strong>");
            clienteCNPJ = true
        }

        if(cliente.fatura.length > 0){
            $('.btn-fatura-padrao').removeClass('d-none')
        }
        
        if(cliente.lista_preco){

            $('#lista_id').val(cliente.lista_preco.id)
            setTimeout(() => {
                todos()
            }, 10)
            setTimeout(() => {
                $("#codBarras").focus();
            }, 500)

        }

        if(cliente.valor_credito > 0){
            swal("", "Esse cliente possui um crédito de R$ " + convertFloatToMoeda(cliente.valor_credito), "info")
            .then(() => {

                $('.cliente-venda').trigger('click')
                $('#inp-valor_credito').val(convertFloatToMoeda(cliente.valor_credito))

                $('#modal_credito').modal('show')
                VALORCREDITO = cliente.valor_credito
            })

        }
    })
    .fail((err) => {
        console.log(err);
    });

})

$('#btn-usar-credito').click(() => {
    let valorCredito = convertMoedaToFloat($('#inp-valor_credito').val())
    if(valorCredito > VALORCREDITO){
        swal("Erro", "Valor limite de crédito R$ " + convertFloatToMoeda(VALORCREDITO), "error")
        return;
    }
    $("#valor_desconto").text("R$ " + convertFloatToMoeda(valorCredito));
    DESCONTO = valorCredito
    $('#modal_credito').modal('hide')
    calcTotal()
})

$('#inp-valor_cashback').blur(() => {
    validaCashBack()
})

function validaCashBack(){

    let valor_setado = $('#inp-valor_cashback').val()
    valor_setado = valor_setado.replace(",", ".")
    valor_setado = parseFloat(valor_setado)
    let total = convertMoedaToFloat($(".total-venda").text())
    if(total == 0){
        swal("Alerta", "Informe ao menos um produto para continuar", "warning")
        return;
    }
    if(CashBackConfig){
        let percentual_maximo_venda = CashBackConfig.percentual_maximo_venda
        let valor_maximo = total * (percentual_maximo_venda/100)

        if(valor_setado > valor_maximo){
            swal("Erro", "Valor máximo permitido R$ " + convertFloatToMoeda(valor_maximo), "warning")
            $('#inp-valor_cashback').val('')
        }else if(valor_setado > valorCashBack){
            swal("Erro", "Valor ultrapassou R$ " + convertFloatToMoeda(valorCashBack), "warning")
            $('#inp-valor_cashback').val('')
        }else{

        }
    }
}

$(function () {
    setTimeout(() => {
        $('#cat_todos').first().trigger('click')

        $("#inp-conta_empresa_sangria_id").select2({
            minimumInputLength: 2,
            language: "pt-BR",
            placeholder: "Digite para buscar a conta",
            width: "100%",
            theme: "bootstrap4",
            dropdownParent: '#sangria_caixa',
            ajax: {
                cache: true,
                url: path_url + "api/contas-empresa",
                dataType: "json",
                data: function (params) {
                    console.clear();
                    let empresa_id = $('#empresa_id').val()
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

        $("#inp-conta_empresa_suprimento_id").select2({
            minimumInputLength: 2,
            language: "pt-BR",
            placeholder: "Digite para buscar a conta",
            width: "100%",
            theme: "bootstrap4",
            dropdownParent: '#suprimento_caixa',
            ajax: {
                cache: true,
                url: path_url + "api/contas-empresa",
                dataType: "json",
                data: function (params) {
                    console.clear();
                    let empresa_id = $('#empresa_id').val()
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
        empresa_id: $('#empresa_id').val(),
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
    $('.btn_cat').removeClass('active')
    $('#cat_todos').addClass('active')

    $.get(path_url + "api/produtos/all", { 
        empresa_id: $('#empresa_id').val(),
        lista_id: $('#lista_id').val(),
        usuario_id: $('#usuario_id').val()
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
                    local_id: $('#local_id').val(),
                })
                .done((e) => {
                    if(e.variacao_modelo_id){
                        if(!codigo_variacao){
                            buscarVariacoes(product_id)
                        }else{

                            $.get(path_url + "api/variacoes/findById", {codigo_variacao: codigo_variacao})
                            .done((e) => {
                                $("#inp-variacao_id").val(codigo_variacao);
                                $("#inp-quantidade").val("1");
                                $("#inp-valor_unitario").val(convertFloatToMoeda(e.valor));
                                $("#inp-subtotal").val(convertFloatToMoeda(e.valor));
                            })
                            .fail((e) => {
                                console.log(e);
                            });
                        }
                    }else{
                        $("#inp-quantidade").val("1");
                        $("#inp-valor_unitario").val(convertFloatToMoeda(e.valor_unitario));
                        $("#inp-subtotal").val(convertFloatToMoeda(e.valor_unitario));
                    }

                    setTimeout(() => {
                        // $("#inp-quantidade").focus()
                    }, 200)
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
    $("#inp-quantidade").val("1,000");
    $("#inp-valor_unitario").val(convertFloatToMoeda(valor));
    $("#inp-subtotal").val(convertFloatToMoeda(valor));
    $("#inp-variacao_id").val(id);

    $('#modal_variacao').modal('hide')

    if(PRODUTOID != null){
        addItem()
    }
    
}

function addItem(){

    $.get(path_url + "api/produtos/findId/" + PRODUTOID)
    .done((res) => {
        // console.log(res)
        var newOption = new Option(res.nome, res.id, false, false);
        $('#inp-produto_id').html('')
        $('#inp-produto_id').append(newOption);
        setTimeout(() => {
            $('.btn-add-item').trigger('click')
            $('.leitor_ativado').text('Leitor Ativado')
        }, 10)
    })
    .fail((err) => {
        console.log(err)
    })
    PRODUTOID = null
}

var PRODUTOID = null
function addProdutos(id) {
    let qtd = 0;
    let agrupar_itens = $('#agrupar_itens').val()

    if(agrupar_itens == 1){
        $('.produto_row').each(function () {
            if(id == $(this).val()){
                qtd = $(this).next().next().next().find('input').val()
            }
        })
    }

    setTimeout(() => {
        $.get(path_url + "api/frenteCaixa/linhaProdutoVendaAdd", {
            id: id, 
            qtd: qtd,
            lista_id: $('#lista_id').val(),
            local_id: $('#local_id').val(),
        })
        .done((e) => {
            $('.leitor_ativado').text('Leitor Ativado')
            if (e == false) {
                swal("Atenção", "Produto com estoque insuficiente!", "warning");
            } else {
                let idDup = 0
                if(agrupar_itens == 1){
                    $(".produto_row").each(function () {
                        if($(this).val() == id){
                            idDup = $(this).val()
                        }
                    })
                }

                setTimeout(() => {
                    if(idDup == 0){
                        $(".table-itens tbody").append(e);
                    }else{
                        // console.clear()
                        $(".table-itens tbody tr").each(function(){
                            if($(this).find('.produto_row').val() == id){
                                let qtdAnt = convertMoedaToFloat($(this).find('.qtd_row').val())
                                $(this).find('.qtd_row').val(convertFloatToMoeda(qtdAnt+1))
                            }
                        })
                    }
                    setTimeout(() => {
                        beepSucesso()
                        calcSubTotal()
                    }, 20)
                }, 10)

            }
        })
        .fail((e) => {
            beepErro()
            PRODUTOID = id
            // console.log(e);
            if(e.status == 402){
                buscarVariacoes(id)
            }else{
                swal("Atenção", e.responseJSON, "warning");
            }
        });
    }, 10);
}

$(".btn-add-item").click(() => {
    console.clear()
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
            let variacao_id = $("#inp-variacao_id").val();

            // let key = $("#inp-key").val()
            $("#inp-variacao_id").val('')
            if (qtd && value_unit && product_id && sub_total) {

                let dataRequest = {
                    qtd: qtd,
                    value_unit: value_unit,
                    sub_total: sub_total,
                    product_id: product_id,
                    variacao_id: variacao_id,
                    local_id: $('#local_id').val(),
                };

                //valida item duplicado
                let idDup = 0
                let qtdDup = 0
                let agrupar_itens = $('#agrupar_itens').val()
                if(!variacao_id && agrupar_itens == 1){
                    $(".produto_row").each(function () {
                        // console.log(product_id)
                        if($(this).val() == product_id){
                            // console.log($(this).val())
                            idDup = product_id
                        }
                    })
                }

                setTimeout(() => {
                    $(".qtd_row").each(function () {
                        let lID = $(this).closest('tr').find('.produto_row').val()
                        if(idDup == lID){
                            qtdDup = convertMoedaToFloat($(this).val())

                        }
                    })
                }, 10)
                setTimeout(() => {
                    if(idDup == 0){
                        $.get(path_url + "api/frenteCaixa/linhaProdutoVenda", dataRequest)
                        .done((e) => {
                            if (e == false) {

                                swal(
                                    "Atenção",
                                    "Produto com estoque insuficiente!",
                                    "warning"
                                    );
                            } else {
                                $(".table-itens tbody").append(e);
                                beepSucesso()
                                calcTotal();
                            }
                        })
                        .fail((e) => {
                            console.log(e);
                            swal("Atenção", e.responseJSON, "warning");
                        });
                    }else{
                        let nQtd = qtdDup + convertMoedaToFloat(qtd)

                        let dataRequest = {
                            qtd: nQtd,
                            product_id: idDup,
                        };
                        $.get(path_url + "api/produtos/valida-estoque", dataRequest)
                        .done((success) => {
                            beepSucesso()
                            $(".table-itens tbody tr").each(function(){

                                if(idDup == $(this).find('.produto_row').val()){
                                    $(this).find('.qtd_row').val(convertFloatToMoeda(nQtd))
                                }
                            })
                            setTimeout(() => {
                                calcSubTotal()
                            }, 20)
                        })
                        .fail((err) => {
                            console.log(err)
                            beepErro()
                            swal("Erro", err.responseJSON, "error")
                        })

                    }
                }, 100)
            } else {
                beepErro()
                swal(
                    "Atenção",
                    "Informe corretamente os campos para continuar!",
                    "warning"
                    );
            }
        } else {
            beepErro()
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

function beepSucesso(){
    let alerta = $('#alerta_sonoro').val()
    if(alerta == 1){
        var audio = new Audio('/audio/beep.mp3');
        audio.addEventListener('canplaythrough', function() {
            audio.play();
        });
    }
}
function beepErro(){
    let alerta = $('#alerta_sonoro').val()
    if(alerta == 1){
        var audio = new Audio('/audio/beep_error.mp3');
        audio.addEventListener('canplaythrough', function() {
            audio.play();
        });
    }
}


function validaCaixa() {
    let abertura = $('#abertura').val()
    if (!abertura) {
        $('#modal-abrir_caixa').modal('show')
        return
    }
}

var total_venda = 0;
function calcTotal() {
    var total = 0;
    let qtdTotal = 0
    $(".subtotal-item").each(function () {
        total += convertMoedaToFloat($(this).val());
        qtdTotal = convertMoedaToFloat($(this).closest('tr').find('.qtd_row').val())
    });

    $('.total-linhas').text($(".table-itens tbody tr").length)
    $('.total-itens').text(qtdTotal)
    setTimeout(() => {
        total_venda = total;

        $(".total-venda").html("R$ "+convertFloatToMoeda(total + parseFloat(VALORACRESCIMO) + parseFloat(VALORFRETE) - parseFloat(DESCONTO)));
        $('#inp-valor_total').val(convertFloatToMoeda(total + parseFloat(VALORACRESCIMO) + parseFloat(VALORFRETE) - parseFloat(DESCONTO)));
        $(".total-venda-modal").html("R$ " + convertFloatToMoeda(total + parseFloat(VALORACRESCIMO) + parseFloat(VALORFRETE) - parseFloat(DESCONTO)));
        $('#inp-valor_integral').val(convertFloatToMoeda(total_venda))

        $('#inp-quantidade').val('')
        $('#inp-valor_unitario').val('')
        $('#inp-produto_id').val('').change()

        validateButtonSave()
    }, 100);
}

var CLIENTESEMLIMITE = 0
$(".btn-modal-multiplo").on("click", (event) => {
    // consultaDebito()
    validateButtonSave()
    $('#salvar_venda').trigger('click')
});

function consultaDebito(){
    CLIENTESEMLIMITE = 0
    let soma = 0
    let tipo_pagamento = $('#inp-tipo_pagamento').val()
    $(".data_multiplo").each(function () {
        let d1 = new Date($(this).val())
        let d2 = new Date();
        if(d1 > d2){
            $valor = $(this).closest('td').next().find('input');
            soma += convertMoedaToFloat($valor.val())
        }
    });

    if(soma == 0 && tipo_pagamento == '06') {
        soma = total_venda
    }

    setTimeout(() => {
        let cliente_id = $("#inp-cliente_id").val();

        if(cliente_id && soma > 0){
            $.get(path_url + "api/clientes/consulta-debito", {cliente_id: cliente_id, total: soma})
            .done((success) => {
                // console.log(success);
            })
            .fail((e) => {
                // console.log(e);
                swal("Erro", e.responseJSON, "error")
                CLIENTESEMLIMITE = 1
                validateButtonSave()
            });
        }
    }, 200)
}

$('#salvar_venda').click(() => {
    // consultaDebito()
    setTimeout(() => {

        if($("#definir_vendedor_pdv").val() == 1 && !$('#inp-funcionario_id').val()){
            toastr.error('Escolha o vendedor para finalizar a venda!');
            return;
        }
        let tipo_pagamento = $('#inp-tipo_pagamento').val()
        if(tipo_pagamento == 17){

            let desconto = convertMoedaToFloat($('#valor_desconto').text())
            let acrescimo = convertMoedaToFloat($('#valor_acrescimo').text())
            let valor_frete = convertMoedaToFloat($('.valor-frete').text())
            let total = total_venda + acrescimo + valor_frete - desconto
            let data = {
                total_venda: total,
                usuario_id: $('#usuario_id').val(),
                empresa_id: $('#empresa_id').val()
            }

            $.post(path_url + 'api/frenteCaixa/qr-code-pix', data)
            .done((success) => {
                // console.log(success)
                swal("Sucesso", "Chave PIX gerada", "success")
                .then(() => {
                    $(".qrcode").attr("src", "data:image/jpeg;base64,"+success['qrcode']);
                    $('#modal-pix').modal('show')
                    let payment_id = success['payment_id']
                    let pay = false

                    setInterval(() => {
                        if(pay == false){
                            let data = {
                                payment_id: payment_id,
                                usuario_id: $('#usuario_id').val(),
                                empresa_id: $('#empresa_id').val()
                            }

                            $.get(path_url + 'api/frenteCaixa/consulta-pix', data)
                            .done((res) => {

                                if(res == "approved"){
                                    $('#modal-pix').modal('hide')
                                    if(pay == false){
                                        swal("Sucesso", "Pagamento aprovado", "success")
                                        .then(() => {
                                            // $('#finalizar_venda').modal('show')
                                            finalizarVendaModal()
                                        })
                                    }
                                    pay = true

                                }
                            })
                            .fail((err) => {

                            })
                        }
                    }, 4000)
                })
            })
            .fail((err) => {
                console.log(err)
                // $('#finalizar_venda').modal('show')
                finalizarVendaModal()
            })
        }else{
            finalizarVendaModal()
            
            if(tipo_pagamento >= 30){
                let data = {
                    tipo_pagamento: tipo_pagamento,
                    total_venda: total_venda,
                    usuario_id: $('#usuario_id').val(),
                    empresa_id: $('#empresa_id').val()
                }

                $.post(path_url + 'api/tef/store', data)
                .done((hash) => {
                    // console.log(hash)
                    consultaStatusTef(hash)
                })
                .fail((err) => {
                    console.log(err)
                })
            }else{
                // $('#finalizar_venda').modal('show')
            }
        }
    }, 100)
})

function finalizarVendaModal(){
    let finalizacao_pdv = $('#inp-finalizacao_pdv').val()
    if(finalizacao_pdv == 'todos'){
        $('#finalizar_venda').modal('show')
    }else if(finalizacao_pdv == 'nao_fiscal'){
        emitirNfce = false
        if($("#form-pdv-update")){
            $("#form-pdv-update").submit()
        }
        if($("#form-pdv")){
            $("#form-pdv").submit()
        }
    }else if(finalizacao_pdv == 'fiscal'){
        $('#cpf_nota').modal('show')
    }
}

$("#inp-valor_recebido").on("keyup", (event) => {
    let v = $("#inp-valor_recebido").val();
    v = v.replace(",", ".");
    v = parseFloat(v)
    VALORACRESCIMO = parseFloat(VALORACRESCIMO)

    let troco = v - (total_venda - DESCONTO + VALORACRESCIMO + VALORFRETE);
    // console.log(troco)
    if (troco > 0) {
        $("#valor-troco").html(convertFloatToMoeda(troco));
        $("#inp-troco").val(convertFloatToMoeda(troco));
    } else {
        $("#valor-troco").html("0,00");
    }
});

$("body").on("click", "#btn-incrementa", function () {

    let inp = $(this).closest('div.input-group-append').prev()[0]
    let prodRow = $(this).closest('.line-product').find('.produto_row')
    let produto_id = prodRow.val()
    if (inp.value) {
        let v = convertMoedaToFloat(inp.value)
        $.get(path_url + "api/produtos/valida-estoque", { qtd: v+1, product_id: produto_id, local_id: $('#local_id').val() })
        .done((res) => {
            // console.log(res)
            v += 1
            inp.value = convertFloatToMoeda(v)
            calcSubTotal()
        })
        .fail((err) => {
            // console.log(err);
            swal("Alerta", err.responseJSON, "warning")
        });
        
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

    if(senhaAcao != ''){
        swal({
            title: "Senha para remover item",
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
                removeItem($(this))
            }else{
                swal("Erro", "Senha incorreta!", "error")
            }
        })
    }else{
        removeItem($(this))
    }
});

function removeItem(element){
    element.closest('tr').remove();
    swal("Sucesso", "Item removido!", "success")
    CLIENTESEMLIMITE = 0
    calcTotal()

    let data = {
        empresa_id: $("#empresa_id").val(),
        usuario_id: $('#usuario_id').val(),
        acao: 'Item removido',
        produto_id: element.closest('tr').find('.produto_row').val()
    }
    registrarLog(data)
}

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

function registrarLog(data){
    console.clear()
    console.log("LOG", data)
    $.post(path_url + 'api/frenteCaixa/pdf-log', data)
    .done((res) => {
        // console.log(res)
    })
    .fail((err) => {
        // console.log(err)
    })
}

function setaDesconto() {
    if (total_venda == 0) {
        swal("Erro", "Total da venda é igual a zero", "warning");
    } else {

        if(senhaAcao != ''){
            swal({
                title: "Senha para desconto",
                text: "Informe a senha para continuar",
                button: {
                    text: "Ok",
                    closeModal: false,
                    type: "error",
                },
                content: {
                    element: "input",
                    attributes: {
                        type: "password",
                        placeholder: "Digite a senha"
                    }
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
        text: "Informe o valor de desconto!",
        content: "input",
        button: {
            text: "Ok",
            closeModal: false,
            type: "error",
        },
    }).then((v) => {
        let total = convertMoedaToFloat($(".total-venda").text())

        if (v) {
            let desconto = v;
            if (desconto.substring(0, 1) == "%") {
                let perc = desconto.substring(1, desconto.length);
                perc = perc.replace(",", ".")

                DESCONTO = total * (perc / 100);
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
                    (total * PERCENTUALMAXDESCONTO) / 100;
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
            $("#valor_desconto").text("R$ " + convertFloatToMoeda(DESCONTO));

            let data = {
                empresa_id: $("#empresa_id").val(),
                usuario_id: $('#usuario_id').val(),
                acao: 'Desconto',
                valor_desconto: DESCONTO,

            }
            registrarLog(data)

            calcTotal();
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
                content: "input",
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

function modalFrete(){
    $('#modal_frete').modal('show')
}

$('.btn-save-frete').click(() => {
    let valorFrete = convertMoedaToFloat($('#valor_frete').val())
    if(valorFrete){
        VALORFRETE = valorFrete
        $('.valor-frete').text("R$ " + $('#valor_frete').val())
        calcTotal()
    }

    $('#modal_frete').modal('hide')
})

$("#inp-transportadora_id").select2({
    minimumInputLength: 2,
    language: "pt-BR",
    placeholder: "Digite para buscar a transportadora",
    dropdownParent: '#modal_frete',
    ajax: {
        cache: true,
        url: path_url + "api/transportadoras/pesquisa",
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

function modalAcrescimo(){
    swal({
        title: "Valor acréscimo?",
        text: "Informe o valor de acréscimo!",
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
            calcTotal();
            VALORACRESCIMO = parseFloat(VALORACRESCIMO);
            $("#valor_acrescimo").text("R$ " + convertFloatToMoeda(VALORACRESCIMO));

            let data = {
                empresa_id: $("#empresa_id").val(),
                usuario_id: $('#usuario_id').val(),
                acao: 'Acréscimo',
                valor_acrescimo: VALORACRESCIMO,
            }
            registrarLog(data)

            calcTotal();
            $("#codBarras").focus();
        }
        swal.close();
    });
}


$("#inp-tipo_pagamento").change(() => {
    $("#inp-valor_recebido").val('');
    let tipo = $("#inp-tipo_pagamento").val();
    let cliente = $("#inp-cliente_id").val();
    if (tipo == '06' && cliente == null) {

        toastr.warning('Informe o cliente!');
        $('#cliente').modal('show')
        // $('#inp-tipo_pagamento').val('').change()
        // $(".div-vencimento").addClass('d-none');
    }

    if (tipo == '06' && cliente != null) {
        // $(".div-vencimento").removeClass('d-none');
    } else {
        // $(".div-vencimento").addClass('d-none');
    }

    if (tipo == "03" || tipo == "04") {
        if($('#inp-abrir_modal_cartao').val() == 1){
            $('#cartao_credito').modal('show')
            // $(".div-vencimento").addClass('d-none');
        }
    }

    if (tipo == "99") {
        $("#modal-pag-outros").modal("show");
        // $(".div-vencimento").addClass('d-none');

    }
    if (tipo == "01") {
        $("#inp-valor_recebido").removeAttr("disabled");
        $("#finalizar-venda").attr("disabled", true);
        $("#finalizar-rascunho").attr("disabled", true);
        $("#finalizar-consignado").attr("disabled", true);
        $(".div-troco").removeClass('d-none');
        $(".div-vencimento").addClass('d-none');
        $(".div-btns").addClass('d-none');

        $('#inp-valor_recebido').val($(".total-venda").text())
    } else {
        $("#inp-valor_recebido").attr("disabled", "true");
        $(".div-troco").addClass('d-none');
        $("#finalizar-venda").removeAttr("disabled");
        $("#finalizar-rascunho").removeAttr("disabled");
        $("#finalizar-consignado").removeAttr("disabled");
        $(".div-btns").removeClass('d-none');
    }

    validateButtonSave()
});

$("#inp-tipo_pagamento_row").change(() => {
    let cliente = $("#inp-cliente_id").val();
    let tipo = $("#inp-tipo_pagamento_row").val();
    if (tipo == '06') {
        if (cliente == null) {
            swal("Alerta", "Informe o cliente!", "warning")
            $('#inp-tipo_pagamento_row').val('').change()
        }
    }

})

$('#inp-valor_recebido').blur(() => {
    validateButtonSave()
})

$("#codBarras").keypress(function(e){
    if(e.which == 13) {
        e.preventDefault();
    }
})

$("#inp-quantidade").keypress(function(e){
    if(e.which == 13) {
        $('#inp-valor_unitario').focus()
        e.preventDefault();
    }
})

$("#inp-valor_unitario").keypress(function(e){
    if(e.which == 13) {
        $('.btn-add-item').trigger('click')
        e.preventDefault();
    }
})

$("body").on("keyup", "#inp-valor_unitario", function () {
    let valor = $(this).val()
    let produto_id = $("#inp-produto_id").val();
    if(!produto_id){
        return
    }
    $.get(path_url + "api/orcamentos/valida-desconto", 
    { 
        produto_id: produto_id, valor: valor, empresa_id: $('#empresa_id').val(), pdv: 1
    }).done((res) => {

    })
    .fail((err) => {
        console.log(err)
        let v = err.responseJSON
        $(this).val(convertFloatToMoeda(v))
        swal("Erro", "Valor minímo para este item " + convertFloatToMoeda(v), "error")
    })
})

$("body").on("keyup", "#inp-quantidade", function () {
    let quantidade = $(this).val()
    let produto_id = $("#inp-produto_id").val();
    $.get(path_url + "api/produtos/valida-atacado", { quantidade: quantidade, produto_id: produto_id })
    .done((success) => {
        if(success){
            $("#inp-valor_unitario").val(convertFloatToMoeda(success));
        }
    })
    .fail((err) => {
        console.log(err);
    });
})

function validateButtonSave() {
    $('#salvar_venda').attr("disabled", 1)
    $('#editar_venda').attr("disabled", 1)

    if(CLIENTESEMLIMITE){
        return;
    }

    let total = convertMoedaToFloat($(".total-venda").text())
    var tipo = $('#inp-tipo_pagamento').val()
    var tipo_row = $('.table-payment').length ? $('.table-payment tbody tr').length : null

    var valor_recebido = convertMoedaToFloat($('#inp-valor_recebido').val())
    // console.log(tipo_row)

    if (total > 0 && (tipo || tipo_row)) {
        if (tipo == '01' && valor_recebido >= total) {
            $('#salvar_venda').removeAttr("disabled")
            $('#editar_venda').removeAttr("disabled")
        }
        else if (tipo != '01') {
            $('#salvar_venda').removeAttr("disabled")
            $('#editar_venda').removeAttr("disabled")
        }
        else if (tipo_row) {
            $('#salvar_venda').removeAttr("disabled")
            $('#editar_venda').removeAttr("disabled")
        }
        else {
            $('#salvar_venda').attr("disabled", 1)
            $('#editar_venda').attr("disabled", 1)
        }
    }
}

$('#editar_venda').click(() => {
    $('#finalizar_venda').modal('show')
})

function consultaStatusTef(hash){
    $('#modal_tef_consulta').modal('show')
    $('.status-tef').text('Processando')
    $('.loading-tef').removeClass('d-none')
    let data = {
        hash: hash,
        usuario_id: $('#usuario_id').val(),
        empresa_id: $('#empresa_id').val()
    }
    $('.modal-loading').remove()
    let intervalo = null;
    intervalo = setInterval(() => {
        $.post(path_url + 'api/tef/consulta', data)
        .done((success) => {
            // console.log(success)
            if(success == "Transação Aceita"){
                $('#tef_hash').val(hash)
                swal("Sucesso", "Transação Aprovada!", "success")
                .then(() => {
                    $('#modal_tef_consulta').modal('hide')
                    // $('#finalizar_venda').modal('show')
                    finalizarVendaModal()
                })
                clearInterval(intervalo)
            }
        })
        .fail((err) => {
            // console.log(err)
            clearInterval(intervalo)
            swal("ERRO TEF", err.responseJSON, "error")

            $('.status-tef').text(err.responseJSON)
            setTimeout(() => {
                $('#modal_tef_consulta').modal('hide')
            }, 2000)
        })
    }, 3000)
}

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
                    // console.log(response)
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

$(".btn-add-payment").click(() => {
    let tipo_pagamento_row = $("#inp-tipo_pagamento_row").val();
    let vencimento = $("#inp-data_vencimento_row").val();
    let valor_integral_row = $("#inp-valor_row").val();
    let obs_row = $("#inp-observacao_row").val();

    validateButtonSave();

    let v = convertMoedaToFloat(valor_integral_row);
    let total = total_venda + parseFloat(VALORACRESCIMO) - parseFloat(DESCONTO);
    // console.log(total)
    // console.log(v)
    // console.log(total_payment)
    // if ((v + total_payment) <= total) {
    //     if (vencimento && valor_integral_row && tipo_pagamento_row) {
    //         let dataRequest = {
    //             data_vencimento_row: vencimento,
    //             valor_integral_row: valor_integral_row,
    //             obs_row: obs_row,
    //             tipo_pagamento_row: tipo_pagamento_row,
    //         };

    //         $.get(path_url + "api/frenteCaixa/linhaParcelaVenda", dataRequest)
    //         .done((e) => {
    //             $(".table-payment tbody").append(e);
    //             calcTotalPayment();

    //         })
    //         .fail((e) => {
    //             console.log(e);
    //         });
    //     } else {
    //         swal(
    //             "Atenção",
    //             "Informe corretamente os campos para continuar!",
    //             "warning"
    //             );
    //     }
    // } else {
    //     swal(
    //         "Atenção",
    //         "A soma das parcelas não corresponde com o valor total da venda",
    //         "warning"
    //         );
    // }

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
});


$(".pagamento_multiplo").click(() => {
    // let cliente = $("#inp-cliente_id").val();
    let count_itens = $(".table-itens tbody tr").length

    setTimeout(() => {
        if (count_itens == 0) {
            swal("Erro", "Adicione um produto!", "warning");
        }
        // if (cliente == null) {
        //     swal("Erro", "Adicione um cliente", "warning");
        // }
    }, 200)
})

$("body").on("click", ".btn-delete", function (e) {

    e.preventDefault();
    var form = $(this).parents("form").attr("id");
    
    swal({
        title: "Você está certo?",
        text: "Uma vez deletado, você não poderá recuperar esse item novamente!",
        icon: "warning",
        buttons: true,
        buttons: ["Cancelar", "Excluir"],
        dangerMode: true,
    }).then((isConfirm) => {
        if (isConfirm) {

            document.getElementById(form).submit();
        } else {
            swal("", "Este item está salvo!", "info");
        }
    });
});

var total_payment = 0;
function calcTotalPayment() {
    $(".div-troco-modal").addClass('d-none');
    $('#inp-troco').val('')
    $('#btn-pag_row').attr("disabled", true)

    var total = 0;
    $(".valor_integral").each(function () {
        total += convertMoedaToFloat($(this).val());
    });
    let troco = 0
    $('.btn-modal-multiplo').prop('disabled', true);

    setTimeout(() => {
        total_payment = total;
        $(".sum-payment").html("R$ " + convertFloatToMoeda(total));
        let t = total_venda + parseFloat(VALORACRESCIMO) - parseFloat(DESCONTO);
        $(".sum-restante").html("R$ " + convertFloatToMoeda(t - total));

        if(t - total < 0){

            troco = total - t
            $(".sum-restante").html("R$ 0,00");
            $(".div-troco-modal").removeClass('d-none');
            $(".sum-troco").html(convertFloatToMoeda(total - t));
            $('#inp-troco').val(troco)
        }


        let dif = total_venda - total + troco;
        console.log("dif", dif)

        let diferenca = parseFloat(dif.toFixed(2));
        // console.log("diferenca", diferenca)
        if(diferenca == 0){
            $('.btn-modal-multiplo').prop('disabled', false);
        }

        if (diferenca <= 10) {
            $("#btn-pag_row").removeAttr("disabled")
        }
    }, 100);
    
}


$(".table-payment").on("click", ".btn-delete-row", function () {
    $(this).closest("tr").remove();
    swal("Sucesso", "Parcela removida!", "success");
    calcTotalPayment();
});


$.fn.serializeFormJSON = function () {

    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

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
            // console.log(res)
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

$("body").on("change", "#inp-lista_preco_id", function () {
    $.get(path_url + "api/lista-preco/find", {id: $(this).val()})
    .done((res) => {
        // console.log(res)
        $('#inp-tipo_pagamento_lista').val(res.tipo_pagamento).change()

        if(res.funcionario_id){
            $('#inp-funcionario_lista_id').val(res.funcionario_id).change();
        }
    })
    .fail((err) => {
        console.log(err);
    });
})

var emitirNfce = false
var clienteCNPJ = false
$('#btn_fiscal').click(() => {
    emitirNfce = true
    $("#form-pdv").submit()
})

$('#btn_nao_fiscal').click(() => {
    emitirNfce = false
    if($("#form-pdv-update")){
        $("#form-pdv-update").submit()
    }
    if($("#form-pdv")){
        $("#form-pdv").submit()
    }
})

$("#form-pdv").on("submit", function (e) {

    e.preventDefault();
    const form = $(e.target);
    var json = $(this).serializeFormJSON();

    json.empresa_id = $('#empresa_id').val()
    json.usuario_id = $('#usuario_id').val()

    json.desconto = convertMoedaToFloat($('#valor_desconto').text())
    json.acrescimo = convertMoedaToFloat($('#valor_acrescimo').text())
    json.valor_frete = convertMoedaToFloat($('.valor-frete').text())
    // console.log(">>>>>>>> salvando ", json);
    // return;
    let documentoPdv = $('#documento_pdv').val()
    let cliente = $("#inp-cliente_id").val();

    if((clienteCNPJ == true && emitirNfce == true) || (documentoPdv == 'nfe' && cliente && emitirNfce == true)){
        storeNfe(json)
    }else{
        $.post(path_url + 'api/frenteCaixa/store', json)
        .done((success) => {
            if (emitirNfce == true) {

                gerarNfce(success)
            } else {
                swal({
                    title: "Sucesso",
                    text: "Venda finalizada com sucesso, deseja imprimir o comprovante?",
                    icon: "success",
                    buttons: true,
                    buttons: ["Não", "Sim"],
                    dangerMode: true,
                }).then((isConfirm) => {
                    if (isConfirm) {
                        imprimirNaoFiscal(success.id, json.tipo_pagamento)
                    }

                    if($('#pedido_delivery_id').length){
                        location.href = '/pedidos-delivery';
                    }else if($('#pedido_id').length){
                        location.href = '/pedidos-cardapio/'+$('#pedido_id').val();
                    }else{

                        if($('.table-payment tbody tr').length > 0 && $('#inp-cliente_id').val()){
                            swal({
                                title: "Sucesso",
                                text: "Deseja imprimir as duplicatas",
                                icon: "success",
                                buttons: ["Não", 'Imprimir'],
                                dangerMode: true,
                            })
                            .then((v) => {
                                if (v) {
                                    window.open(path_url + 'frontbox/imprimir-carne/' + success.id, "_blank")

                                    location.href = '/frontbox/create';
                                } else {
                                    location.href = '/frontbox/create';
                                }
                            });
                        }else{
                            location.href = '/frontbox/create';
                        }
                    }

                });
            }
        }).fail((err) => {
            swal("Erro", err.responseJSON, "error")
            console.log(err)
        })
    }
});

function storeNfe(json){
    // console.log(json)
    $.post(path_url + 'api/frenteCaixa/storeNfe', json)
    .done((success) => {
        // console.log(success)
        gerarNfe(success)
        
    }).fail((err) => {
        swal("Erro", err.responseJSON, "error")
        console.log(err)
    })
}

function imprimirNaoFiscal(id, tipo_pagamento){

    let impressao_sem_janela_cupom = $('#impressao_sem_janela_cupom').val()
    if(impressao_sem_janela_cupom == 0){
        var disp_setting="toolbar=yes,location=no,";
        disp_setting+="directories=yes,menubar=yes,";
        disp_setting+="scrollbars=yes,width=850, height=600, left=100, top=25";

        var docprint=window.open(path_url+"frontbox/imprimir-nao-fiscal/"+id,"",disp_setting);
        docprint.focus();
    }else{
        window.open(path_url+"frontbox/imprimir-nao-fiscal-html/"+id)
    }

    if(tipo_pagamento >= 30){
        window.open(path_url+"tef-imprimir/"+id)
    }
}


$("body").on("click", "#btnSuspenderVenda", function () {
    //atualizar venda suspensa
    swal({
        title: "Você esta certo?",
        text: "Deseja suspender esta venda novamente?",
        icon: "warning",
        buttons: true,
        buttons: ["Cancelar", "Suspender"],
    }).then(confirm => {
        if (confirm) {
            console.clear()

            var json = $("#form-pdv").serializeFormJSON();
            json.empresa_id = $('#empresa_id').val()
            json.usuario_id = $('#usuario_id').val()

            console.log(json)
            $.post(path_url + 'api/frenteCaixa/suspender-update', json)
            .done((success) => {
                // console.log(success)
                swal("Sucesso", "Venda suspensa atualizada!", "success")
                .then(() => {
                    location.href = path_url+'frontbox/create'
                })
            })
            .fail((err) => {
                console.log(err)
                swal("Erro", "Algo deu errado", "error")
            })
        }
    });
});

$("body").on("click", "#btn-suspender", function () {
    swal({
        title: "Você esta certo?",
        text: "Deseja suspender esta venda?",
        icon: "warning",
        buttons: true,
        buttons: ["Cancelar", "Suspender"],
    }).then(confirm => {
        if (confirm) {
            console.clear()

            var json = $("#form-pdv").serializeFormJSON();
            json.empresa_id = $('#empresa_id').val()
            json.usuario_id = $('#usuario_id').val()

            // console.log(json)
            $.post(path_url + 'api/frenteCaixa/suspender', json)
            .done((success) => {
                // console.log(success)
                swal("Sucesso", "Venda suspensa!", "success")
                .then(() => {
                    location.reload()
                })
            })
            .fail((err) => {
                console.log(err)
                swal("Erro", "Algo deu errado", "error")
            })
        }
    });
})

var update = false
$("#form-pdv-update").on("submit", function (e) {
    update = true
    e.preventDefault();
    const form = $(e.target);
    var json = $(this).serializeFormJSON();

    json.empresa_id = $('#empresa_id').val()
    json.usuario_id = $('#usuario_id').val()

    json.desconto = convertMoedaToFloat($('#valor_desconto').text())
    json.acrescimo = convertMoedaToFloat($('#valor_acrescimo').text())
    json.valor_frete = convertMoedaToFloat($('.valor-frete').text())

    // console.log(">>>>>>>> salvando ", json);
    $.post(path_url + 'api/frenteCaixa/update/'+$('#venda_id').val(), json)
    .done((success) => {

        if (emitirNfce == true) {
            gerarNfce(success)
        } else {
            swal("Sucesso", "Venda atualizada com sucesso, deseja imprimir o comprovante?", "success")

            swal({
                title: "Sucesso",
                text: "Venda finalizada com sucesso, deseja imprimir o comprovante?",
                icon: "success",
                buttons: true,
                buttons: ["Não", "Sim"],
                dangerMode: true,
            }).then((isConfirm) => {
                if (isConfirm) {
                    window.open(path_url + 'frontbox/imprimir-nao-fiscal/' + success.id, "_blank")
                } else {
                    // location.reload()
                }
                if($('#pedido_delivery_id').length){
                    location.href = '/pedidos-delivery';
                }else if($('#pedido_id').length){
                    location.href = '/pedidos-cardapio';
                }else{
                    if(update){
                        location.href = path_url+'frontbox'
                    }else{
                        location.reload()
                    }
                }
            });
        }
    }).fail((err) => {
        console.log(err)
    })
});

function gerarNfe(venda) {
    console.clear()

    $.post(path_url + "api/nfe_painel/emitir", {
        id: venda.id
    })
    .done((success) => {
        swal("Sucesso", "NFe emitida " + success.recibo + " - chave: [" + success.chave + "]", "success")
        .then(() => {
            window.open(path_url + 'nfe/imprimir/' + venda.id, "_blank")
            setTimeout(() => {
                location.reload()
            }, 100)
        })
    })
    .fail((err) => {
        // console.log(err)
        try{
            if(err.responseJSON.error){
                let o = err.responseJSON.error.protNFe.infProt
                swal("Algo deu errado", o.cStat + " - " + o.xMotivo, "error")
                .then(() => {
                    location.reload()
                })
            }else{
                swal("Algo deu errado", err[0], "error")
            }
        }catch{
            if(err.responseJSON.message){
                swal("Algo deu errado", err.responseJSON.message, "error")
                .then(() => {
                    location.reload()
                })
            }else{
                try{
                    if(err.responseJSON.xMotivo){
                        swal("Algo deu errado", err.responseJSON.xMotivo, "error")
                        .then(() => {
                            location.reload()
                        })
                    }else{
                        if(err.responseJSON.error){
                            swal("Algo deu errado", err.responseJSON.error, "error")
                            .then(() => {
                                location.reload()
                            })
                        }else{
                            swal("Algo deu errado", err.responseJSON, "error")
                            .then(() => {
                                location.reload()
                            })
                        }
                    }
                }catch{
                    swal("Algo deu errado", err.responseJSON[0], "error")
                    .then(() => {
                        location.reload()
                    })
                }
            }
        }
        
    })

}

function gerarNfce(venda) {

    let empresa_id = $("#empresa_id").val();

    $.post(path_url + "api/nfce_painel/emitir", {
        id: venda.id,
    })
    .done((success) => {
        swal("Sucesso", "NFCe emitida " + success.recibo + " - chave: [" + success.chave + "]", "success")
        .then(() => {
            window.open(path_url + 'nfce/imprimir/' + venda.id, "_blank")
            setTimeout(() => {
                if(!update){
                    location.reload()
                }else{
                    location.href = path_url+'frontbox'
                }
            }, 100)
        })
    })
    .fail((err) => {
        // console.log(err)

        // swal("Algo deu errado", err.responseJSON, "error")
        if(err.responseJSON.message){
            swal("Algo deu errado", err.responseJSON.message, "error")
            .then(() => {
                location.reload()
            })
        }else{
            swal("Algo deu errado", err.responseJSON, "error")
            .then(() => {
                location.reload()
            })
        }

    })
}

function adicionaZero(numero) {
    if (numero <= 9)
        return "0" + numero;
    else
        return numero;
}

$(function () {

    $('.btn-modal-multiplo').prop('disabled', false);
    let data = new Date
    let dataFormatada = (data.getFullYear() + "-" + adicionaZero((data.getMonth() + 1)) + "-" + adicionaZero(data.getDate()));
    $('.data_atual').val(dataFormatada)
})


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


