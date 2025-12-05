@extends('layouts.app', ['title' => 'Impressão para Inventário'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Impressão para Inventário</h4>
        
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('inventarios.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">

        <div class="pl-lg-4">

            <div class="row">
                <h5>Total de produtos cadastrados: <strong>{{ $countProdutos }}</strong></h5>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <label>Produto</label>
                    <select name="produto_id" class="form-control" id="inp-produto_id"></select>
                </div>
                <div class="col-md-2">
                    <label>Categoria</label>
                    <select name="categoria_id" class="form-control form-select" id="inp-categoria_id">
                        <option value="">Selecione</option>
                        @foreach($categorias as $c)
                        <option value="{{ $c->id }}">{{ $c->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <br>
                    @if($item->status == 1) 
                    <button class="btn btn-success" id="btn-adicionar-produto">
                        <i class="ri-add-circle-fill"></i> Adicionar
                    </button>
                    @endif
                </div>
            </div>
            <div class="table-responsive mt-2">

                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Código de barras</th>
                            <th>Referência</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-produtos">
                        @foreach($item->itensImpresso as $p)
                        <tr data-id="{{ $p->id }}">
                            <td>{{ $p->produto->nome }}</td>
                            <td>{{ $p->produto->categoria ? $p->produto->categoria->nome : '' }}</td>
                            <td>{{ $p->produto->codigo_barras ?? '--' }}</td>
                            <td>{{ $p->produto->referencia ?? '--' }}</td>
                            <td>
                                @if($item->status == 1)
                                <button class="btn btn-danger btn-delete-item">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <a target="_blank" class="btn btn-light btn-imprimir @if(sizeof($item->itensImpresso) == 0) disabled @endif" href="{{ route('inventarios.renderizar', [$item->id]) }}">
                    <i class="ri-printer-line"></i> Imprimir
                </a>

            </div>
        </div>

    </div>
</div>
@section('js')
<script type="text/javascript">
    $('#btn-adicionar-produto').on('click', function (e) {
        e.preventDefault();

        let produto_id = $('#inp-produto_id').val();
        let categoria_id = $('select[name="categoria_id"]').val();

        if (!produto_id && !categoria_id) {
            toastr.error("Selecione um produto ou uma categoria!");
            return;
        }

        $.ajax({
            url: "{{ route('inventarios.add-produto') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                produto_id: produto_id,
                categoria_id: categoria_id,
                inventario_id: '{{ $item->id }}'
            },
            success: function (res) {
                $('#tbody-produtos').html(res);
                $('.btn-imprimir').removeClass('disabled')

                toastr.success("Produto(s) adicionado(s)!");

                $('#inp-produto_id').val('').trigger('change');
                $('#inp-categoria_id').val('').change();
            },
            error: function () {
                toastr.error("Erro ao adicionar.");
            }
        });
    });

    $(function(){
        $('#inp-categoria_id').val('').change()
    })

    $(document).on('click', '.btn-delete-item', function() {
        let tr = $(this).closest('tr');
        let id = tr.data('id');

        swal({
            title: "Excluir item?",
            text: "Esta ação não pode ser desfeita.",
            icon: "warning",
            buttons: ["Cancelar", "Excluir"],
            dangerMode: true,
        }).then((confirm) => {

            if (!confirm) return;

            $.ajax({
                method: "POST",
                url: "{{ route('inventarios.delete-item') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(res) {
                    if (res.status) {
                        tr.remove();
                        toastr.success("Item removido!");
                    } else {
                        toastr.error(res.msg || "Erro ao remover item.");
                    }
                },
                error: function() {
                    toastr.error("Erro ao tentar excluir.");
                }
            });

        });
    });

</script>
@endsection
@endsection
