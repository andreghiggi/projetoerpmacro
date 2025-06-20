<?php

namespace App\Models;

class Role extends \Spatie\Permission\Models\Role
{

    // protected $fillable = [
    //     'name', 'empresa_id', 'guard_name', 'is_default'
    // ];

    public static function types()
    {
        return [
            1 => 'Plataforma',
            'Admin',
            'Caixa'
        ];
    }

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
