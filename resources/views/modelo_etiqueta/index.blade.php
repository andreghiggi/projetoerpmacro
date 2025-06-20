@extends('layouts.app', ['title' => 'Modelos de etiqueta'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">

                    <a href="{{ route('modelo-etiquetas.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo modelo
                    </a>

                    @if($importar)
                    <a href="{{ route('modelo-etiquetas.import') }}" class="btn btn-dark">
                        <i class="ri-chat-upload-line"></i>
                        Importar Modelos
                    </a>
                    @endif

                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::text('nome', 'Pesquisar por nome')
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('modelo-etiquetas.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
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
                                    <th>Altura</th>
                                    <th>Largura</th>
                                    <th>Observação</th>
                                    <th>Tipo</th>
                                    <th width="20%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>

                                    <td>{{ $item->nome }}</td>
                                    <td>{{ $item->altura }}</td>
                                    <td>{{ $item->largura }}</td>
                                    <td>{{ $item->observacao }}</td>
                                    <td>{{ $item->tipo == 'simples' ? 'Simples' : 'Gôndola' }}</td>
                                    <td>
                                        <form action="{{ route('modelo-etiquetas.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf

                                            <a class="btn btn-warning btn-sm" href="{{ route('modelo-etiquetas.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>

                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>

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
                        <br>
                        
                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection


