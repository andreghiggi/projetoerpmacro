@extends('layouts.app', ['title' => 'Empresas'])

@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h3>Empresas</h3>
                <div class="row mb-2">
                    <div class="col-12">
                        <a href="{{ route('contador-empresas.create') }}" class="btn btn-success float-end">
                            <i class="ri-add-circle-fill"></i>
                            Nova Empresa
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-6">
                        <h5>Empresas do contador <strong class="text-success">{{ sizeof(__empresasDoContador()) }}</strong></h5>                        
                    </div>
                    <div class="col-md-6 col-6">
                        <h6 class="float-end">Limite de empresas para cadastro: 
                            <strong class="text-danger">{{ Auth::user()->empresa->empresa->limite_cadastro_empresas }}</strong>
                        </h6>               
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Razão Social</th>
                                <th>CPF/CNPJ</th>
                                <th>Status</th>
                                <th>Data de cadastro</th>
                                <th>Plano</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($data as $e)
                            <tr>
                                <td>{{ $e->nome }}</td>
                                <td>{{ $e->cpf_cnpj }}</td>
                                <td>
                                    @if($e->status)
                                    <i class="ri-checkbox-circle-fill text-success"></i>
                                    @else
                                    <i class="ri-close-circle-fill text-danger"></i>
                                    @endif
                                </td>
                                <td>{{ __data_pt($e->created_at) }}</td>
                                <td>
                                    @if($e->plano)
                                    {{ $e->plano->plano->nome }}
                                    @else
                                    <i class="ri-close-circle-fill text-danger"></i>

                                    @endif

                                </td>
                                <td>
                                    <form action="{{ route('contador-empresas.destroy', $e->id) }}" method="post" id="form-{{$e->id}}" style="width: 120px">
                                        @method('delete')
                                        @csrf

                                        <a class="btn btn-warning btn-sm" href="{{ route('contador-empresas.edit', [$e->id]) }}">
                                            <i class="ri-pencil-fill"></i>
                                        </a>
                                        <button type="button" class="btn btn-delete btn-sm btn-danger">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        <a href="{{ route('impersonate-contador.start', $e->id) }}" class="btn btn-info btn-sm" title="Entrar como esta empresa">
                                            <i class="ri-login-circle-line"></i>
                                        </a>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection