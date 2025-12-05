var EDIT = 0
$(function() {
    changeDericadoPetroleo()
    changeCardapio()
    changeDelivery()
    changeVariavel()
    changeEcommerce()
    changeCombo()
    changeMeradoLivre()
    changeNuvemShop()
    changeWoocommerce()
    changeIfood()
    changeVendiZap()
    if($('#edit').val() == 1){
        EDIT = 1
    }
    if($('.table-variacao tbody tr').length == 0){
        $('#inp-variacao_modelo_id').val('').change()
        $('#inp-sub_variacao_modelo_id').val('').change()
    }

    setTimeout(() => {
        if($('#inp-padrao_id').val()){
            $('#inp-padrao_id').change()
        }

        $('.select2-group').css('width', '80%')
    }, 1100)
})

$('#btn-store').click(() => {

})

$('.btn-store-marca').click(() => {
    let item = {
        nome: $('#inp-nome_marca').val(),
        empresa_id: $('#empresa_id').val()
    }

    $.post(path_url + "api/produtos/marca-store", item)
    .done((result) => {
        var newOption = new Option(result.nome, result.id, 1, false);
        $('#marca_id').append(newOption);
        $('#marca_id').val(result.id).change();
        $('#inp-nome_marca').val('')
        $('#modal_marca').modal('hide')
        swal("Sucesso", "Marca cadastrada!", "success")
    })
    .fail((err) => {
        console.log(err)
        swal("Erro", "Erro ao salvar marca", "error")
    })
})

$('.btn-store-categoria').click(() => {
    let item = {
        nome: $('#inp-nome_categoria').val(),
        empresa_id: $('#empresa_id').val()
    }

    $.post(path_url + "api/produtos/categoria-store", item)
    .done((result) => {
        var newOption = new Option(result.nome, result.id, 1, false);
        $('#categoria_id').append(newOption);
        $('#categoria_id').val(result.id).change();
        $('#inp-nome_categoria').val('')
        $('#modal_categoria_produto').modal('hide')
        swal("Sucesso", "Categoria cadastrada!", "success")
    })
    .fail((err) => {
        console.log(err)
        swal("Erro", "Erro ao salvar categoria", "error")
    })
})

$(document).on("click", ".gerar-codigo", function () {
    $.get(path_url + "produtos-gerar-codigo-ean")
    .done((res) => {
        $(this).prev().val(res)
    })
    .fail((err) => {
        swal("Erro", "Erro ao buscar código", "error")
    })

});

function gerarCode(inp) {
    $.get(path_url + "produtos-gerar-codigo-ean")
    .done((res) => {
        if(inp == 1){
            $('#codigo_barras').val(res)
        }else{
            $('#codigo_barras'+inp).val(res)
        }
    })
    .fail((err) => {
        swal("Erro", "Erro ao buscar código", "error")
    })
}

$('.btn-action').click(() => {
    addClassRequired()
})

function addClassRequired() {
    let isInalid = false
    $('.tab-fiscal').trigger('click')
    let campos = ""
    $("body #form-produto").find('input, select').each(function () {
        if ($(this).prop('required')) {

            if ($(this).val() == "" || $(this).val() == null) {
                $(this).addClass('is-invalid')
                isInalid = true
                if($(this).prev()[0].textContent){
                    campos += $(this).prev()[0].textContent + ", "
                }
            } else {
                $(this).removeClass('is-invalid')
            }
        } else {
            $(this).removeClass('is-invalid')
        }
    })

    setTimeout(() => {
        if(isInalid){
            audioError()
            campos = campos.substring(0, campos.length-2)
            toastr.error('Campos obrigatórios não preenchidos: ' + campos);
        }else{
            $body.addClass("loading");
        }
    }, 50)
}

