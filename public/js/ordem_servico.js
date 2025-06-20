$(function () {
    setTimeout(() => {
        $("#inp-servico_id").change(() => {
            let servico_id = $("#inp-servico_id").val()
            if (servico_id) {
                $.get(path_url + "api/ordemServico/find/" + servico_id)
                .done((e) => {
                    $('#inp-quantidade').val('1,00')
                    $('#inp-nome').val(e.nome)
                    $('#inp-valor').val(convertFloatToMoeda(e.valor))
                })
                .fail((e) => {
                    console.log(e)
                })
            }
        })
    }, 100)
})


$(function () {
    setTimeout(() => {
        $("#inp-produto_id").change(() => {
            let produto_id = $("#inp-produto_id").val()
            if (produto_id) {
                $.get(path_url + "api/ordemServico/findProduto/" + produto_id)
                .done((e) => {
                    $('#inp-quantidade_produto').val('1,00')
                    $('#inp-nome_produto').val(e.nome)
                    $('#inp-valor_produto').val(convertFloatToMoeda(e.valor_unitario))
                })
                .fail((e) => {
                    console.log(e)
                })
            }
        })
    }, 100)
})

function imprimir(id){
    var disp_setting="toolbar=yes,location=no,";
    disp_setting+="directories=yes,menubar=yes,";
    disp_setting+="scrollbars=yes,width=850, height=600, left=100, top=25";

    var docprint=window.open(path_url+"ordem-servico/imprimir/"+id, "",disp_setting);

    docprint.focus();
}

$('.btn-add-servico').click(() => {
    let qtd = $("#inp-quantidade").val();
    let valor = $("#inp-valor").val();
    let servico_id = $("#inp-servico_id").val()
    let status = $("#inp-status").val()
    let nome = $("#inp-nome").val()
    if (qtd && valor && servico_id && status && nome) {
        let dataRequest = {
            qtd: qtd,
            valor: valor,
            servico_id: servico_id,
            status: status,
            nome: nome
        }
        $.get(path_url + "api/ordemServico/linhaServico", dataRequest)
        .done((e) => {
            $('.table-servico tbody').append(e)
                // calcTotal()
            })
        .fail((e) => {
            console.log(e)
        })
    } else {
        swal("Atenção", "Informe corretamente os campos para continuar!", "warning")
    }
})


$('.btn-add-produto').click(() => {
    let qtd = $("#inp-quantidade_produto").val();
    let valor = $("#inp-valor_produto").val();
    let servico_id = $("#inp-produto_id").val()
    if (qtd && valor && servico_id) {
        let dataRequest = {
            qtd: qtd,
            valor: valor,
            servico_id: servico_id,
        }
        $.get(path_url + "api/ordemServico/linhaProduto", dataRequest)
        .done((e) => {
            $('.table-produto tbody').append(e)
                // calcTotal()
            })
        .fail((e) => {
            console.log(e)
        })
    } else {
        swal("Atenção", "Informe corretamente os campos para continuar!", "warning")
    }
})