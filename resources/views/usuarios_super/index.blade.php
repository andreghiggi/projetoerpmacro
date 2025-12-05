@extends('layouts.app', ['title' => 'Usuários'])

@section('css')
<style type="text/css">
    .super{
        background: #27BCC2;
    }
    .super td{
        color: #fff;
    }
</style>
@endsection
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    <a href="{{ route('usuario-super.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo Usuário
                    </a>
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('empresa', 'Pesquisar por empresa')
                            ->options($empresa != null ? [$empresa->id => $empresa->info] : [])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::text('name', 'Pesquisar por nome')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::text('email', 'Pesquisar por email')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('tipo_contador', 'Tipo contador', ['' => 'Todos', 1 => 'Sim', 0 => 'Não'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('usuario-super.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Empresa</th>
                                    <th>Controle de acesso</th>
                                    <th>Tipo contador</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr class="@if($item->email == env('MAILMASTER')) super @endif">
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>

                                        {{ $item->empresa ? $item->empresa->empresa->nome : '' }}
                                        @if($item->email == env('MAILMASTER')) SUPER @endif
                                    </td>
                                    <td>
                                        {{ sizeof($item->roles) > 0 ? $item->roles->first()->description : '' }}
                                        @if($item->suporte) SUPORTE @endif
                                    </td>
                                    <td>
                                        @if($item->tipo_contador)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->email != env('MAILMASTER'))
                                        <form action="{{ route('usuario-super.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            <a class="btn btn-warning btn-sm" href="{{ route('usuario-super.edit', [$item->id]) }}">
                                                <i class="ri-edit-line"></i>
                                            </a> 
                                            @csrf
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>


                                            <a href="{{ route('impersonate.start', $item->id) }}" class="btn btn-info btn-sm" title="Entrar como este usuário">
                                                <i class="ri-login-circle-line"></i>
                                            </a>
                                        </form>
                                        @endif
                                    </td>
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
