function modalData(id) {
    $('#dados_produto_unico').modal('show')

    $.get(path_url + 'api/produtos/dados-produto-unico/' + id)
    .done((res) => {
        $('#dados_produto_unico .modal-body').html(res)
    })
    .fail((e) => {
        console.log(e)
    })
}