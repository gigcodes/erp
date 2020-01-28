<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailinglistEmail extends Model
{
    public function audience()
    {
        return $this->hasOne(Mailinglist::class, 'id', 'mailinglist_id');
    }
    public function template()
    {
        return $this->hasOne(MailinglistTemplate::class, 'id', 'template_id');
    }
}
