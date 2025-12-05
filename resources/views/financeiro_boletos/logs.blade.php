@extends('layouts.app', ['title' => 'Logs Boletos'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}

                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('empresa', 'Pesquisar por empresa')
                            ->options($empresa ? [$empresa->id => $empresa->info] : [])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data inicial venc.')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data final venc.')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('status', 'Status', ['' => 'Selecione', 1 => 'Aprovado', -1 => 'Rejeitado'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('financeiro-boleto.logs') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Empresa</th>
                                    <th>Tipo</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Descrição</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>

                                    <td>{{ $item->empresa ? $item->empresa->info : '--' }}</td>
                                    <td>{{ $item->tipo == 'geracao' ? 'Geração' : 'Confirmação'}}</td>
                                    <td>{{ __data_pt($item->created_at, 1) }}</td>
                                    <td>
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>{{ $item->descricao }}</td>
                                    
                                </tr>
                                @endforeach
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

