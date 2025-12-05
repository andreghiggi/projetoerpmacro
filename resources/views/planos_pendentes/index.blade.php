@extends('layouts.app', ['title' => 'Planos Pendentes'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                
                <hr class="mt-3">
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Empresa</th>
                                    <th>Contador</th>
                                    <th>Plano</th>
                                    <th>Valor</th>
                                    <th>Data de cadastro</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>

                                    <td>{{ $item->empresa->nome }}</td>
                                    <td>{{ $item->contador->nome }}</td>
                                    <td>{{ $item->plano->nome }}</td>
                                    <td>{{ __moeda($item->valor) }}</td>
                                    <td>{{ __data_pt($item->created_at, 1) }}</td>
                                    <td>
                                        <form action="{{ route('planos-pendentes.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf

                                            <a class="btn btn-success btn-sm" href="{{ route('planos-pendentes.edit', [$item->id]) }}">
                                                <i class="ri-check-fill"></i>
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
                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}

            </div>
        </div>
    </div>
</div>

@endsection

