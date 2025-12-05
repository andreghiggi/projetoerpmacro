@extends('layouts.app', ['title' => 'Contigência'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    @can('contigencia_create')
                    <a href="{{ route('contigencia.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Ativar contigência
                    </a>
                    @endcan

                </div>
                <hr class="mt-3">

                <div class="col-md-12 mt-3 table-responsive">

                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
    <thead class="table-dark">
        <tr>
            <th>Data</th>
            <th>Motivo</th>
            <th>Tipo</th>
            <th>Documento</th>
            <th>Status</th>
            <th width="10%">Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $item)
        <tr>
            <td data-label="Data">{{ __data_pt($item->created_at) }}</td>
            <td data-label="Motivo">{{ $item->motivo }}</td>
            <td data-label="Tipo">{{ $item->tipo }}</td>
            <td data-label="Documento">{{ $item->documento }}</td>
            <td data-label="Status">
                @if($item->status)
                <i class="ri-checkbox-circle-fill text-success"></i>
                @else
                <i class="ri-close-circle-fill text-danger"></i>
                @endif
            </td>
            <td>
                @if($item->status)
                <a href="{{ route('contigencia.desactive', [$item->id]) }}" class="btn btn-danger btn-sm">
                    Desativar
                </a>
                @endif
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
                <br>

            </div>
        </div>
    </div>
</div>
@endsection