

$(function () {
    validateButtonSave()
    validLineSelect()
    
})

$('body').on('change', '#inp-local_id', function () {
    let local_id = $(this).val()
    $.get(path_url + "api/localizacao/find-number-doc", {local_id: local_id})
    .done((res) => {
        console.log(res)
        $('#inp-mdfe_numero').val(res.numero_mdfe)
    })
    .fail((err) => {
        console.error(err)
    })
});

function selectDiv2(ref) {
    $('.btn-outline-primary').removeClass('active')
    if (ref == 'gerais') {
        $('.div-gerais').removeClass('d-none')
        $('.div-transporte').addClass('d-none')
        $('.div-descarregamento').addClass('d-none')
        $('.btn-gerais').addClass('active')

    } else if (ref == 'transporte') {
        $('.div-gerais').addClass('d-none')
        $('.div-transporte').removeClass('d-none')
        $('.div-descarregamento').addClass('d-none')
        $('.btn-transporte').addClass('active')

    } else {
        $('.div-transporte').addClass('d-none')
        $('.div-gerais').addClass('d-none')
        $('.div-descarregamento').removeClass('d-none')
        $('.btn-descarregamento').addClass('active')
    }
}

$("body").on("blur", ".class-required", function () {
    validateButtonSave()
})

$("body").on("blur", "input", function () {
    if ($(this).prop('required')) {
        if ($(this).val() != "") {
            $(this).removeClass('is-invalid')
        }
    }
})

$("body").on("change", ".class-required", function () {
    validateButtonSave()
})

function validateButtonSave() {
    $('.alerts').html('')

    let tp_emit = $('#inp-tp_emit').val()
    let veiculo_tracao_id = $('#inp-veiculo_tracao_id').val()
    let municipio = $('.class-municipio').val()
    let descarregamento = $(".table-descarregamento tbody tr").length

    if (!tp_emit) {
        alertCreate("Selecione um tipo de emitente!")
    }
    if (!veiculo_tracao_id) {
        alertCreate("Informe o veículo de tração!")
    }
    if (municipio == '') {
        alertCreate("Selecione um município de carregamento!")
    }
    let condutor = true
    $(".class-condutor").each(function () {
        if ($(this).val() == '') {
            condutor = false
        }
    });
    if (!condutor) {
        alertCreate("Informe um condutor!")
    }
    if (descarregamento == 0) {
        alertCreate("Informe dados do descarregamento!")
    }


    setTimeout(() => {
        if ($('.alerts').html() == "") {
            $('.btn-salvarMdfe').removeAttr("disabled")
        } else {
            $('.btn-salvarMdfe').attr("disabled", true);
        }

    }, 100)

}


function alertCreate(msg) {
    var div = '<div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">'
    div += '<div class="text-white">' + msg + '</div>'
    div += '</div>'
    $('.alerts').append(div)
}

function validLineSelect() {

    $('.btn-action').attr('disabled', 'disabled')

    $('.checkbox').each(function (i, e) {
        if ($(this).is(':checked')) {
            $('#btn-xml-temp').attr('disabled', 1)
            $('#btn-enviar-xml').attr('disabled', 1)
            $('#btn-transmitir').attr('disabled', 1)
            $('#btn-imprimir').attr('disabled', 1)
            $('#btn-consultar').attr('disabled', 1)
            $('#btn-cancelar').attr('disabled', 1)

            let status = $(this).data('status')
            if (status == 'novo' || status == 'rejeitado') {
                $('#btn-enviar').removeAttr('disabled')
                $('#btn-xml-temp').removeAttr('disabled')
            } else if (status == 'aprovado') {
                $('#btn-imprimir').removeAttr('disabled')
                $('#btn-consultar').removeAttr('disabled')
                $('#btn-cancelar').removeAttr('disabled')
                $('#btn-enviar-email').removeAttr('disabled')
                $('#btn-baixar-xml').removeAttr('disabled')

            } else if (status == 'cancelado') {
                $('#btn-imprimir-cancela').removeAttr('disabled')
            }
        }
    })
}

$('#inp-chave_nfe').on('keyup', () => {
    if ($('#inp-chave_nfe').val().length > 0) {
        $('#inp-chave_cte').attr('disabled', true)
        $('#inp-seg_cod_cte').attr('disabled', true)

        $('#inp-chave_cte').val("")
        $('#inp-seg_cod_cte').val("")
    } else {
        $('#inp-chave_cte').attr('disabled', false)
        $('#inp-seg_cod_cte').attr('disabled', false)
    }
})

$('#inp-chave_cte').on('keyup', () => {
    if ($('#inp-chave_cte').val().length > 0) {
        $('#inp-chave_nfe').attr('disabled', true)
        $('#inp-seg_cod_nfe').attr('disabled', true)

        $('#inp-chave_nfe').val("")
        $('#inp-seg_cod_nfe').val("")
    } else {
        $('#inp-chave_nfe').attr('disabled', false)
        $('#inp-seg_cod_nfe').attr('disabled', false)
    }
})


