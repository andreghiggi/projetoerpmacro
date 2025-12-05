$("#select-all-checkbox").on("click", function (e) {
    if($(this).is(':checked')){
        $('.check-delete').prop('checked', 1)
    }else{
        $('.check-delete').prop('checked', 0)
    }

    validaButtonBoleto()
});

$(".check-delete").on("click", function (e) {
    validaButtonBoleto()
})

$(function(){
    validaButtonBoleto()
})

function validaButtonBoleto(){
    $('.btn-boleto').attr('disabled', 1)
    if(!$('.check-delete').is(':checked')){
        $('.btn-boleto').attr('disabled', 1)
    }else{
        $('.btn-boleto').removeAttr('disabled')
    }
    $('#form-gerar-boletos div').html('')
    $('.check-delete').each(function(){
        if($(this).is(':checked')){
            let v = $(this).val()
            console.log(v)
            $inp = "<input type='hidden' name='conta_id[]' value='"+v+"'>"
            $('#form-gerar-boletos div').append($inp)
        }

    })
}

