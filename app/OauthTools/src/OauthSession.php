<?php

namespace AlfredNutileInc\OauthTools;

use Illuminate\Database\Eloquent\Model;

class OauthSession extends Model
{
    public $incrementing = true;

    public function oauth_client()
    {
        return $this->belongsTo(\AlfredNutileInc\OauthTools\OauthClient::class, 'client_id', 'id');
    }

    public function oauth_access_tokens()
    {
        return $this->hasOne(\AlfredNutileInc\OauthTools\OauthAccessToken::class, 'session_id', 'id');
    }


}
