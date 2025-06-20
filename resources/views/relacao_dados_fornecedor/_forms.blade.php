<div class="row g-2">
    <div class="col-md-4">
        {!!Form::select('cst_csosn_entrada', 'CST/CSOSN Entrada', ['' => 'Selecione'] + App\Models\Produto::listaCSTCSOSN())
        ->attrs(['class' => 'select2 cst_csosn'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('cfop_entrada', 'CFOP Entrada')
        ->attrs(['class' => 'cfop'])
        !!}
    </div>
    <div class="col-md-4">
        {!!Form::select('cst_csosn_saida', 'CST/CSOSN Saída', ['' => 'Selecione'] + App\Models\Produto::listaCSTCSOSN())
        ->attrs(['class' => 'select2 cst_csosn'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('cfop_saida', 'CFOP Saída')
        ->attrs(['class' => 'cfop'])
        !!}
    </div>
    
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script type="text/javascript">
    $(document).on("blur", ".cfop", function () {
        let v = $(this).val()
        if(v.length > 0){
            $(".cfop").each(function () {
                $(this).attr('required', 1)
                $(this).prev().addClass('required')
            })
        }else{
            $(".cfop").each(function () {
                $(this).removeAttr('required')
                $(this).prev().removeClass('required')
            })
        }
    });

    $(document).on("change", ".cst_csosn", function () {
        let v = $(this).val()
        if(v){
            $(".cst_csosn").each(function () {
                $(this).attr('required', 1)
                $(this).prev().addClass('required')
            })
        }else{
            $(".cst_csosn").each(function () {
                $(this).removeAttr('required')
                $(this).prev().removeClass('required')
            })
        }
    });
</script>
@endsection