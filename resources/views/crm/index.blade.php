@extends('layouts.app', ['title' => 'CRM'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('crm_create')
                    <a href="{{ route('crm.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo Registro
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3 g-1">

                        <div class="col-md-3">
                            {!!Form::select('cliente_id', 'Cliente')
                            ->options($cliente != null ? [$cliente->id => $cliente->info] : [])
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::select('fornecedor_id', 'Fornecedor')
                            ->options($fornecedor != null ? [$fornecedor->id => $fornecedor->info] : [])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('funcionario_id', 'Vendedor')
                            ->options($funcionario != null ? [$funcionario->id => $funcionario->nome] : [])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data inicial')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data final')
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('status', 'Status', ['' => 'Todos'] + App\Models\CrmAnotacao::getStatus())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        
                        <div class="col-lg-3 col-12">
                            <br>

                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('crm.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="row mb-2" style="float: right;">
                        <form method="get" id="form-print" action="{{ route('crm.print') }}">
                            <input type="hidden" name="excel" id="inp-excel" value="-1">
                            <input type="hidden" name="cliente_id" value="{{ request()->cliente_id }}">
                            <input type="hidden" name="fornecedor_id" value="{{ request()->fornecedor_id }}">
                            <input type="hidden" name="funcionario_id" value="{{ request()->funcionario_id }}">
                            <input type="hidden" name="start_date" value="{{ request()->start_date }}">
                            <input type="hidden" name="end_date" value="{{ request()->end_date }}">
                            <input type="hidden" name="status" value="{{ request()->status }}">

                            <button type="button" class="btn btn-sm btn-primary btn-print">
                                <i class="ri-printer-line"></i>
                                Imprimir
                            </button>
                            <button type="button" class="btn btn-sm btn-dark btn-excel">
                                <i class="ri-file-excel-2-fill"></i>
                                Exportar excel
                            </button>
                        </form>
                    </div>
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Fornecedor</th>
                                    <th>Vendedor</th>
                                    <th>Status</th>
                                    <th>Assunto</th>
                                    <th>Conclusão</th>
                                    <th>Data de cadastro</th>
                                    <th>Data de retorno</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td>{{ $item->cliente ? $item->cliente->info : '--' }}</td>
                                    <td>{{ $item->fornecedor ? $item->fornecedor->info : '--' }}</td>
                                    <td>{{ $item->funcionario ? $item->funcionario->nome : '--' }}</td>
                                    <td>

                                        @if($item->status == 'positivo')
                                        <span class="badge bg-success">Positivo</span>
                                        @elseif($item->status == 'bom')
                                        <span class="badge bg-warning">Bom</span>
                                        @elseif($item->status == 'negativo')
                                        <span class="badge bg-danger">Negativo</span>
                                        @else
                                        @endif
                                    </td>
                                    <td>{{ $item->assunto }}</td>
                                    <td>{{ $item->conclusao }}</td>
                                    <td style="width: 150px;">{{ __data_pt($item->created_at) }}</td>
                                    <td style="width: 150px;">{{ $item->data_retorno ? __data_pt($item->data_retorno, 0) : '--' }}</td>

                                    <td width="300">
                                        <form action="{{ route('crm.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf
                                            
                                            @if($item->estado != 'aprovada')
                                            @can('crm_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('crm.edit', $item->id) }}">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            @endcan

                                            @can('crm_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                            @endif

                                            <a title="Ver resposta" class="btn btn-primary btn-sm" href="{{ route('crm.show', $item->id) }}">
                                                <i class="ri-eye-2-line"></i>
                                            </a>
                                            
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <br>
                    {!! $data->appends(request()->all())->links() !!}
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<script type="text/javascript">
    $('.btn-excel').click(() => {
        $('#inp-excel').val(1)
        $('#form-print').submit()
    })

    $('.btn-print').click(() => {
        $('#inp-excel').val(-1)
        $('#form-print').submit()
    })
</script>
@endsection


