<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean("viewed")->default(false) ; //* زمانی که کاربر میبینه درخواست رو *//
            $table->integer('status')->default(0) ; // 0 default not action , 1 accepted , 2 rejected
            $table->boolean('default_plan')->default(true) ; // agar plan ma default boot hatman in ra true kon ;
            $table->unsignedInteger("user_id") ;
            $table->unsignedInteger("team_id") ;
            $table->unsignedInteger("plan_user_id")->nullable() ;

            $table->ipAddress("user_ip")->nullable() ;
            $table->text("content")->nullable() ;            
            $table->timestamp("created_at")->default(\DB::raw("CURRENT_TIMESTAMP"));

            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade")->onUpdate('cascade') ;
            $table->foreign("team_id")->references("id")->on("teams")->onDelete("cascade")->onUpdate('cascade') ;
            $table->foreign("plan_user_id")->references("id")->on("plan_user")->onDelete("cascade")->onUpdate("cascade") ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers');
    }
}
