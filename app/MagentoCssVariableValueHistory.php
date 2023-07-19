<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use App\Models\MagentoCssVariable;
use Illuminate\Database\Eloquent\Model;

class MagentoCssVariableValueHistory extends Model
{

    public function magentoCssVariable()
    {
        return $this->belongsTo(MagentoCssVariable::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}