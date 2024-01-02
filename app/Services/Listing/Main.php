<?php

namespace App\Services\Listing;

use App\Product;

class Main
{
    /**
     * @var NameChecker
     */
    private $nameChecker;

    private $descriptionChecker;

    /**
     * @var CompositionChecker
     */
    private $compositionChecker;

    /**
     * @var SizesChecker
     */
    private $sizeChecker;

    /**
     * @var ColorChecker
     */
    private $colorChecker;

    private $product;

    /**
     * Main constructor.
     */
    public function __construct(NameChecker $nameChecker, CompositionChecker $compositionChecker, ColorChecker $colorChecker, SizesChecker $sizesChecker, ShortDescriptionChecker $shortDescriptionChecker)
    {
        $this->nameChecker = $nameChecker;
        $this->descriptionChecker = $shortDescriptionChecker;
        $this->colorChecker = $colorChecker;
        $this->sizeChecker = $sizesChecker;
        $this->compositionChecker = $compositionChecker;
    }

    public function validate(Product $product): bool
    {
        $this->product = $product;
        $composition = $this->isCompositionCorrect();
        $name = $this->isNameCorrect();
        $shortDescription = $this->isShortDescriptionCorrect();
//        $color = $this->isColorCorrect();
        $measurement = $this->areMeasurementsCorrect();
        $sizes = $this->isSizeCorrect();

        $status = $composition &&
            $name &&
            $shortDescription &&
//            $color &&
            ($measurement || $sizes);

        return $status;
    }

    public function isSizeCorrect(): bool
    {
        $size = $this->sizeChecker->check($this->product);
//        dump('SIZE: ' . $size);
        return $size;
    }

    public function isColorCorrect(): bool
    {
        $color = $this->colorChecker->check($this->product);
//        dump('COLOR: ' . $color);
        return $color;
    }

    public function isCompositionCorrect(): bool
    {
        $composition = $this->compositionChecker->check($this->product);
//        dump('COMPOSITION: ' . $composition);
        return $composition;
    }

    public function isShortDescriptionCorrect(): bool
    {
        $description = $this->descriptionChecker->check($this->product);
//        dump('DESCRIPTION: ' . $description);
        return $description;
    }

    public function isNameCorrect(): bool
    {
        $name = $this->nameChecker->check($this->product);
//        dump('NAME: ' . $name);
        return $name;
    }

    public function areMeasurementsCorrect(): bool
    {
        $meas = $this->product->lmeasurement && $this->product->hmeasurement && $this->product->dmeasurement;
//        dump('MEAS: ' . $meas);
        return $meas;
    }
}
