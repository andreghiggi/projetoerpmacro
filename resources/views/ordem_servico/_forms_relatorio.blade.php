<input type="hidden" value="{{$ordem->id}}" name="ordem_servico_id">
<div class="col-md-12">
    {!! Form::textarea('texto', 'Descrição do relatório')
    ->attrs(['rows' => '10', 'class' => 'tiny'])
    ->required() !!}
</div>
<div class="col-12 mt-3">
    <button class="btn btn-success" type="submit">Salvar</button>
</div>

@section('js')
<script src="/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
    $(function(){
        tinymce.init({ selector: 'textarea.tiny', language: 'pt_BR'})
        setTimeout(() => {
            $('.tox-promotion, .tox-statusbar__right-container').addClass('d-none')
        }, 500)
    })
</script>
@endsection
