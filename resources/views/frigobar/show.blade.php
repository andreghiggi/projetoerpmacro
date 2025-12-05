@extends('layouts.app', ['title' => 'Frigobar'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Frigobar <strong>{{ $item->modelo }}</strong></h4>
        <h5>Acomodação: <strong class="text-danger">{{ $item->acomodacao->info }}</strong></h5>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('frigobar.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('frigobar.store-default', [$item->id])
        !!}
        <div class="pl-lg-4">

            <div class="row g-2">

                <div class="table-responsive">
                    <table class="table table-dynamic">
                        <thead class="table-dark">
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(sizeof($item->padraoProdutos) > 0)
                            @foreach($item->padraoProdutos as $p)
                            <tr class="dynamic-form">

                                <td style="width: 700px">

                                    <select required class="form-control select2 produto_id" name="produto_id[]" id="inp-produto_id">
                                        <option value="{{ $p->produto_id }}">{{ $p->produto->nome }} | R$ {{ __moeda($p->produto->valor_unitario) }}</option>
                                    </select>
                                </td>
                                <td style="width: 180px">
                                    <input type="tel" value="{{ $p->quantidade }}" class="form-control" data-mask-reverse="true" data-mask="0000.00" name="quantidade[]" required>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-remove-tr">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr class="dynamic-form">

                                <td style="width: 700px">
                                    <select required class="form-control select2 produto_id" name="produto_id[]" id="inp-produto_id">
                                    </select>
                                </td>
                                <td style="width: 180px">
                                    <input type="tel" class="form-control quantidade" name="quantidade[]" required data-mask-reverse="true" data-mask="0000.00">
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-remove-tr">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="row col-12 col-lg-2 mt-3">
                    <br>
                    <button type="button" class="btn btn-dark btn-add-tr-prod px-2">
                        <i class="ri-add-fill"></i>
                        Adicionar Produto
                    </button>
                </div>


                <hr class="mt-4">
                <div class="col-12" style="text-align: right;">
                    <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
                </div>
            </div>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript" src="/js/frigobar.js"></script>
@endsection


