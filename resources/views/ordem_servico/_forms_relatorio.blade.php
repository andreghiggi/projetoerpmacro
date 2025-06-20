<input type="hidden" value="{{$ordem->id}}" name="ordem_servico_id">
<div class="col-md-12">
    {!! Form::textarea('texto', 'Descrição do relatório')->required() !!}
</div>
<div class="col-12 mt-3">
    <button class="btn btn-success" type="submit">Salvar</button>
</div>
