<div class="row">
    <div class="col-md-2">
        {!!Form::select('tipo', 'Tipo', ['' => 'Selecione'] + \App\Models\Contigencia::tiposContigencia())
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('documento', 'Documento', ['' => 'Selecione', 'NFe' => 'NFe', 'NFCe' => 'NFCe'])
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-5">
        {!!Form::text('motivo', 'Motivo')
        ->required()!!}
    </div>
    

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>
@section('js')
<script type="text/javascript">
    $(document).on("change", "#inp-tipo", function() {
        let tipo = $(this).val()
        // $("#inp-documento option").removeAttr('disabled');
        // if(tipo == 'OFFLINE'){
        //     $("#inp-documento option[value='NFe']").attr('disabled', 1);
        // }else{
        //     $("#inp-documento option[value='NFCe']").attr('disabled', 1);
        // }
    })

</script>
@endsection
