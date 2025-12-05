@extends('layouts.app', ['title' => 'Boletos'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                @can('boleto_create')
                <div class="col-12">
                    <a href="{{ route('remessa-boleto.index') }}" class="btn btn-success">
                        <i class="ri-file-list-2-fill"></i>
                        Remessas de boletos
                    </a>
                </div>
                @endcan
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('cliente_id', 'Cliente')
                            ->attrs(['class' => 'select2'])
                            ->options($cliente != null ? [$cliente->id => $cliente->info] : [])
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
                            {!!Form::select('banco', 'Pesquisar por banco', ['' => 'Selecione'] + $contasBoleto->pluck('info', 'id')->all())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('boleto.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Banco</th>
                                    <th>Cliente</th>
                                    <th>Vencimento</th>
                                    <th>Status</th>
                                    <th>Valor</th>
                                    <th>Data de registro</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Banco">{{ $item->contaBoleto->banco }}</td>
                                    <td data-label="Cliente">{{ $item->contaReceber->cliente->info }}</td>
                                    <td data-label="Vencimento">{{ __data_pt($item->vencimento, 0) }}</td>
                                    <td data-label="Status">
                                        @if($item->contaReceber->status)
                                        <span class="btn btn-success position-relative me-lg-5 btn-sm">
                                            <i class="ri-checkbox-line"></i> Recebido
                                        </span>
                                        @else
                                        <span class="btn btn-warning position-relative me-lg-5 btn-sm">
                                            <i class="ri-alert-line"></i> Pendente
                                        </span>
                                        @endif
                                    </td>
                                    <td data-label="Valor">{{ __moeda($item->valor) }}</td>
                                    <td data-label="Data de registro">{{ __data_pt($item->created_at) }}</td>
                                    <td>
                                        <form style="width: 120px;" action="{{ route('boleto.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf
                                            <a target="_blank" title="Imprimir boleto" class="btn btn-dark btn-sm" href="{{ route('boleto.print', [$item->id]) }}">
                                                <i class="ri-printer-line"></i>
                                            </a>
                                            @if(!$item->contaReceber->status)
                                            @can('conta_receber_edit')
                                            <a title="Receber conta" href="{{ route('conta-receber.pay', $item->contaReceber->id) }}" class="btn btn-success btn-sm text-white">
                                                <i class="ri-money-dollar-box-line"></i>
                                            </a>
                                            @endcan
                                            @endif
                                            @can('boleto_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <br>
                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
