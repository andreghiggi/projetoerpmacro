var CLIENTES = []
var REMETENTE = null;
var DESTINATARIO = null;
var CHAVESNFE = []
var xmlValido = false;

$(function () {
    validLineSelect()
    validateButtonSave()
    validaTomador()

    if($('#inp-remetente_id').val()){
        $('#inp-remetente_id').change()
    }

    if($('#inp-destinatario_id').val()){
        $('#inp-destinatario_id').change()
    }
})

$('#inp-tomador').change(function () {
    validaTomador()
})

function validaTomador(){
    let tomador = $('#inp-tomador').val()
    if(tomador == 4){
        $('.dados-tomador').removeClass('d-none')
    }else{
        $('.dados-tomador').addClass('d-none')
    }
}

$('#inp-xml').change(function () {
    $('#form-import').submit();
});

$('#inp-tipo-remetente').click(function () {
    let remetente_id = $('#inp-remetente_id').val()
    getClient(remetente_id)
})

$('#inp-tipo-destinatario').click(function () {
    let destinatario_id = $('#inp-destinatario_id').val()
    getClient(destinatario_id)
})

function getClient(id) {
    if(id){
        $.get(path_url + "api/clientes/find/" + id)
        .done((res) => {
            $('#inp-logradouro_tomador').val(res.rua)
            $('#inp-numero_tomador').val(res.numero)
            $('#inp-cep_tomador').val(res.cep)
            $('#inp-bairro_tomador').val(res.bairro)
            $('#inp-municipio_tomador').val(res.cidade_id).change()
            selectMunicipios(res)
        })
        .fail((err) => {
            console.error(err)
        })
    }
}

function selectMunicipios(res) {

    $('#inp-municipio_envio').val(res.cidade_id).change()
    $('#inp-municipio_inicio').val(res.cidade_id).change()

    let destinatario_id = $('#inp-destinatario_id').val()
    if(destinatario_id){

        $.get(path_url + "api/clientes/find/" + destinatario_id)
        .done((res) => {
            $('#inp-municipio_fim').val(res.cidade_id).change()

        })
        .fail((err) => {
            console.error(err)
        })
    }
}

function selectDiv(ref) {
    $('.btn-outline-primary').removeClass('link-active')

    if (ref == 'nfe') {
        $('.div-nfe').removeClass('d-none')
        $('.div-outros').addClass('d-none')
        $('.btn-nfe').addClass('link-active')
    } else {
        $('.div-nfe').addClass('d-none')
        $('.div-outros').removeClass('d-none')
        $('.btn-outros').addClass('link-active')
    }
}

function validLineSelect() {
    $('.btn-action').attr('disabled', 'disabled')

    $('.checkbox').each(function (i, e) {
        if ($(this).is(':checked')) {
            let status = $(this).data('status')
            if (status == 'novo' || status == 'rejeitado') {
                $('#btn-enviar').removeAttr('disabled')
                $('#btn-dacte-temp').removeAttr('disabled')
                $('#btn-xml-temp').removeAttr('disabled')
            } else if (status == 'aprovado') {
                $('#btn-imprimir').removeAttr('disabled')
                $('#btn-imprimir-cce').removeAttr('disabled')
                $('#btn-consultar').removeAttr('disabled')
                $('#btn-cancelar').removeAttr('disabled')
                $('#btn-corrigir').removeAttr('disabled')
                $('#btn-enviar-email').removeAttr('disabled')
                $('#btn-baixar-xml').removeAttr('disabled')
            } else if (status == 'cancelado') {
                $('#btn-imprimir-cancela').removeAttr('disabled')
            }
        }
    })
}

$('#btn-baixar-xml').click(function () {
    getChecked((id) => {
        window.open(path_url + 'cte/baixar-xml/' + id, "_blank")
    })
})


$('.checkbox').click(function () {
    $value = $(this).val()
    $('.checkbox').each(function (i, e) {
        if (e.value != $value) {
            e.checked = false
        }

        validLineSelect()
    })
})

$('#btn-xml-temp').click(function () {
    getChecked((id) => {
        let href = $(this).data('href')
        window.open(path_url + 'cte-xml-temp/' + id, "_blank")
    })
})

$('#btn-dacte-temp').click(function () {
    getChecked((id) => {
        let href = $(this).data('href')
        window.open(path_url + 'cte-dacte-temp/' + id, "_blank")
    })
})

