@if($item != null)
<div class="row">
    <h4 class="text-success">Caixa Aberto!</h4>
    @if($item->contaEmpresa)
    <h5 class="text-primary">Conta: <strong>{{ $item->contaEmpresa->nome }}</strong></h5>
    @endif
</div>
@else
<div class="row g-2">
    <div class="col-md-2">
        {!!Form::text('valor_abertura', 'Valor de abertura')->attrs(['class' => 'moeda'])->required()
        !!}
    </div>

    @if(__countLocalAtivo() > 1)
    <div class="col-md-2">
        <label for="">Local</label>

        <select id="inp-local_id" required class="select2 class-required" data-toggle="select2" name="local_id">
            <option value="">Selecione</option>
            @foreach(__getLocaisAtivoUsuario() as $local)
            <option @isset($item) @if($item->local_id == $local->id) selected @endif @endif value="{{ $local->id }}">{{ $local->descricao }}</option>
            @endforeach
        </select>
    </div>
    @else
    <input id="inp-local_id" type="hidden" value="{{ __getLocalAtivo() ? __getLocalAtivo()->id : '' }}" name="local_id">
    @endif

    <div class="col-md-3 div-conta-empresa">
        {!!Form::select('conta_empresa_id', 'Conta empresa')
        ->required()
        !!}
    </div>

    <div class="col-md-6">
        {!!Form::text('observacao', 'Observação')->attrs(['class' => ''])
        !!}
    </div>
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>
@endif
