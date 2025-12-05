$('#btn_token').click(() => {

    let token = generate_token(25);
    swal({
        title: "Atenção", 
        text: "Esse token é o responsavel pela comunicação com a API!!", 
        icon: "warning", 
        buttons: true,
        dangerMode: true
    }).then((confirmed) => {
        if (confirmed) {
            $('#api_token').val(token)
        }
    });
})

function generate_token(length) {
    var a = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890".split("");
    var b = [];
    for (var i = 0; i < length; i++) {
        var j = (Math.random() * (a.length - 1)).toFixed(0);
        b[i] = a[j];
    }
    return b.join("");
}

$('body').on('click', '.check_todos', function () {
    setTimeout(() => {
        checkTodos()
    }, 10)
})

$('body').on('click', '#btn-store', function () {
    let token = $('#api_token').val()
    if(!token){
        swal("Erro", "Token é obrigatório!", "error")
    }else{
        $('#form').submit()
    }
})

function checkTodos(){

    if($('.check_todos').is(':checked')){
        $('.check-action').prop('checked', 1)
    }else{
        $('.check-action').prop('checked', 0)
    }
}