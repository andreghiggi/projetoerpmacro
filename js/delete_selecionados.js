$("#select-all-checkbox").on("click", function (e) {
    if($(this).is(':checked')){
        $('.check-delete').prop('checked', 1)
    }else{
        $('.check-delete').prop('checked', 0)
    }

    validaButtonDelete()
});

$(".check-delete").on("click", function (e) {
    validaButtonDelete()
})

function validaButtonDelete(){
    $('.btn-delete-all').attr('disabled', 1)
    $('.btn-desactive-all').attr('disabled', 1)
    if(!$('.check-delete').is(':checked')){
        $('.btn-delete-all').attr('disabled', 1)
        $('.btn-desactive-all').attr('disabled', 1)
    }else{
        $('.btn-delete-all').removeAttr('disabled')
        $('.btn-desactive-all').removeAttr('disabled')
    }
    $('#form-delete-select div').html('')
    $('.check-delete').each(function(){
        if($(this).is(':checked')){
            let v = $(this).val()
            $inp = "<input type='hidden' name='item_delete[]' value='"+v+"'>"
            $('#form-delete-select div').append($inp)
        }
    })

    $('#form-desactive-select div').html('')
    $('.check-delete').each(function(){
        if($(this).is(':checked')){
            let v = $(this).val()
            $inp = "<input type='hidden' name='item_delete[]' value='"+v+"'>"
            $('#form-desactive-select div').append($inp)
        }
    })
}

$(function(){
    validaButtonDelete()
})

$(".btn-delete-all").on("click", function (e) {
    e.preventDefault();

    swal({
        title: "Exclusão em lote?",
        text: "Uma vez deletado, você não poderá recuperar esses itens novamente!",
        icon: "warning",
        buttons: true,
        buttons: ["Cancelar", "Excluir"],
        dangerMode: true,
    }).then((isConfirm) => {
        if (isConfirm) {
            document.getElementById('form-delete-select').submit();
        } else {
            swal("", "Os itens estão a salvo!", "info");
        }
    });
});

$(".btn-desactive-all").on("click", function (e) {
    e.preventDefault();

    swal({
        title: "Desativar em lote?",
        text: "Tem certeza que deseja desativar esses itens!",
        icon: "warning",
        buttons: true,
        buttons: ["Cancelar", "Desativar"],
        dangerMode: true,
    }).then((isConfirm) => {
        if (isConfirm) {
            document.getElementById('form-desactive-select').submit();
        } else {
            swal("", "Os itens estão a salvo!", "info");
        }
    });
});