<?php

namespace App\Console\Commands;

use App\Models\ConectaVendaConfig;
use App\Models\Empresa;
use App\Models\MovimentacaoProduto;
use App\Models\Produto;
use App\Utils\ConectaVendaUtil;
use Illuminate\Console\Command;


class SyncEstoque extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-estoque';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronica o estoque com o conecta venda';

    protected ConectaVendaUtil $util;

    public function __construct(ConectaVendaUtil $util)
    {
        parent::__construct();
        $this->util = $util;

    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $empresas = Empresa::where('status', 1)->get();
        foreach ($empresas as $empresa) {

            $config = ConectaVendaConfig::where('empresa_id', $empresa->id)->first();
            if (!$config) {
                continue;
            }

            $movimentacoes = MovimentacaoProduto::where('created_at', '>=', now()->subMinutes(10))
                ->whereHas('produto', fn ($q) => $q->where('empresa_id', $empresa->id))
                ->pluck('produto_id')
                ->unique();

            $produtos = Produto::whereIn('id', $movimentacoes)
                ->whereNotNull('conecta_venda_id')
                ->where('empresa_id', $empresa->id)
                ->get();
            if ($produtos->isEmpty()) {
                $this->line("Nenhum produto com movimentação recente na empresa {$empresa->id}.");
            }

            foreach ($produtos as $produto) {
                try {
                    $this->util->atualizarEstoque($config, $produto);
                    $this->line("Produto {$produto->id} sincronizado com sucesso.");
                } catch (\Exception $e) {
                    $this->error("Erro no produto {$produto->id}: {$e->getMessage()}");
                }
            }
        }

        $this->info('Sincronização de estoque finalizada.');

    }
}
