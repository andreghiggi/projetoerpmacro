<div class="row g-2">

    <div class="col-md-2">
        {!!Form::text('codigo_conta_analitica', 'Código conta analítica')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('codigo_receita', 'Código da receita')
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::select('gerar_bloco_k', 'Gerar bloco K', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('layout_bloco_k', 'Layout bloco K', [0 => 'Leiaute simplificado', 1 => 'Leiaute completo', 2 => 'Leiaute restrito aos saldos de estoque'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('codigo_obrigacao', 'Código de obrigação E116', App\Models\SpedConfig::codigosDeObrigacao())
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('data_vencimento', 'Data de vencimento E116')
        ->attrs(['data-mask' => '00'])

        !!}
    </div>

    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

