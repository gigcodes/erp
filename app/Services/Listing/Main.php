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
    /**
     * @var
     */
    private $product;

    /**
     * Main constructor.
     * @param NameChecker $nameChecker
     * @param CompositionChecker $compositionChecker
     * @param ColorChecker $colorChecker
     * @param SizesChecker $sizesChecker
     * @param ShortDescriptionChecker $shortDescriptionChecker
     */
    public function __construct(NameChecker $nameChecker, CompositionChecker $compositionChecker, ColorChecker $colorChecker, SizesChecker $sizesChecker, ShortDescriptionChecker $shortDescriptionChecker)
    {
        $this->nameChecker = $nameChecker;
        $this->descriptionChecker = $shortDescriptionChecker;
        $this->colorChecker = $colorChecker;
        $this->sizeChecker = $sizesChecker;
        $this->compositionChecker = $compositionChecker;
    }


    /**
     * @param Product $product
     * @return bool
     */
    public function validate(Product $product): bool
    {
        $this->product = $product;
        return $this->isCompositionCorrect() &&
            $this->isNameCorrect() &&
            $this->isShortDescriptionCorrect() &&
            $this->isColorCorrect() &&
            ($this->areMeasurementsCorrect() || $this->isSizeCorrect());
    }

    /**
     * @return bool
     */
    public function isSizeCorrect(): bool
    {
        return $this->sizeChecker->check($this->product);
    }

    /**
     * @return bool
     */
    public function isColorCorrect(): bool
    {
        return $this->colorChecker->check($this->product);
    }

    /**
     * @return bool
     */
    public function isCompositionCorrect(): bool
    {
        return $this->compositionChecker->check($this->product);
    }

    /**
     * @return bool
     */
    public function isShortDescriptionCorrect(): bool
    {
        return $this->descriptionChecker->check($this->product);
    }

    /**
     * @return bool
     */
    public function isNameCorrect(): bool
    {
        return $this->nameChecker->check($this->product);
    }

    /**
     * @return bool
     */
    public function areMeasurementsCorrect(): bool
    {
        return $this->product->lmeasurement && $this->product->hmeasurement && $this->product->dmeasurement;
    }
}