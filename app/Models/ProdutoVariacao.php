<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProdutoVariacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id', 'descricao', 'valor', 'codigo_barras', 'referencia', 'imagem'
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function estoque(){
        return $this->hasOne(Estoque::class, 'produto_variacao_id');
    }

    public function movimentacaoProduto(){
        return $this->hasOne(MovimentacaoProduto::class, 'produto_variacao_id');
    }

    public function getImgAppAttribute()
    {
        if($this->imagem == ""){
            return env("APP_URL") . "/imgs/no-image.png";
        }
        return env("APP_URL") . "/uploads/produtos/$this->imagem";
    }
    public function getImgAttribute()
    {
        if($this->imagem == ""){
            return "/imgs/no-image.png";
        }
        return "/uploads/produtos/$this->imagem";
    }

    public function imagens()
	{
		$imagem = ProdutoImagens::where([
			["produto_id", $this->produto_id],
			["produto_variacao_id", $this->id ],
		])
		->orderBy('ordem')->get();
		
		$imagens = array_map( function($img){
			return "/uploads/produtos/$img[imagem]";	
		} ,$imagem->toArray()  );

		return $imagens;

		if(!$imagem){
			return ["/imgs/no-image.png"];
		}
	}

    public static function removerVariacoesNaoPresentes( $produto_id, $variacoes_presentes = [] ) {
        DB::transaction(function () use ($produto_id, $variacoes_presentes) {
            $variacoes = ProdutoVariacao::where( 'produto_id', $produto_id )
            ->whereNotIn('id', $variacoes_presentes)->get();

            $variacoes_ids = $variacoes->pluck('id');

            if( $variacoes_ids->isNotEmpty() ) {
                Estoque::whereIn( 'produto_variacao_id', $variacoes_ids )->delete();
                MovimentacaoProduto::whereIn( 'produto_variacao_id', $variacoes_ids )->delete();
                ProdutoVariacao::whereIn( 'id', $variacoes_ids )->delete();
            }
        });
    }

}
