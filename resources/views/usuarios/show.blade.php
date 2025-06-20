@extends('layouts.app', ['title' => 'Acessos'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4>Acessos do usuário <strong class="text-primary">{{ $item->name }}</strong></h4>
                <div style="text-align: right; margin-top: -35px;">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
                <hr class="mt-3">

                <div class="table-responsive">
                    <table class="table table-striped table-centered mb-0 table-infoValidade">
                        <thead class="table-dark">
                            <tr>
                                <th>IP</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                           @foreach($item->acessos as $i)
                           <tr>
                               <td>{{ $i->ip }}</td>
                               <td>{{ __data_pt($i->created_at) }}</td>
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