var IDNFE = null;

function transmitir(id) {
    console.clear()
    $("#btn-consulta-cnpj span").removeClass("d-none");
    let empresa_id = $("#empresa_id").val();

    $.post(path_url + "api/mdfe_painel/emitir", {
        id: id,
        empresa_id: empresa_id,
    })
        .done((success) => {
            swal("Sucesso", "MDFe emitida " + success, "success")
                .then(() => {
                    window.open(path_url + 'mdfe/imprimir/' + id, "_blank")
                    setTimeout(() => {
                        location.reload()
                    }, 100)
                })
        })
        .fail((err) => {
            console.log(err)
            if (err.status == 403) {
                swal("Algo deu errado", err.responseJSON, "error")
            } else {
                try {
                    swal("Algo deu errado", err.responseJSON, "error")
                } catch {
                    swal("Algo deu errado", err.responseText, "error")
                }
            }
        })
}

function consultar(id, numero) {

    if (id) {
        let empresa_id = $("#empresa_id").val();

        $.post(path_url + "api/mdfe_painel/consultar", {
            id: id,
            empresa_id: empresa_id,
        })
            .done((success) => {
                let infProt = success.protMDFe.infProt
                swal("Sucesso", "[" + infProt.chMDFe + "] " + infProt.xMotivo, "success")

            })
            .fail((err) => {
                console.log(err)
                swal("Algo deu errado", err.responseJSON, "error")

            })
    } else {
        swal("Alerta", "Selecione uma venda!", "warning")
    }
}

function cancelar(id, numero) {
    IDNFE = id
    $('.ref-numero').text(numero)
    $('#modal-cancelar').modal('show')
}


$('#btn-cancelar').click(() => {
    let empresa_id = $("#empresa_id").val();
    let motivo = $('#inp-motivo-cancela').val()
    if (motivo.length >= 15) {
        $.post(path_url + "api/mdfe/cancelar", {
            id: IDNFE,
            empresa_id: empresa_id,
            motivo: motivo
        })
            .done((success) => {
                let infEvento = success.infEvento
                swal("Sucesso", "[" + infEvento.cStat + "] " + infEvento.xMotivo, "success")
                    .then(() => {
                        // window.open(path_url + 'mdfe/imprimir-cancela/' + id, "_blank")
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
})