function getChecked(call) {
    let id = null
    $('.checkbox').each(function (i, e) {
        if (e.checked) {
            id = e.value
        }
    })
    call(id)
}

$('#btn-enviar').click(() => {
    console.clear()
    getChecked((id) => {
        $("#btn-consulta-cnpj span").removeClass("d-none");
        let empresa_id = $("#empresa_id").val();

        $.post(path_url + "api/cte/transmitir", {
            id: id,
            empresa_id: empresa_id,
        })
        .done((success) => {
            swal("Sucesso", "CTe emitida " + success, "success")
            .then(() => {
                window.open(path_url + 'cte/imprimir/' + id, "_blank")
                setTimeout(() => {
                    location.reload()
                }, 100)
            })
        })
        .fail((err) => {
            console.log(err)
            if (err.status == 403) {
                console.log(err.responseJSON.protCTe.infProt)
                let infProt = err.responseJSON.protCTe.infProt
                swal("Algo deu errado", infProt.cStat + " - " + infProt.xMotivo, "error")
            } else {
                try {
                    swal("Algo deu errado", err.responseJSON, "error")
                } catch {
                    swal("Algo deu errado", err.responseText, "error")
                }
            }
        })
    })
})

$('#btn-imprimir').click(function () {
    getChecked((id) => {
        window.open(path_url + 'cte/imprimir/' + id, "_blank")
    })
})

$('#btn-consultar').click(() => {
    console.clear()
    getChecked((id) => {
        if (id) {
            let empresa_id = $("#empresa_id").val();

            $.post(path_url + "api/cte/consultar", {
                id: id,
                empresa_id: empresa_id,
            })
            .done((success) => {
                let infProt = success.protCTe.infProt
                swal("Sucesso", "[" + infProt.chCTe + "] " + infProt.xMotivo, "success")

            })
            .fail((err) => {
                console.log(err)
                swal("Algo deu errado", err.responseJSON, "error")

            })
        } else {
            swal("Alerta", "Selecione uma venda!", "warning")
        }
    })
})

$('#btn-corrigir').click(() => {
    console.clear()
    getCheckedElement((el) => {
        let numero_cte = el.data('numero_cte')
        if (numero_cte > 0) {
            $('.numero_cte').text(numero_cte)
            $('#modal-corrigir').modal('show')
        }
    })
})

$('#btn-cancelar').click(() => {
    console.clear()
    getCheckedElement((el) => {
        let numero_cte = el.data('numero_cte')
        if (numero_cte > 0) {
            $('.numero_cte').text(numero_cte)
            $('#modal-cancelar').modal('show')
        }
    })
})

function getCheckedElement(call) {
    $el = null
    $('.checkbox').each(function (i, e) {
        if (e.checked) {
            $el = $(this)
        }
    })
    call($el)
}

$('#btn-corrige-send').click(() => {
    getChecked((id) => {
        if (id) {
            let empresa_id = $("#empresa_id").val();
            let motivo = $('#inp-motivo-corrige').val()
            let campo = $('#inp-campo').val()
            let grupo = $('#inp-grupo').val()
            if (motivo.length >= 15) {
                $.post(path_url + "api/cte/corrigir", {
                    id: id,
                    empresa_id: empresa_id,
                    motivo: motivo,
                    campo: campo,
                    grupo: grupo,
                })
                .done((success) => {
                    let infEvento = success.infEvento
                    swal("Sucesso", "[" + infEvento.cStat + "] " + infEvento.xMotivo, "success")
                    .then(() => {
                        window.open(path_url + 'cte/imprimir-cce/' + id, "_blank")
                        $('#modal-corrigir').modal('hide')

                    })

                })
                .fail((err) => {
                    console.log(err)
                    try {
                        swal("Algo deu errado", err.responseJSON.infEvento.xMotivo, "error")
                    } catch {
                        swal("Algo deu errado", err.responseJSON, "error")
                    }
                })
            } else {
                swal("Alerta", "Informe no mínimo 15 caracteres", "warning")
            }
        } else {
            swal("Alerta", "Selecione uma cte!", "warning")
        }
    })
})

