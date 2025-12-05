@extends('layouts.app', ['title' => 'Cidades'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    <a href="{{ route('cidades.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Cidade
                    </a>
                </div>
                <hr class="mt-3">
                {!! Form::open()->fill(request()->all())->get() !!}
                <div class="row">
                    <div class="col-md-5">
                        {!! Form::text('nome', 'Pesquisar por nome') !!}
                    </div>
                    <div class="col-md-6 text-left ">
                        <br>
                        <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                        <a id="clear-filter" class="btn btn-danger" href="{{ route('cidades.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                    </div>
                </div>
                {!! Form::close() !!}
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Cidade</th>
                                    <th>UF</th>
                                    <th>Código IBGE</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td>{{ $item->nome }}</td>
                                    <td>{{ $item->uf }}</td>
                                    <td>{{ $item->codigo }}</td>
                                    <td>
                                        <form action="{{ route('cidades.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            <a class="btn btn-warning btn-sm" href="{{ route('cidades.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
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
