<?php

namespace AlfredNutileInc\OauthTools;

use Illuminate\Database\Eloquent\Model;

class OauthRefreshToken extends Model
{
    public function oauth_access_token()
    {
        return $this->belongsTo(\AlfredNutileInc\OauthTools\OauthAccessToken::class, 'access_token_id', 'id');
    }
}
