@extends('layouts.app', ['title' => 'Impressoras para Pedido'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('impressora_pedido_create')
                    <a href="{{ route('impressoras-pedido.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Impressora
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">

                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Descrição</th>
                                    <th>Total de itens</th>
                                    <th>Timeout de requisição</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Descrição">{{ $item->descricao }}</td>
                                    <td data-label="Total de itens">{{ sizeof($item->produtos) }}</td>
                                    <td data-label="Timeout de requisição">{{ $item->requisicao_segundos }}</td>
                                    <td data-label="Status">
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <form style="width: 100px;" action="{{ route('impressoras-pedido.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @can('impressora_pedido_edit')
                                            <a class="btn btn-warning btn-sm text-white" href="{{ route('impressoras-pedido.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('impressora_pedido_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
