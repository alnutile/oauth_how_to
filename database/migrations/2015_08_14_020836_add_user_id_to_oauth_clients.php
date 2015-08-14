<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdToOauthClients extends Migration
{
    public function up()
    {
        Schema::table('oauth_clients', function(Blueprint $table)
        {
            $table->string('user_id', 36)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oauth_clients', function(Blueprint $table)
        {
            if(Schema::hasColumn('oauth_clients', 'user_id'))
                $table->dropColumn('user_id');

        });
    }
}
