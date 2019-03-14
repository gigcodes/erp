<?php

namespace App\Exports;

use App\Purchase;
use Plank\Mediable\Media;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;

class PurchasesExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $selected_purchases;

    public function __construct(array $selected_purchases)
    {
      $this->selected_purchases = $selected_purchases;
    }

    public function array(): array
    {
      $products_array = [];
      $purchases = Purchase::whereIn('id', $this->selected_purchases)->get();

      foreach ($purchases as $purchase) {
        foreach ($purchase->products as $key => $product) {
          foreach ($product->orderproducts as $order_product) {
            $image_url = $product->getMedia(config('constants.media_tags'))->first() ? $product->getMedia(config('constants.media_tags'))->first()->getUrl() : '';
            $products_array[$key]['image'] = $image_url;
            $products_array[$key]['size'] = $order_product->size;
            $products_array[$key]['sku'] = $product->sku;
            $products_array[$key]['price'] = $product->price;
            $products_array[$key]['discount'] = $product->percentage . "%";
            $products_array[$key]['qty'] = '1';
            $products_array[$key]['final_cost'] = $product->price - ($product->price * $product->percentage / 100) - $product->factor;
            $products_array[$key]['client_name'] = $order_product->order ? ($order_product->order->customer ? $order_product->order->customer->name : 'No Customer') : 'No Order';
          }
        }
      }

      return $products_array;
    }

    public function headings(): array
    {
      return [
        'Image',
        'Size',
        'SKU Code',
        'Price',
        'Discount',
        'Qty',
        'Final cost',
        'Client Name'
      ];
    }

    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            // BeforeExport::class => function(BeforeExport $event) {
            //   $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            //   $drawing->setName('Logo');
            //   $drawing->setDescription('Logo');
            //   $drawing->setPath(public_path('uploads/simple2.jpg'));
            //   $drawing->setCoordinates('A2');
            //   $drawing->setHeight('50');
            //
            //   // $drawing->setWorksheet($event->getActiveSheet());
            //
            //   // $event->sheet->setHeight([
            //   //   1 => 100,
            //   //   2 => 200
            //   // ]);
            // },
        ];
    }

    // public function drawings()
    // {
    //   $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    //   $drawing->setName('Logo');
    //   $drawing->setDescription('Logo');
    //   $drawing->setPath(public_path('uploads/simple2.jpg'));
    //   $drawing->setHeight(90);
    //   $drawing->SetCoordinates('A2');
    //
    //   return $drawing;
    // }
}
