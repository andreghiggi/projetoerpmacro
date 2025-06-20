@extends('layouts.app', ['title' => 'Nova MDFe'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Nova MDFe</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('mdfe.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('mdfe.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('mdfe._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

@section('js')
<script src="/js/mdfe.js"></script>
@endsection
