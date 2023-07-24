<?php

namespace App;

use App\Models\MagentoCssVariable;
use Illuminate\Database\Eloquent\Model;

class MagentoCssVariableJobLog extends Model
{
    protected $fillable = [
        'magento_css_variable_id',
        'command',
        'message',
        'status',
        'csv_file_path',
    ];

    public function magentoCssVariable()
    {
        return $this->belongsTo(MagentoCssVariable::class);
    }
}
