<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class DownloadDatabaseEnvLogs extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     */
    protected $fillable = ['store_website_id', 'user_id', 'type', 'cmd', 'output', 'return_var'];
    
    //Tell laravel to fetch text values and set them as arrays
    protected $casts = [
        'output' => 'array',
    ];
    
    protected $appends = ['output_string'];

    // $return_var === 0 - Command executed successfully
    // $return_var != 0 - Command failed to execute. Error code is returing in this varibale. 
    public function saveLog($store_website_id, $user_id, $type, $cmd, $output=[], $return_var = null)
    {
        $this->store_website_id = $store_website_id;
        $this->user_id = $user_id;
        $this->type = $type;
        $this->cmd = $cmd;
        $this->output = $output;
        $this->return_var = $return_var;
        $this->save();
    }

    public function storeWebsite()
    {
        return $this->belongsTo(\App\StoreWebsite::class, 'store_website_id', 'id');
    }
    
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }
    
    public function getOutputStringAttribute()
    {
        if (is_array($this->output)) {
            return json_encode($this->output);
        }

        return $this->output;
    }
}
