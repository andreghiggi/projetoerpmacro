@extends('layouts.app', ['title' => 'Controle de Impressão'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @foreach($data as $item)
                    <div class="col-md-3 mt-1">
                        <a class="btn btn-lg btn-primary w-100" href="{{ route('impressao-pedido.show', [$item->id]) }}">
                            <i class="ri-printer-line"></i>
                            {{ $item->descricao }}
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection