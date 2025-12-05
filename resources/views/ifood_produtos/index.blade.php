@extends('layouts.app', ['title' => 'Produtos IFood'])

@section('content')

<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">

                <a class="btn btn-success px-3" href="{{ route('produtos.create', ['ifood=1']) }}">
                    <i class="ri-add-circle-fill"></i>
                    Novo Produto
                </a>
                <div class="table-responsive-sm mt-3">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Nome</th>
                                <th>ID</th>
                                <th>Status</th>
                                <th>Valor</th>
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
                                <td>{{ __moeda($item->valor) }}</td>

                                <td>
                                    <form action="{{ route('ifood-produtos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                        <a class="btn btn-warning btn-sm text-white" href="{{ route('ifood-produtos.edit', [$item->id]) }}">
                                            <i class="ri-pencil-fill"></i>
                                        </a>
                                        @csrf
                                        @method('delete')
                                        <button type="submit" title="Deletar" class="btn btn-danger btn-delete btn-sm"><i class="ri-delete-bin-2-line"></i></button>

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
