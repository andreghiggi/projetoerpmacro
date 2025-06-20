@extends('layouts.app', ['title' => 'Assinatura de Contrato'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Assinatura de Contrato</h4>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('assinar-contrato.store')
        !!}

        {!! $texto !!}
        

        <div class="col-md-5">
            {!!Form::checkbox('aceito', 'Li e aceitos os termos do contrato')
            ->required()!!}
        </div>
        <div class="col-12" style="text-align: right;">
            <button type="submit" class="btn btn-success px-5" id="btn-store">Confirmar</button>
        </div>

        {!!Form::close()!!}

    </div>
</div>
@endsection


