@extends('layouts.app', ['title' => 'Categorias de Produto IFood'])

@section('content')

<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">

                <a class="btn btn-success px-3" href="{{ route('ifood-categoria-produtos.create') }}">
                    <i class="ri-add-circle-fill"></i>
                    Nova Categoria
                </a>
                <div class="table-responsive-sm mt-3">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Nome</th>
                                <th>ID</th>
                                <th>Status</th>
                                <th>Template</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $item)
                            <tr>
                                <td>{{ $item->nome }}</td>
                                <td>{{ $item->ifood_id }}</td>
                                <td>
                                    @if($item->status == 'AVAILABLE')
                                    <i class="ri-checkbox-circle-fill text-success"></i>
                                    @else
                                    <i class="ri-close-circle-fill text-danger"></i>
                                    @endif
                                </td>

                                <td>{{ $item->template }}</td>

                                <td>
                                    <form action="{{ route('ifood-categoria-produtos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" title="Deletar" class="btn btn-danger btn-delete btn-sm"><i class="ri-delete-bin-2-line"></i></button>
                                        <a class="btn btn-warning btn-sm text-white" href="{{ route('ifood-categoria-produtos.edit', [$item->id]) }}">
                                            <i class="ri-pencil-fill"></i>
                                        </a>
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

@endsection
