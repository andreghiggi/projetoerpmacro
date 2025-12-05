@extends('layouts.app', ['title' => 'Usuários'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                @can('usuarios_create')
                <div class="col-md-2">
                    <a href="{{ route('usuarios.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo Usuário
                    </a>
                </div>
                @endcan
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-4">
                            {!!Form::text('name', 'Pesquisar por nome')
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('usuarios.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Admin</th>
                                    <th>Status</th>
                                    <th>Controle de acesso</th>
                                    @if(__countLocalAtivo() > 1)
                                    <th>Locais de acesso</th>
                                    @endif
                                    <th width="15%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Nome">{{ $item->name }}</td>
                                    <td data-label="Email">{{ $item->email }}</td>
                                    <td data-label="Admin">
                                        @if($item->admin)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td data-label="Status">
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td data-label="Controle de acesso">{{ sizeof($item->roles) > 0 ? $item->roles->first()->description : '' }}</td>

                                    @if(__countLocalAtivo() > 1)
                                    <td data-label="Locais de acesso">
                                        @foreach($item->locais as $local)
                                        {{ $local->localizacao->descricao }} @if(!$loop->last) | @endif
                                        @endforeach
                                    </td>
                                    @endif

                                    <td>
                                        @if(__isAdmin())
                                        <form action="{{ route('usuarios.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 250px;">
                                            @method('delete')
                                            @can('usuarios_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('usuarios.edit', [$item->id]) }}">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('usuarios_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                            <a title="Logs de acesso" class="btn btn-dark btn-sm" href="{{ route('usuarios.show', [$item->id]) }}">
                                                <i class="ri-key-2-line"></i>
                                            </a>

                                            <a title="Alterar senha" class="btn btn-primary btn-sm" href="{{ route('usuarios.alterar-senha', [$item->id]) }}">
                                                <i class="ri-fingerprint-line"></i>
                                            </a>
                                        </form>
                                        @endif
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
