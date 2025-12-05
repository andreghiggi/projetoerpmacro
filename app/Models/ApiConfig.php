<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'status', 'token', 'permissoes_acesso'
    ];

    public static function permissoes(){
        return [
            'categoria_produtos' => 'Categoria de produtos',
            'produtos' => 'Produtos',
            'clientes' => 'Clientes',
            'fornecedores' => 'Fornecedores',
            'vendas_pdv' => 'Vendas PDV',
            'vendas_pedido' => 'Vendas Pedido',
            'usuarios' => 'Usuários',
            'caixa' => 'Caixa',
            'cotacao' => 'Cotação',
            'natureza_operacao' => 'Natureza de operação',
            'padrao_fiscal' => 'Padrão fiscal',
            'emitente' => 'Emitente',
        ];
    }

    public static function acoes(){
        return [
            'create' => 'Criar',
            'update' => 'Atualizar',
            'read' => 'Ler',
            'delete' => 'Remover'
        ];
    }

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public static function inArrayPermissoes($key, $key2){
        $data = [
            'categoria_produtos' => ['create', 'update', 'read', 'delete'],
            'produtos' => ['create', 'update', 'read', 'delete'],
            'clientes' => ['create', 'update', 'read', 'delete'],
            'fornecedores' => ['create', 'update', 'read', 'delete'],
            'vendas_pdv' => ['create', 'update', 'read', 'delete'],
            'vendas_pedido' => ['create', 'update', 'read', 'delete'],
            'usuarios' => ['read'],
            'caixa' => ['read', 'create'],
            'cotacao' => ['create', 'update', 'read', 'delete'],
            'natureza_operacao' => ['create', 'update', 'read', 'delete'],
            'padrao_fiscal' => ['create', 'update', 'read', 'delete'],
            'emitente' => ['read', 'update'],
        ];

        $p = $data[$key];
        if(in_array($key2, $p)) return 1;
        return 0;
    }

}
