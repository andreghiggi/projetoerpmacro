$(".fornecedor_id").select2({
    minimumInputLength: 2,
    language: "pt-BR",
    placeholder: "Digite para buscar o fornecedor",
    ajax: {
        cache: true,
        url: path_url + "api/fornecedores/pesquisa",
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

$('.btn-add-line').on("click", function () {

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

        $(".fornecedor_id").select2({
            minimumInputLength: 2,
            language: "pt-BR",
            placeholder: "Digite para buscar o fornecedor",
            ajax: {
                cache: true,
                url: path_url + "api/fornecedores/pesquisa",
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
    }, 100); 
})

$(document).on('keyup', '.valor', function(){
    let total = 0
    $(".valor").each(function () {
        total += convertMoedaToFloat($(this).val());
    });
    setTimeout(() => {
        $('.total-despesa').text("R$ " + convertFloatToMoeda(total))        
    }, 10)
});