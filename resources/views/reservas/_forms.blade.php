<div class="row g-2">
    <div class="col-md-2">
        {!!Form::date('data_checkin', 'Data Checkin')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::date('data_checkout', 'Data Checkout')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('qtd_hospedes', 'Hóspedes')
        ->required()
        !!}
    </div>

    <div class="col-md-3">
        <br>
        <button type="button" class="btn btn-dark btn-procura-acomodacoes">
            <i class="ri-search-line"></i> Procurar acomodações
        </button>
    </div>

    <div class="row acomodacoes-view">
        
    </div>

    
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

