$('#btn-enviar').click(() => {
    console.clear()
    getChecked((id) => {
        $("#btn-consulta-cnpj span").removeClass("d-none");
        let empresa_id = $("#empresa_id").val();

        $.post(path_url + "api/cte-os/transmitir", {
            id: id,
            empresa_id: empresa_id,
        })
        .done((success) => {
            swal("Sucesso", "CTe emitida " + success, "success")
            .then(() => {
                window.open(path_url + 'cte-os/imprimir/' + id, "_blank")
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

function getChecked(call) {
    let id = null
    $('.checkbox').each(function (i, e) {
        if (e.checked) {
            id = e.value
        }
    })
    call(id)
}

$('#btn-xml-temp').click(function () {
    getChecked((id) => {
        let href = $(this).data('href')
        window.open(path_url + 'cte-os/xml-temp/' + id, "_blank")
    })
})

$('#btn-cancelar').click(() => {
    console.clear()
    getCheckedElement((el) => {
        let numero_cte = el.data('numero_cte')
        if (numero_cte == "") {
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

$('#btn-cancelar-send').click(() => {
    getChecked((id) => {
        if (id) {
            let empresa_id = $("#empresa_id").val();
            let motivo = $('#inp-motivo-cancela').val()
            if (motivo.length >= 15) {
                $.post(path_url + "api/cte-os/cancelar", {
                    id: id,
                    empresa_id: empresa_id,
                    motivo: motivo
                })
                .done((success) => {
                    let infEvento = success.infEvento
                    swal("Sucesso", "[" + infEvento.cStat + "] " + infEvento.xMotivo, "success")
                    .then(() => {
                        window.open(path_url + 'cte-os/imprimir-cancela/' + id, "_blank")
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


$('#btn-corrigir').click(() => {
    console.clear()
    getCheckedElement((el) => {
        let numero_cte = el.data('numero_cte')
        if (numero_cte == "") {
            $('.numero_cte').text(numero_cte)
            $('#modal-corrigir').modal('show')
        }
    })
})

$('#btn-corrige-send').click(() => {
    getChecked((id) => {
        if (id) {
            let empresa_id = $("#empresa_id").val();
            let motivo = $('#inp-motivo-corrige').val()
            let campo = $('#inp-campo').val()
            let grupo = $('#inp-grupo').val()
            if (motivo.length >= 15) {
                $.post(path_url + "api/cte-os/corrigir", {
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
                        window.open(path_url + 'cte-os/imprimir-cce/' + id, "_blank")
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

$('#btn-imprimir-cce').click(function () {
    getChecked((id) => {
        window.open(path_url + 'cte-os/imprimir-cce/' + id, "_blank")
    })
})

$('#btn-imprimir-cancela').click(function () {
    getChecked((id) => {
        window.open(path_url + 'cte-os/imprimir-cancela/' + id, "_blank")
    })
})

$('#btn-baixar-xml').click(function () {
	getChecked((id) => {
		window.open(path_url + 'cte-os/baixar-xml/' + id, "_blank")
	})
})
