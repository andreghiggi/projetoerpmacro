@extends('layouts.app', ['title' => 'Controle de pedidos'])
@section('css')
<style type="text/css">

</style>
@endsection
@section('content')

<div class="mt-3">
    <div class="row append">
            
    </div>
</div>

<div class="modal fade" id="modal-item" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('pedidos-cardapio.store') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript" src="/js/controle_pedidos.js"></script>
@endsection

