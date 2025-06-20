@extends('layouts.app', ['title' => 'Editar Token API'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Token API</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('config-api.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('config-api.update', [$item->id])
        ->id('form')
        !!}
        <div class="pl-lg-4">
            @include('api_config._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection

@section('js')
<script type="text/javascript" src="/js/config_api.js"></script>
@endsection
