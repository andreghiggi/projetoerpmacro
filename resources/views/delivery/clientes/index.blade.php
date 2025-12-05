@extends('layouts.app', ['title' => 'Clientes de Delivery'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::text('razao_social', 'Pesquisar por nome')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::text('telefone', 'Pesquisar por Telefone')
                            ->attrs(['class' => ''])
                            ->type('tel')
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                          
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('clientes-delivery.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nome</th>
                                    <th>Telefone</th>
                                    <th>Endereço principal</th>
                                    <th>Total de pedidos</th>
                                    <th>Status</th>
                                    <th>UID</th>
                                    <th width="15%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td width="500">{{ $item->razao_social }}</td>
                                    <td>{{ $item->telefone }}</td>
                                    <td>{{ $item->enderecoPrincipal ? $item->enderecoPrincipal->info : '--' }}</td>
                                    <td>{{ sizeof($item->pedidos) }}</td>
                                    <td>
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>{{ $item->uid }}</td>

                                    <td>
                                        <form action="{{ route('clientes-delivery.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf

                                            <a class="btn btn-warning btn-sm" href="{{ route('clientes-delivery.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>

                                            <a title="Pedidos" class="btn btn-dark btn-sm" href="{{ route('clientes-delivery.show', [$item->id]) }}">
                                                <i class="ri-send-plane-2-line"></i>
                                            </a>

                                            <a title="Endereços" class="btn btn-primary btn-sm" href="{{ route('clientes-delivery.enderecos', [$item->id]) }}">
                                                <i class="ri-road-map-line"></i>
                                            </a>
                                        </form>
                                    </td>
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
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
