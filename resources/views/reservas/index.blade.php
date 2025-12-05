@extends('layouts.app', ['title' => 'Reservas'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('reserva_create')
                    <a href="{{ route('reservas.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Reserva
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('cliente_id', 'Cliente')
                            ->attrs(['class' => 'select2'])
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
                            {!!Form::select('estado', 'Estado',
                            ['pendente' => 'Pendente',
                            'iniciado' => 'Iníciado',
                            'finalizado' => 'Finalizado',
                            'cancelado' => 'Cancelado',
                            '' => 'Todos'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('reservas.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="row">

                        @foreach($data as $item)
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <strong class="text-success">#{{ $item->numero_sequencial }}</strong>
                                </div>
                                <div class="card-body">
                                    <h6>Data checkin: <strong>{{ __data_pt($item->data_checkin, 0) }}</strong></h6>
                                    <h6>Data checkout: <strong>{{ __data_pt($item->data_checkout, 0) }}</strong></h6>
                                    <h6>Cliente: <strong>{{ $item->cliente->info }}</strong></h6>
                                    <h6>Acomodação: <strong>{{ $item->acomodacao->info }}</strong></h6>
                                    <h6>Qtd. hospedes: <strong>{{ $item->total_hospedes }}</strong></h6>
                                    <span class="badge bg-{{ $item->colorStatus() }}">{{ strtoupper($item->estado) }}</span>
                                </div>

                                <div class="card-footer">
                                    <a href="{{ route('reservas.show', [$item->id]) }}" class="btn btn-dark w-100">
                                        <i class="ri-eye-2-line"></i>
                                        Visualizar reserva
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
