<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 8/13/15
 * Time: 7:37 AM
 */

namespace AlfredNutileInc\OauthTools;

use AlfredNutileInc\OauthTools\OauthClient;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Rhumsaa\Uuid\Uuid;

class OauthService {


    public function makeClientToken(User $user)
    {
        $user->load('oauth_client');

        if(!$user->oauth_client)
        {
            $client_id = $this->createOauthClient(false, false, $user->email, false, $user->id);
        }
        else
        {
            $client_id = $user->oauth_client->id;
        }

        $session_id = $this->createSession(false, $client_id, $user, 'client');

        $token_id = $this->createAccessToken(false, $session_id);

        return $token_id;
    }

    public function checkerUserExists($owner_email)
    {
        if(!$owner = User::where('email', $owner_email)->first())
        {
            $this->error("Looks like that email is not in the system start with the oauth-tools:adduser command");

            exit();
        }
        return $owner;
    }

    /**
     * @param $client_id
     */
    public function checkIfClientExist($client_id)
    {
        if (!$client = OauthClient::find($client_id)->first()) {
            $this->error("Looks like that ID is not in the system start with the oauth-tools:adduser command");

            exit();
        }
        return $client;
    }


    public function cleanUpPrevisousClientSessions($client_id)
    {
        if($sessions = OauthSession::where('client_id', $client_id))
        {
            foreach($sessions as $sesion)
            {
                $this->cleanUpExistingTokens($sesion->id);
            }
            $sessions->delete();
        }
    }

    /**
     * @param $session_id
     * @param $client_id
     * @param $owner
     */
    public function createSession($session_id = false, $client_id, User $owner, $type = 'user')
    {

        try
        {
            $session = new OauthSession();
            $session->client_id = $client_id;
            $session->owner_type = $type;
            $session->owner_id = $owner->id;
            $session->client_redirect_uri = '';

            $session->save();

            return $session->id;
        }
        catch(\Exception $e)
        {
            throw new \Exception(sprintf("Error making the Session %s", $e->getMessage()));
        }
    }

    public function cleanUpExistingTokens($session_id)
    {
        if($tokens = OauthAccessToken::where('session_id', $session_id))
        {
            $tokens->delete();
        }
    }

    /**
     * @param $token_id
     * @param $session_id
     */
    public function createAccessToken($token_id = false, $session_id)
    {
        if($token_id == false)
            $token_id = $this->getUuid();

        $token = new OauthAccessToken();
        $token->id = $token_id;
        $token->session_id = $session_id;
        $token->expire_time = Carbon::create()->addYears(10)->timestamp;
        $token->save();

        return $token_id;

    }

    public function createRefreshTokenId($refresh_id, $token_id)
    {
        $token = new OauthRefreshToken();
        $token->id = $token_id;
        $token->access_token_id = $token_id;
        $token->expire_time = Carbon::create()->addYears(10)->timestamp;
        $token->save();

    }

    public function createOauthClient($client_id = false, $client_secret = false, $user_email, $datetime = false, $user_id)
    {
        if($client_id == false)
            $client_id = $this->getUuid();

        if($client_secret == false)
            $client_secret = Str::random();

        if($datetime == false)
            $datetime = Carbon::create()->addYears(10)->timestamp;

        $client = new OauthClient();
        $client->id = $client_id;
        $client->secret = $client_secret;
        $client->name = $user_email;
        $client->created_at = $datetime;
        $client->updated_at = $datetime;
        $client->user_id = $user_id;
        $client->save();

        return $client_id;
    }

    public function updateClientSecret($user)
    {
        $client_secret = Str::random();

        $user->secret = $client_secret;

        $user->save();
        return $client_secret;
    }

    private function getUuid()
    {
        return Uuid::uuid4()->toString();
    }
}