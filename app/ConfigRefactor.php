<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class ConfigRefactor extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     */
    protected $fillable = [
        'store_website_id',
        'config_refactor_section_id', 
        'user_id',
        'step_1_status',
        'step_1_remark',
        'step_2_status',
        'step_2_remark',
        'step_3_status',
        'step_3_remark',
        'step_3_1_status',
        'step_3_1_remark',
        'step_3_2_status',
        'step_3_2_remark'
    ];

    public function storeWebsite()
    {
        return $this->belongsTo(\App\StoreWebsite::class);
    }

    public function configRefactorSection()
    {
        return $this->belongsTo(ConfigRefactorSection::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function step1Status()
    {
        return $this->belongsTo(ConfigRefactorStatus::class, 'step_1_status');
    }

    public function step2Status()
    {
        return $this->belongsTo(ConfigRefactorStatus::class, 'step_2_status');
    }

    public function step3Status()
    {
        return $this->belongsTo(ConfigRefactorStatus::class, 'step_3_status');
    }

    public function step31Status()
    {
        return $this->belongsTo(ConfigRefactorStatus::class, 'step_31_status');
    }

    public function step32Status()
    {
        return $this->belongsTo(ConfigRefactorStatus::class, 'step_32_status');
    }
}
