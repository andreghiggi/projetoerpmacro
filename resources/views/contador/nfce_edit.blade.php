@extends('layouts.app', ['title' => 'Editar NFCe'])
@section('content')

<div class="card mt-1">
    <div class="card-header">

        <h4>Editar NFCe</h4>
        <div style="text-align: right; margin-top: -35px;">
            
            <a href="{{ route('contador-empresa.nfce') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('contador-empresa-nfce.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('nfce._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>

@section('js')
<script src="/js/nfce.js"></script>
@endsection
@endsection
