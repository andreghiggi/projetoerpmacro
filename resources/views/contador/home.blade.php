@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h3>Painel</h3>
                <!-- <div class="row mb-2">
                    <div class="col-12">
                        <a href="{{ route('contador.empresa-create') }}" class="btn btn-success float-end">
                            <i class="ri-add-circle-fill"></i>
                            Nova Empresa
                        </a>
                    </div>
                </div> -->
                <div class="row">
                    <div class="col-md-6 col-6">
                        <h5>Empresas do contador <strong class="text-success">{{ sizeof(__empresasDoContador()) }}</strong></h5>                        
                    </div>
                    <!-- <div class="col-md-6 col-6">
                        <h6 class="float-end">Limite de empresas para cadastro: 
                            <strong class="text-danger">{{ Auth::user()->empresa->empresa->limite_cadastro_empresas }}</strong>
                        </h6>               
                    </div> -->
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

                            @foreach(__empresasDoContador() as $e)
                            <tr>
                                <td>{{ $e->empresa->nome }}</td>
                                <td>{{ $e->empresa->cpf_cnpj }}</td>
                                <td>
                                    @if($e->empresa->status)
                                    <i class="ri-checkbox-circle-fill text-success"></i>
                                    @else
                                    <i class="ri-close-circle-fill text-danger"></i>
                                    @endif
                                </td>
                                <td>{{ __data_pt($e->empresa->created_at) }}</td>
                                <td>
                                    @if($e->empresa->plano)
                                    {{ $e->empresa->plano->plano->nome }}
                                    @else
                                    <i class="ri-close-circle-fill text-danger"></i>

                                    @if(!$e->__planoPendente())
                                    <a class="btn btn-sm btn-danger" href="{{ route('contador-empresa.plano', [$e->empresa->id]) }}">
                                        atribuir
                                    </a>
                                    @else
                                    <span class="bg-warning p-1 text-white rounded">aguardando liberação</span>
                                    @endif

                                    @endif

                                </td>
                                <td>
                                    @if(Auth::user()->empresa->empresa->empresa_selecionada != $e->empresa->id)
                                    <a class="btn btn-success btn-sm" href="{{ route('contador.set-empresa', [$e->empresa->id]) }}" title="Selecionar empresa e visualizar os dados">
                                        <i class="ri-check-line"></i>
                                    </a>
                                    @endif
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