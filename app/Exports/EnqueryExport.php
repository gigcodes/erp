<?php

namespace App\Exports;

use App\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
class EnqueryExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /***
    * @return \Illuminate\Support\Collection
    */

    protected $products;
    protected $count = 0;
    protected $path = null;

    public function __construct(array $products, $path)
    {
      $this->products = $products;
      $this->path = $path;
    }


    // public function collection()
    // {
    //     return Product::all();
    // }


    public function array(): array
    {
      $products_array = [];
      $products = Product::whereIn('id', $this->products)->get();

      foreach($products as $product) {
        $arr = [];
            $arr['name'] = $product->name;
            $arr['sku'] = $product->sku;
            $arr['short_description'] = $product->short_description;
            $arr['price'] = $product->price;
            $arr['composition'] = $product->composition;
            $arr['product_link'] = $product->product_link;
            $products_array[] = $arr;
}

      return $products_array;
    }

    public function headings(): array
    {
      return [
        'Name',
        'SKU Code',
        'Description',
        'Price',
        'Composition',
        'Product Link'
      ];
    }

    // public function registerEvents(): array
    // {
    //     return [
    //         // Handle by a closure.
    //         AfterSheet::class => function(AfterSheet $event) {
    //           for ($i = 1; $i <= count($this->products); $i++) {

    //             $coordinates = "A" . (string) ($i + 1);
    //             $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    //             $drawing->setName('Logo');
    //             $drawing->setDescription('Logo');
    //             $drawing->setPath($this->path);
    //             $drawing->setCoordinates($coordinates);
    //             $drawing->setHeight('100');
    //             $drawing->setOffsetY('10');
    //             $drawing->setWorksheet($event->sheet->getDelegate());
    //             $event->sheet->getDelegate()->getRowDimension($i + 1)->setRowHeight(100);
    //           }

    //           $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(12);
    //         },
    //     ];
    // }
}