$('.btn_info_desc').click(() => {
    let hasEmpty = false;
    $('.form-descarregamento').find('input, select').each(function () {
        if (($(this).val() == '') && !$(this).hasClass('ignore')) {
            $(this).addClass('is-invalid')
            hasEmpty = true
        }
    })
    if (hasEmpty) {
        swal("Atenção", "Informe corretamente os campos para continuar!", "warning")
        return;
    }
    let tp_und_transp = $("#inp-tp_unid_transp").val();
    let id_und_transp = $("#inp-id_unid_transp").val();
    let quantidade_rateio = $("#inp-quantidade_rateio").val();
    let quantidade_rateio_carga = $("#inp-quantidade_rateio_carga").val();
    let chave_nfe = $("#inp-chave_nfe").val();
    let chave_cte = $("#inp-chave_cte").val();
    let municipio_descarregamento = $("#inp-municipio_descarregamento").val();
    let lacres_transporte = [];
    let lacres_unidade = [];

    $(".numero_transporte").each(function () {
        lacres_transporte.push($(this).val())

    });
    $(".numero_carga").each(function () {
        lacres_unidade.push($(this).val())
    });

    if (tp_und_transp || id_und_transp || quantidade_rateio || quantidade_rateio_carga || chave_nfe ||
        chave_cte || municipio_descarregamento || lacres_transporte || lacres_unidade) {
        let dataRequest = {
            tp_und_transp: tp_und_transp,
            id_und_transp: id_und_transp,
            quantidade_rateio: quantidade_rateio,
            quantidade_rateio_carga: quantidade_rateio_carga,
            chave_nfe: chave_nfe,
            chave_cte: chave_cte,
            municipio_descarregamento: municipio_descarregamento,
            lacres_transporte: lacres_transporte,
            lacres_unidade: lacres_unidade
        }

        $.get(path_url + "api/mdfe/linhaInfoDescarregamento", dataRequest)
            .done((e) => {
                $('.table-descarregamento tbody').append(e)
                // calcTotal()
            })
            .fail((e) => {
                console.log(e)
            })

        setTimeout(() => {
            limparInput()
            limparLinhas()
            validateButtonSave()
        }, 1000)

    } else {
        swal("Atenção", "Informe corretamente os campos para continuar!", "warning")
    }
})

$(".table-descarregamento").on('click', '.btn-delete-row', function () {
    $(this).closest('tr').remove();
    swal("Sucesso", "Item removido!", "success")
    calcTotal()
});

function limparInput() {
    $("#inp-tp_unid_transp").val('').change();
    $("#inp-id_unid_transp").val('');
    $("#inp-quantidade_rateio").val('');
    $("#inp-quantidade_rateio_carga").val('');
    $("#inp-chave_nfe").val('');
    $("#inp-chave_cte").val('');
    $("#inp-seg_cod_nfe").val('');
    $("#inp-seg_cod_cte").val('');
    $("#inp-municipio_descarregamento").val('').change();
    $("#inp-id_unidade_carga").val('');
    $(".numero_carga").val('');
    $(".numero_transporte").val('');
}

function limparLinhas() {
    var $tr = $('.table-lacres').find(".dynamic-form").first();
    var $trc = $('.table-lacres-carga').find(".dynamic-form").first();

    $('.table-lacres tbody').html('')
    $('.table-lacres-carga tbody').html('')

    var $clone = $tr.clone();
    var $clonec = $trc.clone();

    $clone.show();
    $clonec.show();

    $clone.find("input,select").val("");
    $clonec.find("input,select").val("");

    $('.table-lacres').append($clone);
    $('.table-lacres-carga').append($clonec);

    $('.form-descarregamento').find('input, select').each(function () {
        $(this).removeClass('is-invalid')
    })
}




function importarNfe() {
    let ids = []
    $('#nfe-list tr').each(function () {
        if ($(this).find('input').is(':checked')) {
            let id = $(this).find('input').val()
            ids.push(id)
        }
    })
    if (ids.length > 0) {
        location.href = path + 'mdfe/create-by-vendas/' + ids
    } else {
        swal("Alerta", "Selecione ao menos um documento!", "warning")
    }
}

// $('#btn-importar_nfe').click(() => {
//     filtro()
// })

function filtro() {
    let empresa_id = $('#empresa_id').val()
    let start_date = $('#inp-start_date').val()
    let end_date = $('#inp-end_date').val()
    $.get(path_url + "api/mdfe/vendas-aprovadas", {
        empresa_id: empresa_id
        , start_date: start_date
        , end_date: end_date
    })
        .done((e) => {
            $('.tbl-vendas tbody').html(e)

        })
        .fail((e) => {
            console.log(e)
        })
}

$('body').on('click', '.btn-filtro', function () {
    filtro()
})
$('body').on('click', '#btn-importar', function () {
    console.clear()
    let id = []
    $('table .checkbox:checked').each(function (e, v) {
        id.push(v.value)
        location.href = path_url + "mdfe/create-by-vendas/" + id
    })
})