$('#btn-cancelar-send').click(() => {
    getChecked((id) => {
        if (id) {
            let empresa_id = $("#empresa_id").val();
            let motivo = $('#inp-motivo-cancela').val()
            if (motivo.length >= 15) {
                $.post(path_url + "api/cte/cancelar", {
                    id: id,
                    empresa_id: empresa_id,
                    motivo: motivo
                })
                .done((success) => {
                    let infEvento = success.infEvento
                    swal("Sucesso", "[" + infEvento.cStat + "] " + infEvento.xMotivo, "success")
                    .then(() => {
                        window.open(path_url + 'cte/imprimir-cancela/' + id, "_blank")
                        setTimeout(() => {
                            location.reload()
                        }, 100)
                    })

                })
                .fail((err) => {
                    console.log(err)
                    try {
                        swal("Algo deu errado", err.responseJSON.infEvento.xMotivo, "error")
                    } catch {
                        swal("Algo deu errado", err.responseJSON, "error")
                    }
                })
            } else {
                swal("Alerta", "Informe no mínimo 15 caracteres", "warning")
            }
        } else {
            swal("Alerta", "Selecione uma cte!", "warning")
        }
    })
})

$('#btn-imprimir-cce').click(function () {
    getChecked((id) => {
        window.open(path_url + 'cte/imprimir-cce/' + id, "_blank")
    })
})

$('#btn-imprimir-cancela').click(function () {
    getChecked((id) => {
        window.open(path_url + 'nfe/imprimir-cancela/' + id, "_blank")
    })
})

$('#btn-inutilizar').click(() => {
    console.clear()

    $('#modal-inutilizar').modal('show')
})

$('#btn-inutiliza-send').click(() => {
    let empresa_id = $("#empresa_id").val();
    let motivo = $('#inp-motivo-inutiliza').val()
    let numero_inicial = $('#inp-numero_inicial').val()
    let numero_final = $('#inp-numero_final').val()
    if (motivo.length >= 15) {
        $.post(path_url + "api/cte/inutiliza", {
            empresa_id: empresa_id,
            motivo: motivo,
            numero_inicial: numero_inicial,
            numero_final: numero_final
        })
        .done((success) => {
            let infInut = success.infInut
            if (infInut.cStat == "102") {
                $('#modal-inutilizar').modal('hide')
                swal("Sucesso", "[" + infInut.nProt + "] " + infInut.xMotivo, "success")
            } else {
                swal("Erro", "[" + infInut.cStat + "] " + infInut.xMotivo, "error")
            }

        })
        .fail((err) => {
            console.log(err)

            swal("Algo deu errado", err.responseJSON, "error")

        })
    } else {
        swal("Alerta", "Informe no mínimo 15 caracteres", "warning")
    }
})

$('body').on('change', '#inp-local_id', function () {
    let local_id = $(this).val()
    $.get(path_url + "api/localizacao/find-number-doc", {local_id: local_id})
    .done((res) => {
        console.log(res)
        $('#inp-numero').val(res.numero_cte)
    })
    .fail((err) => {
        console.error(err)
    })
});

$('#inp-remetente_id').change(() => {
    $('.div-remetente').addClass('d-none')
    let remetente_id = $('#inp-remetente_id').val()
    if (remetente_id != '') {
        $('.div-remetente').removeClass('d-none')
    } else {
        $('.div-remetente').addClass('d-none')
    }
    if (remetente_id != '') {
        $.get(path_url + "api/clientes/find/" + remetente_id)
        .done((res) => {
            $('#razao_social_remetente').html(res.razao_social)
            $('#cnpj_remetente').html(res.cpf_cnpj)
            $('#cidade_remetente').html(res.cidade.nome + ' (' + res.cidade.uf + ')')
        })
        .fail((err) => {
            console.error(err)
        })
    }
})

$('#inp-destinatario_id').change(() => {
    $('.div-destinatario').addClass('d-none')
    let destinatario_id = $('#inp-destinatario_id').val()
    if (destinatario_id != '') {
        $('.div-destinatario').removeClass('d-none')
    } else {
        $('.div-destinatario').addClass('d-none')
    }
    if (destinatario_id != '') {
        $.get(path_url + "api/clientes/find/" + destinatario_id)
        .done((res) => {
            $('#razao_social_destinatario').html(res.razao_social)
            $('#cnpj_destinatario').html(res.cpf_cnpj)
            $('#cidade_destinatario').html(res.cidade.nome + ' (' + res.cidade.uf + ')')
        })
        .fail((err) => {
            console.error(err)
        })
    }
})

