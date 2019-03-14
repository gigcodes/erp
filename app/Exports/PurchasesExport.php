<?php

namespace App\Exports;

use App\Purchase;
use Plank\Mediable\Media;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PurchasesExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $selected_purchases;
    protected $count = 0;
    protected $path = [];

    public function __construct(array $selected_purchases)
    {
      $this->selected_purchases = $selected_purchases;
    }

    public function array(): array
    {
      $products_array = [];
      $purchases = Purchase::whereIn('id', $this->selected_purchases)->get();

      foreach ($purchases as $purchase) {
        foreach ($purchase->products as $product) {
          foreach ($product->orderproducts as $order_product) {
            $path = $product->getMedia(config('constants.media_tags'))->first() ? $product->getMedia(config('constants.media_tags'))->first()->getAbsolutePath() : '';
            $this->path[] = $path;

            $products_array[$this->count]['image'] = 'Image.......';
            $products_array[$this->count]['size'] = $order_product->size;
            $products_array[$this->count]['sku'] = $product->sku;
            $products_array[$this->count]['price'] = $product->price;
            $products_array[$this->count]['discount'] = $product->percentage . "%";
            $products_array[$this->count]['qty'] = $order_product->qty;
            $products_array[$this->count]['final_cost'] = ($product->price - ($product->price * $product->percentage / 100) - $product->factor) * $order_product->qty;
            $products_array[$this->count]['client_name'] = $order_product->order ? ($order_product->order->customer ? $order_product->order->customer->name : 'No Customer') : 'No Order';

            $this->count++;
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
            AfterSheet::class => function(AfterSheet $event) {
              for ($i = 1; $i <= $this->count; $i++) {
                $coordinates = "A" . (string) ($i + 1);
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath($this->path[$i - 1]);
                $drawing->setCoordinates($coordinates);
                $drawing->setHeight('100');
                $drawing->setOffsetY('10');
                $drawing->setWorksheet($event->sheet->getDelegate());

                $event->sheet->getDelegate()->getRowDimension($i + 1)->setRowHeight(100);
              }

              $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(12);
            },
        ];
    }
}
