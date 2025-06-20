<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Helpers\Menu;
use App\Models\User;
use App\Models\UsuarioEmpresa;
use Illuminate\Support\Str;

class UsuarioSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
         $empresa = Empresa::create([
             'nome' => 'Slym',
             'nome_fantasia' => 'Slym',
             'rua' => 'Aldo ribas',
             'numero' => '190',
             'bairro' => 'Centro',
             'cidade_id' => 4081,
             'status' => 1,
             'email' => 'slym@slym.com',
             'celular' => '00000000000',
             'cpf_cnpj' => '',
             'ambiente' => 2
         ]);

         $usuario = User::create([
             'name' => 'Super',
             'email' => 'slym@slym.com',
             'password' => '123',
         ]);

         UsuarioEmpresa::create([
             'empresa_id' => $empresa->id,
             'usuario_id' => $usuario->id
         ]);
    }

}
