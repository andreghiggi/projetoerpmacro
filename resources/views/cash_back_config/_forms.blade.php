<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('valor_percentual', 'Percentual de crédito sobre a venda')
        ->attrs(['class' => 'percentual'])->required()
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::text('percentual_maximo_venda', 'Percentual máximo por venda')
        ->attrs(['class' => 'percentual'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('dias_expiracao', 'Dias expiração')
        ->attrs(['class' => 'percentual', 'data-mask' => '0000'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('valor_minimo_venda', 'Valor minímo de venda')
        ->attrs(['class' => 'moeda'])->required()
        ->value(isset($item) ? __moeda($item->valor_minimo_venda) : '')
        !!}
    </div>

    <div class="col-md-6">
        {!!Form::text('mensagem_padrao_whatsapp', 'Mensagem padrão do whatsApp')
        ->attrs(['class' => ''])
        !!}
        <p class="text-danger ml-1 mr-1">*Use {credito} para o valor do crédito, use {expiracao} para data de expiração, use {nome} para o nome do cliente - EXEMPLO: O valor do seu CashBack é de {credito}, com validade até {expiracao}, obrigado {nome}</p>
    </div>

    <div class="col-md-6">
        {!!Form::text('mensagem_automatica_5_dias', 'Mensagem de expiração 5 dias')
        ->attrs(['class' => ''])
        !!}
        <p class="text-danger ml-1 mr-1">*EXEMPLO: O valor {credito} irá expirar!</p>
    </div>

    <div class="col-md-6">
        {!!Form::text('mensagem_automatica_1_dia', 'Mensagem de expiração 1 dia')
        ->attrs(['class' => ''])
        !!}
        <p class="text-danger ml-1 mr-1">*EXEMPLO: O valor {credito} irá expirar!</p>
    </div>
    
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5">Salvar</button>
    </div>
</div>