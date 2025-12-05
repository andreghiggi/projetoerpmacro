<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('nome', 'Nome')
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', ['1' => 'Sim', '0' => 'Não'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('ajuste_sobre', 'Ajuste sobre', ['' => 'Selecione', 'valor_compra' => 'Valor de compra', 'valor_venda' => 'Valor de venda'])
        ->required()->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('tipo', 'Tipo', ['' => 'Selecione', 'incremento' => 'Incremento', 'reducao' => 'Redução'])
        ->required()->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('percentual_alteracao', 'Percentual de alteração')
        ->attrs(['class' => 'percentual'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('valor_alteracao', 'Valor de alteração')
        ->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->valor_alteracao) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('tipo_pagamento', 'Tipo de pagamento', ['' => 'Selecione'] + App\Models\ListaPreco::tiposPagamento())->attrs(['class' => 'form-select'])
        !!}
    </div>
    
    <div class="col-md-3">
        {!!Form::select('funcionario_id', 'Funcionário')
        ->options((isset($item) && $item->funcionario) ? [$item->funcionario_id => $item->funcionario->nome] : [])
        !!}
    </div>

    <div class="col-lg-5 col-12">
        <label class="required">Usuários</label>
        <select required class="select2 form-control select2-multiple" name="usuarios[]" data-toggle="select2" multiple="multiple" id="usuarios">
            @foreach ($usuarios as $u)
            <option @isset($item) @if(in_array($u->id, $item->usuarios)) selected @endif @endif value="{{$u->id}}">{{$u->name}}</option>
            @endforeach
        </select>
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>