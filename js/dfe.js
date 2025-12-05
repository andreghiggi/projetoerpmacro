
$(function () {
    buscarDocumentos()
});

function buscarDocumentos() {
    let empresa_id = $('#empresa_id').val()
    $.post(path_url + 'api/manifesto/novos-documentos', { empresa_id: empresa_id })
        .done((success) => {
            if (success.length > 0) {
                montaTabela(success, (html) => {
                    $('table tbody').html(html)
                    $('#table').css('display', 'block')
                })
                swal("Sucesso", "Foram encontrados " + success.length + " novos registros!", "success")
            } else {
                swal("Sucesso", "A requisição obteve sucesso, porém sem novos registros!!", "success")
                $('#sem-resultado').css('display', 'block')
            }
        }).fail((err) => {
            console.log(err)
            swal("Erro", err.responseJSON.message, "error")
        })
}

function montaTabela(array, call) {
    let html = '';
    array.map(v => {
        html += '<tr>';
        html += '<td>'
            + v.nome[0] + '</td>'
        html += '<td>'
            + v.documento[0] + '</td>'
        html += '<td>'
            + v.valor[0].replace('.', ',') + '</td>'
        html += '<td>'
            + v.chave[0] + '</td>'
        html += '</tr>';
    })
    call(html)
}