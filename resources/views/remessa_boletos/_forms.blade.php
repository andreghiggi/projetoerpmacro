<div class="row g-2">
    <div class="col-md-12 mt-3">
        <div class="table-responsive-sm">
            <table class="table table-striped table-centered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>
                            <div class="form-check form-checkbox-danger mb-2">
                                <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                            </div>
                        </th>
                        <th>Cliente</th>
                        <th>Valor</th>
                        <th>Data de registro</th>
                        <th>Data de vencimento</th>
                        <th>Banco</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                    <tr>
                        <td>
                            <div class="form-check form-checkbox-danger mb-2">
                                <input class="form-check-input check-delete" type="checkbox" name="boleto_id[]" value="{{ $item->id }}">
                            </div>
                        </td>
                        <td>{{ $item->contaReceber->cliente->info }}</td>
                        <td>{{ __moeda($item->valor) }}</td>
                        <td>{{ __data_pt($item->created_at) }}</td>
                        <td>{{ __data_pt($item->vencimento, 0) }}</td>
                        <td>{{ $item->contaBoleto->banco }}</td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Nada encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Gerar</button>
    </div>
</div>

@section('js')
<script type="text/javascript">
    $("#select-all-checkbox").on("click", function (e) {
        if($(this).is(':checked')){
            $('.check-delete').prop('checked', 1)
        }else{
            $('.check-delete').prop('checked', 0)
        }

    });
</script>
@endsection
