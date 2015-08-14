<?php

namespace AlfredNutileInc\OauthTools;

use AlfredNutileInc\OauthTools\OauthClient;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Rhumsaa\Uuid\Uuid;

class OauthToolGenerateToken extends Command
{
    protected $signature = 'oauth-tools:generate-token
                                {client_id}
                                {owner_email}
                                {--grant_type=user}';

    protected $description = 'Create user token set pass the know client_id and email for user account';
    /**
     * @var OauthService
     */
    private $service;

    public function __construct(OauthService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client_id = $this->argument('client_id');

        $grant_type = $this->option('grant_type');

        $owner_email = $this->argument('owner_email');

        $owner = $this->getService()->checkerUserExists($owner_email);

        $client = $this->getService()->checkIfClientExist($client_id);

        $session_id = Uuid::uuid4()->toString();
        $this->getService()->createSession($session_id, $client_id, $owner, $grant_type);

        $token_id = Uuid::uuid4()->toString();
        $this->getService()->createAccessToken($token_id, $session_id);

        $refresh_token_id = Uuid::uuid4()->toString();
        $this->getService()->createRefreshTokenId($refresh_token_id, $token_id);

        $this->info("Done making Token");
        $this->info(sprintf("Your token is %s", $token_id));
        $this->info(sprintf("Your refresh token is %s", $refresh_token_id));

    }


    /**
     * @return OauthService
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param OauthService $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

}
