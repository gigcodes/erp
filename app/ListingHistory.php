<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListingHistory extends Model
{
    protected $casts = [
        'content' => 'array'
    ];

    public static function createNewListing( $userId = NULL, $productId = NULL, $content = [], $action = NULL )
    {
        // Create new activity for listing history
        $listingHistory = new ListingHistory();
        $listingHistory->user_id = $userId;
        $listingHistory->product_id = $productId;
        $listingHistory->content = $content;
        $listingHistory->action = $action;
        return $listingHistory->save();
    }

    public function user()
    {
        return $this->belongsTo( User::class );
    }
}
