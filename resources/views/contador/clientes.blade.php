@extends('layouts.app', ['title' => 'Clientes'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-5">
                            {!!Form::text('razao_social', 'Pesquisar por nome')
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::text('cpf_cnpj', 'Pesquisar por CPF/CNPJ')
                            ->attrs(['class' => 'cpf_cnpj'])
                            ->type('tel')
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                          
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('contador-empresa.clientes') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Razão Social</th>
                                    <th>CPF/CNPJ</th>
                                    <th>Cidade</th>
                                    <th>Endereço</th>
                                    <th>CEP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td width="500">{{ $item->razao_social }}</td>
                                    <td>{{ $item->cpf_cnpj }}</td>
                                    <td>{{ $item->cidade->info }}</td>
                                    <td>{{ $item->endereco }}</td>
                                    <td>{{ $item->cep }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
