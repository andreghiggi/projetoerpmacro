@extends('layouts.app', ['title' => 'Apontar Itens'])
@section('content')

<div class="card mt-1">
    <div class="card-header">

        <div class="col-12 mt-2">
            <br>
            <h5 class="text-danger">#{{ $item->numero_sequencial }} - REF: {{ $item->referencia }}</h5>

            <p>Total de produtos: <strong>{{ sizeof($item->itensImpresso) }}</strong></p>
        </div>
        
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('inventarios.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">

            <table class="table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Código de barras</th>
                        <th>Quantidade</th>
                        <th>Observação</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($item->itensImpresso as $i)
                    <tr>
                        <td>
                            <label style="width: 500px;">{{ $i->produto->nome }}</label>
                        </td>
                        <td>{{ $i->produto->codigo_barras ?? '--' }}</td>

                        <td>

                            <input style="width: 120px;"
                            type="tel"
                            @if($item->status == 0) disabled @endif
                            class="form-control inventario-field qtd"
                            name="quantidade"
                            data-id="{{ $i->id }}"
                            data-field="quantidade"
                            value="{{ $i->quantidade != '' ? (!$i->produto->unidadeDecimal() ? number_format($i->quantidade, 0, '', '') : number_format($i->quantidade, 3, '.', '')) : '' }}">
                        </td>

                        <td>
                            <input style="width: 350px;"
                            type="text"
                            @if($item->status == 0) disabled @endif
                            class="form-control inventario-field"
                            name="observacao"
                            data-id="{{ $i->id }}"
                            data-field="observacao"
                            value="{{ $i->observacao }}">
                        </td>

                        <td>
                            <select @if($item->status == 0) disabled @endif class="form-control form-select inventario-field" name="estado" data-id="{{ $i->id }}" data-field="estado">
                                <option value="">Selecione</option>
                                @foreach(\App\Models\ItemInventario::estados() as $e)
                                <option @if($i->estado == $e) selected @endif value="{{ $e }}">{{ $e }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
            @if($item->status == 1) 
            <form id="form-acerto" action="{{ route('inventario.acerto') }}" method="POST">
                @csrf
                <input type="hidden" name="inventario_id" value="{{ $item->id }}">
                <div class="col-12 text-end">
                    <button type="button" disabled id="btnAcerto" class="btn btn-warning">
                        Acerto de estoque
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>
@section('js')
<script type="text/javascript">

    $(function(){
        validarQuantidades()
    })

    function validarQuantidades() {
        let tudoOk = true;

        $('.qtd').each(function () {
            let v = $(this).val().trim();

            if (v === "") {
                tudoOk = false;
                return false;
            }

        });

        $('#btnAcerto').prop('disabled', !tudoOk);
    }

    // dispara validação a cada digitação ou perda de foco
    $(document).on('keyup blur change', '.qtd', function () {
        validarQuantidades();
    });

    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('blur', '.inventario-field', function () {
            let $el   = $(this);
            let id    = $el.data('id');
            let campo = $el.data('field');
            let valor = $el.val();

            if (!id || !campo) return;

            $.post("{{ route('inventario.atualizar-item') }}", {
                id: id,
                campo: campo,
                valor: valor
            }).done(function (resp) {
                // console.log(resp);
            }).fail(function (err) {
                // console.log(err);

            });
        });

        $(document).on('change', 'select.inventario-field', function () {
            $(this).trigger('blur');
        });
    });

    $('#btnAcerto').on('click', function (e) {

        swal({
            title: "Confirmar acerto?",
            text: "Essa ação vai lançar o acerto de estoque. Deseja continuar?",
            icon: "warning",
            buttons: {
                cancel: "Cancelar",
                confirm: {
                    text: "Sim, confirmar",
                    closeModal: true
                }
            },
            dangerMode: true,
        }).then((confirmado) => {
            if (confirmado) {
                $('#form-acerto').submit();
            }
        });

    });

</script>
@endsection
@endsection
