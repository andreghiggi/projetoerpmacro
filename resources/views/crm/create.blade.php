@extends('layouts.app', ['title' => 'Novo Registro CRM'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Novo Registro CRM</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('crm.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('crm.store')
        ->id('form-crm')
        !!}
        <div class="pl-lg-4">
            @include('crm._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection


