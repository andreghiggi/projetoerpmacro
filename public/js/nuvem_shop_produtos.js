setTimeout(() => {
    if($('#inp-padrao_id').val()){
        $('#inp-padrao_id').change()
    }
}, 100)

$(document).on("change", "#inp-padrao_id", function() {
    let padrao = $(this).val()
    if (padrao) {
        $.get(path_url + "api/produtos/padrao", {
            padrao: padrao
        })
        .done((result) => {
            console.log(result)

            // var newOption = new Option(result._ncm.descricao, result._ncm.codigo, 1, false);
            // $('.ncm').append(newOption);

            $('.ncm').val(result._ncm.codigo)
            $('.cest').val(result.cest)
            $('.perc_icms').val(result.perc_icms)
            $('.perc_pis').val(result.perc_pis)
            $('.perc_cofins').val(result.perc_cofins)
            $('.perc_ipi').val(result.perc_ipi)
            $('.cst_csosn').val(result.cst_csosn).change()
            $('.cst_pis').val(result.cst_pis).change()
            $('.cst_cofins').val(result.cst_cofins).change()
            $('.cst_ipi').val(result.cst_ipi).change()
            $('.cEnq').val(result.cEnq).change()
            $('.cfop_estadual').val(result.cfop_estadual)
            $('.cfop_outro_estado').val(result.cfop_outro_estado)

            $('.cfop_entrada_estadual').val(result.cfop_entrada_estadual)
            $('.cfop_entrada_outro_estado').val(result.cfop_entrada_outro_estado)
        })
        .fail((err) => {
            console.log(err)
        })
    }
});

$(".produto").select2({
    minimumInputLength: 2,
    language: "pt-BR",
    placeholder: "Digite para buscar o produto para vincular (opcional)",
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
            let compra = 0
            

            $.each(response, function (i, v) {
                var o = {};
                o.id = v.id;
                if(v.codigo_variacao){
                    o.codigo_variacao = v.codigo_variacao
                }

                o.text = v.nome
                
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