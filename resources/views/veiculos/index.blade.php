@extends('layouts.app', ['title' => 'Veículos'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('veiculos_create')
                    <a href="{{ route('veiculos.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo Veículo
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-4">
                            {!!Form::text('placa', 'Pesquisar por placa')
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('veiculos.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Placa</th>
                                    <th>Modelo</th>
                                    <th>Renavam</th>
                                    <th>Proprietário</th>
                                    <th>CPF/CNPJ</th>
                                    <th>Status</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Placa">{{ $item->placa }}</td>
                                    <td data-label="Modelo">{{ $item->modelo }}</td>
                                    <td data-label="Renavam">{{ $item->renavam }}</td>
                                    <td data-label="Proprietário">{{ $item->proprietario_nome }}</td>
                                    <td data-label="CPF/CNPJ">{{ $item->proprietario_documento }}</td>
                                    <td data-label="Status">
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <form style="width: 100px;" action="{{ route('veiculos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @can('veiculos_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('veiculos.edit', [$item->id]) }}">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('veiculos_delete')
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

                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
