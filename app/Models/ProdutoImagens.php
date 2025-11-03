<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProdutoImagens extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id', 'produto_variacao_id', 'imagem', 'sha256'
    ];
    const UPDATED_AT = null;

    public static function create_all( $insert_list = [] ) {
        foreach( $insert_list as $index => &$row) {
            $row['created_at'] = now();
        }
        return DB::table('produto_imagens')->insert( $insert_list );
    }

    public static function all_by_name( $produto_id , $variacao_id = 0 ) {
        $imagens = DB::table('produto_imagens')->where([
            ['produto_id', $produto_id],
            ['produto_variacao_id', $variacao_id],
        ])->get();
        return array_column( $imagens->toArray(), null, 'imagem' );
    }

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function variacao(){
        return $this->belongsTo(ProdutoVariacao::class, 'produto_variacao_id');
    }

    public static function replace_all( $data, $produto_id, $produto_variacao_id = [] ) {
        if(!$produto_variacao_id) {
            ProdutoImagens::where( [
                "produto_id"          => $produto_id,
                "produto_variacao_id" => 0,
            ])->delete();
        } else {
            ProdutoImagens::where( [
                "produto_id"          => $produto_id,
            ])->whereIn(
                'produto_variacao_id', $produto_variacao_id
            )->delete();
        }
        return self::create_all( $data );
    }
}
