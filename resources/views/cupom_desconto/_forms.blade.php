<div class="row g-2">

    <div class="col-md-2">
        <label class="required">Código</label>
        <div class="input-group">
            <input required data-mask="AAAAAA" type="text" class="form-control" id="codigo" name="codigo" value="{{ isset($item) ? $item->codigo : '' }}">
            <button type="button" class="btn btn-primary" id="btn-codigo"><a class="ri-refresh-line text-white"></a></button>
        </div>
    </div>

    <div class="col-md-2">
        {!!Form::tel('valor', 'Valor')
        ->attrs(['class' => 'moeda'])
        ->required()
        ->value(isset($item) ? __moeda($item->valor) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('tipo_desconto', 'Tipo', ['percentual' => 'Percentual', 'valor' => 'Valor'])
        ->attrs(['class' => 'form-select'])
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Tipo', ['1' => 'Ativo', '0' => 'Desativado'])
        ->attrs(['class' => 'form-select'])
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('valor_minimo_pedido', 'Valor mínimo do pedido')
        ->attrs(['class' => 'moeda'])
        ->required()
        ->value(isset($item) ? __moeda($item->valor_minimo_pedido) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::date('expiracao', 'Expiração')
        ->required()
        !!}
    </div>

    <div class="col-md-6">
        {!!Form::text('descricao', 'Descrição')
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::select('cliente_id', 'Cliente')
        ->options((isset($item) && $item->cliente_id) ? [$item->cliente_id => $item->cliente->razao_social] : [])

        !!}
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script type="text/javascript">
    $('#btn-codigo').click(() => {
        $('#codigo').val(generate_token(6))
    })

    function generate_token(length) {
        var a = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890".split("");
        var b = [];
        for (var i = 0; i < length; i++) {
            var j = (Math.random() * (a.length - 1)).toFixed(0);
            b[i] = a[j];
        }
        return b.join("");
    }
</script>
@endsection