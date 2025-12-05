@extends('layouts.app', ['title' => 'Alterar Valor/Estoque'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Alterar Valor/Estoque</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('produtos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <form autocomplete="off" id="form-busca">
        <div class="row m-3">

            <div class="col-md-3 col-12">
                {!!Form::text('nome', 'Nome do Produto')
                !!}
            </div>

            <div class="col-md-3 col-12">
                {!!Form::text('codigo_barras', 'Código de Barras')
                !!}
            </div>

            <div class="col-md-2">
                {!!Form::select('categoria_id', 'Categoria', ['' => 'Selecione'] + $categorias->pluck('nome', 'id')->all())
                ->attrs(['class' => 'form-select select2'])
                ->id('categoria1')
                !!}
            </div>

            <div class="col-md-2">
                {!!Form::select('marca_id', 'Marca', ['' => 'Selecione'] + $marcas->pluck('nome', 'id')->all())
                ->attrs(['class' => 'form-select'])
                !!}
            </div>
            @if(__countLocalAtivo() > 1)
            <div class="col-md-2">
                {!!Form::select('local_id', 'Local', __getLocaisAtivoUsuario()->pluck('descricao', 'id')->all())
                ->attrs(['class' => 'select2'])
                !!}
            </div>
            @else
            <input id="inp-local_id" type="hidden" value="{{ __getLocalAtivo() ? __getLocalAtivo()->id : '' }}" name="local_id">
            @endif


            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-search-line"></i> Pesquisar
                    </button>
                    <a href="{{ route('produtos.alterar-valor-estoque') }}" class="btn btn-secondary">
                        <i class="ri-refresh-line"></i> Limpar
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body">

        <div class="table-responsive">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th>Produto</th>
                        <th>Referência</th>
                        <th>Código de barras</th>
                        <th>Categoria</th>
                        <th>Marca</th>
                        <th>Valor de venda</th>
                        <th>Valor de compra</th>
                        <th>Estoque</th>
                    </tr>
                </thead>
                <tbody id="tbody-produtos"></tbody>
            </table>
        </div>

    </div>
</div>
@section('js')
<script>
    $("#form-busca").on("submit", function(e){
        e.preventDefault();

        let dados = $(this).serialize();

        $.ajax({
            url: "{{ route('produtos.buscar-ajuste') }}",
            type: "GET",
            data: dados,
            success: function(res){
                let tbody = $("#tbody-produtos");
                tbody.html(res);
            },
            error: function(err){
                console.log(err)
                swal("Erro!", "Não foi possível buscar os produtos.", "error");
            }
        });

    });

    $(document).on("blur", ".input-edit", function(){

        let input = $(this);
        let valor = input.val();
        let local_id = $('#inp-local_id').val();
        let campo = input.attr("name");
        let id = input.closest("tr").data("id");
        
        $.ajax({
            url: "{{ route('produtos.alterar-campo') }}",
            type: "POST",
            data: {
                id: id,
                campo: campo,
                valor: valor,
                local_id: local_id,
                _token: "{{ csrf_token() }}"
            },
            success: function(res){
                input.addClass("border-success");

            // setTimeout(() => input.removeClass("border-success"), 800);
        },
        error: function(err){
            console.log(err)

            input.addClass("border-danger");
            setTimeout(() => input.removeClass("border-danger"), 1000);

            swal("Erro", "Não foi possível atualizar o produto.", "error");
        }
    });

    });

</script>

@endsection
@endsection
