@extends('layouts.app', ['title' => 'CashBack Clientes'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <hr class="mt-3">
                <h5>Registros: <strong class="text-primary">{{ $item->razao_social }}</strong></h5>

                <label class="text-muted">Registros de CASHBACK</label>
                <div class="col-md-12 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Data</th>
                                    <th>Valor do crédito</th>
                                    <th>Percentual</th>
                                    <th>Valor da venda</th>
                                    <th>Data de expiração</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse($item->cashBacks as $c)
                                <tr>
                                    <td>{{ __data_pt($c->created_at, 1) }}</td>
                                    <td>{{ __moeda($c->valor_credito) }}</td>
                                    <td>{{ __moeda($c->valor_percentual) }}</td>
                                    <td>{{ __moeda($c->valor_venda) }}</td>

                                    <td>{{ __data_pt($c->data_expiracao, 0) }}</td>
                                    <td>
                                        @if($c->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-success">
                                    <td class="text-white">Total</td>
                                    <td class="text-white">{{ __moeda($item->cashBacks->sum('valor_credito')) }}</td>
                                    <td colspan="4"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <br>

                <label class="text-muted">Registros de Crédito Troca</label>
                <div class="col-md-12 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Troca</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse($item->creditosCliente as $c)
                                <tr>
                                    <td>{{ __data_pt($c->created_at) }}</td>
                                    <td>{{ __moeda($c->valor) }}</td>
                                    <td>
                                        @if($c->troca)
                                        #{{ $c->troca->numero_sequencial }}

                                        <a href="{{ route('trocas.show', [$c->troca->id]) }}" class="btn btn-sm btn-secondary">Ver troca</a>
                                        @endif
                                    </td>
                                    <td data-label="Status">
                                        @if($c->status == 1)
                                        <i class="ri-checkbox-circle-fill text-success"></i>

                                        <button class="btn btn-danger btn-sm btn-usar-credito" data-id="{{ $c->id }}">
                                            Crédito utilizado
                                        </button>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-success">
                                    <td class="text-white">Total</td>
                                    <td class="text-white">{{ __moeda($item->creditosCliente->sum('valor')) }}</td>
                                    <td colspan="2" class="text-white"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@section('js')
<script type="text/javascript">

    $(document).on('click', '.btn-usar-credito', function () {

        let id = $(this).data('id');
        let linha = $(this).closest('tr');

        swal({
            title: "Confirmar?",
            text: "Essa ação marcará o crédito como utilizado.",
            icon: "warning",
            buttons: ["Cancelar", "Sim"],
            dangerMode: true,
        })
        .then((confirm) => {
            if (!confirm) return;

            $.post("{{ route('clientes.alterar-status-credito') }}", {
                _token: "{{ csrf_token() }}",
                id: id
            })
            .done((res) => {
                console.log(res)
                if (res.status == 0) {

                    linha.find('td[data-label="Status"]').html(`
                        <i class="ri-close-circle-fill text-danger"></i>
                        `);

                    toastr.success("Crédito marcado como utilizado!");

                } else {
                    toastr.error(res.msg || "Erro ao alterar status.");
                }
            })
            .fail((err) => {
                console.log(err)
                toastr.error("Erro ao processar requisição.");
            });

        });
    });

</script>
@endsection
@endsection
