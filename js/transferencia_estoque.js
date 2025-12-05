$(function(){
    $("tbody #inp-produto_id").select2({
        minimumInputLength: 2,
        language: "pt-BR",
        placeholder: "Digite para buscar o produto",
        width: "100%",
        theme: "bootstrap4",
        ajax: {
            cache: true,
            url: path_url + "api/produtos/com-estoque",
            dataType: "json",
            data: function (params) {
                let empresa_id = $('#empresa_id').val()
                let local_saida_id = $('#inp-local_saida_id').val()
                let local_entrada_id = $('#inp-local_entrada_id').val()
                console.clear();
                if(!local_saida_id){
                    swal(
                        "Atenção",
                        "Informe o local de saída",
                        "warning"
                        );
                    return;
                }
                if(!local_entrada_id){
                    swal(
                        "Atenção",
                        "Informe o local de entrada",
                        "warning"
                        );
                    return;
                }

                var query = {
                    pesquisa: params.term,
                    empresa_id: empresa_id,
                    local_saida_id: local_saida_id,
                    local_entrada_id: local_entrada_id,
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

                    console.log(v)

                    o.text = v.nome;

                    if(v.estoque){
                        o.text += ' | estoque: ' + parseFloat(v.estoque.quantidade).toFixed(2);
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
})

$('.btn-add-tr-prod').on("click", function () {
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
                url: path_url + "api/produtos/com-estoque",
                dataType: "json",
                data: function (params) {
                    let empresa_id = $('#empresa_id').val()
                    let local_saida_id = $('#inp-local_saida_id').val()
                    let local_entrada_id = $('#inp-local_entrada_id').val()
                    console.clear();
                    if(!local_saida_id){
                        swal(
                            "Atenção",
                            "Informe o local de saída",
                            "warning"
                            );
                        return;
                    }
                    if(!local_entrada_id){
                        swal(
                            "Atenção",
                            "Informe o local de entrada",
                            "warning"
                            );
                        return;
                    }

                    var query = {
                        pesquisa: params.term,
                        empresa_id: empresa_id,
                        local_saida_id: local_saida_id,
                        local_entrada_id: local_entrada_id,
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

                        console.log(v)

                        o.text = v.nome;

                        if(v.estoque){
                            o.text += ' | estoque: ' + parseFloat(v.estoque.quantidade).toFixed(2);
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