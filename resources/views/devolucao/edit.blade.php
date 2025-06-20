@extends('layouts.app', ['title' => 'Editar Devolução'])
@section('content')

<div class="card mt-1">
    <div class="card-header">

        <h4>Editar Devolução</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('devolucao.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('devolucao.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('devolucao._forms_xml')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@section('js')
<script src="/js/devolucao.js"></script>
@endsection
@endsection
