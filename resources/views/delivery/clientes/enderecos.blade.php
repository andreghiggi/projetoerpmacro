@extends('layouts.app', ['title' => 'Endereços'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                
                <hr class="mt-3">
                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Rua</th>
                                    <th>Número</th>
                                    <th>Bairro</th>
                                    <th>Referência</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cliente->enderecos as $item)
                                <tr>
                                    <td>{{ $item->rua }}</td>
                                    <td>{{ $item->numero }}</td>
                                    <td>{{ $item->bairro->nome }}</td>
                                    <td>{{ $item->referencia }}</td>
                                    <td>{{ strtoupper($item->tipo) }}</td>
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

            </div>
        </div>
    </div>
</div>
@endsection
