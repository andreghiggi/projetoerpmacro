<div class="row">
    <div class="col-md-2">
        {!!Form::text('codigo', 'Código')
        ->attrs(['data-mask' => '0000.00.00'])
        !!}
    </div>
    <div class="col-md-4">
        {!!Form::text('descricao', 'Descrição')
        !!}
    </div>
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script type="text/javascript">
    $(document).on("blur", "#inp-codigo", function () {
        let codigo = $(this).val()
        let descricao = $('#inp-descricao').val()
        if(descricao[1]){
            descricao = descricao.split("-")
            $('#inp-descricao').val(codigo + " - " + descricao[1])
        }
    });

</script>
@endsection