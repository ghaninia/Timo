<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger("status")->default(0)->comment("0 => not confirmed , 1 => confirmed , 2 => expired") ;
            $table->timestamp('expired_at')->nullable() ; // zamani ke karbar plan dash in barasas plan taeed mishe

            $table->unsignedInteger("user_id") ; // maker team user id
            $table->unsignedInteger("plan_user_id")->nullable(); // maker team plan id

            $table->string("name") ;
            $table->string("slug")->nullable() ;


            // dar sorate pardaljt ghabel namayesh ast !
            $table->string("phone")->nullable() ;
            $table->string("fax")->nullable() ;
            $table->string("mobile")->nullable() ;
            $table->string("email")->nullable() ;
            $table->string("website")->nullable() ;


            $table->text("excerpt")->nullable() ;
            $table->text("content")->nullable() ;

            $table->integer('count_member')->default(1) ;
            $table->text('required_gender')->nullable() ;

            $table->text("type_assist")->nullable()  ; // نوع همکاری  dorkari,tamamvaght,parevaght,karamozi,
            $table->text("interplay_fiscal")->nullable() ; //نوع تعامل مالی : هم بنیان گذار / شراکتی حقوق ثابت

            $table->string("min_salary")->default(0) ;
            $table->string("max_salary")->default(0) ;


            $table->unsignedInteger("province_id")->nullable();
            $table->unsignedInteger("city_id")->nullable();
            $table->text("address")->nullable() ;


            $table->timestamps();

            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade")->onUpdate('cascade') ;
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
        Schema::dropIfExists('teams');
    }
}
