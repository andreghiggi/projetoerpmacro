<div class="row g-2">
    <div class="col-md-2">
        {!!Form::select('uf', 'UF', \App\Models\Cidade::estados())->required()
        ->attrs(['class' => 'form-select select2'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('cfop', 'CFOP')->required()
        ->attrs(['class' => 'cfop'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('pICMSUFDest', '% ICMS UF Destino')->required()
        ->attrs(['class' => 'percentual'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('pICMSInter', '% ICMS Interno')->required()
        ->attrs(['class' => 'percentual'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('pICMSInterPart', '% ICMS Interestadual UF')->required()
        ->attrs(['class' => 'percentual'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('pFCPUFDest', '% Fundo Combate a Pobreza')->required()
        ->attrs(['class' => 'percentual'])
        !!}
    </div>
    
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>