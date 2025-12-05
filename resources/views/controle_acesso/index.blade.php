@extends('layouts.app', ['title' => 'Controles de acesso'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    @can('controle_acesso_create')
                    <a href="{{ route('controle-acesso.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo controle de acesso
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
                            {!!Form::text('descricao', 'Pesquisar por descrição')
                            !!}
                        </div>
                        
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('controle-acesso.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>

                <div class="col-md-12 mt-3 table-responsive">
                    <h5>Total de registros: <strong class="text-success">{{ $data->total() }}</strong></h5>

                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Descrição</th>
                                    <th>Data de cadastro</th>
                                    <th>Data de atualização</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Descrição">{{ $item->description }}</td>
                                    <td data-label="Data de cadastro">{{ __data_pt($item->created_at) }}</td>
                                    <td data-label="Data de atualização">{{ __data_pt($item->updated_at) }}</td>
                                    <td>
                                        <form style="width: 100px;" action="{{ route('controle-acesso.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @can('controle_acesso_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('controle-acesso.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('controle_acesso_delete')
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
                <br>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection