@extends('layouts.app', ['title' => 'Configuração Usuário Emissão'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('config_fiscal_usuario_create')
                    <a href="{{ route('config-fiscal-usuario.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Configuração
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
                            {!!Form::select('usuario_id', 'Usuário')
                            ->options($usuario != null ? [$usuario->id => $usuario->name] : [])
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('config-fiscal-usuario.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Usuário</th>
                                    <th>Número de série NFCe</th>
                                    <th>Último número de NFCe</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Usuário">{{ $item->usuario->name }}</td>
                                    <td data-label="Número de série NFCe">{{ $item->numero_serie_nfce }}</td>
                                    <td data-label="Último número de NFCe">{{ $item->numero_ultima_nfce }}</td>

                                    <td>
                                        <form style="width: 100px;" action="{{ route('config-fiscal-usuario.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @can('config_fiscal_usuario_edit')
                                            <a class="btn btn-warning btn-sm text-white" href="{{ route('config-fiscal-usuario.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('config_fiscal_usuario_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
