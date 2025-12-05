<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Produto;
use App\Models\Empresa;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Localizacao;
use App\Models\ProdutoLocalizacao;
use App\Models\UsuarioLocalizacao;
use App\Models\NaturezaOperacaoSuper;
use App\Models\NaturezaOperacao;
use App\Models\PadraoTributacaoProdutoSuper;
use App\Models\PadraoTributacaoProduto;

class EmpresaUtil
{

	public function defaultPermissions($empresa_id){
		$empresa = Empresa::findOrFail($empresa_id);
		$usuarios = $empresa->usuarios;
		
		$roles = Role::where('empresa_id', null)->get();

		// if(sizeof($roles) == 0){
		// 	$roles = Role::where('empresa_id', '!=', null)
		// 	->limit(1)
		// 	->get();
		// }

		\Artisan::call('cache:forget spatie.permission.cache');
		foreach($roles as $role){

			if($role->name != 'gestor_plataforma'){

				foreach($usuarios as $u){
					$user = $u->usuario;
					$r = Role::create([ 
						'name' => $role->description . '#' . $empresa_id,
						'description' => $role->description,
						'empresa_id' => $empresa_id,
						'guard_name' => 'web', 
						'is_default' => 1,
						'type_user' => 2
					]);
					$permissions = [];
					foreach($role->permissions as $p){
						array_push($permissions, 
							[
								'permission_id' => $p->id,
								'role_id' => $r->id,
							]
						);
					}

					$role->permissions()->attach($permissions);
					$user->assignRole($r->name);

				}
			}
		}
	}

	public function getPermissions($empresa_id){
		$empresa = Empresa::findOrFail($empresa_id);
		$user = $empresa->usuarios[0]->usuario;

		return $user->getAllPermissions();
	}

	public function createPermissions(){
		$count = Permission::count();
		if($count == 0){
			$this->createPermissionsDefault();
		}

		$count = Role::count();
		if($count == 0){
			$this->createRolesDefault();
		}

	}

	private function createPermissionsDefault()
	{
        // Seed the default permissions
		$permissions = Permission::defaultPermissions();

		foreach ($permissions as $permission) {
			Permission::updateOrCreate(
				['name' => $permission['name']],
				$permission
			);
		}

	}

	private function createRolesDefault()
	{
		$superadmin = Role::firstOrCreate([
			'name' => 'gestor_plataforma'
		], [
			'description' => 'Gestor Plataforma',
			'type_user' => 1
		]);
		$superadmin->permissions()->sync(Permission::all());

		$admin = Role::firstOrCreate([
			'name' => 'admin',
		], [
			'description' => 'Admin',
			'type_user' => 2
		]);
		$admin->permissions()->sync(Permission::all());

	}

	public function initLocation($empresa){

		$localizacao = Localizacao::where('empresa_id', $empresa->id)->first();
		if(!$localizacao){
			$localizacao = $empresa->toArray();
			$localizacao['descricao'] = 'BL0001';
			$localizacao['empresa_id'] = $empresa->id;

			$localizacao = Localizacao::create($localizacao);

			foreach($empresa->usuarios as $u){
				UsuarioLocalizacao::updateOrCreate([
					'usuario_id' => $u->usuario_id,
					'localizacao_id' => $localizacao->id
				]);
			}
		}

		$this->initProducts($empresa->id);
		$this->initRegisters($empresa->id);
	}

	public function initNaturezaTributacao($empresa){

		$data = NaturezaOperacaoSuper::where('status', 1)->get();
		foreach($data as $item){
			$obj = $item->toArray();
			$obj['empresa_id'] = $empresa->id;
			NaturezaOperacao::create($obj);
		}

		$data = PadraoTributacaoProdutoSuper::where('status', 1)->get();
		foreach($data as $item){
			$obj = $item->toArray();
			$obj['empresa_id'] = $empresa->id;
			PadraoTributacaoProduto::create($obj);
		}
	}

	private function initProducts($empresa_id){
		$produtos = Produto::where('empresa_id', $empresa_id)->get();
		$localizacao = Localizacao::where('empresa_id', $empresa_id)->first();
		if($localizacao){
			foreach($produtos as $p){
				$produtoLocalizacao = ProdutoLocalizacao::where('produto_id', $p->id)->first();
				if($produtoLocalizacao == null){
					ProdutoLocalizacao::updateOrCreate([
						'produto_id' => $p->id,
						'localizacao_id' => $localizacao->id
					]);
				}
			}
		}
	}

	private function initRegisters($empresa_id){
		$localizacao = Localizacao::where('empresa_id', $empresa_id)->first();

		\App\Models\Nfe::where('empresa_id', $empresa_id)->where('local_id', null)
		->update(['local_id' => $localizacao->id]);

		\App\Models\Nfce::where('empresa_id', $empresa_id)->where('local_id', null)
		->update(['local_id' => $localizacao->id]);

		\App\Models\Cte::where('empresa_id', $empresa_id)->where('local_id', null)
		->update(['local_id' => $localizacao->id]);

		\App\Models\Mdfe::where('empresa_id', $empresa_id)->where('local_id', null)
		->update(['local_id' => $localizacao->id]);

		\App\Models\ContaPagar::where('empresa_id', $empresa_id)->where('local_id', null)
		->update(['local_id' => $localizacao->id]);

		\App\Models\ContaReceber::where('empresa_id', $empresa_id)->where('local_id', null)
		->update(['local_id' => $localizacao->id]);
	}

	public function initUserLocations($user){
		if($user->empresa && sizeof($user->locais) == 0){
			$empresa_id = $user->empresa->empresa_id;
			$localizacao = Localizacao::where('empresa_id', $empresa_id)->first();
			UsuarioLocalizacao::updateOrCreate([
				'usuario_id' => $user->id,
				'localizacao_id' => $localizacao->id
			]);
		}
	}

}