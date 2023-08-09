<?php

namespace Database\Seeders;

use App\GoogleDoc;
use Illuminate\Database\Seeder;
use App\Models\GoogleDocsCategory;

class GoogleDocsCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $googleDocCategory = GoogleDoc::distinct('category')->get('category')->pluck('category');
            // dd($googleDocCategory);
            $defaultCategory = new GoogleDocsCategory;
            $defaultCategory->name = 'default';
            $defaultCategory->save();

            $insertCategory = [];

            if (! empty($googleDocCategory)) {
                foreach ($googleDocCategory as $key => $category) {
                    if ($category == '' || $category == null) {
                        GoogleDoc::where('category', $category)->update([
                            'category' => $defaultCategory->id,
                        ]);
                    } else {
                        $docCategory = new GoogleDocsCategory;
                        $docCategory->name = $category;
                        $docCategory->save();

                        GoogleDoc::where('category', $docCategory->name)->update([
                            'category' => $docCategory->id,
                        ]);
                    }
                }
                dd('Migrated successfuly');
            } else {
                dd('Category is empty');
            }
        } catch (\Exception $e) {
            dd('Error while seeding google category data');
        }
    }
}
