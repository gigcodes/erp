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
    
    protected $data;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->data = $params['data'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // dd($this->data);
        $userId = \Auth::user()->id;
        if($this->data['attribute_id'] == StatusHelper::$unknownSize) {
            
            $find_products = Product::where('status_id',$this->data['attribute_id']);
                                    $find_products->where('size',$this->data['find_size']);
                                    $find_products = $find_products->get();
            
            $attribute_arr = [];
            if(isset($find_products) && !empty($find_products)) {
                foreach ($find_products as $fp_key => $fp_value) {
                    
                    $old_value = $fp_value->size;
                    $fp_value->size = $this->data['replace_size'];
                    $fp_value->save();
            
                    $attribute_arr[] = [
                        'old_value' => $old_value,
                        'new_value' => $this->data['replace_size'],
                        'attribute_name' => 'size',
                        'attribute_id' => $this->data['attribute_id'],
                        'product_id' => $fp_value->id,
                        'user_id' => $userId,
                    ];
                }
               
            }
            
            if(!empty( $attribute_arr )) {
                $productUpdatedAttributeHistory = \App\ProductUpdatedAttributeHistory::insert($attribute_arr);
            }
            
        } else if($this->data['attribute_id'] == StatusHelper::$unknownMeasurement) {
          
            $find_products = Product::where('status_id',$this->data['attribute_id']);
                                    $find_products->where('lmeasurement',$this->data['find_lmeasurement']);
                                    $find_products = $find_products->get();
            
            $attribute_arr = [];
            if(isset($find_products) && !empty($find_products)) {
                foreach ($find_products as $fp_key => $fp_value) {
                    
                    $old_value = $fp_value->lmeasurement;
                    $fp_value->lmeasurement = $this->data['replace_lmeasurement'];
                    $fp_value->save();
            
                    $attribute_arr[] = [
                        'old_value' => $old_value,
                        'new_value' => $this->data['replace_lmeasurement'],
                        'attribute_name' => 'lmeasurement',
                        'attribute_id' => $this->data['attribute_id'],
                        'product_id' => $fp_value->id,
                        'user_id' => $userId,
                    ];
                }
            
            }
            
            if(!empty( $attribute_arr )) {
                $productUpdatedAttributeHistory = \App\ProductUpdatedAttributeHistory::insert($attribute_arr);
            }
        
           
            $find_products = Product::where('status_id',$this->data['attribute_id']);
                                        $find_products->where('hmeasurement',$this->data['find_hmeasurement']);
                                        $find_products = $find_products->get();

            $attribute_arr = [];
            if(isset($find_products) && !empty($find_products)) {
                foreach ($find_products as $fp_key => $fp_value) {

                    $old_value = $fp_value->hmeasurement;
                    $fp_value->hmeasurement = $this->data['replace_hmeasurement'];
                    $fp_value->save();

                    $attribute_arr[] = [
                        'old_value' => $old_value,
                        'new_value' => $this->data['replace_hmeasurement'],
                        'attribute_name' => 'hmeasurement',
                        'attribute_id' => $this->data['attribute_id'],
                        'product_id' => $fp_value->id,
                        'user_id' => $userId,
                    ];
                }

            }

            if(!empty( $attribute_arr )) {
                $productUpdatedAttributeHistory = \App\ProductUpdatedAttributeHistory::insert($attribute_arr);
            }
            
            
            $find_products = Product::where('status_id',$this->data['attribute_id']);
                                        $find_products->where('dmeasurement',$this->data['find_dmeasurement']);
                                        $find_products = $find_products->get();

            $attribute_arr = [];
            if(isset($find_products) && !empty($find_products)) {
                foreach ($find_products as $fp_key => $fp_value) {

                    $old_value = $fp_value->dmeasurement;
                    $fp_value->dmeasurement = $this->data['replace_dmeasurement'];
                    $fp_value->save();

                    $attribute_arr[] = [
                        'old_value' => $old_value,
                        'new_value' => $this->data['replace_dmeasurement'],
                        'attribute_name' => 'dmeasurement',
                        'attribute_id' => $this->data['attribute_id'],
                        'product_id' => $fp_value->id,
                        'user_id' => $userId,
                    ];
                }

            }

            if(!empty( $attribute_arr )) {
                $productUpdatedAttributeHistory = \App\ProductUpdatedAttributeHistory::insert($attribute_arr);
            }

                            
        } else if($this->data['attribute_id'] == StatusHelper::$unknownCategory) {
            
            $find_products = Product::where('status_id',$this->data['attribute_id']);
                                    $find_products->where('category',$this->data['find_category']);
                                    $find_products = $find_products->get();
            
            $attribute_arr = [];
            if(isset($find_products) && !empty($find_products)) {
                foreach ($find_products as $fp_key => $fp_value) {
                    
                    $old_value = $fp_value->category;
                    $fp_value->category = $this->data['replace_category'];
                    $fp_value->save();
            
                    $attribute_arr[] = [
                        'old_value' => $old_value,
                        'new_value' => $this->data['replace_category'],
                        'attribute_name' => 'category',
                        'attribute_id' => $this->data['attribute_id'],
                        'product_id' => $fp_value->id,
                        'user_id' => $userId,
                    ];
                }
               
            }
            
            if(!empty( $attribute_arr )) {
                $productUpdatedAttributeHistory = \App\ProductUpdatedAttributeHistory::insert($attribute_arr);
            }
                                    
        } else if($this->data['attribute_id'] == StatusHelper::$unknownColor) {
                                
            $find_products = Product::where('status_id',$this->data['attribute_id']);
                                $find_products->where('color',$this->data['find_color']);
                                $find_products = $find_products->get();
        
            $attribute_arr = [];
            if(isset($find_products) && !empty($find_products)) {
                foreach ($find_products as $fp_key => $fp_value) {
                    
                    $old_value = $fp_value->color;
                    $fp_value->color = $this->data['replace_color'];
                    $fp_value->save();
            
                    $attribute_arr[] = [
                        'old_value' => $old_value,
                        'new_value' => $this->data['replace_color'],
                        'attribute_name' => 'color',
                        'attribute_id' => $this->data['attribute_id'],
                        'product_id' => $fp_value->id,
                        'user_id' => $userId,
                    ];
                }
            
            }
            
            if(!empty( $attribute_arr )) {
                $productUpdatedAttributeHistory = \App\ProductUpdatedAttributeHistory::insert($attribute_arr);
            }
        }
    }
}
