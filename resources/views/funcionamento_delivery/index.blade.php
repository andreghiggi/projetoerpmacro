@extends('layouts.app', ['title' => 'Funcionamento Delivery'])

@section('content')

<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4>Configurar o(s) dia(s) funcionamento, horário de início e fim</h4>
                <hr>
                <a class="btn btn-success px-3" href="{{ route('funcionamento-delivery.create') }}">
                    <i class="ri-add-circle-fill"></i>
                    Novo horário de funcionamento
                </a>

                <hr class="mt-3">
                
                <div class="table-responsive-sm mt-3">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Dia</th>
                                <th>Início</th>
                                <th>Fim</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $item)
                            <tr>
                                <td>{{ $item->getDiaStr() }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->inicio)->format('H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->fim)->format('H:i') }}</td>
                                <td>
                                    <form action="{{ route('funcionamento-delivery.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                        @method('delete')
                                        <a class="btn btn-warning btn-sm" href="{{ route('funcionamento-delivery.edit', [$item->id]) }}">
                                            <i class="ri-pencil-fill"></i>
                                        </a>

                                        @csrf
                                        <button type="button" class="btn btn-delete btn-sm btn-danger">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Nada encontrado</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <br>    
            </div>
        </div>
    </div>
</div>

@endsection
