<?php


namespace App\Services\Listing;


use App\Colors;

class ColorChecker implements CheckerInterface
{

    private $availableColors;

    public function __construct()
    {
        $this->setAvailableColors();
    }

    public function check($product): bool {
        $color = title_case($product->color);
        dd($this->availableColors);
        if (in_array($color, $this->availableColors, false)) {
            $product->color = $color;
            $product->save();
            return true;
        }

        return false;
    }

    public function improvise($data, $data2 = null)
    {
        return $data;
    }


    public function setAvailableColors(): void
    {
        $this->availableColors = (new Colors)->all();
    }

}