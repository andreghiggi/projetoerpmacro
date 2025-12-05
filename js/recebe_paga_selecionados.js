function validaButtonSelect(){
    $('.btn-recebe-paga-all').attr('disabled', 1)
    if(!$('.check-delete').is(':checked')){
        $('.btn-recebe-paga-all').attr('disabled', 1)
    }else{
        $('.btn-recebe-paga-all').removeAttr('disabled')
    }
    $('#form-recebe-paga-select div').html('')
    $('.check-delete').each(function(){
        if($(this).is(':checked')){
            let v = $(this).val()
            $inp = "<input type='hidden' name='item_recebe_paga[]' value='"+v+"'>"
            $('#form-recebe-paga-select div').append($inp)
        }
    })
}

$(function(){
    validaButtonSelect()
})

$("#select-all-checkbox").on("click", function (e) {
    if($(this).is(':checked')){
        $('.check-delete').prop('checked', 1)
    }else{
        $('.check-delete').prop('checked', 0)
    }

    validaButtonSelect()
});

$(".check-delete").on("click", function (e) {
    validaButtonSelect()
})

// $(".btn-recebe-paga-all").on("click", function (e) {
//     e.preventDefault();

//     swal({
//         title: "Alteração em lote?",
//         text: "Uma vez alterado, você não poderá desfazer!",
//         icon: "warning",
//         buttons: true,
//         buttons: ["Cancelar", "Confirmar"],
//         dangerMode: true,
//     }).then((isConfirm) => {
//         if (isConfirm) {
//             document.getElementById('form-recebe-paga-select').submit();
//         } else {
//             swal("", "Os itens não foram alterados!", "info");
//         }
//     });
// });