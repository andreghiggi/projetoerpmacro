@extends('layouts.app', ['title' => 'Inventários'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('inventario_create')
                    <a href="{{ route('inventarios.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo Inventário
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-2">
                            {!!Form::select('usuario_id', 'Usuário', ['' => 'Todos'] + $usuarios->pluck('name', 'id')->all())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('status', 'Status', ['' => 'Todos', '1' => 'Ativos', '0' => 'Finalizados'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('inventarios.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>

                                    <th>#</th>
                                    <th>Início</th>
                                    <th>Fim</th>
                                    <th>Data de cadastro</th>
                                    <th>Status</th>
                                    <th>Tipo</th>
                                    <th>Usuário</th>
                                    <th>Itens apontados</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Código">{{ $item->numero_sequencial }}</td>
                                    <td data-label="Início">{{ __data_pt($item->inicio, 0) }}</td>
                                    <td data-label="Fim">{{ __data_pt($item->fim, 0) }}</td>
                                    <td data-label="Data de cadastro">{{ __data_pt($item->created_at) }}</td>
                                    <td data-label="Status">
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td data-label="Tipo">{{ $item->tipo }}</td>
                                    <td data-label="Usuário">{{ $item->usuario ? $item->usuario->name : '' }}</td>
                                    <td data-label="Itens apontados">
                                        {{ sizeof($item->itens) }}
                                    </td>
                                    <td>
                                        <form action="{{ route('inventarios.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @can('servico_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('inventarios.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan

                                            @csrf
                                            @can('servico_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan

                                            <a title="Apontar" class="btn btn-dark btn-sm" href="{{ route('inventarios.apontar', [$item->id]) }}">
                                                <i class="ri-barcode-box-line"></i>
                                            </a>

                                            <a title="Imprimir" class="btn btn-light btn-sm" href="{{ route('inventarios.imprimir', [$item->id]) }}">
                                                <i class="ri-printer-line"></i>
                                            </a>

                                            <a class="btn btn-primary btn-sm" href="{{ route('inventarios.duplicar', [$item->id]) }}" title="Duplicar inventário">
                                                <i class="ri-file-copy-line"></i>
                                            </a>

                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Nada encontrado</td>
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

