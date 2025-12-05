$("tbody .produto_id").select2({
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
                empresa_id: empresa_id
            };
            return query;
        },
        processResults: function (response) {
            var results = [];
            
            $.each(response, function (i, v) {
                var o = {};
                o.id = v.id;
                if(v.codigo_variacao){
                    o.codigo_variacao = v.codigo_variacao
                }

                o.text = v.nome;
                o.text += ' R$ ' + convertFloatToMoeda(v.valor_compra);
                
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

$(document).on("click", ".btn-desvincular-todos", function () {

    $('tbody .first-col').each(function (i, v) {
        $select = $(this).find('.produto_id')
        $inpNome = $(this).find('.produto_nome')
        $select.remove()
        var newOption = new Option($inpNome.val(), '0', false, 1);
        $select.append(newOption);

        $(this).find('.text-primary').text('*Produto será cadastrado no sistema')
        .removeClass('text-primary').addClass('text-danger')

        $(this).find('.cadastrar_produto').val('1')
    })

    // var newOption = new Option(nome, '0', false, 1);
    // $('.line_'+KEY).find('.produto_id').append(newOption);
});

$('.btn-padrao-item').click(() => {
    $('.td-padrao').removeClass('d-none')
    $('.padrao_item_id').val('').change()
})

$(document).on("change", ".padrao_item_id", function() {
    let padrao = $(this).val()
    if (padrao) {
        $.get(path_url + "api/produtos/padrao", {
            padrao: padrao
        })
        .done((result) => {
            // console.log(result)

            $(this).closest('tr').find('.perc_icms').val(result.perc_icms)
            $(this).closest('tr').find('.perc_pis').val(result.perc_pis)
            $(this).closest('tr').find('.perc_cofins').val(result.perc_cofins)
            $(this).closest('tr').find('.perc_ipi').val(result.perc_ipi)

            $(this).closest('tr').find('.cst_csosn').val(result.cst_csosn).change()
            $(this).closest('tr').find('.cst_pis').val(result.cst_pis).change()
            $(this).closest('tr').find('.cst_cofins').val(result.cst_cofins).change()
            $(this).closest('tr').find('.cst_ipi').val(result.cst_ipi).change()

        })
        .fail((err) => {
            console.log(err)
        })
    }
});

$(function(){
    // $('.btn-modal-altera').first().trigger('click')
    $inpDate = $('.table-fatura').find('input[type="date"]').first()
    if($inpDate){
        let hoje = new Date();
        let dataDoInput = new Date($inpDate.val());

        hoje.setHours(0, 0, 0, 0);
        dataDoInput.setHours(0, 0, 0, 0);
        if (dataDoInput > hoje) {
            swal({
                title: "Gerar Conta a Pagar?",
                text: "Este XML possuí uma data posterior à data de hoje!",
                icon: "warning",
                buttons: true,
                buttons: ["Não", "Sim"],
                dangerMode: true,
            }).then((isConfirm) => {
                if (isConfirm) {
                    $('#inp-gerar_conta_pagar').val('1').change()
                }
            });
        }
    }
})

$(document).on("change", "#inp-padrao_id", function() {
    let padrao = $(this).val()
    if (padrao) {
        $.get(path_url + "api/produtos/padrao", {
            padrao: padrao
        })
        .done((result) => {
            // console.log(result)

            $('.perc_icms').val(result.perc_icms)
            $('.perc_pis').val(result.perc_pis)
            $('.perc_cofins').val(result.perc_cofins)
            $('.perc_ipi').val(result.perc_ipi)

            $('.cst_csosn').val(result.cst_csosn).change()
            $('.cst_pis').val(result.cst_pis).change()
            $('.cst_cofins').val(result.cst_cofins).change()
            $('.cst_ipi').val(result.cst_ipi).change()

        })
        .fail((err) => {
            console.log(err)
        })
    }
});

$(document).on("click", ".btn-modal-categoria", function () {
    $('#modal_categoria_produto').modal('show')
})

$(document).on("click", ".btn-modal-marca", function () {
    $('#modal_marca').modal('show')
})

$("#modal_sub_categoria_id").select2({
    minimumInputLength: 2,
    language: "pt-BR",
    placeholder: "Digite para buscar a subcategoria",
    width: "100%",
    dropdownParent: $('#modal_altera_produto'),
    ajax: {
        cache: true,
        url: path_url + "api/subcategorias",
        dataType: "json",
        data: function (params) {
            console.clear();
            
            var query = {
                pesquisa: params.term,
                categoria_id: $('#modal_categoria_id').val()
            };
            return query;
        },
        processResults: function (response) {
            var results = [];
            if(!$('#modal_categoria_id').val()){
                swal("Alerta", "Selecione a categoria primeiro!", "warning")
                return
            }
            $.each(response, function (i, v) {

                var o = {};

                o.id = v.id;
                o.text = v.nome
                results.push(o);
            });
            return {
                results: results,
            };
        },
    },
});

$('.btn-store-categoria').click(() => {
    let item = {
        nome: $('#inp-nome_categoria').val(),
        empresa_id: $('#empresa_id').val()
    }

    $.post(path_url + "api/produtos/categoria-store", item)
    .done((result) => {
        var newOption = new Option(result.nome, result.id, 1, false);
        $('#modal_categoria_id').append(newOption);
        $('#modal_categoria_id').val(result.id).change();
        $('#inp-nome_categoria').val('')
        $('#modal_categoria_produto').modal('hide')
        swal("Sucesso", "Categoria cadastrada!", "success")
    })
    .fail((err) => {
        console.log(err)
        swal("Erro", "Erro ao salvar categoria", "error")
    })
})

$('.btn-store-marca').click(() => {
    let item = {
        nome: $('#inp-nome_marca').val(),
        empresa_id: $('#empresa_id').val()
    }

    $.post(path_url + "api/produtos/marca-store", item)
    .done((result) => {
        var newOption = new Option(result.nome, result.id, 1, false);
        $('#modal_marca_id').append(newOption);
        $('#modal_marca_id').val(result.id).change();
        $('#inp-nome_marca').val('')
        $('#modal_marca').modal('hide')
        swal("Sucesso", "Marca cadastrada!", "success")
    })
    .fail((err) => {
        console.log(err)
        swal("Erro", "Erro ao salvar marca", "error")
    })
})


var KEY = null;

$(document).on("click", ".btn-modal-altera", function () {
    // $('#modal_altera_produto #check').removeAttr('checked')
    $inpNome = $(this).closest("td").find('.produto_nome')
    $inpGerenciaEstoque = $(this).closest("td").find('._gerenciar_estoque')
    $inpNcm = $(this).closest("tr").find('.ncm')
    $inpProdutoId = $(this).closest("td").find('._produto_id')
    $inpCategoriaId = $(this).closest("td").find('._categoria_id')
    $inpSubCategoriaId = $(this).closest("td").find('._sub_categoria_id')
    $inpSubCategoriaNome = $(this).closest("td").find('._sub_categoria_nome')
    $inpMarcaId = $(this).closest("td").find('._marca_id')
    $inpQtd = $(this).closest("tr").find('.qtd')
    $inpValorVenda = $(this).closest("tr").find('.valor_venda')
    $inpValorAtacado = $(this).closest("tr").find('._valor_atacado')

    $inpQtdAtacado = $(this).closest("tr").find('._quantidade_atacado')
    $inpValorMinimoVenda = $(this).closest("tr").find('._valor_minimo_venda')

    $inpValorCompra = $(this).closest("tr").find('.valor_compra')
    $inpEstoqueMinimo = $(this).closest("tr").find('._estoque_minimo')
    $inpCodigoBarras = $(this).closest("tr").find('._codigo_barras')
    $inpCodigoBarras2 = $(this).closest("tr").find('._codigo_barras2')
    $inpCheck = $(this).closest("tr").find('._check')
    $inpMargem = $(this).closest("tr").find('._margem')
    $inpReferencia = $(this).closest("tr").find('._referencia')
    $inpReferenciaBalanca = $(this).closest("tr").find('._referencia_balanca')
    $inpUnidade = $(this).closest("tr").find('.unidade')
    $inpExportarBalanca = $(this).closest("tr").find('._exportar_balanca')
    $inpObservacao = $(this).closest("tr").find('._observacao')
    $inpObservacao2 = $(this).closest("tr").find('._observacao2')
    $inpObservacao3 = $(this).closest("tr").find('._observacao3')
    $inpObservacao4 = $(this).closest("tr").find('._observacao4')
    $inpDisponibilidade = $(this).closest("tr").find('._disponibilidade')

    KEY = $(this).data('key')

    if($inpProdutoId.val() > 0){
        $('.select-desvincular').removeClass('d-none')
    }

    $('#modal_altera_produto').modal('show')
    $('#modal_altera_produto .modal-title').text($inpNome.val())
    $('#modal_altera_produto #modal_codigo').val($inpProdutoId.val())
    $('#modal_altera_produto #modal_nome').val($inpNome.val())

    $('#modal_altera_produto #modal_categoria_id').val($inpCategoriaId.val()).change()
    $('#modal_altera_produto #modal_codigo_barras').val($inpCodigoBarras.val()).change()
    $('#modal_altera_produto #modal_codigo_barras2').val($inpCodigoBarras2.val()).change()
    $('#modal_altera_produto #modal_marca_id').val($inpMarcaId.val()).change()
    $('#modal_altera_produto #modal_gerenciar_estoque').val($inpGerenciaEstoque.val()).change()
    $('#modal_altera_produto #modal_ncm').val($inpNcm.val())
    $('#modal_altera_produto #modal_quantidade').val($inpQtd.val())
    $('#modal_altera_produto #modal_valor_venda').val($inpValorVenda.val())

    $('#modal_altera_produto #modal_valor_atacado').val(convertFloatToMoeda($inpValorAtacado.val()))

    $('#modal_altera_produto #modal_quantidade_atacado').val($inpQtdAtacado.val())
    $('#modal_altera_produto #modal_valor_minimo_venda').val(convertFloatToMoeda($inpValorMinimoVenda.val()))

    $('#modal_altera_produto #modal_valor_compra').val($inpValorCompra.val())
    $('#modal_altera_produto #modal_estoque_minimo').val($inpEstoqueMinimo.val())
    $('#modal_altera_produto #modal_margem').val($inpMargem.val())

    $('#modal_altera_produto #modal_referencia').val($inpReferencia.val())
    $('#modal_altera_produto #modal_referencia_balanca').val($inpReferenciaBalanca.val())
    $('#modal_altera_produto #modal_unidade').val($inpUnidade.val()).change()
    $('#modal_altera_produto #modal_exportar_balanca').val($inpExportarBalanca.val()).change()

    $('#modal_altera_produto #modal_observacao').val($inpObservacao.val())
    $('#modal_altera_produto #modal_observacao2').val($inpObservacao2.val())
    $('#modal_altera_produto #modal_observacao3').val($inpObservacao3.val())
    $('#modal_altera_produto #modal_observacao4').val($inpObservacao4.val())
    $('#modal_altera_produto #modal_disponibilidade').val(JSON.parse($inpDisponibilidade.val())).change()

    if($inpCheck.val() == 1){
        $('#modal_altera_produto #check').prop('checked', 1)
    }else{
        $('#modal_altera_produto #check').prop('checked', 0)
    }

    setTimeout(() => {
        if($inpSubCategoriaId.val() > 0){
            var newOption = new Option($inpSubCategoriaNome.val(), $inpSubCategoriaId.val(), false, 1);
            // console.log(newOption)
            $('#modal_sub_categoria_id').append(newOption);
        }
    }, 100)

    // if($inpProdutoId.val() > 0){
        // let produto_id = $inpProdutoId.val()
        // $.get(path_url + "api/produtos/findId/" + produto_id)
        // .done((res) => {
        //     console.log(res)
        // })
        // .fail((err) => {
        //     console.log(err)
        // })
    // }

})

$(".btn-salvar-nfe").on("click", function (e) {
    $(this).prop('disabled', true).text('Processando...')

    let campos = ""
    let isInalid = false

    $('#form-xml').find("input, select").each(function () {
        if (($(this).val() == "" || $(this).val() == null) && $(this).prop('required')) {
            $(this).addClass('is-invalid')
            isInalid = true;
            $(this).prev()[0].textContent
            campos += $(this).prev()[0].textContent + ", "
        }
    });

    setTimeout(() => {
        if(isInalid){
            audioError()
            campos = campos.substring(0, campos.length-2)
            toastr.error('Campos obrigatórios não preenchidos: ' + campos);
        }else{
            $('#form-xml').submit()
        }
    }, 50)

    setTimeout(() => {
        $(this).prop('disabled', false).text('Salvar')
    }, 1000)
})

$(document).on("blur", "#modal_quantidade", function () {
    let qtd = convertMoedaToFloat($(this).val())
    if(qtd <= 0){
        $(this).val('1')
        qtd = 1
    }
    let valor_compra_original = convertMoedaToFloat($('#modal_valor_compra').val())
    let sub_total_original = $('.line_'+KEY).find('._valor_sub_total_original').val()
    
    let v = sub_total_original/qtd
    $('#modal_valor_compra').val(convertFloatToMoeda(v))
    let valor_venda = convertMoedaToFloat($('#modal_valor_venda').val())

    let dif = (valor_venda - v)/v*100;

    $('#modal_margem').val(dif.toFixed(2))

});

$(document).on("blur", "#modal_margem", function () {
    let margem = $(this).val()
    let valor_compra = convertMoedaToFloat($('#modal_valor_compra').val())

    $('#modal_valor_venda').val(convertFloatToMoeda(valor_compra + (valor_compra*(margem/100))))

});

$(document).on("blur", "#modal_valor_compra", function () {
    let valor_compra = convertMoedaToFloat($(this).val())
    let margem = $('#modal_margem').val()

    $('#modal_valor_venda').val(convertFloatToMoeda(valor_compra + (valor_compra*(margem/100))))
});

$(document).on("blur", "#modal_valor_venda", function () {
    let valor_venda = convertMoedaToFloat($(this).val())
    let valor_compra = convertMoedaToFloat($('#modal_valor_compra').val())

    let dif = (valor_venda - valor_compra)/valor_compra*100;
    $('#modal_margem').val(dif.toFixed(2))
});

$(document).on("click", ".btn-modal-alterar", function () {
    let nome = $('#modal_altera_produto #modal_nome').val()
    let categoria_id = $('#modal_altera_produto #modal_categoria_id').val()
    let sub_categoria_id = $('#modal_altera_produto #modal_sub_categoria_id').val()
    let codigo_barras = $('#modal_altera_produto #modal_codigo_barras').val()
    let codigo_barras2 = $('#modal_altera_produto #modal_codigo_barras2').val()
    let marca_id = $('#modal_altera_produto #modal_marca_id').val()
    let gerenciar_estoque = $('#modal_altera_produto #modal_gerenciar_estoque').val()
    let ncm = $('#modal_altera_produto #modal_ncm').val()
    let qtd = $('#modal_altera_produto #modal_quantidade').val()
    let valor_venda = $('#modal_altera_produto #modal_valor_venda').val()
    let valor_atacado = $('#modal_altera_produto #modal_valor_atacado').val()

    let quantidade_atacado = $('#modal_altera_produto #modal_quantidade_atacado').val()
    let valor_minimo_venda = $('#modal_altera_produto #modal_valor_minimo_venda').val()

    let valor_compra = $('#modal_altera_produto #modal_valor_compra').val()
    let estoque_minimo = $('#modal_altera_produto #modal_estoque_minimo').val()
    let margem = $('#modal_altera_produto #modal_margem').val()
    let check = $('#modal_altera_produto #check').is(':checked')

    let referencia = $('#modal_altera_produto #modal_referencia').val()
    let referencia_balanca = $('#modal_altera_produto #modal_referencia_balanca').val()
    let unidade = $('#modal_altera_produto #modal_unidade').val()
    let exportar_balanca = $('#modal_altera_produto #modal_exportar_balanca').val()
    let observacao = $('#modal_altera_produto #modal_observacao').val()
    let observacao2 = $('#modal_altera_produto #modal_observacao2').val()
    let observacao3 = $('#modal_altera_produto #modal_observacao3').val()
    let observacao4 = $('#modal_altera_produto #modal_observacao4').val()
    let disponibilidade = $('#modal_altera_produto #modal_disponibilidade').val()
    let desvincular = $('#modal_altera_produto #modal_desvincular').val()

    disponibilidade = JSON.stringify(disponibilidade)
    $('.line_'+KEY).find('.ncm').val(ncm)
    $('.line_'+KEY).find('.produto_nome').val(nome)
    $('.line_'+KEY).find('.qtd').val(qtd)
    $('.line_'+KEY).find('._categoria_id').val(categoria_id)
    $('.line_'+KEY).find('._sub_categoria_id').val(sub_categoria_id)
    $('.line_'+KEY).find('._codigo_barras').val(codigo_barras)
    $('.line_'+KEY).find('._codigo_barras2').val(codigo_barras2)
    $('.line_'+KEY).find('._marca_id').val(marca_id)
    $('.line_'+KEY).find('._gerenciar_estoque').val(gerenciar_estoque)
    $('.line_'+KEY).find('.valor_venda').val(valor_venda)

    $('.line_'+KEY).find('._valor_minimo_venda').val(valor_minimo_venda)
    $('.line_'+KEY).find('._quantidade_atacado').val(quantidade_atacado)

    $('.line_'+KEY).find('._valor_atacado').val(convertMoedaToFloat(valor_atacado))

    $('.line_'+KEY).find('._quantidade_atacado').val(quantidade_atacado)
    $('.line_'+KEY).find('._valor_minimo_venda').val(convertMoedaToFloat(valor_minimo_venda))

    $('.line_'+KEY).find('.valor_compra').val(valor_compra)
    $('.line_'+KEY).find('._estoque_minimo').val(estoque_minimo)
    $('.line_'+KEY).find('._margem').val(margem)

    $('.line_'+KEY).find('._referencia').val(referencia)
    $('.line_'+KEY).find('._referencia_balanca').val(referencia_balanca)
    $('.line_'+KEY).find('.unidade').val(unidade)
    $('.line_'+KEY).find('._exportar_balanca').val(exportar_balanca)
    $('.line_'+KEY).find('._observacao').val(observacao)
    $('.line_'+KEY).find('._observacao2').val(observacao2)
    $('.line_'+KEY).find('._observacao3').val(observacao3)
    $('.line_'+KEY).find('._observacao4').val(observacao4)
    if(disponibilidade){
        $('.line_'+KEY).find('._disponibilidade').val(disponibilidade)
    }

    if(check == true){
        $('.line_'+KEY).find('._check').val(1)
        $('.line_'+KEY).addClass('bg-success')
    }else{
        $('.line_'+KEY).find('._check').val(0)
        $('.line_'+KEY).removeClass('bg-success')
    }

    if(desvincular == 1){
        $('.line_'+KEY).find('.cadastrar_produto').val('1')
        $('.line_'+KEY).find('.produto_id option').remove()

        var newOption = new Option(nome, '0', false, 1);
        $('.line_'+KEY).find('.produto_id').append(newOption);

        $('.line_'+KEY).find('.text-primary').text('*Produto será cadastrado no sistema')
        .removeClass('text-primary').addClass('text-danger')
    }
    $('#modal_altera_produto').modal('hide')

});

$(document).on("change", "#inp-gerenciar_estoque", function () {
    let gerenciar_estoque = $(this).val()
    $('._gerenciar_estoque').val(gerenciar_estoque)
})
$(document).on("change", ".produto_id", function () {
    let produto_id = $(this).val()
    $cadProd = $(this).prev().prev().prev().prev().prev();
    $label = $(this).closest('td').find('.text-danger');
    $inpKey = $(this).closest('td').find('._key');
    let key = $inpKey.val()
    // console.log($label)
    // console.log($cadProd)
    // $cadProd.val('0')
    $label.text('Produto vinculado')
    $label.removeClass('text-danger')
    $label.addClass('text-primary')

    if(produto_id){
        $(this).closest('td').find('.cadastrar_produto').val('0')
    }
    $.get(path_url + "api/produtos/findId/" + produto_id)
    .done((res) => {
        // console.log(res)
        $('.line_'+key).find('._produto_id').val(res.id)
        $('.line_'+key).find('.produto_nome').val(res.nome)
        $('.line_'+key).find('._categoria_id').val(res.categoria_id)
        $('.line_'+key).find('._sub_categoria_id').val(res.sub_categoria_id)
        $('.line_'+key).find('._marca_id').val(res.marca_id)
        $('.line_'+key).find('._codigo_barras').val(res.codigo_barras)
        $('.line_'+key).find('._codigo_barras2').val(res.codigo_barras2)
        $('.line_'+key).find('.ncm').val(res.ncm)
        $('.line_'+key).find('._estoque_minimo').val(res.estoque_minimo)
        $('.line_'+key).find('._gerenciar_estoque').val(res.gerenciar_estoque).change()
        $('.line_'+key).find('.valor_venda').val(convertFloatToMoeda(res.valor_unitario))
        $('.line_'+key).find('.valor_compra').val(convertFloatToMoeda(res.valor_compra))
        $('.line_'+key).find('._margem').val(res.percentual_lucro)
        $('.line_'+key).find('._referencia').val(res.referencia)
        $('.line_'+key).find('._referencia_balanca').val(res.referencia_balanca)
        $('.line_'+key).find('._valor_atacado').val(convertFloatToMoeda(res.valor_atacado))
        $('.line_'+key).find('._quantidade_atacado').val(res.quantidade_atacado)
        $('.line_'+key).find('._valor_minimo_venda').val(convertFloatToMoeda(res.valor_minimo_venda))
        $('.line_'+key).find('._sub_categoria_nome').val(res.sub_categoria_id ? res.subcategoria.nome : '')
        $('.line_'+key).find('._exportar_balanca').val(res.exportar_balanca)

        $('.line_'+key).find('._observacao').val(res.observacao)
        $('.line_'+key).find('._observacao2').val(res.observacao2)
        $('.line_'+key).find('._observacao3').val(res.observacao3)
        $('.line_'+key).find('._observacao4').val(res.observacao4)
        $('.line_'+key).find('.unidade').val(res.unidade)
        $('.line_'+key).find('._disponibilidade').val(res.disponibilidade)


    })
    .fail((err) => {
        console.log(err)
    })
    // alterar dados no html
})

function modalXml(nome, valor, cfop){

    $('#modal_show_xml').modal('show')
    let html = '<h3>Descrição: <strong>' + nome + '</strong></h3>'
    html += '<h4>Valor: <strong>R$ ' + convertFloatToMoeda(valor) + '</strong></h4>'
    html += '<h4>CFOP: <strong>' + cfop + '</strong></h4>'
    $('#modal_show_xml .modal-body').html(html)
}


