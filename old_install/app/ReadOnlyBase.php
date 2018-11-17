<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReadOnlyBase
{
	protected $sizes_array = [];

	public function all(){

		return $this->sizes_array;
	}

	public function get($id){

		return $this->sizes_array[$id];
	}

}
