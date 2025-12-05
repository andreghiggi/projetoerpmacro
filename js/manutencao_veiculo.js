$(".servico_id").select2({
    minimumInputLength: 2,
    language: "pt-BR",
    placeholder: "Digite para buscar o seviço",
    width: "100%",
    ajax: {
        cache: true,
        url: path_url + "api/servicos",
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

                o.text = v.nome + ' R$ ' + convertFloatToMoeda(v.valor);
                o.value = v.id;
                results.push(o);
            });
            return {
                results: results,
            };
        },
    },
});

$(".produto_id").select2({
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

                o.text = v.nome + ' R$ ' + convertFloatToMoeda(v.valor_unitario);
                o.value = v.id;
                results.push(o);
            });
            return {
                results: results,
            };
        },
    },
});

$("body").on("change", ".servico_id", function () {
    let servico_id = $(this).val()
    $.get(path_url + "api/servicos/find/"+servico_id)
    .done((success) => {
        console.log(success)
        $qtd = $(this).closest('td').next().find('input');
        $valorUnit = $(this).closest('td').next().next().find('input');
        $sub = $(this).closest('td').next().next().next().find('input');
        $qtd.val('1')
        $valorUnit.val(convertFloatToMoeda(success.valor))
        $sub.val(convertFloatToMoeda(success.valor))
        calcTotal()

    })
    .fail((error) => {
        console.log(error)
    })
})

$("body").on("change", ".produto_id", function () {
    let produto_id = $(this).val()
    $.get(path_url + "api/produtos/find",{
        usuario_id: $('#usuario_id').val(),
        produto_id: produto_id
    })
    .done((success) => {
        console.log(success)
        $qtd = $(this).closest('td').next().find('input');
        $valorUnit = $(this).closest('td').next().next().find('input');
        $sub = $(this).closest('td').next().next().next().find('input');
        $qtd.val('1')
        $valorUnit.val(convertFloatToMoeda(success.valor_unitario))
        $sub.val(convertFloatToMoeda(success.valor_unitario))
        calcTotal()

    })
    .fail((error) => {
        console.log(error)
    })
})

$("body").on("blur", "#inp-desconto, #inp-acrescimo", function () {
    calcTotal()
})

$("body").on("blur", ".qtd", function () {
    let quantidade = $(this).val()
    $valorUnit = $(this).closest('td').next().find('input');
    $sub = $(this).closest('td').next().next().find('input');
    $sub.val(convertFloatToMoeda(convertMoedaToFloat($valorUnit.val()) * convertMoedaToFloat(quantidade)));
    calcTotal()
});

$("body").on("blur", ".valor_unitario", function () {
    let valorUnit = $(this).val()
    $qtd = $(this).closest('td').prev().find('input');
    $sub = $(this).closest('td').next().find('input');
    $sub.val(convertFloatToMoeda(convertMoedaToFloat($qtd.val()) * convertMoedaToFloat(valorUnit)));
    calcTotal()
});

function calcTotal(){
    var totalServico = 0
    var totalProduto = 0
    $(".table-servicos .sub_total").each(function () {
        totalServico += convertMoedaToFloat($(this).val())
    })

    $(".table-produtos .sub_total").each(function () {
        totalProduto += convertMoedaToFloat($(this).val())
    })

    setTimeout(() => {
        let desconto = convertMoedaToFloat($('#inp-desconto').val())
        let acrescimo = convertMoedaToFloat($('#inp-acrescimo').val())
        $('.total-servico').text("R$ " + convertFloatToMoeda(totalServico))
        $('.total-produto').text("R$ " + convertFloatToMoeda(totalProduto))
        $('#inp-total').val(convertFloatToMoeda(totalServico + totalProduto + acrescimo - desconto))
    }, 100)
}

$('.btn-add-line-servico').on("click", function () {
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
    $clone.find("span").html("");
    $table.append($clone);

    setTimeout(function () {
        $("tbody select.select2").select2({
            language: "pt-BR",
            width: "100%",
            theme: "bootstrap4"
        });

        $(".servico_id").select2({
            minimumInputLength: 2,
            language: "pt-BR",
            placeholder: "Digite para buscar o seviço",
            width: "100%",
            ajax: {
                cache: true,
                url: path_url + "api/servicos",
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

                        o.text = v.nome + ' R$ ' + convertFloatToMoeda(v.valor);
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

$('.btn-add-line-produto').on("click", function () {
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
    $clone.find("span").html("");
    $table.append($clone);

    setTimeout(function () {
        $("tbody select.select2").select2({
            language: "pt-BR",
            width: "100%",
            theme: "bootstrap4"
        });

        $(".produto_id").select2({
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

                        o.text = v.nome + ' R$ ' + convertFloatToMoeda(v.valor_unitario);
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


