@extends('layouts.app', ['title' => 'Categorias VendiZap'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <a href="{{ route('vendizap-categorias.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Categoria
                    </a>
                </div>
                <hr class="mt-3">
                
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Imagem</th>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Data de cadastro</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>

                                    <td>
                                        @if(isset($item->imagem))
                                        <img class="img-60" src="{{ $item->imagem }}">
                                        @else
                                        <img class="img-60" src="/imgs/no-image.png">
                                        @endif
                                    </td>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->nome }}</td>
                                    <td>{{ $item->data_cadastro }}</td>

                                    <td>
                                        <a class="btn btn-warning btn-sm text-white" href="{{ route('vendizap-categorias.edit', [$item->id]) }}">
                                            <i class="ri-pencil-fill"></i>
                                        </a>

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

            </div>
        </div>
    </div>
</div>
@endsection

