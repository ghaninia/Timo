<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

Route::namespace('Dashboard\User')->name('user.')->middleware(['auth:user' , 'check.plan'])->group(function (){

    Route::name('api.')->namespace('Api\\')->prefix('api')->group(function (){
        Route::post( "skills" , 'ApiController@skills' )->name("skill") ;
        Route::post( "tags" , 'ApiController@tags' )->name("tag");
        Route::post("provinces/{province?}" , "ApiController@provinces")->name("province") ;
    });

    Route::post("ajax" , 'AjaxController@ajaxHandle' )->name("ajax") ;

    // Route access if Guard user
    Route::get('main', 'MainController@index')->name('main') ;

    //* روت های پروفایل کاربری  *//
    Route::name("profile.")->prefix("profile")->group(function (){

        Route::prefix("account")->name("account.")->group(function (){
            Route::get("/"  , "ProfileController@account")->name("index") ;
            Route::post( "/"  , "ProfileController@accountStore")->name("store");
        });

        Route::prefix("password")->name("password.")->group(function (){
            Route::get("/"  , "ProfileController@password")->name("index") ;
            Route::post( "/"  , "ProfileController@passwordStore")->name("store") ;
        });

        Route::prefix("notification")->name("notification.")->group(function (){
            Route::get("/"  , "ProfileController@notification")->name("index") ;
            Route::post( "/"  , "ProfileController@notificationStore")->name("store") ;
        });

        Route::prefix("plan")->name("plan.")->group(function (){
            Route::get("payment" , "ProfileController@planPayment")->name("payment") ;
            Route::get("/"  , "ProfileController@plan")->name("index") ;
            Route::get( "{plan}"  , "ProfileController@planShow")->name("show") ;
            Route::post( "{plan}"  , "ProfileController@planStore")->name("store") ;
        });

        Route::prefix("skill")->name("skill.")->group(function (){
            Route::get("/"  , "ProfileController@skill")->name("index") ;
            Route::post( "/"  , "ProfileController@skillStore")->name("store") ;
        });

        Route::post( "logout"  , "ProfileController@logout")->name("logout") ;
    });

    Route::resource('payment', 'PaymentController' , ['only' => ['index' , 'show']]);
    Route::resource('team' , 'TeamController');
});
