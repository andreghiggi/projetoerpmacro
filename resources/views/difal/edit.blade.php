@extends('layouts.app', ['title' => 'Editar Difal'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Difal</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('difal.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('difal.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('difal._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