$("#inp-sub_categoria_id").select2({
    minimumInputLength: 2,
    language: "pt-BR",
    placeholder: "Digite para buscar a subcategoria",
    width: "100%",
    ajax: {
        cache: true,
        url: path_url + "api/subcategorias",
        dataType: "json",
        data: function (params) {
            console.clear();
            var query = {
                pesquisa: params.term,
                categoria_id: $('#categoria_id').val()
            };
            return query;
        },
        processResults: function (response) {
            var results = [];

            $.each(response, function (i, v) {
                if(!$('#categoria_id').val()){
                    swal("Alerta", "Selecione a categoria primeiro!", "warning")
                    return
                }
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

$(document).on("change", "#inp-padrao_id", function() {
    let padrao = $(this).val()
    if (padrao) {
        $.get(path_url + "api/produtos/padrao", {
            padrao: padrao
        })
        .done((result) => {

            if(EDIT == 0){
                if(result._ncm){
                    var newOption = new Option(result._ncm.descricao, result._ncm.codigo, 1, 1);
                    $('#inp-ncm').append(newOption);
                }

                // $('#inp-ncm').val(result.ncm)
                $('#inp-cest').val(result.cest)
                $('#inp-perc_icms').val(result.perc_icms)
                $('#inp-perc_pis').val(result.perc_pis)
                $('#inp-perc_cofins').val(result.perc_cofins)
                $('#inp-perc_ipi').val(result.perc_ipi)
                $('#inp-cst_csosn').val(result.cst_csosn).change()
                $('#inp-cst_pis').val(result.cst_pis).change()
                $('#inp-cst_cofins').val(result.cst_cofins).change()
                $('#inp-cst_ipi').val(result.cst_ipi).change()
                $('#inp-cEnq').val(result.cEnq).change()
                $('#inp-cfop_estadual').val(result.cfop_estadual)
                $('#inp-cfop_outro_estado').val(result.cfop_outro_estado)
                $('#inp-codigo_beneficio_fiscal').val(result.codigo_beneficio_fiscal)

                $('#inp-cfop_entrada_estadual').val(result.cfop_entrada_estadual)
                $('#inp-cfop_entrada_outro_estado').val(result.cfop_entrada_outro_estado)
                $('#inp-modBCST').val(result.modBCST).change()
                $('#inp-pICMSST').val(result.pICMSST)
                $('#inp-pMVAST').val(result.pMVAST)
                $('#inp-redBCST').val(result.redBCST)

                $('#inp-cst_ibscbs').val(result.cst_ibscbs).change()
                $('#inp-cclass_trib').val(result.cclass_trib)
                $('#inp-perc_ibs_uf').val(result.perc_ibs_uf)
                $('#inp-perc_ibs_mun').val(result.perc_ibs_mun)
                $('#inp-perc_cbs').val(result.perc_cbs)
                $('#inp-perc_dif').val(result.perc_dif)
                

            }else{
                EDIT = 0
            }
        })
        .fail((err) => {
            console.log(err)
        })
    }
});

function changeDericadoPetroleo() {
    let check = $('#inp-petroleo').is(':checked')
    if (check) {
        $('.div-petroleo').removeClass('d-none')
    } else {
        $('.div-petroleo').addClass('d-none')
    }
}

$('#inp-petroleo').change(() => {
    changeDericadoPetroleo()
})

function changeCardapio() {
    let check = $('#inp-cardapio').is(':checked')
    if (check) {
        $('.div-cardapio').removeClass('d-none')
    } else {
        $('.div-cardapio').addClass('d-none')
    }
}

$('#inp-cardapio').change(() => {
    changeCardapio()
})

function changeNuvemShop() {
    let check = $('#inp-nuvemshop').is(':checked')
    if (check) {
        $('.div-nuvemshop').removeClass('d-none')
        $('.inp-nuvemshop').attr('required', 1)
    } else {
        $('.div-nuvemshop').addClass('d-none')
        $('.inp-nuvemshop').removeAttr('required')
    }
}

$('#inp-nuvemshop').change(() => {
    changeNuvemShop()
})

function changeDelivery() {
    let check = $('#inp-delivery').is(':checked')
    if (check) {
        $('.div-delivery').removeClass('d-none')
    } else {
        $('.div-delivery').addClass('d-none')
    }
}

$('#inp-delivery').change(() => {
    changeDelivery()
})

function changeEcommerce() {
    let check = $('#inp-ecommerce').is(':checked')
    if (check) {
        $('.div-ecommerce').removeClass('d-none')
    } else {
        $('.div-ecommerce').addClass('d-none')
    }
}

$('#inp-ecommerce').change(() => {
    changeEcommerce()
})

function changeWoocommerce() {
    let check = $('#inp-woocommerce').is(':checked')
    if (check) {
        $('.div-woocommerce').removeClass('d-none')
        // $('.inp-woocommerce').attr('required', 1)
    } else {
        $('.div-woocommerce').addClass('d-none')
        // $('.inp-woocommerce').removeAttr('required')
    }
}

$('#inp-woocommerce').change(() => {
    changeWoocommerce()
})

function changeIfood() {
    let check = $('#inp-ifood').is(':checked')
    if (check) {
        $('.div-ifood').removeClass('d-none')
    } else {
        $('.div-ifood').addClass('d-none')
    }
}

$('#inp-ifood').change(() => {
    changeIfood()
})

function changeVendiZap() {
    let check = $('#inp-vendizap').is(':checked')
    if (check) {
        $('.div-vendizap').removeClass('d-none')
    } else {
        $('.div-vendizap').addClass('d-none')
    }
}

$('#inp-vendizap').change(() => {
    changeVendiZap()
})

function changeMeradoLivre() {
    let check = $('#inp-mercadolivre').is(':checked')
    if (check) {
        $('.div-mercadolivre').removeClass('d-none')
        $('.input-ml').attr('required', 1)
        getTiposPublicacao()
    } else {
        $('.div-mercadolivre').addClass('d-none')
        $('.input-ml').removeAttr('required')
    }
}

function getTiposPublicacao(){

    $.get(path_url + "api/mercadolivre/get-tipo-publicacao", {
        empresa_id: $('#empresa_id').val()
    })
    .done((res) => {
        if(res.code == 'unauthorized'){
            return;
        }
        $('#inp-mercado_livre_tipo_publicacao').html('')
        var newOption = new Option('Selecione', '', false, false);
        $('#inp-mercado_livre_tipo_publicacao').append(newOption);
        res.map((x) => {
            var newOption = new Option(x.name, x.id, false, false);
            $('#inp-mercado_livre_tipo_publicacao').append(newOption);
        })

        setTimeout(() => {
            $('#inp-mercado_livre_tipo_publicacao').val($('#tipo_publicacao_hidden').val()).change()
        }, 100)
    })
    .fail((err) => {
        console.log(err)
        swal("Erro", "Algo deu errado", "error")
    })
}
$('#inp-mercadolivre').change(() => {
    changeMeradoLivre()
})

$(document).ready(function() {
    $("form").bind("keypress", function(e) {
        if (e.keyCode == 13) {
            return false;
        }
    });
});

function changeVariavel() {
    let variavel = $('#inp-variavel').val()
    if (variavel == 1) {
        $('.div-variavel').removeClass('d-none')
        $('#inp-valor_unitario').val('0')
        //$('#inp-valor_compra').val('0')
    } else {
        $('.div-variavel').addClass('d-none')
    }
}

$('#inp-variavel').change(() => {
    changeVariavel()
})

function changeCombo() {
    let variavel = $('#inp-combo').val()
    if (variavel == 1) {
        $('.div-combo').removeClass('d-none')
    } else {
        $('.div-combo').addClass('d-none')
    }
}

$('#inp-combo').change(() => {
    changeCombo()
})

// variacoes

$(document).on("change", "#inp-variacao_modelo_id", function () {

    let variacao_modelo_id = $(this).val()
    if(variacao_modelo_id){
        $.get(path_url + "api/variacoes/modelo", {
            variacao_modelo_id: variacao_modelo_id
        })
        .done((res) => {
            $('.table-variacao tbody').html(res)
            $('#inp-sub_variacao_modelo_id').val('').change()
        })
        .fail((err) => {
            console.log(err)
            swal("Erro", "Algo deu errado", "error")
        })
    }
})
$(document).on("change", "#inp-sub_variacao_modelo_id", function () {

    let sub_variacao_modelo_id = $(this).val()
    let variacao_modelo_id = $('#inp-variacao_modelo_id').val()
    if(!sub_variacao_modelo_id) return;
    if(variacao_modelo_id){
        $.get(path_url + "api/variacoes/modelo-subvariacoes", {
            variacao_modelo_id: variacao_modelo_id,
            sub_variacao_modelo_id: sub_variacao_modelo_id
        })
        .done((res) => {
            $('.table-variacao tbody').html(res)
        })
        .fail((err) => {
            console.log(err)
            swal("Erro", "Algo deu errado", "error")
        })
    }else{
        swal("Erro", "Selecione a variação principal primeiro!", "error")
    }
})

$(document).on("blur", "#inp-valor_compra", function () {
    let valorCompra = convertMoedaToFloat($(this).val())
    $percLucro = $(this).closest('.col-produto').next().find('input');
    $valorUnitario = $(this).closest('.col-produto').next().next().find('input');
    
    if($percLucro.val()){
        let valor = valorCompra + ((valorCompra*$percLucro.val())/100)
        $valorUnitario.val(convertFloatToMoeda(valor))
    }
})

$(document).on("blur", "#inp-percentual_lucro", function () {
    let percLucro = $(this).val()
    $valorCompra = $(this).closest('.col-produto').prev().find('input');
    $valorUnitario = $(this).closest('.col-produto').next().find('input');
    if($valorCompra.val()){
        let vlCompra = convertMoedaToFloat($valorCompra.val())

        let valor = vlCompra + ((vlCompra*percLucro)/100)
        $valorUnitario.val(convertFloatToMoeda(valor))
    }
})

$(document).on("blur", "#inp-valor_unitario", function () {
    let valorUnitario = convertMoedaToFloat($(this).val())
    $percLucro = $(this).closest('.col-produto').prev().find('input');

    $valorCompra = $(this).closest('.col-produto').prev().prev().find('input');
    if($valorCompra.val() && valorUnitario){
        let vlCompra = convertMoedaToFloat($valorCompra.val())
        let dif = (valorUnitario - vlCompra)/vlCompra*100;
        $percLucro.val(dif.toFixed(2))
    }else{
        $percLucro.val('0')
    }
})

$(document).on("blur", ".valor-compra-combo", function () {
    let valorCompra = convertMoedaToFloat($(this).val())
    $subtotal = $(this).closest('td').next().find('input');
    $qtd = $(this).closest('td').prev().find('input');
    let qtd = $qtd.val()

    $subtotal.val(convertFloatToMoeda(valorCompra * qtd))
    calcValorCombo()
})

$(document).on("blur", ".qtd-combo", function () {
    let qtd = $(this).val()
    $subtotal = $(this).closest('td').next().next().find('input');
    $valorCompra = $(this).closest('td').next().find('input');
    let valorCompra = convertMoedaToFloat($valorCompra.val())

    $subtotal.val(convertFloatToMoeda(valorCompra * qtd))
    calcValorCombo()
})

$(document).on("blur", "#inp-margem_combo", function () {
    calcValorCombo()
})

function calcValorCombo(){
    let totalCombo = 0
    let margem = $('#inp-margem_combo').val()
    $(".subtotal-combo").each(function () {
        totalCombo += convertMoedaToFloat($(this).val())
    })
    $('#inp-valor_compra').val(convertFloatToMoeda(totalCombo))
    totalCombo += (totalCombo*(parseFloat(margem)/100))
    setTimeout(() => {
        $('#inp-valor_combo').val(convertFloatToMoeda(totalCombo))
        $('#inp-valor_unitario').val(convertFloatToMoeda(totalCombo))
    }, 100)

}
$(document).on("blur", "#inp-valor_unitario", function () {
    $('#inp-nuvem_shop_valor').val($(this).val())
})
$(document).on("change", "#inp-produto_combo_id", function () {
    let produto_id = $(this).val()
    if(produto_id){
        $.get(path_url + "api/combos/modelo", {
            produto_id: produto_id
        })
        .done((res) => {
            $("#inp-produto_combo_id").val('').change()
            $('.table-combo tbody').append(res)
            setTimeout(() => {
                calcValorCombo()
            }, 10)
        })
        .fail((err) => {
            console.log(err)
            swal("Erro", "Algo deu errado", "error")
        })
    }
})

$(document).delegate(".btn-remove-tr-variacao", "click", function (e) {
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
                swal("Atenção", "Você deve ter ao menos um item na lista", "warning");
            }
        }
    });
});

$(document).delegate(".btn-remove-tr-combo", "click", function (e) {
    e.preventDefault();
    swal({
        title: "Você esta certo?",
        text: "Deseja remover esse item mesmo?",
        icon: "warning",
        buttons: true
    }).then(willDelete => {
        if (willDelete) {

            $(this).closest("tr").remove();
            calcValorCombo()
        }
    });
});


$('.btn-add-tr-variacao').on("click", function () {
    console.clear()
    var $table = $(this)
    .closest(".row")
    .prev()
    .find(".table-variacao");

    console.log($table)

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
    $clone.find("input,select").removeAttr('readonly');
    $table.append($clone);
    setTimeout(function () {
        $("tbody select.select2").select2({
            language: "pt-BR",
            width: "100%",
            theme: "bootstrap4"
        });
    }, 100);

})
