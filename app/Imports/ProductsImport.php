<?php

namespace App\Imports;

use App\Brand;
use App\ScrapedProducts;
use App\Services\Products\ProductsCreator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow
{
    use Importable;
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $brand = trim($row['brand']);
            $originalSku = $row['sku'];
            $size = $row['size'];
            $category = $row['category'];
            $gender = $row['gender'];
            $unit_price = $row['unit_price'];


            if ($brand == "TOD'S") {
                $brand = 'TODS';
            }
            if ($brand == 'VALENTINO') {
                $brand = 'VALENTINO GARAVANI';
            }
            if ($brand == 'SAINT LAURENT') {
                $brand = 'YVES SAINT LAURENT';
            }
            if ($brand == 'MOSCHINO LOVE') {
                $brand = 'MOSCHINO';
            }
            if ($brand == 'DIOR') {
                $brand = 'CHRISTIAN DIOR';
            }
            if ($brand == "CHLOE'") {
                $brand = 'CHLOE';
            }

            $brand = Brand::where('name', $brand)->first();

            if(!$brand) {
                continue;
            }

            echo "$brand->name FOUND.. \n";
            $scrapedProduct = new ScrapedProducts();
            $scrapedProduct->brand_id = $brand->id;
            $properties = [
                'category' => $category,
                'sizes' => $size,
                'gender' => $gender,
                'price' => $unit_price,
            ];
            $sku = str_replace([' ', '-', '/', "\\", '_'], '', $originalSku);
            $scrapedProduct->sku = $sku;
            $scrapedProduct->original_sku = $originalSku;
            $scrapedProduct->properties = $properties;
            $scrapedProduct->save();

        }

    }

//    public function headingRow(): int
//    {
//        return 0;
//    }
}
