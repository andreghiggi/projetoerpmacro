
$('#btn-buscar-horarios').click(() => {
    console.clear()
    let servicos = []
    $("#servicos > option:selected").each(function(){
        servicos.push($(this).val())
    })
    let js = {
        servicos: JSON.stringify(servicos),
        data: DATACHANGE,
        start_time: HORARIOCHANGE,
        empresa_id: $('#empresa_id').val(),
        funcionario_id: $('#inp-funcionario_id').val(),
    }
    if (servicos.length > 0) {
        $.get(path_url + "api/agendamentos/buscar-horarios", js)
        .done((success) => {
            // console.log(success)
            $('#tabela-novo-agendamento tbody').html(success)
        })
        .fail((err) => {
            console.log(err)
        })
    } else {
        swal("Alerta", "Selecione ao menos um serviço par buscar horários", "warning")
    }
})


var TEMPOSERVICO = 0

function escolheHorario(data){
    data = JSON.parse(data)
    // console.log(data)
    $('#inp-inicio').val(data.inicio)
    $('#inp-termino').val(data.fim)
    $('#inp-total').val(convertFloatToMoeda(data.total))
    $('#funcionario').val(data.funcionario_id)
    TEMPOSERVICO = data.tempoServico
}

$(document).on("blur", "#inp-inicio", function () {
    let inicio = $(this).val()
    // let t = toTimestamp(inicio)
    $('#inp-termino').val(addMin(inicio))
});

function toTimestamp(horario){
  var aux = horario.split(':'), dt = new Date();
  dt.setHours(aux[0]);
  dt.setMinutes(aux[1]);
  dt.setSeconds(0);
  return dt.getTime();
}

function addMin(hora){
  var timeHoraFinal = toTimestamp(hora) + (TEMPOSERVICO*60*1000);
  var dt = new Date(timeHoraFinal);
  var horaRetorno = (dt.getHours() < 10) ? '0'+dt.getHours() : dt.getHours();
  horaRetorno += (dt.getMinutes() < 10) ? ':0'+dt.getMinutes() : ":"+dt.getMinutes();
  return horaRetorno;
}

$('#btn-save-event').click(() => {
    if(!$('#inp-cliente_id').val()){
        swal("Alerta", "Informe o cliente", "warning")
    }
    else if(!$('#inp-total').val()){
        swal("Alerta", "É necessário um total para o agendamento", "warning")
    }
    else if(!$('#inp-inicio').val() || !$('#inp-inicio').val()){
        swal("Alerta", "Informe o horário de início e término do agendamento", "warning")
    }
    else if(!$('#inp-funcionario_id').val()){
        swal("Alerta", "Funcionário não selecionado", "warning")
    }else{
        $('#form-event').submit()
    }

})

