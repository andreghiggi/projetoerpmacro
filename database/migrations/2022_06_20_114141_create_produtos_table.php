<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->foreignId('categoria_id')->nullable()->constrained('categoria_produtos');
            $table->foreignId('sub_categoria_id')->nullable()->constrained('categoria_produtos');
            $table->foreignId('padrao_id')->nullable()->constrained('padrao_tributacao_produtos');
            $table->foreignId('marca_id')->nullable()->constrained('marcas');
            $table->foreignId('variacao_modelo_id')->nullable();

            $table->string('nome', 200);
            $table->string('codigo_barras', 20)->nullable();
            $table->string('codigo_barras2', 20)->nullable();
            $table->string('codigo_barras3', 20)->nullable();
            $table->string('referencia', 20)->nullable();
            $table->string('ncm', 10);
            $table->string('unidade', 20);
            $table->string('imagem', 25)->nullable();

            $table->decimal('perc_icms', 10,2)->default(0);
            $table->decimal('perc_pis', 10,2)->default(0);
            $table->decimal('perc_cofins', 10,2)->default(0);
            $table->decimal('perc_ipi', 10,2)->default(0);
            $table->string('cest', 10)->nullable();
            $table->integer('origem')->default(0);

            $table->string('cst_csosn', 3)->nullable();
            $table->string('cst_pis', 3)->nullable();
            $table->string('cst_cofins', 3)->nullable();
            $table->string('cst_ipi', 3)->nullable();
            
            $table->decimal('perc_red_bc', 5,2)->nullable();
            $table->decimal('pST', 5,2)->nullable();

            $table->decimal('valor_unitario', 12,4);
            $table->decimal('valor_prazo', 12,4)->nullable();
            $table->decimal('valor_minimo_venda', 12,4);
            $table->decimal('valor_compra', 12,4);
            $table->decimal('percentual_lucro', 10,2)->default(0);

            $table->string('cfop_estadual', 4);
            $table->string('cfop_outro_estado', 4);

            $table->string('cfop_entrada_estadual', 4)->nullable();
            $table->string('cfop_entrada_outro_estado', 4)->nullable();
            $table->string('codigo_beneficio_fiscal', 15)->nullable();
            $table->string('cEnq', 3)->nullable();

            $table->boolean('gerenciar_estoque')->default(0);

            $table->decimal('adRemICMSRet', 10, 4)->default(0);
            $table->decimal('pBio', 10, 4)->default(0);
            $table->boolean('tipo_servico')->default(0);
            $table->integer('indImport')->default(0);
            $table->string('cUFOrig', 2)->nullable();
            $table->decimal('pOrig', 5, 2)->default(0);

            $table->string('codigo_anp', 10)->nullable();
            $table->decimal('perc_glp', 5,2)->default(0);
            $table->decimal('perc_gnn', 5,2)->default(0);
            $table->decimal('perc_gni', 5,2)->default(0);
            $table->decimal('valor_partida', 10, 2)->default(0);
            $table->string('unidade_tributavel', 4)->default('');
            $table->decimal('quantidade_tributavel', 10, 2)->default(0);

            $table->boolean('status')->default(1);
            $table->boolean('cardapio')->default(0);
            $table->boolean('delivery')->default(0);
            $table->boolean('reserva')->default(0);
            $table->boolean('ecommerce')->default(0);
            $table->string('nome_en', 80)->nullable();
            $table->string('nome_es', 80)->nullable();
            $table->string('descricao', 255)->nullable();
            $table->string('descricao_en', 255)->nullable();
            $table->string('descricao_es', 255)->nullable();
            $table->decimal('valor_cardapio', 12, 4)->nullable();
            $table->decimal('valor_delivery', 12, 4)->nullable();

            $table->boolean('destaque_delivery')->nullable();
            $table->boolean('oferta_delivery')->nullable();
            
            $table->integer('tempo_preparo')->nullable();
            $table->boolean('tipo_carne')->default(0);

            $table->boolean('tipo_unico')->default(0);

            $table->boolean('composto')->default(0);
            $table->boolean('combo')->default(0);
            $table->decimal('margem_combo', 5,2)->default(0);

            $table->decimal('estoque_minimo', 5,2)->default(0);
            $table->integer('alerta_validade')->nullable();

            $table->integer('referencia_balanca')->nullable();
            $table->boolean('balanca_pdv')->default(0);
            $table->boolean('exportar_balanca')->default(0);

            //variaveis para ecommerce

            $table->decimal('valor_ecommerce', 12, 4)->nullable();
            $table->boolean('destaque_ecommerce')->nullable();
            $table->integer('percentual_desconto')->nullable();
            $table->string('descricao_ecommerce', 255)->nullable();
            $table->text('texto_ecommerce');
            $table->decimal('largura', 8, 2)->nullable();
            $table->decimal('comprimento', 8, 2)->nullable();
            $table->decimal('altura', 8, 2)->nullable();
            $table->decimal('peso', 12, 3)->nullable();
            $table->string('hash_ecommerce', 50)->nullable();
            $table->string('hash_delivery', 50)->nullable();
            $table->text('texto_delivery');

            $table->string('mercado_livre_id', 20)->nullable();
            $table->string('mercado_livre_link', 255)->nullable();
            $table->decimal('mercado_livre_valor', 12, 4)->nullable();
            $table->string('mercado_livre_categoria', 20)->nullable();
            $table->string('condicao_mercado_livre', 20)->nullable();
            $table->integer('quantidade_mercado_livre')->nullable();
            $table->string('mercado_livre_tipo_publicacao', 20)->nullable();
            $table->string('mercado_livre_youtube', 100)->nullable();
            $table->text('mercado_livre_descricao');
            $table->string('mercado_livre_status', 20);
            $table->string('mercado_livre_modelo', 100)->nullable();

            $table->string('woocommerce_id', 20)->nullable();
            $table->string('woocommerce_slug', 80)->nullable();
            $table->string('woocommerce_link', 255)->nullable();
            $table->decimal('woocommerce_valor', 12, 4)->nullable();
            $table->string('woocommerce_type', 30)->nullable();
            $table->string('woocommerce_status', 30)->nullable();
            $table->text('woocommerce_descricao');
            $table->string('woocommerce_stock_status', 30)->nullable();
            $table->text('categorias_woocommerce');

            $table->string('nuvem_shop_id', 20)->nullable();
            $table->decimal('nuvem_shop_valor', 12, 4)->nullable();
            $table->text('texto_nuvem_shop');

            $table->integer('modBCST')->nullable();
            $table->decimal('pMVAST', 5,2)->nullable();
            $table->decimal('pICMSST', 5,2)->nullable();
            $table->decimal('redBCST', 5,2)->nullable();

            $table->decimal('valor_atacado', 22,7)->default(0);
            $table->integer('quantidade_atacado')->nullable();
            $table->string('referencia_xml', 50)->nullable();

            $table->boolean('tipo_dimensao')->default(0);
            $table->boolean('tipo_producao')->default(0);
            $table->decimal('espessura', 8, 2)->nullable();
            $table->integer('_id_import')->nullable();

            $table->string('observacao', 100)->nullable();
            $table->string('observacao2', 100)->nullable();
            $table->string('observacao3', 100)->nullable();
            $table->string('observacao4', 100)->nullable();
            $table->integer('numero_sequencial')->nullable();

            // alter table produtos add column valor_compra decimal(12,4);
            // alter table produtos add column valor_delivery decimal(12,4);
            // alter table produtos add column tempo_preparo integer default null;
            // alter table produtos add column referencia varchar(20) default null;

            // alter table produtos add column adRemICMSRet decimal(10,4) default 0;
            // alter table produtos add column pBio decimal(10,4) default 0;
            // alter table produtos add column tipo_servico boolean default 0;
            // alter table produtos add column tipo_unico boolean default 0;
            // alter table produtos add column delivery boolean default 0;
            // alter table produtos add column reserva boolean default 0;
            // alter table produtos add column cUFOrig varchar(2) default null;
            // alter table produtos add column pOrig decimal(5,2) default 0;
            // alter table produtos add column indImport integer default 0;

            // alter table produtos add column codigo_anp varchar(10) default null;
            // alter table produtos add column perc_glp decimal(5,2) default 0;
            // alter table produtos add column perc_gnn decimal(5,2) default 0;
            // alter table produtos add column perc_gni decimal(5,2) default 0;
            // alter table produtos add column valor_partida decimal(10, 2) default 0;
            // alter table produtos add column unidade_tributavel varchar(4) default '';
            // alter table produtos add column quantidade_tributavel decimal(10, 2) default 0;

            // alter table produtos add column composto boolean default 0;
            // alter table produtos add column combo boolean default 0;

            // alter table produtos add column margem_combo decimal(5, 2) default 0;
            // alter table produtos add column estoque_minimo decimal(5, 2) default 0;
            // alter table produtos add column alerta_validade integer default 0;

            // alter table produtos add column referencia_balanca integer default null;
            // alter table produtos add column variacao_modelo_id integer default null;

            // alter table produtos add column cfop_entrada_estadual varchar(4) default null;
            // alter table produtos add column cfop_entrada_outro_estado varchar(4) default null;


            // alter table produtos add column ecommerce boolean default 0;
            // alter table produtos add column valor_ecommerce decimal(12,4) default null;
            // alter table produtos add column percentual_desconto integer default null;
            // alter table produtos add column descricao_ecommerce varchar(255) default null;
            // alter table produtos add column largura decimal(8, 2) default null;
            // alter table produtos add column comprimento decimal(8, 2) default null;
            // alter table produtos add column altura decimal(8, 2) default null;
            // alter table produtos add column peso decimal(12, 3) default null;
            // alter table produtos add column destaque_ecommerce boolean default 0;
            // alter table produtos add column destaque_delivery boolean default 0;
            // alter table produtos add column oferta_delivery boolean default 0;
            // alter table produtos add column hash_ecommerce varchar(50) default null;
            // alter table produtos add column hash_delivery varchar(50) default null;
            // alter table produtos add column texto_ecommerce text;
            // alter table produtos add column texto_delivery text;
            // alter table produtos add column mercado_livre_id varchar(20) default null;
            // alter table produtos add column mercado_livre_link varchar(255) default null;
            // alter table produtos add column mercado_livre_valor decimal(12, 4) default null;
            // alter table produtos add column mercado_livre_modelo varchar(100) default null;

            // alter table produtos add column mercado_livre_categoria varchar(20) default null;
            // alter table produtos add column condicao_mercado_livre varchar(20) default null;
            // alter table produtos add column quantidade_mercado_livre integer default null;
            // alter table produtos add column mercado_livre_tipo_publicacao varchar(20) default null;
            // alter table produtos add column mercado_livre_youtube varchar(100) default null;
            // alter table produtos add column mercado_livre_descricao text;
            // alter table produtos add column mercado_livre_status varchar(20) default null;
            // alter table produtos add column nuvem_shop_id varchar(20) default null;
            // alter table produtos add column nuvem_shop_valor decimal(12, 4) default null;
            // alter table produtos add column texto_nuvem_shop text;
            
            // alter table produtos add column modBCST integer default null;
            // alter table produtos add column pMVAST decimal(5,2) default null;
            // alter table produtos add column pICMSST decimal(5,2) default null;
            // alter table produtos add column redBCST decimal(5,2) default null;
            // alter table produtos modify column nome varchar(200);
            // alter table produtos add column percentual_lucro decimal(10,2) default 0;
            
            // alter table produtos add column codigo_barras2 varchar(20) default null;
            // alter table produtos add column codigo_barras3 varchar(20) default null;
            // alter table produtos add column sub_categoria_id integer default null;

            // alter table produtos add column valor_atacado decimal(22,7) default 0;
            // alter table produtos add column valor_minimo_venda decimal(12,4) default 0;
            // alter table produtos add column quantidade_atacado integer default null;

            // alter table produtos add column woocommerce_id varchar(20) default null;
            // alter table produtos add column woocommerce_slug varchar(80) default null;
            // alter table produtos add column woocommerce_link varchar(255) default null;
            // alter table produtos add column woocommerce_valor decimal(12, 4) default null;
            // alter table produtos add column woocommerce_type varchar(30) default null;
            // alter table produtos add column woocommerce_status varchar(30) default null;
            // alter table produtos add column woocommerce_descricao text;
            // alter table produtos add column categorias_woocommerce text;
            // alter table produtos add column woocommerce_stock_status varchar(30) default null;
            // alter table produtos add column tipo_unico boolean default 0;
            // alter table produtos add column balanca_pdv boolean default 0;
            // alter table produtos add column exportar_balanca boolean default 0;
            // alter table produtos add column referencia_xml varchar(50) default null;
            // alter table produtos add column tipo_dimensao boolean default 0;
            // alter table produtos add column tipo_producao boolean default 0;
            // alter table produtos add column espessura decimal(8,2) default null;
            // alter table produtos add column _id_import integer default null;

            // alter table produtos add column observacao varchar(100) default null;
            // alter table produtos add column observacao2 varchar(100) default null;
            // alter table produtos add column observacao3 varchar(100) default null;
            // alter table produtos add column observacao4 varchar(100) default null;

            // alter table produtos add column numero_sequencial integer default null;
            // alter table produtos add column valor_prazo decimal(12,4) default null;
            
            $table->timestamps();
        });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
