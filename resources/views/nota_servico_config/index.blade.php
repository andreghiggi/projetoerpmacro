@extends('layouts.app', ['title' => 'Configuração NFSe'])
@section('content')

<div class="card mt-1">
    <div class="card-header">

        <h4>Configuração NFSe</h4>

    </div>
    @if($item != null)
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('nota-servico-config.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('nota_servico_config._forms')
        </div>
        {!!Form::close()!!}
    </div>

    @else
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('nota-servico-config.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('nota_servico_config._forms')
        </div>
        {!!Form::close()!!}
    </div>
    @endif
</div>
@endsection

@section('js')
<script src="/js/nfse.js"></script>
@endsection
