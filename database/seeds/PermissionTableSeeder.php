<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $permissions = [
		    'role-list',
		    'role-create',
		    'role-edit',
		    'role-delete',
		    'product-list',
		    'product-create',
		    'product-edit',
		    'product-delete',
		    'user-list',
		    'user-create',
		    'user-edit',
		    'user-delete',
		    'selection-list',
		    'selection-create',
		    'selection-update',
		    'selection-delete',
		    'searcher-list',
		    'searcher-create',
		    'searcher-update',
		    'searcher-delete',
		    'setting-list',
		    'setting-create',
	    ];


	    foreach ($permissions as $permission) {
		    Permission::create(['name' => $permission]);
	    }
    }
}
