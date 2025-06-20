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

var KEY = null;
$(document).on("click", ".btn-modal-altera", function () {
    // $('#modal_altera_produto #check').removeAttr('checked')
    $inpNome = $(this).closest("td").find('.produto_nome')
    $inpGerenciaEstoque = $(this).closest("td").find('._gerenciar_estoque')
    $inpNcm = $(this).closest("tr").find('.ncm')
    $inpProdutoId = $(this).closest("td").find('._produto_id')
    $inpCategoriaId = $(this).closest("td").find('._categoria_id')
    $inpMarcaId = $(this).closest("td").find('._marca_id')
    $inpValorVenda = $(this).closest("tr").find('.valor_venda')
    $inpValorCompra = $(this).closest("tr").find('.valor_compra')
    $inpEstoqueMinimo = $(this).closest("tr").find('._estoque_minimo')
    $inpCodigoBarras = $(this).closest("tr").find('._codigo_barras')
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

    $('#modal_altera_produto').modal('show')
    $('#modal_altera_produto .modal-title').text($inpNome.val())
    $('#modal_altera_produto #modal_codigo').val($inpProdutoId.val())
    $('#modal_altera_produto #modal_nome').val($inpNome.val())

    $('#modal_altera_produto #modal_categoria_id').val($inpCategoriaId.val()).change()
    $('#modal_altera_produto #modal_codigo_barras').val($inpCodigoBarras.val()).change()
    $('#modal_altera_produto #modal_marca_id').val($inpMarcaId.val()).change()
    $('#modal_altera_produto #modal_gerenciar_estoque').val($inpGerenciaEstoque.val()).change()
    $('#modal_altera_produto #modal_ncm').val($inpNcm.val())
    $('#modal_altera_produto #modal_valor_venda').val($inpValorVenda.val())
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
    let codigo_barras = $('#modal_altera_produto #modal_codigo_barras').val()
    let marca_id = $('#modal_altera_produto #modal_marca_id').val()
    let gerenciar_estoque = $('#modal_altera_produto #modal_gerenciar_estoque').val()
    let ncm = $('#modal_altera_produto #modal_ncm').val()
    let valor_venda = $('#modal_altera_produto #modal_valor_venda').val()
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
    
    disponibilidade = JSON.stringify(disponibilidade)
    $('.line_'+KEY).find('.ncm').val(ncm)
    $('.line_'+KEY).find('.produto_nome').val(nome)
    $('.line_'+KEY).find('._categoria_id').val(categoria_id)
    $('.line_'+KEY).find('._codigo_barras').val(codigo_barras)
    $('.line_'+KEY).find('._marca_id').val(marca_id)
    $('.line_'+KEY).find('._gerenciar_estoque').val(gerenciar_estoque)
    $('.line_'+KEY).find('.valor_venda').val(valor_venda)
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
    $('.line_'+KEY).find('._disponibilidade').val(disponibilidade)

    if(check == true){
        $('.line_'+KEY).find('._check').val(1)
        $('.line_'+KEY).addClass('bg-success')
    }else{
        $('.line_'+KEY).find('._check').val(0)
        $('.line_'+KEY).removeClass('bg-success')
    }
    $('#modal_altera_produto').modal('hide')

});

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


    $.get(path_url + "api/produtos/findId/" + produto_id)
    .done((res) => {
        // console.log(res)
        $('.line_'+key).find('._produto_id').val(res.id)
        $('.line_'+key).find('.produto_nome').val(res.nome)
        $('.line_'+key).find('._categoria_id').val(res.categoria_id)
        $('.line_'+key).find('._marca_id').val(res.marca_id)
        $('.line_'+key).find('._codigo_barras').val(res.codigo_barras)
        $('.line_'+key).find('.ncm').val(res.ncm)
        $('.line_'+key).find('._estoque_minimo').val(res.estoque_minimo)
        $('.line_'+key).find('._gerenciar_estoque').val(res.gerenciar_estoque).change()
        $('.line_'+key).find('.valor_venda').val(convertFloatToMoeda(res.valor_unitario))
        $('.line_'+key).find('.valor_compra').val(convertFloatToMoeda(res.valor_compra))
        $('.line_'+key).find('._margem').val(res.percentual_lucro)

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


