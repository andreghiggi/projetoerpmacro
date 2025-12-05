<div class="modal fade" id="modal-importar_nfe" aria-modal="true" role="dialog" style="overflow:scroll;" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Documentos NFe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="row m-3">
                <div class="col-md-3">
                    {!! Form::date('start_date_filtro', 'Data Inicial')->attrs(['class' => 'ignore']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::date('end_date_filtro', 'Data Final')->attrs(['class' => 'ignore']) !!}
                </div>

                <div class="col-md-3">
                    {!! Form::select('tipo_filtro', 'Tipo', [1 => 'Saída', 0 => 'Entrada'])->attrs(['class' => 'form-select']) !!}
                </div>
                <div class="col-md-3">
                    <br>
                    <button class="btn btn-dark btn-filtro px-3"><i class="ri-search-fill" style="margin-top: -16px"></i> Filtrar</button>
                </div>
            </div>
            <div class="table-responsive m-3">
                <table class="table mb-0 table-striped tbl-vendas">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Data</th>
                            <th>Razão Social</th>
                            <th>Valor Total</th>
                            <th>Chave</th>
                            <th>Nº NFe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="6">Filtre para buscar</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button id="btn-importar" type="button" class="btn btn-success px-5">Importar</button>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
   
</script>

@endsection
