<div class="row g-2">

    <div class="col-md-4">
        <label for="" class="required">Token</label>
        <div class="input-group">
            <input readonly required type="text" class="form-control" id="api_token" name="token" value="{{ isset($item) ? $item->token : '' }}">
            <button type="button" class="btn btn-info" id="btn_token"><a class="ri-refresh-line text-white"></a></button>
        </div>
    </div>
    <div class="col-md-2">
        {!!Form::select('status', 'Status', [1 => 'Ativo', 0 => 'Desativado'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>
    <hr>
    <div class="row mt-2">
        <h5>Permissões de Acesso</h5>
        @if(!isset($item))
        <div class="row">
            <div class="col-lg-3 col-6">
                <input type="checkbox" class="form-check-input check_todos" style=" width: 25px; height: 25px;">
                <label class="form-check-label m-1" for="customCheck1">Marcar todos</label>
            </div>
        </div>
        @endif

        <div class="row">

            @foreach(\App\Models\ApiConfig::permissoes() as $key => $p)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <label>{{ $p }}</label>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach(\App\Models\ApiConfig::acoes() as $key2 => $acao)
                            @if(\App\Models\ApiConfig::inArrayPermissoes($key, $key2))
                            <div class="col-md-3 col-6">
                                <input name="permissoes_acesso[]" value="{{ $key }}.{{ $key2 }}" type="checkbox" class="form-check-input check-action" style=" width: 25px; height: 25px;" @isset($item) @if(sizeof($item->permissoes_acesso) > 0 && in_array($key . "." . $key2, $item->permissoes_acesso)) checked="true" @endif @endif>
                                <label class="form-check-label m-1" for="customCheck1">{{ $acao }}</label>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

    </div>
    

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="button" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>
