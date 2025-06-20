
<div class="modal fade" id="modal-edit-endereco" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <form method="post" action="{{ route('food.endereco-update') }}">
        <input type="hidden" name="link" value="{{ $config->loja_id }}">
        <input type="hidden" id="endereco_id" name="endereco_id">

        @csrf
        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLongTitle">Editar endereço</h4>
          <button style="margin-top: -30px" type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" style="color: #999">x</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-12 col-md-8">
              <label>Rua:</label>
              <input required type="text" class="" id="rua" name="rua" placeholder="Rua">
            </div>

            <div class="col-4 col-md-4">
              <label>Número:</label>
              <input required type="text" class="" id="numero" name="numero" placeholder="Nº">
            </div>

            <div class="col-8 col-md-6">
              <label>Bairro:</label>
              <div class="fake-select">
                <i class="lni lni-chevron-down"></i>
                <select required class="w-100 _bairro_id" id="bairro_id" name="bairro_id">
                  @foreach($bairros as $b)
                  <option value="{{ $b->id }}">{{ $b->nome }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <label>CEP:</label>
              <input type="text" class="maskcep" id="cep" name="cep" placeholder="CEP">
            </div>

            <div class="col-12 col-md-12">
              <label>Referênia:</label>
              <input type="text" class="" id="referencia" name="referencia" placeholder="Referência">
            </div>

            <div class="col-12 col-md-6">
              <label>Tipo:</label>
              <div class="fake-select">
                <i class="lni lni-chevron-down"></i>
                <select required class="w-100" id="tipo" name="tipo">
                  <option value="casa">Casa</option>
                  <option value="trabalho">Trabalho</option>
                </select>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <label>Endereço padrão:</label>
              <div class="fake-select">
                <i class="lni lni-chevron-down"></i>
                <select required id="padrao" name="padrao">
                  <option value="0">Não</option>
                  <option value="1">Sim</option>
                </select>
              </div>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" style="float: right;" class="btn botao-acao">
            <i class="fa fa-check"></i>
            Salvar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>