<div class="modal fade" id="modalProduto" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content produto-modal">

			<div class="modal-header produto-header">
				<h5 class="modal-title">Produto</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
			</div>

			<div class="modal-body">

				<div class="text-center mb-3">
					<img id="modalImagem" src="" class="produto-img">

					<div class="estoque-box text-center mb-3">
						<span id="modalEstoque" class="estoque-numero">0</span>
						<div class="estoque-label">Estoque disponível</div>
					</div>
				</div>

				<div class="mb-2">
					<label>Nome</label>
					<input type="text" class="form-control produto-input" id="modalNome" readonly disabled>
				</div>

				<div class="row">
					<div class="col-6 mb-2">
						<label>Valor Unitário</label>
						<input type="tel" class="form-control produto-input moeda" id="modalValor">
					</div>
					<div class="col-6 mb-2">
						<label>Quantidade</label>
						<div class="produto-qtd-box">
							<button type="button" class="qtd-btn" id="btnQtdMenos">−</button>
							<input type="tel" class="form-control produto-input qtd-input" id="modalQtd" value="1" min="1">
							<button type="button" class="qtd-btn" id="btnQtdMais">+</button>
						</div>
					</div>

					<button class="btn produto-btn-info d-none" id="btnEscolherAdicionais">
						Escolher Adicionais
					</button>

				</div>

				<div class="mb-2 mt-1">
					<label>Observação</label>
					<input type="text" class="form-control produto-input" id="modalObservacao">
				</div>

			</div>

			<div class="produto-footer">
				<button class="btn produto-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
				<button class="btn produto-btn-primary" id="modalAdicionar">Adicionar</button>
			</div>

		</div>
	</div>
</div>
