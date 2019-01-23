<?php

namespace App\Imports;

use App\Product;
use Maatwebsite\Excel\Concerns\ToModel;

class InventoryImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
      if ($product = Product::where('sku', $row[0])->first()) {
        $product->stock = $row[1];
        $product->save();
      } else {
        return null;
      }
    }
}
