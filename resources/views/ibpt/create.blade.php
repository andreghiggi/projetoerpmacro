@extends('layouts.app', ['title' => 'Nova Importação'])

@section('content')

<div class="mt-3">
    <div class="row">
        {!!Form::open()
        ->post()
        ->route('ibpt.store')
        ->multipart()
        !!}
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h4>Nova importação</h4>
                    <div style="text-align: right; margin-top: -35px;">
                        <a href="{{ route('ibpt.index') }}" class="btn btn-danger btn-sm px-3">
                            <i class="ri-arrow-left-double-fill"></i>Voltar
                        </a>
                    </div>
                    <hr>
                    @include('ibpt._forms')
                </div>
            </div>
        </div>
        {!!Form::close()!!}
    </div>
</div>

@endsection
