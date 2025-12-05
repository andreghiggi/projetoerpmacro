<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('tempo_descanso_entre_agendamento', 'Tempo descanso entre os agendamentos (min)')
        ->required()
        ->attrs(['data-mask' => '000'])
        !!}
    </div>

    <div class="col-md-6">
        {!!Form::text('token_whatsapp', 'Token WhatsApp')
        !!}
        <h5 class="text-success mt-1">Para enviar mensagens de alertas de agendamento https://criarwhats.com</h5>
    </div>
    <hr>
    <div class="col-md-3">
        {!!Form::select('msg_wpp_manha', 'Enviar mensagem para o cliente de manhã', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::tel('msg_wpp_manha_horario', 'Horário de envio da mensagem de manhã')
        ->attrs(['data-mask' => '00:00'])
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::select('msg_wpp_alerta', 'Enviar mensagem para o cliente antecedência', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::tel('msg_wpp_alerta_minutos_antecedencia', 'Minutos de envio da mensagem com antecedência')
        ->attrs(['data-mask' => '000'])
        !!}
    </div>

    <div class="col-md-6">
        {!!Form::textarea('mensagem_manha', 'Mensagem de envio manhã')
        ->attrs(['rows' => '5'])
        !!}
        <br>
        <h5>Use %nome% para o nome do cliente, %data% para data, %hora% para horário, %serviços% para serviço.</h5>
        <h5 class="text-success">Exemplo: Olá %nome%, confirmado seu agendamento para a %data%, horário: %hora%, Obrigado!</h5>
    </div>

    <div class="col-md-6">
        {!!Form::textarea('mensagem_alerta', 'Mensagem de envio alerta')
        ->attrs(['rows' => '5'])
        !!}
        <br>
        <h5>Use %nome% para o nome do cliente, %data% para data, %hora% para horário, %serviços% para serviço.</h5>
        <h5 class="text-success">Exemplo: Está quase chegando o horário do nosso agendamento %nome%, por favor chegue com antecedência, obrigado!</h5>
    </div>

    @if($item != null && $item->token_whatsapp)
    <div class="col-md-3">
        <button type="button" data-bs-toggle="modal" data-bs-target="#modal_wpp" class="btn btn-dark w-100">
            <i class="ri-whatsapp-fill"></i>
            Testar envio de mensagem WhatsApp
        </button>
    </div>

    <div class="col-md-3">
        <a href="{{ route('config-agendamento.logs-message') }}" class="btn btn-danger w-100">
            <i class="ri-list-indefinite"></i>
            Logs de mensagem WhatsApp
        </a>
    </div>
    @endif

    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>





