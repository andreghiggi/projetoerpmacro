@extends('layouts.app', ['title' => 'Atribuir Serviço ao Funcionário'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Atribuir serviço ao funcionário</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('funcionarios.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('funcionarios.atribuir-servico')
        !!}
        @csrf
        <div class="pl-lg-4">
            <div class="row g-3">
                <h4>Funcionário: <strong class="text-success">{{ $item->nome }}</strong></h4>
                <input type="hidden" name="funcionario_id" value="{{ $item->id }}">
                <div class="col-md-4">
                    {!!Form::select('servico_id', 'Serviços')
                    !!}
                </div>
                <div class="col-md-2">
                    <br>
                    <button type="submit" class="btn btn-success px-5">
                        <i class="ri-play-list-add-fill"></i>
                        Atribuir
                    </button>
                </div>
            </div>
        </div>
        {!!Form::close()!!}
        <hr class="mt-3">

        <div class="row">
            <div class="table-responsive-sm">
                <table class="table table-centered">
                    <thead class="table-dark">
                        <tr>
                            <th width="95%">Serviço</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $item)
                        <tr>
                            <td>{{ $item->servico ? $item->servico->nome : '' }}</td>
                            <td>
                                <form action="{{ route('funcionarios.deletarAtribuicao', $item->id) }}" method="post" id="form-{{$item->id}}">
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
@endsection
