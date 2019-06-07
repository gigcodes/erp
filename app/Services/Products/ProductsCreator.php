<?php

namespace App\Services\Products;

use App\Brand;
use App\Product;
use App\Setting;
use App\Category;
use App\Supplier;
use Validator;
use Storage;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class ProductsCreator
{
    public function createProduct($image)
    {
      $data['sku'] = (string) str_replace(' ', '', $image->sku);
      $validator = Validator::make($data, [
        'sku' => 'unique:products,sku'
      ]);

      if ($validator->fails()) {
        $product = Product::where('sku', $data['sku'])->first();
      } else {
        $product = new Product;
      }

      if ($product === null) {
          echo "SKIPPED ============================================== \n";
          return;
      }


      $properties_array = $image->properties;

      switch ($image->website) {
        case 'lidiashopping':
          $supplier = 'Lidia';
          $formatted_details = $this->getLidiaDetails($properties_array);

          break;
        case 'cuccuini':
          $supplier = 'Cuccini';
          $formatted_details = $this->getCucciniDetails($properties_array);

          break;
        case 'DoubleF':
          $supplier = 'Double F';
          $formatted_details = $this->getDoubleDetails($properties_array);

          break;
        case 'G&B':
          $supplier = 'G & B Negozionline';
          $formatted_details = $this->getGnbDetails($properties_array);

          break;
        case 'Tory':
          $supplier = 'Tory Burch';
          $formatted_details = $this->getToryDetails($properties_array);

          break;
        case 'Wiseboutique':
          $supplier = 'Wise Boutique';
          $formatted_details = $this->getWiseDetails($properties_array);

          break;
        case 'Divo':
          $supplier = 'Divo Boutique';
          $formatted_details = $this->getDivoDetails($properties_array);

          break;
        case 'Spinnaker':
          $supplier = 'Spinnaker 101';
          $formatted_details = $this->getSpinnakerDetails($properties_array);

          break;
        case 'alducadaosta':
          $supplier = "Al Duca d'Aosta";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'biffi':
          $supplier = "Biffi Boutique (S.P.A.)";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'brunarosso':
          $supplier = "BRUNA ROSSO";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'carofigliojunior':
          $supplier = "Carofiglio Junior";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'italiani':
          $supplier = "Italiani";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'coltorti':
          $supplier = "Coltorti";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'griffo210':
          $supplier = "Grifo210";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'linoricci':
          $supplier = "Lino Ricci Lei";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'conceptstore':
          $supplier = "Women Concept Store Cagliari";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'deliberti':
          $supplier = "Deliberti";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'giglio':
          $supplier = "Giglio Lamezia Terme";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'laferramenta':
          $supplier = "La Ferramenta";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'les-market':
          $supplier = "Les Market";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'leam':
          $supplier = "Leam";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'mimmaninnishop':
          $supplier = "Mimma Ninni Boutique";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'montiboutique':
          $supplier = "Monti";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'nugnes1920':
          $supplier = "Nugnes 1920";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'railso':
          $supplier = "Rail";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'savannahs':
          $supplier = "Savannah's";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
        case 'tessabit':
          $supplier = "Tessabit";
          $formatted_details = $this->getGeneralDetails($properties_array);

          break;
          case 'stilmoda':
            $supplier = "Stilmoda";
            $formatted_details = $this->getGeneralDetails($properties_array);

            break;
          case 'tizianafausti':
            $supplier = "Tiziana Fausti";
            $formatted_details = $this->getGeneralDetails($properties_array);

            break;
          case 'vinicio':
            $supplier = "Vinicio";
            $formatted_details = $this->getGeneralDetails($properties_array);

            break;
          case 'mariastore':
            $supplier = "MARIA STORE";
            $formatted_details = $this->getGeneralDetails($properties_array);

            break;
          case 'angelominetti':
            $supplier = "MINETTI";
            $formatted_details = $this->getGeneralDetails($properties_array);

            break;
        default:
          return;
      }

       $product->sku = str_replace(' ', '', $image->sku);
       $product->brand = $image->brand_id;
       $product->supplier = $supplier;
       $product->name = $image->title;
       $product->short_description = $image->description;
       $product->supplier_link = $image->url;
       $product->stage = 3;
       $product->is_scraped = 1;
       $product->stock = 1;
       $product->is_without_image = 1;
       $product->is_on_sale = $image->is_sale ? 1 : 0;

       $product->composition = $formatted_details['composition'];
       $product->color = $formatted_details['color'];
       $product->size = $formatted_details['size'];
       $product->lmeasurement = (int) $formatted_details['lmeasurement'];
       $product->hmeasurement = (int) $formatted_details['hmeasurement'];
       $product->dmeasurement = (int) $formatted_details['dmeasurement'];
       $product->measurement_size_type = $formatted_details['measurement_size_type'];
       $product->made_in = $formatted_details['made_in'];
       $product->category = $formatted_details['category'];

       $brand = Brand::find($image->brand_id);

       if (strpos($image->price, ',') !== false) {
         if (strpos($image->price, '.') !== false) {
           if (strpos($image->price, ',') < strpos($image->price, '.')) {
             $final_price = str_replace(',', '', $image->price);;
           } else {
             $final_price = $image->price;
           }
         } else {
           $final_price = str_replace(',', '.', $image->price);
         }
       } else {
         $final_price = $image->price;
       }

       $price =  round(preg_replace('/[\&euro;€,]/', '', $final_price));
       $product->price = $price;

       if(!empty($brand->euro_to_inr))
         $product->price_inr = $brand->euro_to_inr * $product->price;
       else
         $product->price_inr = Setting::get('euro_to_inr') * $product->price;

       $product->price_inr = round($product->price_inr, -3);
       $product->price_special = $product->price_inr - ($product->price_inr * $brand->deduction_percentage) / 100;

       $product->price_special = round($product->price_special, -3);

       $product->save();

       if ($db_supplier = Supplier::where('supplier', $supplier)->first()) {
         $product->suppliers()->syncWithoutDetaching($db_supplier->id);
       }

       // $images = $image->images;
       //
       // $product->detachMediaTags(config('constants.media_tags'));
       //
       // foreach ($images as $image_name) {
       //   // Storage::disk('uploads')->delete('/social-media/' . $image_name);
       //
       //   $path = public_path('uploads') . '/social-media/' . $image_name;
       //   $media = MediaUploader::fromSource($path)->upload();
       //   $product->attachMedia($media,config('constants.media_tags'));
       // }

    }

    public function getDoubleDetails($properties_array)
    {
      if (array_key_exists('Composition', $properties_array)) {
        $composition = (string) $properties_array['Composition'];
      }

      if (array_key_exists('Color code', $properties_array)) {
        $color = $properties_array['Color code'];
      }

      if (array_key_exists('Made In', $properties_array)) {
        $made_in = $properties_array['Made In'];
      }

      if (array_key_exists('category', $properties_array)) {
        $categories = Category::all();
        $category_id = 1;

        foreach ($properties_array['category'] as $key => $cat) {
          $up_cat = strtoupper($cat);

          if ($up_cat == 'WOMAN') {
            $up_cat = 'WOMEN';
          }

          if ($key == 0 && $up_cat == 'WOMEN') {
            $women_children = Category::where('title', 'WOMEN')->first()->childs;
          }

          if (isset($women_children)) {
            foreach ($women_children as $children) {
              if (strtoupper($children->title) == $up_cat) {
                $category_id = $children->id;
              }

              foreach ($children->childs as $child) {
                if (strtoupper($child->title) == $up_cat) {
                  $category_id = $child->id;
                }
              }
            }
          } else {
            foreach ($categories as $category) {
              if (strtoupper($category->title) == $up_cat) {
                $category_id = $category->id;
              }
            }
          }

        }

        $category = $category_id;
      }

      return [
        'composition' => isset($composition) ? $composition : '',
        'color' => isset($color) ? $color : '',
        'size' => '',
        'lmeasurement' => '',
        'hmeasurement' => '',
        'dmeasurement' => '',
        'measurement_size_type' => '',
        'made_in' => isset($made_in) ? $made_in : '',
        'category' => isset($category) ? $category : 1,
      ];
    }

    public function getLidiaDetails($properties_array)
    {
      if (array_key_exists('material_used', $properties_array)) {
        $composition = $properties_array['material_used'];
      }

      if (array_key_exists('color', $properties_array)) {
        $color = $properties_array['color'];
      }

      if (array_key_exists('sizes', $properties_array)) {
        $sizes = $properties_array['sizes'];
        $imploded_sizes = implode(',', $sizes);
        $size = $imploded_sizes;
      }

      if (array_key_exists('Category', $properties_array)) {
        $categories = Category::all();
        $category_id = 1;

        foreach ($properties_array['Category'] as $cat) {
          if ($cat == 'WOMAN') {
            $cat = 'WOMEN';
          }

          foreach ($categories as $category) {
            if (strtoupper($category->title) == $cat) {
              $category_id = $category->id;
            }
          }
        }

        $category = $category_id;
      }

      return [
        'composition' => isset($composition) ? $composition : '',
        'color' => isset($color) ? $color : '',
        'size' => isset($size) ? $size : '',
        'lmeasurement' => '',
        'hmeasurement' => '',
        'dmeasurement' => '',
        'measurement_size_type' => '',
        'made_in' => '',
        'category' => isset($category) ? $category : 1,
      ];
    }

    public function getCucciniDetails($properties_array)
    {
      if (array_key_exists('COMPOSIZIONE', $properties_array)) {
        $composition = $properties_array['COMPOSIZIONE'];
      }

      if (array_key_exists('COLORI', $properties_array)) {
        $color = $properties_array['COLORI'];
      }

      return [
        'composition' => isset($composition) ? $composition : '',
        'color' => isset($color) ? $color : '',
        'size' => '',
        'lmeasurement' => '',
        'hmeasurement' => '',
        'dmeasurement' => '',
        'measurement_size_type' => '',
        'made_in' => '',
        'category' => 1,
      ];
    }

    public function getGnbDetails($properties_array)
    {
      if (array_key_exists('Details', $properties_array)) {
        if (strpos($properties_array['Details'], 'Made in') !== false) {
          $made_in = str_replace('\n', '', substr($properties_array['Details'], strpos($properties_array['Details'], 'Made in') + 8));

          $composition = str_replace('\n', ' ', substr($properties_array['Details'], 0, strpos($properties_array['Details'], 'Made in')));
        } else {
          $composition = (string) $properties_array['Details'];
        }
      }

      if (array_key_exists('Color Code', $properties_array)) {
        $color = $properties_array['Color Code'];
      }

      if (array_key_exists('sizes', $properties_array)) {
        $size = implode(',', $properties_array['sizes']);
      }

      if (array_key_exists('Size & Fit', $properties_array)) {
        $sizes = $properties_array['Size & Fit'];
        if (strpos($sizes, 'Width:') !== false) {
          preg_match_all('/Width: ([\d]+)/', $sizes, $match);

          $lmeasurement = (int) $match[1][0];
          $measurement_size_type = 'measurement';
        }

        if (strpos($sizes, 'Height:') !== false) {
          if (preg_match_all('/Height: ([\d]+)/', $sizes, $match)) {
              $hmeasurement = (int) $match[1][0];
          }
        }

        if (strpos($sizes, 'Depth:') !== false) {
          if (preg_match_all('/Depth: ([\d]+)/', $sizes, $match)) {
              $dmeasurement = (int) $match[1][0];
          }
        }
      }

      return [
        'composition' => isset($composition) ? $composition : '',
        'color' => isset($color) ? $color : '',
        'size' => isset($size) ? $size : '',
        'lmeasurement' => isset($lmeasurement) ? $lmeasurement : '',
        'hmeasurement' => isset($hmeasurement) ? $hmeasurement : '',
        'dmeasurement' => isset($dmeasurement) ? $dmeasurement : '',
        'measurement_size_type' => isset($measurement_size_type) ? $measurement_size_type : '',
        'made_in' => isset($made_in) ? $made_in : '',
        'category' => 1,
      ];
    }

    public function getToryDetails($properties_array)
    {
      if (array_key_exists('color', $properties_array)) {
        $color = $properties_array['color'];
      }

      if (array_key_exists('sizes', $properties_array)) {
        $sizes = $properties_array['sizes'];
        if (array_key_exists('Length', $sizes)) {
          preg_match_all('/\s((.*))\s/', $sizes['Length'], $match);

          if (array_key_exists('0', $match[1])) {
            $lmeasurement = (int) $match[1][0];
            $measurement_size_type = 'measurement';
          }
        }
      }

      return [
        'composition' => '',
        'color' => isset($color) ? $color : '',
        'size' => '',
        'lmeasurement' => isset($lmeasurement) ? $lmeasurement : '',
        'hmeasurement' => '',
        'dmeasurement' => '',
        'measurement_size_type' => isset($measurement_size_type) ? $measurement_size_type : '',
        'made_in' => '',
        'category' => 1,
      ];
    }

    public function getWiseDetails($properties_array)
    {
      if (array_key_exists('1', $properties_array)) {
        $composition = (string) $properties_array['1'];
      }

      if (array_key_exists('Colors', $properties_array)) {
        $color = $properties_array['Colors'];
      }

      if (array_key_exists('sizes', $properties_array)) {
        $size = implode(',', $properties_array['sizes']);
      }

      foreach ($properties_array as $property) {
        if (!is_array($property)) {
          if (strpos($property, 'Width:') !== false) {
            preg_match_all('/Width: ([\d]+)/', $property, $match);

            $lmeasurement = (int) $match[1];
            $measurement_size_type = 'measurement';
          }

          if (strpos($property, 'Height:') !== false) {
            preg_match_all('/Height: ([\d]+)/', $property, $match);

            $hmeasurement = (int) $match[1];
          }

          if (strpos($property, 'Depth:') !== false) {
            preg_match_all('/Depth: ([\d]+)/', $property, $match);

            $dmeasurement = (int) $match[1];
          }
        }
      }

      if (array_key_exists('category', $properties_array)) {
        $categories = Category::all();
        $category_id = 1;

        foreach ($properties_array['category'] as $key => $cat) {
          $up_cat = strtoupper($cat);

          if ($up_cat == 'WOMAN') {
            $up_cat = 'WOMEN';
          }

          if ($key == 0 && $up_cat == 'WOMEN') {
            $women_children = Category::where('title', 'WOMEN')->first()->childs;
          }

          if (isset($women_children)) {
            foreach ($women_children as $children) {
              if (strtoupper($children->title) == $up_cat) {
                $category_id = $children->id;
              }

              foreach ($children->childs as $child) {
                if (strtoupper($child->title) == $up_cat) {
                  $category_id = $child->id;
                }
              }
            }
          } else {
            foreach ($categories as $category) {
              if (strtoupper($category->title) == $up_cat) {
                $category_id = $category->id;
              }
            }
          }

        }

        $category = $category_id;
      }

      return [
        'composition' => isset($composition) ? $composition : '',
        'color' => isset($color) ? $color : '',
        'size' => isset($size) ? $size : '',
        'lmeasurement' => isset($lmeasurement) ? $lmeasurement : '',
        'hmeasurement' => isset($hmeasurement) ? $hmeasurement : '',
        'dmeasurement' => isset($dmeasurement) ? $dmeasurement : '',
        'measurement_size_type' => isset($measurement_size_type) ? $measurement_size_type : '',
        'made_in' => '',
        'category' => isset($category) ? $category : 1,
      ];
    }

    public function getDivoDetails($properties_array)
    {
      if (array_key_exists('materialUsed', $properties_array)) {
        $composition = (string) $properties_array['materialUsed'];
      }

      if (array_key_exists('color', $properties_array)) {
        $color = $properties_array['color'];
      }

      // foreach ($properties_array as $property) {
      //   if (!is_array($property)) {
      //     if (strpos($property, 'Width:') !== false) {
      //       preg_match_all('/Width: ([\d]+)/', $property, $match);
      //
      //       $lmeasurement = (int) $match[1];
      //       $measurement_size_type = 'measurement';
      //     }
      //
      //     if (strpos($property, 'Height:') !== false) {
      //       preg_match_all('/Height: ([\d]+)/', $property, $match);
      //
      //       $hmeasurement = (int) $match[1];
      //     }
      //
      //     if (strpos($property, 'Depth:') !== false) {
      //       preg_match_all('/Depth: ([\d]+)/', $property, $match);
      //
      //       $dmeasurement = (int) $match[1];
      //     }
      //   }
      // }

      if (array_key_exists('category', $properties_array)) {
        $categories = Category::all();
        $category_id = 1;

        foreach ($properties_array['category'] as $key => $cat) {
          $up_cat = strtoupper($cat);

          if ($up_cat == 'WOMAN') {
            $up_cat = 'WOMEN';
          }

          if ($key == 0 && $up_cat == 'WOMEN') {
            $women_children = Category::where('title', 'WOMEN')->first()->childs;
          }

          if (isset($women_children)) {
            foreach ($women_children as $children) {
              if (strtoupper($children->title) == $up_cat) {
                $category_id = $children->id;
              }

              foreach ($children->childs as $child) {
                if (strtoupper($child->title) == $up_cat) {
                  $category_id = $child->id;
                }
              }
            }
          } else {
            foreach ($categories as $category) {
              if (strtoupper($category->title) == $up_cat) {
                $category_id = $category->id;
              }
            }
          }

        }

        $category = $category_id;
      }

      if (array_key_exists('country', $properties_array)) {
        $made_in = $properties_array['country'];
      }

      return [
        'composition' => isset($composition) ? $composition : '',
        'color' => isset($color) ? $color : '',
        'size' => '',
        'lmeasurement' => '',
        'hmeasurement' => '',
        'dmeasurement' => '',
        'measurement_size_type' => '',
        'made_in' => isset($made_in) ? $made_in : '',
        'category' => isset($category) ? $category : 1,
      ];
    }

    public function getSpinnakerDetails($properties_array)
    {
      if (array_key_exists('material_used', $properties_array)) {
        $composition = (string) $properties_array['material_used'];
      }

      if (array_key_exists('color', $properties_array)) {
        $color = $properties_array['color'];
      }

      if (array_key_exists('sizes', $properties_array)) {
        $sizes = $properties_array['sizes'];
        $size = implode(',', $sizes);
        // if (array_key_exists('Length', $sizes)) {
        //   preg_match_all('/\s((.*))\s/', $sizes['Length'], $match);
        //
        //   if (array_key_exists('0', $match[1])) {
        //     $lmeasurement = (int) $match[1][0];
        //     $measurement_size_type = 'measurement';
        //   }
        // }
      }

      // foreach ($properties_array as $property) {
      //   if (!is_array($property)) {
      //     if (strpos($property, 'Width:') !== false) {
      //       preg_match_all('/Width: ([\d]+)/', $property, $match);
      //
      //       $lmeasurement = (int) $match[1];
      //       $measurement_size_type = 'measurement';
      //     }
      //
      //     if (strpos($property, 'Height:') !== false) {
      //       preg_match_all('/Height: ([\d]+)/', $property, $match);
      //
      //       $hmeasurement = (int) $match[1];
      //     }
      //
      //     if (strpos($property, 'Depth:') !== false) {
      //       preg_match_all('/Depth: ([\d]+)/', $property, $match);
      //
      //       $dmeasurement = (int) $match[1];
      //     }
      //   }
      // }

      // if (array_key_exists('category', $properties_array)) {
      //   $categories = Category::all();
      //   $category_id = 1;
      //   $cat = strtoupper($properties_array['category']);
      //   // foreach ($properties_array['category'] as $cat) {
      //     if ($cat == 'WOMAN') {
      //       $cat = 'WOMEN';
      //     }
      //
      //     foreach ($categories as $category) {
      //       if (strtoupper($category->title) == $cat) {
      //         $category_id = $category->id;
      //       }
      //     }
      //   // }
      //
      //   $category = $category_id;
      // }

      if (array_key_exists('country', $properties_array)) {
        $made_in = $properties_array['country'];
      }

      return [
        'composition' => isset($composition) ? $composition : '',
        'color' => isset($color) ? $color : '',
        'size' => isset($size) ? $size : '',
        'lmeasurement' => '',
        'hmeasurement' => '',
        'dmeasurement' => '',
        'measurement_size_type' => '',
        'made_in' => isset($made_in) ? $made_in : '',
        'category' => isset($category) ? $category : 1,
      ];
    }

    public function getGeneralDetails($properties_array)
    {
      if (array_key_exists('material_used', $properties_array)) {
        $composition = (string) $properties_array['material_used'];
      }

      if (array_key_exists('color', $properties_array)) {
        $color = $properties_array['color'];
      }

      if (array_key_exists('sizes', $properties_array)) {
        $sizes = $properties_array['sizes'];
        $size = implode(',', $sizes);
      }

      if (array_key_exists('dimension', $properties_array)) {
        if (!is_array($properties_array['dimension'])) {
          if (strpos($properties_array['dimension'], 'Width:') !== false) {
            if (preg_match_all('/Width: ([\d]+)/', $properties_array['dimension'], $match)) {
              $lmeasurement = (int) $match[1][0];
              $measurement_size_type = 'measurement';
            }

          }

          if (strpos($properties_array['dimension'], 'Height:') !== false) {
            if (preg_match_all('/Height: ([\d]+)/', $properties_array['dimension'], $match)) {
              $hmeasurement = (int) $match[1][0];
            }
          }

          if (strpos($properties_array['dimension'], 'Depth:') !== false) {
            if (preg_match_all('/Depth: ([\d]+)/', $properties_array['dimension'], $match)) {
              $dmeasurement = (int) $match[1][0];
            }
          }
        }
      }

      if (array_key_exists('category', $properties_array)) {
        $categories = Category::all();
        $category_id = 1;

        foreach ($properties_array['category'] as $key => $cat) {
          $up_cat = strtoupper($cat);

          if ($up_cat == 'WOMAN') {
            $up_cat = 'WOMEN';
          }

          if ($key == 0 && $up_cat == 'WOMEN') {
            $women_children = Category::where('title', 'WOMEN')->first()->childs;
          }

          if (isset($women_children)) {
            foreach ($women_children as $children) {
              if (strtoupper($children->title) == $up_cat) {
                $category_id = $children->id;
              }

              foreach ($children->childs as $child) {
                if (strtoupper($child->title) == $up_cat) {
                  $category_id = $child->id;
                }
              }
            }
          } else {
            foreach ($categories as $category) {
              if (strtoupper($category->title) == $up_cat) {
                $category_id = $category->id;
              }
            }
          }

        }

        $category = $category_id;
      }

      if (array_key_exists('country', $properties_array)) {
        $made_in = $properties_array['country'];
      }

      return [
        'composition' => isset($composition) ? $composition : '',
        'color' => isset($color) ? $color : '',
        'size' => isset($size) ? $size : '',
        'lmeasurement' => isset($lmeasurement) ? $lmeasurement : '',
        'hmeasurement' => isset($hmeasurement) ? $hmeasurement : '',
        'dmeasurement' => isset($dmeasurement) ? $dmeasurement : '',
        'measurement_size_type' => isset($measurement_size_type) ? $measurement_size_type : '',
        'made_in' => isset($made_in) ? $made_in : '',
        'category' => isset($category) ? $category : 1,
      ];
    }
}
