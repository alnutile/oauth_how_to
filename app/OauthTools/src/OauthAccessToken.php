<?php

namespace AlfredNutileInc\OauthTools;

use Illuminate\Database\Eloquent\Model;

class OauthAccessToken extends Model
{

    protected $fillable = [
        'session_id'
    ];

    public function oauth_session()
    {
        return $this->belongsTo(\AlfredNutileInc\OauthTools\OauthSession::class, 'session_id', 'id');
    }
}
