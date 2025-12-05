<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('titulo', 'Título')->attrs(['class' => ''])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', ['1' => 'Sim', '0' => 'Não'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

   <!--  <div class="col-md-2">
        {!!Form::select('mensagem_para_agendamento', 'Para agendamento', ['1' => 'Sim', '0' => 'Não'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div> -->

    <div class="col-md-2">
        {!!Form::select('tipo', 'Tipo', ['' => 'Selecione'] + \App\Models\MensagemPadraoCrm::tipos())
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-2 d-horario_envio d-none">
        {!!Form::tel('horario_envio', 'Horário envio')
        ->attrs(['class' => 'timer'])
        !!}
    </div>

    <div class="col-md-2 d-dias_apos_venda d-none">
        {!!Form::tel('dias_apos_venda', 'Dias após venda')
        ->attrs(['data-mask' => '0000'])
        !!}
    </div>

    <div class="col-md-2 d-dias_apos_agendamento d-none">
        {!!Form::tel('dias_apos_agendamento', 'Dias após agendamento')
        ->attrs(['data-mask' => '0000'])
        !!}
    </div>

    <div class="col-md-2 d-dias_ultima_venda d-none">
        {!!Form::tel('dias_ultima_venda', 'Dias ultima venda')
        ->attrs(['data-mask' => '0000'])
        !!}
    </div>

    <div class="col-md-12">
        <br>
        {!!Form::checkbox('enviar_whatsapp', 'Enviar WhatsApp', true)
        ->value(1)
        ->checked(isset($item) ? $item->enviar_whatsapp : 0)
        !!}

        {!!Form::checkbox('enviar_email', 'Enviar Email')
        ->value(1)
        ->checked(isset($item) ? $item->enviar_email : 0)
        !!}
    </div>

    <div class="col-md-12">
        {!!Form::textarea('mensagem', 'Mensagem')
        ->attrs(['rows' => '4'])
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal">
            <i class="ri-compass-2-fill"></i>
            Variáveis dinâmicas
        </button>
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="tituloModal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="tituloModal">Variáveis dinâmicas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Placeholder</th>
                                    <th>Descrição</th>
                                    <th>Exemplo de uso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>[nome_cliente]</td>
                                    <td>Nome do cliente</td>
                                    <td>Olá [nome_cliente], como foi sua experiência?</td>
                                </tr>

                                <tr>
                                    <td>[data_venda]</td>
                                    <td>Data da venda</td>
                                    <td>Olá [nome_cliente], tudo certo com sua compra do dia [data_venda]?</td>
                                </tr>

                                <tr>
                                    <td>[dias_inativo]</td>
                                    <td>Número de dias sem compra</td>
                                    <td>Faz [dias_inativo] dias desde sua última compra!</td>
                                </tr>

                                <tr>
                                    <td>[valor_total]</td>
                                    <td>Valor da compra/agendamento</td>
                                    <td>O total da sua compra foi [valor_total]</td>
                                </tr>

                                <tr>
                                    <td>[data_agendamento]</td>
                                    <td>Data de agendamento</td>
                                    <td>Olá [nome_cliente], tudo certo com seu agendamento do dia [data_agendamento]?</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>

        </div>
    </div>
</div>

@section('js')
<script type="text/javascript">

    $(function(){
        changeTipo()
    })

    $(document).on("click", "#inp-enviar_whatsapp", function () {
        if($(this).is(':checked')){
            $.get(path_url + "api/config-geral", {empresa_id: $('#empresa_id').val()})
            .done((data) => {
                if(!data || data.token_whatsapp == null || data.token_whatsapp.length < 5){
                    toastr.error("Defina a configuração geral com o token do WhatsApp")
                }

            })
            .fail((err) => {
                console.log(err)
            })
        }
    })

    $(document).on("change", "#inp-tipo", function () {
        changeTipo()
    })

    function changeTipo(){
        let tipo = $("#inp-tipo").val()
        if(tipo == 'pos_venda'){
            $('.d-horario_envio').addClass('d-none')
            $('.d-dias_ultima_venda').addClass('d-none')
            $('.d-dias_apos_agendamento').addClass('d-none')
            $('.d-dias_apos_venda').removeClass('d-none')

            $('.d-dias_apos_venda').find('label').addClass('required')
            $('.d-dias_apos_venda').find('input').attr('required', 1)
            $('.d-horario_envio').find('input').removeAttr('required')
            $('.d-dias_ultima_venda').find('input').removeAttr('required')
            $('.d-dias_apos_agendamento').find('input').removeAttr('required')

        }else if(tipo == 'aniversario'){
            $('.d-horario_envio').removeClass('d-none')
            $('.d-dias_ultima_venda').addClass('d-none')
            $('.d-dias_apos_venda').addClass('d-none')
            $('.d-dias_apos_agendamento').addClass('d-none')

            $('.d-horario_envio').find('label').addClass('required')
            $('.d-horario_envio').find('input').attr('required', 1)
            $('.d-dias_ultima_venda').find('input').removeAttr('required')
            $('.d-dias_apos_venda').find('input').removeAttr('required')
            $('.d-dias_apos_agendamento').find('input').removeAttr('required')
            
        }else if(tipo == 'reativacao'){
            $('.d-horario_envio').addClass('d-none')
            $('.d-dias_ultima_venda').removeClass('d-none')
            $('.d-dias_apos_venda').addClass('d-none')
            $('.d-dias_apos_agendamento').addClass('d-none')

            $('.d-dias_ultima_venda').find('label').addClass('required')
            $('.d-dias_ultima_venda').find('input').attr('required', 1)
            $('.d-horario_envio').find('input').removeAttr('required')
            $('.d-dias_apos_venda').find('input').removeAttr('required')
            $('.d-dias_apos_agendamento').find('input').removeAttr('required')
        }else if(tipo == 'pos_agendamento'){
            $('.d-horario_envio').addClass('d-none')
            $('.d-dias_apos_agendamento').removeClass('d-none')
            $('.d-dias_ultima_venda').addClass('d-none')
            $('.d-dias_apos_venda').addClass('d-none')

            $('.d-dias_apos_agendamento').find('label').addClass('required')
            $('.d-dias_apos_agendamento').find('input').attr('required', 1)
            $('.d-horario_envio').find('input').removeAttr('required')
            $('.d-dias_apos_venda').find('input').removeAttr('required')
            $('.d-dias_ultima_venda').find('input').removeAttr('required')
        }
    }
</script>
@endsection
