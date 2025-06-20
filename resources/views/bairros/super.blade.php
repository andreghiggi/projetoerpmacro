@extends('layouts.app', ['title' => 'Bairros'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="">
                    @csrf
                    <div class="col-md-12 mt-3">
                        <div class="table-responsive-sm">
                            <table class="table table-striped table-centered mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="100">Nome</th>
                                        <th width="100">Valor de entrega</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $item)
                                    <tr>
                                        <td>{{ $item->nome }}</td>
                                        <td>
                                            <input class="form-control" type="hidden" name="id[]" value="{{ $item->id }}">
                                            <input class="form-control moeda" type="tel" name="valor_entrega[]" value="{{ $item->valor_entrega ? __moeda($item->valor_entrega) : '' }}">
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Nada encontrado</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                    <div class="col-12">
                        <button class="btn btn-success float-end">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection