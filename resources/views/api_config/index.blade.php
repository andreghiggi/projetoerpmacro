@extends('layouts.app', ['title' => 'Configuração de API'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <a href="{{ route('config-api.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo Token
                    </a>

                    <a href="{{ route('config-api.logs') }}" class="btn btn-dark float-end">
                        <i class="ri-survey-fill"></i>
                        Logs
                    </a>
                </div>
                <hr class="mt-3">
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Token</th>
                                    <th>Status</th>
                                    <th>Data de cadastro</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td>{{ $item->token }}</td>
                                    <td>
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>{{ __data_pt($item->created_at) }}</td>
                                    
                                    <td>
                                        <form action="{{ route('config-api.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 200px">
                                            @method('delete')
                                            <a class="btn btn-warning btn-sm text-white" href="{{ route('config-api.edit', [$item->id]) }}">
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
                                    <td colspan="3" class="text-center">Nada encontrado</td>
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

