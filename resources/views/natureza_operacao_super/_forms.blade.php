<div class="row">
    <div class="col-md-4">
        {!!Form::text('descricao', 'Descrição')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', [ 1 => 'Sim', 0 => 'Não'])
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('padrao', 'Padrão', [0 => 'Não', 1 => 'Sim'])
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('sobrescrever_cfop', 'Sobrescrever CFOP', [0 => 'Não', 1 => 'Sim'])
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <hr class="mt-2">

    <div class="card">
        <div class="card-header">
            <h5>Dados para emissão</h5>
            <p class="text-danger">
                <i class="ri-information-line"></i>
                Os campos abaixo são opcionais, se preenchidos iram sobrescrever os dados informados no cadastro do produto para gerar o XML.
            </p>
        </div>

        <div class="card-body" style="margin-top: -30px">
            <div class="row g-2">
                <div class="col-md-6">
                    {!!Form::select('cst_csosn', 'CST/CSOSN', ['' => 'Selecione'] + $listaCTSCSOSN)
                    ->attrs(['class' => 'form-select'])
                    !!}
                </div>
                <div class="col-md-6">
                    {!!Form::select('cst_pis', 'CST PIS', ['' => 'Selecione'] + App\Models\Produto::listaCST_PIS_COFINS())
                    ->attrs(['class' => 'form-select'])
                    !!}
                </div>
                <div class="col-md-6">
                    {!!Form::select('cst_cofins', 'CST COFINS', ['' => 'Selecione'] + App\Models\Produto::listaCST_PIS_COFINS())
                    ->attrs(['class' => 'form-select'])
                    !!}
                </div>
                <div class="col-md-6">
                    {!!Form::select('cst_ipi', 'CST IPI', ['' => 'Selecione'] + App\Models\Produto::listaCST_IPI())
                    ->attrs(['class' => 'form-select'])
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::tel('perc_icms', '% ICMS')
                    ->attrs(['class' => 'percentual'])
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::tel('perc_pis', '% PIS')
                    ->attrs(['class' => 'percentual'])
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::tel('perc_cofins', '% COFINS')
                    ->attrs(['class' => 'percentual'])
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::tel('perc_ipi', '% IPI')
                    ->attrs(['class' => 'percentual'])
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::tel('cfop_estadual', 'CFOP Estadual')
                    ->attrs(['class' => 'cfop'])
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::tel('cfop_outro_estado', 'CFOP Inter Estadual')
                    ->attrs(['class' => 'cfop'])
                    !!}
                </div>

                <div class="col-md-2">
                    {!!Form::tel('cfop_entrada_estadual', 'CFOP Entrada Estadual')
                    ->attrs(['class' => 'cfop'])
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::tel('cfop_entrada_outro_estado', 'CFOP Entrada Inter Estadual')
                    ->attrs(['class' => 'cfop'])
                    !!}
                </div>
            </div>
        </div>
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>
@section('js')
<script type="text/javascript">

    $(document).on("blur", "#inp-cfop_estadual", function () {

        let v = $(this).val().substring(1,4)
        $("#inp-cfop_outro_estado").val('6'+v)
        $("#inp-cfop_entrada_estadual").val('1'+v)
        $("#inp-cfop_entrada_outro_estado").val('2'+v)
    })
</script>
@endsection