<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Product;
use App\Helpers\StatusHelper;
class AttributeAssignment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $attribute_id;
    
    protected $attribute_value;
    
    protected $product_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->attribute_id = $data['attribute_id'];
        $this->attribute_value = $data['attribute_value'];
        $this->product_id = $data['product_id'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->attribute_id == StatusHelper::$unknownSize) {
            $update_product = Product::where('status_id',$this->attribute_id);
                                    if(!empty($this->product_id)) {
                                        $update_product->where('id',$this->product_id);
                                    }
                                    $update_product->update(['size'=>$this->attribute_value]);
        } else if($this->attribute_id == StatusHelper::$unknownMeasurement) {
            $update_product = Product::where('status_id',$this->attribute_id);
                                        if(!empty($this->product_id)) {
                                            $update_product->where('id',$this->product_id);
                                        }
                                        $update_product->update(['measurement_size_type'=>'measurement','lmeasurement'=> $this->attribute_value]);
        } else if($this->attribute_id == StatusHelper::$unknownCategory) {
            $update_product = Product::where('status_id',$this->attribute_id);
                                        if(!empty($this->product_id)) {
                                            $update_product->where('id',$this->product_id);
                                        }
                                        $update_product->update(['category'=>$this->attribute_value]);
        } else if($this->attribute_id == StatusHelper::$unknownColor) {
            $update_product = Product::where('status_id',$this->attribute_id);
                                        if(!empty($this->product_id)) {
                                            $update_product->where('id',$this->product_id);
                                        }
                                        $update_product->update(['color'=>$this->attribute_value]);
        }
    }
}
