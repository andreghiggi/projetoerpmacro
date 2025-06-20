@extends('layouts.app', ['title' => 'Escritórios Contábeis'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4>Listando os cadastros de escritórios de empresas</h4>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::text('razao_social', 'Pesquisar por razão social')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('cnpj', 'Pesquisar por CNPJ')
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::select('cidade_id', 'Pesquisar por cidade')
                            ->options($cidade != null ? [$cidade->id => $cidade->info] : [])
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('escritorio-contabils') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>

                <div class="col-md-12 mt-3">
                    <h5>Total de registros: <strong>{{ $data->total() }}</strong></h5>

                    <div class="table-responsive-sm">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Razão social</th>
                                    <th>Nome fantasia</th>
                                    <th>CNPJ</th>
                                    <th>CPF</th>
                                    <th>Cidade</th>
                                    <th>Telefone</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td>{{ $item->razao_social }}</td>
                                    <td>{{ $item->nome_fantasia }}</td>
                                    <td>{{ $item->cnpj }}</td>
                                    <td>{{ $item->cpf }}</td>
                                    <td>{{ $item->cidade->info }}</td>
                                    <td>{{ $item->telefone }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                {!! $data->appends(request()->all())->links() !!}

            </div>
        </div>
    </div>
</div>
@endsection
