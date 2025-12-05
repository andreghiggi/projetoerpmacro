@extends('layouts.app', ['title' => 'Arquivos XML NFe Entrada'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Arquivos XML NFe Entrada</h4>
        
    </div>
    <div class="card-body">
        <hr class="mt-3">
        <div class="col-lg-12">
            {!!Form::open()->fill(request()->all())
            ->get()
            !!}
            <div class="row mt-3">
                <div class="col-md-2">
                    {!!Form::date('start_date', 'Data inicial')
                    !!}
                </div>
                <div class="col-md-2">
                    {!!Form::date('end_date', 'Data final')
                    !!}
                </div>

                <div class="col-lg-4 col-12">
                    <br>
                    <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                </div>
            </div>
            {!!Form::close()!!}

            <div class="col-md-12 mt-3">
                <div class="table-responsive">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Fornecedor</th>
                                <th>Número</th>
                                <th>Chave</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            @if(file_exists(public_path("xml_nfe/").$item->chave.".xml"))
                            <tr>
                                <td data-label="Fornecedor">{{ $item->fornecedor ? $item->fornecedor->info : '--' }}</td>
                                <td data-label="Número">{{ $item->numero }}</td>
                                <td data-label="Chave">{{ $item->chave }}</td>
                                <td data-label="Valor">{{ __moeda($item->total) }}</td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>

                        @if(sizeof($data) > 0)
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right;">Total</td>
                                <td>{{ __moeda($data->sum('total')) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>

                </div>
            </div>

            @if(sizeof($data) > 0)
            <br>
            <div class="row">
                <div class="col-md-2 col-12 mt-1">
                    <form method="get" action="{{ route('nfe-entrada-xml.download') }}">
                        <input type="hidden" name="start_date" value="{{ request()->start_date }}">
                        <input type="hidden" name="end_date" value="{{ request()->end_date }}">
                        <button class="btn btn-dark w-100">
                            <i class="ri-file-zip-line"></i>
                            Download Zip
                        </button>
                    </form>
                </div>
                <div class="col-md-7 col-12"></div>
                @if($escritorio != null && $escritorio->email)
                <div class="col-md-3 col-12 text-end mt-1">
                    <form method="get" action="{{ route('nfe-entrada-xml.envio-contador') }}">
                        <input type="hidden" name="start_date" value="{{ request()->start_date }}">
                        <input type="hidden" name="end_date" value="{{ request()->end_date }}">
                        <input type="hidden" name="estado" value="{{ request()->estado }}">
                        <input type="hidden" name="local_id" value="{{ request()->local_id }}">
                        <button class="btn btn-success w-100">
                            <i class="ri-mail-send-fill"></i>
                            Enviar XML para o contador
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @else
            <p class="text-danger">Filtre por período para buscar os arquivos</p>
            @endif
        </div>
    </div>
</div>

@endsection
