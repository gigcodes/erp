<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Nestable\NestableTrait;

class Category extends Model
{

    use NestableTrait;

    protected $parent = 'parent_id';

    public $fillable = [ 'title', 'parent_id', 'magento_id', 'show_all_id' ];

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function childs()
    {
        return $this->hasMany( __CLASS__, 'parent_id', 'id' );
    }

    public function parent()
    {
        return $this->hasOne( 'App\Category', 'id', 'parent_id' );
    }

    public static function isParent( $id )
    {

        $child_count = DB::table( 'categories as c' )
            ->where( 'parent_id', $id )
            ->count();

        return $child_count ? true : false;
    }


    public static function hasProducts( $id )
    {

        $products_count = DB::table( 'products as p' )
            ->where( 'category', $id )
            ->count();

        return $products_count ? true : false;

    }

    public static function getCategoryIdByKeyword( $keyword, $gender=null, $genderAlternative=null )
    {
        // Set gender
        if ( empty( $gender ) ) {
            $gender = $genderAlternative;
        }

        // Check database for result
        $dbResult = self::where( 'title', $keyword )->get();

        // No result? Try where like
        if ( $dbResult->count() == 0 ) {
            $dbResult = self::where( 'references', 'like', '%' . $keyword . '%' )->get();
        }

        // Still no result
        if ( $dbResult === NULL ) {
            return 0;
        }

        // Just one result
        if ( $dbResult->count() == 1 ) {
            return $dbResult->first()->id;
        }

        // Checking the result by gender only works if the gender is set
        if ( empty( $gender ) ) {
            return 0;
        }

        // Check results
        foreach ( $dbResult as $result ) {
            // Get parent Id
            $parentId = $result->parent_id;

            // Return 0 for a top category
            if ( $parentId == 0 ) {
                return $result->id;
            }

            // Return correct result by gender
            if ( $parentId == 2 && strtolower( $gender ) == 'women' ) {
                return $result->id;
            }

            // Return correct result by gender
            if ( $parentId == 3 && strtolower( $gender ) == 'men' ) {
                return $result->id;
            }

            // Other
            if ( $parentId > 0 ) {
                // Store category ID
                $categoryId = $result->id;

                // Get parent
                $dbParentResult = Category::find( $result->parent_id );

                // No result
                if ( $dbParentResult->count() == 0 ) {
                    return 0;
                }

                // Return correct result for women
                if ( $dbParentResult->parent_id == 2 && strtolower( $gender ) == 'women' ) {
                    return $categoryId;
                }

                // Return correct result for men
                if ( $dbParentResult->parent_id == 3 && strtolower( $gender ) == 'men' ) {
                    return $categoryId;
                }
            }
        }
    }

    public static function getCategoryTreeMagento( $id )
    {
        // Load new category model
        $category = new Category();

        // Create category instance
        $categoryInstance = $category->find( $id );

        // Set empty category tree for holding categories
        $categoryTree = [];

        // Continue only if category is not null
        if ( $categoryInstance !== NULL ) {

            // Load initial category
            $categoryTree[] = $categoryInstance->magento_id;

            // Set parent ID
            $parentId = $categoryInstance->parent_id;

            // Loop until we found the top category
            while ( $parentId != 0 ) {
                // find next category
                $categoryInstance = $category->find( $parentId );

                // Add category to tree
                $categoryTree[] = $categoryInstance->magento_id;

                // Add additional category to tree
                if ( !empty( $categoryInstance->show_all_id ) )
                    $categoryTree[] = $categoryInstance->show_all_id;

                // Set new parent ID
                $parentId = $categoryInstance->parent_id;
            }
        }

        // Return reverse array
        return array_reverse( $categoryTree );
    }

}
