@extends('layouts.app', ['title' => 'Mensagens CRM Padrão'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">

                    <a href="{{ route('mensagem-padrao-crm.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Mensagem
                    </a>

                </div>
                
                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    
                                    <th>Título</th>
                                    <th>Status</th>
                                    <th>Tipo</th>
                                    <th>Data de cadastro</th>
                                    
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    
                                    <td>{{ $item->titulo }}</td>

                                    <td>
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>{{ $item->_tipo() }}</td>
                                    <td>{{ __data_pt($item->created_at) }}</td>

                                    <td>
                                        <form action="{{ route('mensagem-padrao-crm.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf

                                            <a class="btn btn-warning btn-sm" href="{{ route('mensagem-padrao-crm.edit', [$item->id]) }}">
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
                
            </div>
        </div>
    </div>
</div>
@endsection


