<?php

namespace AlfredNutileInc\OauthTools;

use AlfredNutileInc\OauthTools\OauthClient;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Rhumsaa\Uuid\Uuid;

class OauthToolAddUser extends Command
{
    protected $signature = 'oauth-tools:adduser {email} {reset-secret?}';

    protected $description = 'Create client id and secret';
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
        $user_email = $this->argument('email');


        if(!$system_user = User::where("email", $user_email)->first())
        {
            $password = $this->createSystemUser($user_email);

            $system_user = User::where("email", $user_email)->first();

            $this->info(sprintf("New System user with username %s and password %s made", $user_email, $password));
        }

        $reset_password = $this->argument('reset-secret');

        if($user = OauthClient::where('name', $user_email)->first())
        {
            $client_id = $user->id;

            if(!$reset_password)
            {
                $this->error("User already exists try the update reset-secret switch");
                exit();
            }
            else
            {
                $client_secret = $this->getService()->updateClientSecret($user);

                $this->info("Updated Client Secret $client_secret");
                $this->info("For Client Id $client_id");

                exit();
            }
        }

        $datetime = Carbon::now();

        $client_id = Uuid::uuid4()->toString();

        $client_secret = Str::random();

        $this->getService()->createOauthClient($client_id, $client_secret, $user_email, $datetime, $system_user->id);

        $this->info("Your Client_secret was set to $client_secret");
        $this->info("Your Client_id was set to $client_id");
        $this->info("Your name is $user_email");
    }

    /**
     * @param $user_email
     * @return string
     */
    protected function createSystemUser($user_email)
    {
        $password = Str::random();
        $password_hashed = bcrypt($password);

        $system_user = new User;
        $system_user->email = $user_email;
        $system_user->password = $password_hashed;
        $system_user->save();

        return $password;
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