$('#inp-expedidor_id').change(() => {
    $('.div-expedidor').addClass('d-none')
    let expedidor_id = $('#inp-expedidor_id').val()
    if (expedidor_id != '') {
        $('.div-expedidor').removeClass('d-none')
    } else {
        $('.div-expedidor').addClass('d-none')
    }
    if (expedidor_id != '') {
        $.get(path_url + "api/clientes/find/" + expedidor_id)
        .done((res) => {
            $('#razao_social_expedidor').html(res.razao_social)
            $('#cnpj_expedidor').html(res.cpf_cnpj)
            $('#cidade_expedidor').html(res.cidade.nome + ' (' + res.cidade.uf + ')')
        })
        .fail((err) => {
            console.error(err)
        })
    }
})

$('#inp-recebedor_id').change(() => {
    $('.div-recebedor').addClass('d-none')
    let recebedor_id = $('#inp-recebedor_id').val()
    if (recebedor_id != '') {
        $('.div-recebedor').removeClass('d-none')
    } else {
        $('.div-recebedor').addClass('d-none')
    }
    if (recebedor_id != '') {
        $.get(path_url + "api/clientes/find/" + recebedor_id)
        .done((res) => {
            $('#razao_social_recebedor').html(res.razao_social)
            $('#cnpj_recebedor').html(res.cpf_cnpj)
            $('#cidade_recebedor').html(res.cidade.nome + ' (' + res.cidade.uf + ')')
        })
        .fail((err) => {
            console.error(err)
        })
    }
})

// $('.class-required').blur(() => {
//     validateButtonSave()
// })

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

    let local_id = $('#inp-local_id').val()
    let natureza_id = $('#inp-natureza_id').val()
    let perc_icms = $('#inp-perc_icms').val()
    let rementente_id = $('#inp-remetente_id').val()
    let destinatario_id = $('#inp-destinatario_id').val()
    let chave_nfe = $("#chave_nfe").val()

    // let count_itens = $(".table-itens tbody tr").length

    if (!local_id) {
        alertCreate("Selecione o local!")
    }
    if (!natureza_id) {
        alertCreate("Selecione uma natureza de operação!")
    }
    if (!perc_icms) {
        alertCreate("Informe o percentual de ICMS!")
    }
    if (!rementente_id) {
        alertCreate("Selecione um remetente!")
    }
    if (!destinatario_id) {
        alertCreate("Selecione um destinatário!")
    }
    let outros = true
    $(".class-outros").each(function () {
        if ($(this).val() == '') {
            outros = false
        }
    });
    if (!chave_nfe && !outros) {
        alertCreate("Adicione referência de documento para CTe!")
    }

    let cargas = true
    $(".class-cargas").each(function () {
        if ($(this).val() == '') {
            cargas = false
        }
    });
    if (!cargas) {
        alertCreate("Adicione informações de carga para CTe!")
    }

    setTimeout(() => {
        if ($('.alerts').html() == "") {
            $('.btn-salvarCte').removeAttr("disabled")
        } else {
            $('.btn-salvarCte').attr("disabled", true);
        }

    }, 100)

}

$('.btn-salvarCte').click(() => {
    addClassRequired()
})

function alertCreate(msg) {
    var div = '<div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">'
    div += '<div class="text-white">' + msg + '</div>'
    div += '</div>'
    $('.alerts').append(div)
}

function addClassRequired() {
    $("body").find('input, select').each(function () {
        if ($(this).prop('required')) {
            if ($(this).val() == "") {
                $(this).addClass('is-invalid')
            } else {
                $(this).removeClass('is-invalid')
            }
        } else {
            $(this).removeClass('is-invalid')
        }
    })
}

// adicinar chave

let chave_import = $('#chave_import').val();
if (chave_import) {
    xmlValido = true;
    CHAVESNFE.push(chave_import)
    $('#chave_nfe').val(chave_import)
}

function addChaveCte() {
    console.clear()
    var $table = $(this)
    .closest(".row")
    .prev()
    .find(".table-dynamic-cte");
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
    var $tr = $table.find(".dynamic-form-cte").first();
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
}

$(document).delegate(".btn-remove-tr-cte", "click", function (e) {
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
                swal(
                    "Atenção",
                    "Você deve ter ao menos um item na lista",
                    "warning"
                    );
            }
        }
    });
});

// fim da chave 

