@extends('layouts.app', ['title' => 'Gerar etiqueta'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Gerar etiqueta compra - <strong class="text-success">#{{ $item->numero_sequencial }}</strong></h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('produtos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('compras.etiqueta-store', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('compras._forms_etiqueta')
        </div>
        {!!Form::close()!!}
    </div>
</div>

@endsection
@section('js')
<script type="text/javascript">
    $(function(){
        $('#inp-modelo_id').val('').change()
    })

    $('body').on('change', '#inp-modelo_id', function () {
        if($(this).val()){
            $.get(path_url + 'api/etiqueta', {modelo_id: $(this).val()})
            .done((res) => {

                $('#inp-tipo').val(res.tipo).change()
                $('#inp-altura').val(res.altura)
                $('#inp-largura').val(res.largura)
                $('#inp-largura').val(res.largura)
                $('#inp-etiquestas_por_linha').val(res.etiquestas_por_linha)
                $('#inp-distancia_etiquetas_lateral').val(res.distancia_etiquetas_lateral)
                $('#inp-distancia_etiquetas_topo').val(res.distancia_etiquetas_topo)
                $('#inp-distancia_entre_linhas').val(res.distancia_entre_linhas)
                $('#inp-quantidade_etiquetas').val(res.quantidade_etiquetas)
                $('#inp-tamanho_fonte').val(res.tamanho_fonte)
                $('#inp-tamanho_codigo_barras').val(res.tamanho_codigo_barras)

                $('#inp-nome_empresa').prop('checked', res.nome_empresa)
                $('#inp-nome_produto').prop('checked', res.nome_produto)
                $('#inp-valor_produto').prop('checked', res.valor_produto)
                $('#inp-codigo_produto').prop('checked', res.codigo_produto)
                $('#inp-codigo_barras_numerico').prop('checked', res.codigo_barras_numerico)
                $('#inp-referencia').prop('checked', res.referencia)
                $('#inp-valor_atacado').prop('checked', res.valor_atacado)

            })
            .fail((err) => {
                console.log(err)
            })
        }
    })

</script>
@endsection
