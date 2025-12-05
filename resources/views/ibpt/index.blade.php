@extends('layouts.app', ['title' => 'IBPT'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">

                <a class="btn btn-success px-3" href="{{ route('ibpt.create') }}">
                    <i class="ri-add-circle-fill"></i>
                    Nova Importação
                </a>
                <div class="table-responsive-sm mt-3">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>UF</th>
                                <th>Versão</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $item)
                            <tr>
                                <td>{{ $item->uf }}</td>
                                <td>{{ $item->versao }}</td>
                                <td>{{ __data_pt($item->created_at) }}</td>

                                <td>
                                    <form action="{{ route('ibpt.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" title="Deletar" class="btn btn-danger btn-delete btn-sm"><i class="ri-delete-bin-2-line"></i></button>

                                        <a title="Ver tabela" href="{{ route('ibpt.show', [$item->id]) }}" class="btn btn-sm btn-dark">
                                            <i class="ri-eye-line"></i>
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
