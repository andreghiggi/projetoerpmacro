@extends('layouts.app', ['title' => 'Perfil'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Contador</h4>
    </div>

    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('contador.profile-update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('contador._forms_profile')
        </div>
        {!!Form::close()!!}
    </div>

</div>
@endsection
