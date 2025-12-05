@extends('layouts.app', ['title' => 'Catálogos IFood'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Catálogos IFood</h4>
        
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                @foreach($data as $item)
                <div class="card">
                    <div class="card-header">
                        {{ $item->context[0] }}
                    </div>
                    <div class="card-body">
                        <p class="text-muted font-weight-bold">status: 
                            <strong class="text-danger">{{ $item->status }}</strong>
                        </p>
                        <p class="text-muted font-weight-bold">ID: 
                            <strong class="text-danger">{{ $item->catalogId }}</strong>
                        </p>
                        <p class="text-muted font-weight-bold">última modificação: 
                            <strong class="text-danger">{{ \Carbon\Carbon::parse($item->modifiedAt)->format('d/m/Y H:i') }}</strong>
                        </p>
                        <p class="text-muted font-weight-bold">Grupo ID: 
                            <strong class="text-danger">{{ $item->groupId }}</strong>
                        </p>
                    </div>
                    <div class="card-footer">
                        @if($config->catalogId != $item->catalogId)
                        <a href="{{ route('ifood-catalogos.definir', [$item->catalogId]) }}" class="btn btn-success w-100">
                            <i class="la la-check"></i>
                            Definir catálogo
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
