@extends('layouts.app', ['title' => 'Natureza de Operação'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-header">

                <div style="text-align: right; margin-top: -5px;">
                    <a href="{{ route('empresas.index') }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <h5>Empresa <strong class="text-primary">{{ $empresa->nome }}</strong></h5>
                
                <div class="col-md-2">
                    <a href="{{ route('natureza-operacao-adm.create', ['empresa='. $empresa->id]) }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Natureza
                    </a>

                </div>
                <hr class="mt-3">
                
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Descrição</th>
                                    <th width="20%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td>{{ $item->descricao }}</td>
                                    <td>
                                        <form action="{{ route('natureza-operacao-adm.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')

                                            @can('natureza_operacao_edit')
                                            <a class="btn btn-warning btn-sm text-white" href="{{ route('natureza-operacao-adm.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            
                                            @csrf
                                            @can('natureza_operacao_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
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