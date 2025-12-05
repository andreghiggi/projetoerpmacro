var DATACHANGE = null
var HORARIOCHANGE = null
var SERVICOS = []
! function (l) {
    "use strict";
    let agendamentos = JSON.parse($('#agendamentos').val())

    function e() {
        this.$body = l("body"), this.$modal = new bootstrap.Modal(document.getElementById("event-modal"), {
            backdrop: "static"
        }), this.$calendar = l("#calendar"), this.$formEvent = l("#form-event"), this.$btnNewEvent = l("#btn-new-event"), this.$btnDeleteEvent = l("#btn-delete-event"), this.$btnSaveEvent = l("#btn-save-event"), this.$modalTitle = l("#modal-title"), this.$calendarObj = null, this.$selectedEvent = null, this.$newEventData = null
    }
    e.prototype.onEventClick = function (e) {
        this.$formEvent[0].reset(), this.$formEvent.removeClass("was-validated"), this.$newEventData = null, this.$btnDeleteEvent.show(), this.$modalTitle.text("Edit Event"), this.$selectedEvent = e.event, l("#event-title").val(this.$selectedEvent.title), l("#event-category").val(this.$selectedEvent.classNames[0])
    }, e.prototype.onSelect = function (e) {
        this.$formEvent[0].reset(), this.$formEvent.removeClass("was-validated"), this.$selectedEvent = null, this.$newEventData = e, this.$btnDeleteEvent.hide(), this.$modalTitle.text("Adiconar Novo Agendamento"), this.$modal.show(), this.$calendarObj.unselect()
    }, e.prototype.init = function () {
        var e = new Date(l.now()),
        e = (new FullCalendar.Draggable(document.getElementById("external-events"), {
            itemSelector: ".external-event",
            eventData: function (e) {
                return {
                    // title: e.innerText,
                    // className: l(e).data("class"),

                }

            }
        }), 
        agendamentos
        
        ),
        a = this;
        a.$calendarObj = new FullCalendar.Calendar(a.$calendar[0], {
            slotDuration: "00:15:00",
            slotMinTime: "08:00:00",
            slotMaxTime: "19:00:00",
            themeSystem: "bootstrap",
            bootstrapFontAwesome: !1,
            locale: 'pt-br',
            buttonText: {
                today: "Hoje",
                month: "Mês",
                week: "Semana",
                day: "Dia",
                list: "Lista",
                prev: "Anterior",
                next: "Próximo",
            },
            initialView: "dayGridMonth",
            handleWindowResize: !0,
            height: l(window).height() - 200,
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth"
            },
            initialEvents: e,
            editable: !0,
            droppable: !0,
            selectable: !0,
            dateClick: function (e) {
                console.log(e)
                $('#tabela-novo-agendamento tbody').html('')

                if($('#create_permission').val() == 0){
                    swal("Erro", "Usuário não tem permissão para cadastrar eventos", "error")
                    $('#event-modal').modal('close')
                }
                $('#servicos').val(null).trigger('change');
                SERVICOS = []
                setModalFuncionario()
                setModalCliente()
                a.onSelect(e)
                let data = e.dateStr.split('T')
                DATACHANGE = data[0]
                $('#data').val(DATACHANGE)

                if(data[1]){
                    $('#inp-inicio').val(data[1].substring(0,5))
                    HORARIOCHANGE = data[1].substring(0,5)
                }

            },
            eventClick: function (e) {
                a.onEventClick(e)
                console.log(e.event._def.publicId)
                location.href = '/agendamentos/'+e.event._def.publicId
            }
        }), a.$calendarObj.render(), a.$btnNewEvent.on("click", function (e) {
            a.onSelect({
                date: new Date,
                allDay: !0,
            })

        }), a.$formEvent.on("submit", function (e) {

        }), l(a.$btnDeleteEvent.on("click", function (e) {
            a.$selectedEvent && (a.$selectedEvent.remove(), a.$selectedEvent = null, a.$modal.hide())
        }))
    }, l.CalendarApp = new e, l.CalendarApp.Constructor = e
}(window.jQuery),
function () {
    "use strict";
    window.jQuery.CalendarApp.init()
}();

function setModalFuncionario(){
    $("#inp-funcionario_id").select2({
        minimumInputLength: 2,
        language: "pt-BR",
        placeholder: "Digite para buscar o funcionário",
        theme: "bootstrap4",
        dropdownParent: $('#event-modal .modal-content'),
        ajax: {
            cache: true,
            url: path_url + "api/funcionarios/pesquisa",
            dataType: "json",
            data: function (params) {
                console.clear();
                var query = {
                    pesquisa: params.term,
                    empresa_id: $("#empresa_id").val(),
                };
                return query;
            },
            processResults: function (response) {
                var results = [];

                $.each(response, function (i, v) {
                    var o = {};
                    o.id = v.id;

                    o.text = v.nome;
                    o.value = v.id;
                    results.push(o);
                });
                return {
                    results: results,
                };
            },
        },
    });
}

function setModalCliente(){
    $("#inp-cliente_id").select2({
        minimumInputLength: 2,
        language: "pt-BR",
        placeholder: "Digite para buscar o cliente",
        theme: "bootstrap4",
        dropdownParent: $('#event-modal .modal-content'),
        ajax: {
            cache: true,
            url: path_url + "api/clientes/pesquisa",
            dataType: "json",
            data: function (params) {
                console.clear();
                var query = {
                    pesquisa: params.term,
                    empresa_id: $("#empresa_id").val(),
                };
                return query;
            },
            processResults: function (response) {
                var results = [];

                $.each(response, function (i, v) {
                    var o = {};
                    o.id = v.id;

                    o.text = v.razao_social + " - " + v.cpf_cnpj;
                    o.value = v.id;
                    results.push(o);
                });
                return {
                    results: results,
                };
            },
        },
    });
}
