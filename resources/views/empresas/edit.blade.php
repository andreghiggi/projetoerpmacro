@extends('layouts.app', ['title' => 'Editar Empresa'])


@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h3>Editar Cadastro da Empresa</h3>
        <div style="text-align: right;" class="">
            <a href="{{ route('empresas.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($infoCertificado)
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <h4>Dados do certificado</h4>
                    <h6>serial <strong>{{ $infoCertificado['serial'] }}</strong></h6>
                    <h6>início <strong>{{ $infoCertificado['inicio'] }}</strong></h6>
                    <h6>expiração <strong>{{ $infoCertificado['expiracao'] }}</strong></h6>
                    <h6>ID <strong>{{ $infoCertificado['id'] }}</strong></h6>
                </div>
            </div>
        </div>
        @endif
        {!!Form::open()->fill($item)
        ->put()
        ->route('empresas.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('empresas._forms', ['edit' => true])
        </div>
        {!!Form::close()!!}

        <form style="width: 330px" action="{{ route('empresas.destroy-produtos', $item->id) }}" method="post" id="form-{{$item->id}}">
            @csrf
            @method('delete')

            <button class="btn btn-danger btn-delete">
                <i class="ri-delete-bin-line"></i>
                Remover todas os produtos ({{ sizeof($item->produtos) }})
            </button>
        </form>
    </div>
</div>
@endsection
