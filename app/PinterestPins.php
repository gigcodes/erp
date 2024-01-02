<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PinterestPins extends Model
{
    protected $fillable = [
        'pinterest_ads_account_id',
        'pin_id',
        'link',
        'title',
        'description',
        'alt_text',
        'pinterest_board_id',
        'pinterest_board_section_id',
        'media_source',
    ];

    public function account()
    {
        return $this->hasOne(PinterestAdsAccounts::class, 'id', 'pinterest_ads_account_id');
    }

    public function board()
    {
        return $this->hasOne(PinterestBoards::class, 'id', 'pinterest_board_id');
    }

    public function boardSection()
    {
        return $this->hasOne(PinterestBoardSections::class, 'id', 'pinterest_board_section_id');
    }
}
