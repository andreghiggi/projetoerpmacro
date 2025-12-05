@extends('layouts.app', ['title' => 'Margem de Comissão'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    @can('categoria_servico_create')
                    <a href="{{ route('comissao-margem.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Margem Comissão
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                
                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Margem</th>
                                    <th>Percentual de comissão</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Margem">{{ $item->margem }}</td>
                                    <td data-label="Percentual de comissão">{{ $item->percentual }}</td>
                                    <td>
                                        <form style="width: 100px;" action="{{ route('comissao-margem.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @can('categoria_servico_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('comissao-margem.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('categoria_servico_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
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

                        <br>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


