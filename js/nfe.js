var TRIBUTACAOCLIENTE = 0

$('body').on('change', '.cliente_id', function () {

    let cliente = $('.cliente_id').val()
    if (cliente != '') {
        getClient(cliente)
    } else {

    }
})


$('body').on('keypress', '.next', function (e) {
    // console.log(e)
    if(e.which == 13) {
        $(this).closest('td').next().find('input').focus()
        e.preventDefault();
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

$('#inp-endereco_entrega_dados_cliente').on("click", function () {
    if($(this).is(':checked')){
        let cliente_id = $('#inp-cliente_id').val()
        if(cliente_id){
            $.get(path_url + "api/clientes/find/" + cliente_id)
            .done((res) => {

                $('#inp-rua_entrega').val(res.rua)
                $('#inp-nome_entrega').val(res.razao_social)
                $('#inp-documento_entrega').val(res.cpf_cnpj)
                $('#inp-numero_entrega').val(res.numero)
                $('#inp-bairro_entrega').val(res.bairro)
                $('#inp-cep_entrega').val(res.cep)
                $('#inp-complemento_entrega').val(res.complemento)

                var newOption = new Option(res.cidade.info, res.cidade.id, false, false);
                $('#inp-cidade_id_entrega').html('')
                $('#inp-cidade_id_entrega').append(newOption).trigger('change');
            })
            .fail((err) => {
                console.log(err)
            })
        }else{
            swal("Erro", "Selecione o cliente!", "warning")
        }
    }else{
        $('#inp-rua_entrega').val('')
        $('#inp-nome_entrega').val('')
        $('#inp-documento_entrega').val('')
        $('#inp-numero_entrega').val('')
        $('#inp-bairro_entrega').val('')
        $('#inp-cep_entrega').val('')
        $('#inp-complemento_entrega').val('')
        $('#inp-cidade_id_entrega').html('')
    }
});

$('.btn-fatura-padrao').on("click", function () {

    console.clear()
    let total = convertMoedaToFloat($('.total_nfe').text())
    if(total <= 0){
        swal("Erro", "Valor precisa ser maior que zero!", "error")
        return;
    }
    
    let data = {
        total: total,
        cliente_id: $('#inp-cliente_id').val()
    }
    $.get(path_url + "api/frenteCaixa/fatura-padrao-cliente", data)
    .done((success) => {
        // console.log(success)
        $('#body-pagamento').html(success)
        calTotalNfe()


    })
    .fail((err) => {
        console.log(err);
    });
});

$('.btn-gerar-fatura').on("click", function () {
    let total = convertMoedaToFloat($('.total_nfe').text())
    if(total == 0){

        swal("Erro", "O valor da venda deve ser maior que 0", "error")
        .then(() => {
            $('#modal_fatura_venda').modal('hide')
        })
        return;
    }

    $('.lbl-total_fatura').text(convertFloatToMoeda(total))
})

$('.btn-store-fatura').on("click", function () {
    console.clear()
    let total = convertMoedaToFloat($('.total_nfe').text())
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
        total: total
    }
    // console.log(data)
    $.get(path_url + "api/frenteCaixa/gerar-fatura", data)
    .done((success) => {
        // console.log(success)
        $('#body-pagamento').html(success)
        calTotalNfe()
        $('#modal_fatura_venda').modal('hide')

    })
    .fail((err) => {
        console.log(err);
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

            let local_id = null
            if($('#inp-local_id').length){
                let local_id = $('#inp-local_id').val()
                if(!local_id){
                    swal("Alerta", "Selecione primeiramente o local", "warning")
                    return;
                }
            }
            console.clear();

            var query = {
                pesquisa: params.term,
                empresa_id: empresa_id,
                usuario_id: $('#usuario_id').val(),
                lista_id: $('#lista_id').val(),
                local_id: $('#inp-local_id').length ? $('#inp-local_id').val() : null
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

                if(v.tipo_dimensao == 1){
                    o.espessura = v.espessura
                    o.tipo_dimensao = 1
                }

                if(v.altura){
                    o.altura = v.altura
                }

                if(v.largura){
                    o.largura = v.largura
                }

                o.estoque_atual = v.estoque_atual

                if(v.gerenciar_estoque){
                    o.gerenciar_estoque = v.gerenciar_estoque
                }

                o.text = ""
                if(v.numero_sequencial){
                    o.text += "["+v.numero_sequencial+"] "
                }
                o.text += v.nome
                if(compra == 0){
                    if(parseFloat(v.valor_unitario) > 0){
                        o.text += ' R$ ' + convertFloatToMoeda(v.valor_unitario);
                    }
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

var line = null
$(document).on("click", ".alterar-descricao", function () {
    line = $(this)
    let descricaoLinha = $(this).closest('td').find('.descricao_item').val()

    $inpProduto = $(this).closest('td').find('select')
    if(descricaoLinha != ""){
        $('#modal_descricao_item').modal('show')
        $('#inp-descricao_item').val(descricaoLinha)
    }else{
        if($inpProduto.val()){

            $.get(path_url + "api/produtos/descricao", { 
                produto_id: $inpProduto.val(),
            })
            .done((descricao) => {
                $('#modal_descricao_item').modal('show')
                $('#inp-descricao_item').val(descricao)
            })
            .fail((err) => {
                console.log(err);
            });
        }else{
            toastr.warning("Selecione o produto primeiro!")
        }
    }
})

$('.salvar-descricao').on("click", function () {
    let html = line.closest('td')
    html.find('.descricao_item').val($('#inp-descricao_item').val())
    html.find('.nova_descricao').text($('#inp-descricao_item').val())

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

    var $tr = $table.find(".dynamic-form").first();

    $tr.find("select.select2").select2("destroy");
    var $clone = $tr.clone();
    $clone.show();

    $clone.find("input,select").val("");
    let lines = $('.dynamic-form').length
    $clone.find("._key").val(lines-1);
    $clone.find("span").html("");
    $clone.find("p").html("");
    $clone.find(".dimensoes-hidden").html("");

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
            ajax: {
                cache: true,
                url: path_url + "api/produtos",
                dataType: "json",
                data: function (params) {
                    let empresa_id = $('#empresa_id').val()
                    console.clear();
                    var query = {
                        pesquisa: params.term,
                        empresa_id: empresa_id,
                        lista_id: $('#lista_id').val(),
                        usuario_id: $('#usuario_id').val(),
                        local_id: $('#inp-local_id').length ? $('#inp-local_id').val() : null
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

                        if(v.tipo_dimensao == 1){
                            o.espessura = v.espessura
                        }

                        o.text = ""
                        if(v.numero_sequencial){
                            o.text += "["+v.numero_sequencial+"] "
                        }
                        o.text += v.nome;
                        if(compra == 0){
                            o.text += ' R$ ' + convertFloatToMoeda(v.valor_unitario);
                        }else{
                            o.text += ' R$ ' + convertFloatToMoeda(v.valor_compra);
                        }
                        if(v.codigo_barras){
                            o.text += ' [' + v.codigo_barras  + ']';
                        }

                        o.estoque_atual = v.estoque_atual

                        if(v.gerenciar_estoque){
                            o.gerenciar_estoque = v.gerenciar_estoque
                        }
                            // console.log(o)
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

$('body').on('change', '#inp-local_id', function () {
    let local_id = $('#inp-local_id').val();

    $.get(path_url + "api/localizacao/find-number-doc", {local_id: local_id})
    .done((res) => {
        console.log(res)
        $('#inp-numero_nfe').val(res.numero_nfe)
    })
    .fail((err) => {
        console.error(err)
    })
});
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

$("body").on("blur", ".qtd", function () {
    setTimeout(() => {
        let quantidade = convertMoedaToFloat($(this).val())
        if($('#lista_id').val() != ''){
            return;
        }
        $produto = $(this).closest('td').prev().find('select');
        $valorUnit = $(this).closest('td').next().find('input');
        $sub = $(this).closest('td').next().next().find('input');

        let compra = $('#is_compra').val()
        if(compra == 1){
            setTimeout(() => {
                calcTotal()
            }, 100)
            return;
        }
        let produto_id = $("#inp-produto_id").val();
        $.get(path_url + "api/produtos/valida-atacado", { 
            quantidade: quantidade, 
            produto_id: $produto.val(),
            local_id: $('#inp-local_id').val()
        })
        .done((success) => {
            if(success){
                $valorUnit.val(convertFloatToMoeda(success));
                $sub.val(convertFloatToMoeda(success*quantidade));
            }
            calcTotal()

        })
        .fail((err) => {
            // console.log(err);
            if(err.status == 401){
                toastr.error(err.responseJSON)
            }
            $(this).val('1')
        });
    }, 100);

})

var isOrcamento = 0
var descontoItem = 0
$(function () {

    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        $('td th').removeClass('first-col')
    }
    setTimeout(() => {
        let compra = $('#is_compra').val()
        if (compra == 1) {
            $('.div-conta-pagar').removeClass('d-none')
            $('.div-conta-receber').addClass('d-none')
        }
        isOrcamento = $('#is_orcamento').val();
    }, 300)
    $("#lista_id").val('')

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

    if(!$('#inp-natureza_id').val()){
        // $('#inp-gerar_conta_receber').val(0).change()
        // $('#inp-gerar_conta_pagar').val(0).change()
    }

    if ($('.fornecedor_id').val()) {
        getFornecedor($('.fornecedor_id').val())
    }
})


$(document).on("change", ".produto_id", function () {
    let product_id = $(this).val()
    // console.log($(this).select2('data')[0])
    let codigo_variacao = $(this).select2('data')[0].codigo_variacao
    let espessura = $(this).select2('data')[0].espessura
    let altura = $(this).select2('data')[0].altura
    let largura = $(this).select2('data')[0].largura
    let tipo_dimensao = $(this).select2('data')[0].tipo_dimensao
    let estoque_atual = $(this).select2('data')[0].estoque_atual
    let gerenciar_estoque = $(this).select2('data')[0].gerenciar_estoque

    let somaQtdLinhasProduto = 0

    $(".produto_id").each(function (e, v) {
        $qtd = $(this).closest('td').next().find('input');
        if(product_id == $(this).val()){
            let qtd = $qtd.val() ? $qtd.val() : '1'
            somaQtdLinhasProduto += convertMoedaToFloat(qtd)
        }
    })
    // console.log(somaQtdLinhasProduto)

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


        if(estoque_atual == 0 && gerenciar_estoque == 1 && ($('#is_compra').length == 0 || $('#is_compra').val() == 0)){
            toastr.warning("Produto sem estoque!")
            $(this).html('')
            $qtd.val('')
            $vlUnit.val('')
            $sub.val('')
            $perc_icms.val('')
            $perc_pis.val('')
            $perc_cofins.val('')
            $perc_ipi.val('')
            $perc_red_bc.val('')
            $cfop_estadual.val('')
            $ncm.val('')
            $codben.val('')
            $cst_csosn.val('').change()
            $cst_pis.val('').change()
            $cst_cofins.val('').change()
            $cst_ipi.val('').change()
            return;
        }
        // console.log(somaQtdLinhasProduto)
        // console.log(estoque_atual)
        if(gerenciar_estoque == 1 && somaQtdLinhasProduto > estoque_atual && $('#is_compra').val() == 0){
            toastr.warning("Produto com estoque insuficiente!")
            $(this).html('')
            $qtd.val('')
            $vlUnit.val('')
            $sub.val('')
            $perc_icms.val('')
            $perc_pis.val('')
            $perc_cofins.val('')
            $perc_ipi.val('')
            $perc_red_bc.val('')
            $cfop_estadual.val('')
            $ncm.val('')
            $codben.val('')
            $cst_csosn.val('').change()
            $cst_pis.val('').change()
            $cst_cofins.val('').change()
            $cst_ipi.val('').change()
            return;
        }

        $.get(path_url + "api/produtos/find", 
        { 
            produto_id: product_id,
            tributacao_cliente: TRIBUTACAOCLIENTE,
            usuario_id: $('#usuario_id').val(),
            cliente_id: $('#inp-cliente_id').val(),
            fornecedor_id: $('#inp-fornecedor_id').val(),
            lista_id: $('#lista_id').val(),
            entrada: $('#is_compra') ? $('#is_compra').val() : 0
        })
        .done((e) => {

            let cfop = e.cfop_atual
            let is_xml = $('#is_xml') ? $('#is_xml').val() : 0
            if(is_xml) return 1
                $qtd.val('1')
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

            // console.log(e)
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
            $qtd.focus()
            if(e.variacao_modelo_id && !codigo_variacao){
                buscarVariacoes(product_id)
            }

            if(codigo_variacao > 0){
                setarVariacao(codigo_variacao)
            }

            if(tipo_dimensao){
                KEY = null
                $('#dados_dimensao').modal('show')
                $('#dimensao_total').val('')
                $('#dados_dimensao .modal-title').text(e.nome)
                $('#dados_dimensao #dimensao_valor_unitario_m2').val(convertFloatToMoeda(e.valor_unitario))
                $('#dados_dimensao #dimensao_espessura').val((e.espessura))
                $('#dados_dimensao #dimensao_largura').val((e.largura))
                $('#dados_dimensao #dimensao_altura').val((e.altura))

                if(e.altura > 0 && e.largura > 0){
                    $('#dados_dimensao #dimensao_largura').prop('readonly', true);
                    $('#dados_dimensao #dimensao_altura').prop('readonly', true);
                }
                $('#btn-salvar-dimensao').text('Salvar')

            }
        })
        .fail((e) => {
            console.log(e)
        })
    }
})

var KEY = null
function alterarDimensoesItem(id, key){
    KEY = key
    DIMENSOES = []
    $.get(path_url + "api/produtos/get-dimensao-edit", {id: id})
    .done(res => {
        $('#dados_dimensao').modal('show')
        $('#dados_dimensao table tbody').html(res.view)
        $('#dados_dimensao .modal-title').text(res.produto.nome)
        $('#dados_dimensao #dimensao_espessura').val(convertFloatToMoeda(res.produto.espessura))
        $('#dados_dimensao #dimensao_valor_unitario_m2').val(convertFloatToMoeda(res.produto.valor_unitario))
        $('#btn-salvar-dimensao').text('Editar')
        let cont = 0;
        res.data.map((x) => {
            let js = {
                key: cont,
                dimensao_valor_unitario_m2: x.valor_unitario_m2,
                dimensao_largura: x.largura,
                dimensao_altura: x.altura,
                dimensao_quantidade: x.quantidade,
                dimensao_m2_total: x.m2_total,
                dimensao_espessura: x.espessura,
                dimensao_sub_total: x.sub_total,
                dimensao_observacao: x.observacao,
            }
            DIMENSOES.push(js)
            cont++

        })

        setTimeout(() => {
            calculaTotalDimensao()
        }, 100)
    })
    .fail(err => {
        console.log(err)
    })
}

$(document).on("blur", "#dimensao_quantidade", function () {
    calculaDimensao()
})

    // $(document).on("blur", "#dimensao_largura", function () {
    //     let value = parseFloat($(this).val())
    //     dif = -1
        // while(dif != 0){
        //     dif = value%5
        //     if(dif != 0){
        //         value++
        //     }else{
        //         $(this).val(value)
        //     }
        // }
    // })

    // $(document).on("blur", "#dimensao_altura", function () {
    //     let value = parseFloat($(this).val())
    //     dif = -1
    //     while(dif != 0){
    //         dif = value%5
    //         if(dif != 0){
    //             value++
    //         }else{
    //             $(this).val(value)
    //         }
    //     }
    // })

    function calculaDimensao(){
        let qtd = parseFloat($('#dimensao_quantidade').val())
        let dimensao_valor_unitario_m2 = convertMoedaToFloat($('#dimensao_valor_unitario_m2').val())
        let dimensao_largura = parseFloat($('#dimensao_largura').val())
        let dif = -1
        while(dif != 0){
            dif = dimensao_largura%5
            if(dif != 0){
                dimensao_largura++
            }
        }
        let dimensao_altura = parseFloat($('#dimensao_altura').val())
        dif = -1
        while(dif != 0){
            dif = dimensao_altura%5
            if(dif != 0){
                dimensao_altura++
            }
        }

        let m2 = qtd * (dimensao_largura/1000) * (dimensao_altura/1000)
        $('#dimensao_m2_total').val(m2.toFixed(3))
        $('#dimensao_sub_total').val(convertFloatToMoeda(m2*dimensao_valor_unitario_m2))
    }

    var DIMENSOES = []
    $(document).on("click", "#btn-add-dimensao", function () {

        let dimensao_valor_unitario_m2 = $('#dimensao_valor_unitario_m2').val()
        let dimensao_largura = $('#dimensao_largura').val()
        let dimensao_altura = $('#dimensao_altura').val()
        let dimensao_quantidade = $('#dimensao_quantidade').val()
        let dimensao_m2_total = $('#dimensao_m2_total').val()
        let dimensao_espessura = $('#dimensao_espessura').val()
        let dimensao_sub_total = $('#dimensao_sub_total').val()
        let dimensao_observacao = $('#dimensao_observacao').val()

        let data = {
            dimensao_valor_unitario_m2: dimensao_valor_unitario_m2,
            dimensao_largura: dimensao_largura,
            dimensao_altura: dimensao_altura,
            dimensao_quantidade: dimensao_quantidade,
            dimensao_m2_total: dimensao_m2_total,
            dimensao_espessura: dimensao_espessura,
            dimensao_sub_total: dimensao_sub_total,
            dimensao_observacao: dimensao_observacao,
        }

        DIMENSOES.push(data)

        $.get(path_url + "api/produtos/linha-dimensao", data)
        .done(res => {
            // console.log(res)
            $('#dados_dimensao table tbody').append(res)
            calculaTotalDimensao()

            if(!$('#dimensao_largura').prop('readonly')) {
                $('#dimensao_largura').val('')
            }
            if(!$('#dimensao_altura').prop('readonly')) {
                $('#dimensao_altura').val('')
            }
            $('#dimensao_quantidade').val('')
            $('#dimensao_m2_total').val('')
            $('#dimensao_sub_total').val('')
            $('#dimensao_observacao').val('')
            $('#dimensao_largura').focus()
        })
        .fail(err => {
            console.log(err)
        })
    })

    $(document).on("click", ".btn-remove-tr-dimensao", function () {
        let key = $(this).data('key')
        $line = $(this).closest('tr')
        $line.remove()
        DIMENSOES = DIMENSOES.filter((x) => {
            return x.key != key
        })
        // console.log(DIMENSOES)
        calculaTotalDimensao()
    })

    function calculaTotalDimensao(){
        setTimeout(() => {
            let total = 0
            $("#dados_dimensao .sub_total").each(function () {
                total += convertMoedaToFloat($(this).text())
            })
            $('#dimensao_total').val(convertFloatToMoeda(total))
        }, 100)
    }

    $(document).on("click", "#btn-salvar-dimensao", function () {
        let salvar = $("#btn-salvar-dimensao").text() == 'Salvar' ? 1 : 0
        $('.dimensoes-hidden').html('')
        if(salvar == 1){
            DIMENSOES.map((x, i) => {

                let l = $('.table-produtos .dynamic-form').length-1

                let input = "<input type='hidden' name='dimensao_altura[]' value='"+x.dimensao_altura+"' />"
                input += "<input type='hidden' name='dimensao_espessura[]' value='"+x.dimensao_espessura+"' />"
                input += "<input type='hidden' name='dimensao_largura[]' value='"+x.dimensao_largura+"' />"
                input += "<input type='hidden' name='dimensao_m2_total[]' value='"+x.dimensao_m2_total+"' />"
                input += "<input type='hidden' name='dimensao_observacao[]' value='"+x.dimensao_observacao+"' />"
                input += "<input type='hidden' name='dimensao_quantidade[]' value='"+x.dimensao_quantidade+"' />"
                input += "<input type='hidden' name='dimensao_sub_total[]' value='"+x.dimensao_sub_total+"' />"
                input += "<input type='hidden' name='dimensao_valor_unitario_m2[]' value='"+x.dimensao_valor_unitario_m2+"' />"
                input += "<input type='hidden' name='_line[]' value='"+l+"' />"
                $('.dimensoes-hidden').last().append(input)
            })
        }else{

            DIMENSOES.map((x, i) => {
                let l = $('.table-produtos .dynamic-form').length-1

                let input = "<input type='hidden' name='dimensao_altura[]' value='"+x.dimensao_altura+"' />"
                input += "<input type='hidden' name='dimensao_espessura[]' value='"+x.dimensao_espessura+"' />"
                input += "<input type='hidden' name='dimensao_largura[]' value='"+x.dimensao_largura+"' />"
                input += "<input type='hidden' name='dimensao_m2_total[]' value='"+x.dimensao_m2_total+"' />"
                input += "<input type='hidden' name='dimensao_observacao[]' value='"+x.dimensao_observacao+"' />"
                input += "<input type='hidden' name='dimensao_quantidade[]' value='"+x.dimensao_quantidade+"' />"
                input += "<input type='hidden' name='dimensao_sub_total[]' value='"+x.dimensao_sub_total+"' />"
                input += "<input type='hidden' name='dimensao_valor_unitario_m2[]' value='"+x.dimensao_valor_unitario_m2+"' />"
                input += "<input type='hidden' name='_line[]' value='"+KEY+"' />"
                $('.dh_'+KEY).last().append(input)
            })
        }

        setTimeout(() => {
            $('#dados_dimensao').modal('hide')
            let dimensao_total = $('#dimensao_total').val()
            $('.valor_unit').last().val(dimensao_total)
            $('.sub_total_new').last().val(dimensao_total)
            calcTotal()
        }, 100)

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

        $varicao = $('.table-produtos').find("tr").last().find('input')[1]
        $qtd = $('.table-produtos').find("tr").last().find('input')[2]
        $vlUnit = $('.table-produtos').find("tr").last().find('input')[3]
        $sub = $('.table-produtos').find("tr").last().find('input')[4]
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
        calcTotal()
        
        if($('#is_compra') && $('#is_compra').val() == 1){
            return;
        }

        if($('#is_orcamento').val() == 0){
            return;
        }
        // validaDescontoItem(qtd, value_unit)
        $inpProduto = $(this).closest('td').prev().prev().find('select')
        if($inpProduto.val()){
            $.get(path_url + "api/orcamentos/valida-desconto", 
            { 
                produto_id: $inpProduto.val(), valor: value_unit, empresa_id: $('#empresa_id').val() 
            }).done((res) => {
                $sub.val(convertFloatToMoeda(qtd * value_unit))
            })
            .fail((err) => {
                console.log(err)
                let v = err.responseJSON
                $(this).val(convertFloatToMoeda(v))
                $sub.val(convertFloatToMoeda(qtd * v))
                swal("Erro", "Valor minímo para este item " + convertFloatToMoeda(v), "error")
            })
        }
    })

    function validaDescontoItem(qtd, value_unit){
        let produto_id
        $sub.val(convertFloatToMoeda(qtd * value_unit))
    }

    $('body').on('blur', '.qtd', function () {
        $value_unit = $(this).closest('td').next().find('input');
        $sub = $(this).closest('td').next().next().find('input');
        let qtd = $(this).val();
        qtd = convertMoedaToFloat(qtd)
        let value_unit = convertMoedaToFloat($value_unit.val())
        $sub.val(convertFloatToMoeda(qtd * value_unit))

        $prod = $(this).closest('td').prev().find('select')
        if($prod.select2('data')[0].gerenciar_estoque == 1 && $('#is_compra').val() == 0){
            let estoque = $prod.select2('data')[0].estoque_atual
            if(qtd > estoque){
                toastr.warning("Quantidade de estoque insuficiente!")
                $(this).val(estoque)
                $sub.val(convertFloatToMoeda(estoque * value_unit))
            }
        }

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
        $('.btn-fatura-padrao').addClass('d-none')

        $.get(path_url + "api/clientes/find/" + cliente)
        .done((res) => {
            // console.log(res)
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

            if(res.fatura.length > 0){
                $('.btn-fatura-padrao').removeClass('d-none')
            }

            if(res.lista_preco_id){
                $('#lista_id').val(res.lista_preco_id)
                $('.lista_selecionada').text('Lista selecionada: ' + res.lista_preco.nome + " " + res.lista_preco.percentual_alteracao + "%")
            }

            let tributacao = res.tributacao
            if(tributacao){
                TRIBUTACAOCLIENTE = 0
                if(tributacao.ncm || tributacao.perc_cofins || tributacao.perc_icms || tributacao.perc_ipi || tributacao.perc_pis || tributacao.perc_red_bc){
                    TRIBUTACAOCLIENTE = 1
                }

                if(tributacao.cest || tributacao.cfop_estadual || tributacao.cfop_outro_estado || tributacao.cst_cofins || tributacao.cst_csosn || tributacao.cst_ipi || tributacao.cst_pis){
                    TRIBUTACAOCLIENTE = 1
                }

                if(TRIBUTACAOCLIENTE == 1){
                    swal({
                        title: "Tributação?",
                        text: "Este cliente possui um padrão de tributação exclusivo deseja utilizar?",
                        icon: "info",
                        buttons: true,
                        buttons: ["NÃO", "SIM"],
                        dangerMode: true,
                    }).then((isConfirm) => {
                        if (isConfirm) {
                            TRIBUTACAOCLIENTE = 1
                        } else {
                            swal("", "O Padrão não será utilizado", "info");
                            TRIBUTACAOCLIENTE = 0
                        }
                    });
                }
            }
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
        // $('#inp-cidade_transp').val(res.cidade_id).change()
        $('#inp-rua_transp').val(res.rua)
        $('#inp-numero_transp').val(res.numero)
        $('#inp-cep_transp').val(res.cep)
        $('#inp-bairro_transp').val(res.bairro)
        $('#inp-complemento_transp').val(res.complemento)

        var newOption = new Option(res.cidade.info, res.cidade.id, false, false);
        $('#inp-cidade_transp').html('')
        $('#inp-cidade_transp').append(newOption).trigger('change');
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
    $('.h5-ipi').addClass('d-none')
    var total = 0
    var totalIpi = 0
    $(".sub_total").each(function () {
        total += convertMoedaToFloat($(this).val())
        $percIpi = $(this).closest('td').next().next().next().next().find('input')
        if($percIpi.val() > 0){
            let vIpi = convertMoedaToFloat($(this).val()) * ($percIpi.val()/100)
            vIpi = parseFloat(vIpi.toFixed(2))
            totalIpi += vIpi
        }
    })
    setTimeout(() => {
        total_venda = total

        $('.total_prod').html("R$ " + convertFloatToMoeda(total))
        if(totalIpi > 0){
            $('.total_ipi').html("R$ " + convertFloatToMoeda(totalIpi))
            $('.h5-ipi').removeClass('d-none')
        }
        $('.total_prod').val(total)
        calTotalNfe()
    }, 100)
}

$('body').on('blur', '.perc_ipi', function () {
    calcTotal()
})

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
    let total_ipi = 0
    if($(".total_ipi").length){
        total_ipi = convertMoedaToFloat($(".total_ipi").text())
    }
    let total_prod = parseFloat($('.total_prod').val())

    setTimeout(() => {
        total_frete = total_fr
        total_nfe = total_prod + total_fr + acrescimo - desconto + total_ipi
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
    if($('#is_compra').val() != 1){
        let infMsg = ""
        $("body #form-nfe").find('input, select').each(function () {
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
        if (!$('.produto_id').val()) {
            infMsg += "Produto\n"
        }
        if (infMsg != "") {
            swal("Campos pendentes", infMsg, "warning")
        }
    }
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
    let text = $('#inp-lista_preco_id option:selected').text()
    $('.lista_selecionada').text('Lista selecionada: ' + text)


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
