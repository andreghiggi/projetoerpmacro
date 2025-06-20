<div class="row g-2">

    <div class="col-md-4">
        {!!Form::text('assunto', 'Assunto')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Status', ['' => 'Selecione'] + App\Models\CrmAnotacao::getStatus())
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('alerta', 'Alerta', [1 => 'Sim', 0 => 'Não'])
        ->attrs(['class' => 'form-select'])
        ->required()
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::select('funcionario_id', 'Vendedor')
        ->options(isset($item) && $item->funcionario ? [$item->funcionario->id => $item->funcionario->info] : [])
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::select('cliente_id', 'Cliente')
        ->options(isset($item) && $item->cliente ? [$item->cliente->id => $item->cliente->info] : [])
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::select('fornecedor_id', 'Fornecedor')
        ->options(isset($item) && $item->fornecedor ? [$item->fornecedor->id => $item->fornecedor->info] : [])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::date('data_retorno', 'Data de retorno')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::date('data_entrega', 'Data de entrega')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('conclusao', 'Conclusão', ['' => 'Selecione'] + App\Models\CrmAnotacao::getConclusoes())
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('tipo_registro', 'Referênciar', ['' => 'Selecione', 'venda' => 'Venda', 'compra' => 'Compra', 'orçamento' => 'Orçamento'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    @if(isset($item) && $item->registro_id)
    <div class="col-md-6 d-registro">
        {!!Form::select('registro_id', 'Registro de venda/compra/orçamento')
        ->attrs(['class' => 'form-select'])
        ->options(isset($item) && $item->registro ? [$item->registro->id => $item->registro->info] : [])
        !!}
    </div>
    @else
    <div class="col-md-6 d-none d-registro">
        {!!Form::select('registro_id', 'Registro de venda/compra/orçamento')
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    @endif
    
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>
@section('js')
<script type="text/javascript">

    $("#form-crm").submit(function(e){
        ;

        let cliente_id = $('#inp-cliente_id').val()
        let fornecedor_id = $('#inp-fornecedor_id').val()

        if(!fornecedor_id && !cliente_id){
            swal("Atenção", "Informe cliente ou fornecedor", "error")
            e.preventDefault()
        }
    });

    $(function(){
        referenciar()
    })

    $(document).on("change", "#inp-tipo_registro", function () {
        referenciar()
    });

    function referenciar(){
        if($("#inp-tipo_registro").val()){
            $('.d-registro').removeClass('d-none')
        }else{
            $('.d-registro').addClass('d-none')
        }
    }

    $("#inp-registro_id").select2({
        minimumInputLength: 2,
        language: "pt-BR",
        placeholder: "Digite para buscar o registro pelo código",
        width: "100%",
        ajax: {
            cache: true,
            url: path_url + "api/crm/referenciar-registro",
            dataType: "json",
            data: function (params) {
                let empresa_id = $('#empresa_id').val()
                let tipo_registro = $('#inp-tipo_registro').val()
                var query = {
                    pesquisa: params.term,
                    empresa_id: empresa_id,
                    tipo_registro: tipo_registro
                };
                return query;
            },
            processResults: function (response) {
                var results = [];

                $.each(response, function (i, v) {
                    var o = {};
                    o.id = v.id;

                    o.text = v.descricao;
                    o.value = v.id;
                    results.push(o);
                });
                return {
                    results: results,
                };
            },
        },
    });
</script>
@endsection

