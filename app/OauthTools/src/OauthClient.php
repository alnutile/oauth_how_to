<?php

namespace AlfredNutileInc\OauthTools;

use Illuminate\Database\Eloquent\Model;

class OauthClient extends Model
{

    protected $hidden = ['secret'];

    protected $fillable = ['user_id'];


    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function oauth_session()
    {
        return $this->hasOne(\AlfredNutileInc\OauthTools\OauthSession::class, 'client_id', 'id');
    }
}
