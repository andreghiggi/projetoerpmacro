@extends('layouts.app', ['title' => 'Registro CRM'])

@section('css')
<style type="text/css">
    @page { size: auto;  margin: 2mm; }

    @media print {
        .print{
            margin: 20px;
        }
    }
</style>
@endsection
@section('content')

<div class="card mt-1 print">

    <div class="card-body">
        <div class="pl-lg-4">

            <div class="ms">

                <div class="mt-3 d-print-none" style="text-align: right;">
                    <a href="{{ route('crm.index') }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6 col-6">
                        <h5>Assunto: <strong class="text-primary">{{ $item->assunto }}</strong></h5>
                    </div>
                    <div class="col-md-6 col-6">
                        @if($item->cliente)
                        <h5>Cliente: <strong class="text-primary">{{ $item->cliente->info }}</strong></h5>
                        @endif

                        @if($item->fornecedor)
                        <h5>Fornecedor: <strong class="text-primary">{{ $item->fornecedor->info }}</strong></h5>
                        @endif
                    </div>

                    <div class="col-md-6 col-6">
                        @if($item->cliente)
                        <h5>Endereço: <strong class="text-primary">{{ $item->cliente->endereco }}</strong></h5>
                        @endif

                        @if($item->fornecedor)
                        <h5>Endereço: <strong class="text-primary">{{ $item->fornecedor->endereco }}</strong></h5>
                        @endif
                    </div>

                    <div class="col-md-6 col-6">
                        @if($item->cliente)
                        <h5>Cidade: <strong class="text-primary">{{ $item->cliente->cidade->info }}</strong></h5>
                        @endif

                        @if($item->fornecedor)
                        <h5>Cidade: <strong class="text-primary">{{ $item->fornecedor->cidade->info }}</strong></h5>
                        @endif
                    </div>

                    <div class="col-md-3 col-6">
                        @if($item->cliente)
                        <h5>Telefone: <strong class="text-primary">{{ $item->cliente->telefone }}</strong></h5>
                        @endif

                        @if($item->fornecedor)
                        <h5>Telefone: <strong class="text-primary">{{ $item->fornecedor->telefone }}</strong></h5>
                        @endif
                    </div>

                    <div class="col-md-3 col-6">
                        @if($item->cliente)
                        <h5>Email: <strong class="text-primary">{{ $item->cliente->email }}</strong></h5>
                        @endif

                        @if($item->fornecedor)
                        <h5>Email: <strong class="text-primary">{{ $item->fornecedor->email }}</strong></h5>
                        @endif
                    </div>

                    <div class="col-md-2 col-4">
                        <h5>Status: 
                            @if($item->status == 'positivo')
                            <span class="badge bg-success">Positivo</span>
                            @elseif($item->status == 'bom')
                            <span class="badge bg-warning">Bom</span>
                            @else
                            <span class="badge bg-danger">Negativo</span>
                            @endif
                        </h5>
                    </div>

                    @if($item->funcionario)
                    <div class="col-md-4 col-4">
                        <h5>Funcionário: <strong class="text-primary">{{ $item->funcionario->nome }}</strong></h5>
                    </div>
                    @endif

                    <div class="col-md-2 col-4">
                        <h5>Data de cadastro: <strong class="text-primary">{{ __data_pt($item->created_at) }}</strong></h5>
                    </div>

                    <div class="col-md-2 col-4">
                        <h5>Data de retorno: <strong class="text-primary">{{ $item->data_retorno ? __data_pt($item->data_retorno, 0) : '--' }}</strong></h5>
                    </div>

                    <div class="col-md-2 col-4">
                        <h5>Data de entrega: <strong class="text-primary">{{ $item->data_entrega ? __data_pt($item->data_entrega, 0) : '--' }}</strong></h5>
                    </div>

                </div>
            </div>

            <a class="btn btn-primary btn-sm d-print-none" href="javascript:window.print()" ><i class="ri-printer-line d-print-none"></i>
                IMPRIMIR
            </a>

            @if($item->registro)
            @if($item->tipo_registro == 'orçamento')
            <a class="btn btn-dark btn-sm d-print-none" href="{{ route('orcamentos.edit', [$item->registro->id]) }}" >
                <i class="ri-eye-2-line d-print-none"></i>
                ORÇAMENTO
            </a>
            @else
            <a class="btn btn-dark btn-sm d-print-none" href="{{ route('nfe.edit', [$item->registro->id]) }}" >
                <i class="ri-eye-2-line d-print-none"></i>
                {{ strtoupper($item->tipo_registro) }}
            </a>
            @endif
            @endif

            <div class="row mt-4">

                {!!Form::open()
                ->post()
                ->route('crm-nota.store', [$item->id])
                !!}
                <div class="pl-lg-4 d-print-none">

                    <div class="col-md-12">
                        {!!Form::textarea('nota', 'Nota')
                        ->attrs(['rows' => '4'])
                        ->required()
                        !!}
                    </div>
                    <div class="col-12 mt-1" style="text-align: right;">
                        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar nota</button>
                    </div>

                </div>
                {!!Form::close()!!}
                <hr class="mt-2">
                <div class="col-12">
                    <h5 class="text-danger">Notas</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nota</th>
                                    <th class="d-print-none">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item->notas as $n)
                                <tr>
                                    <td style="width: 90%">{{ $n->nota }}</td>
                                    <td class="d-print-none">
                                        <form action="{{ route('crm.destroy-nota', $n->id) }}" method="post" id="form-{{$n->id}}">
                                            @method('delete')
                                            @csrf

                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
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
</div>
@endsection


