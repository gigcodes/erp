<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PinterestBoardSections extends Model
{
    protected $fillable = [
        'pinterest_ads_account_id',
        'pinterest_board_id',
        'board_section_id',
        'name',
    ];

    public function account()
    {
        return $this->hasOne(PinterestAdsAccounts::class, 'id', 'pinterest_ads_account_id');
    }

    public function board()
    {
        return $this->hasOne(PinterestBoards::class, 'id', 'pinterest_board_id');
    }
}
