<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\accountStore;
use App\Http\Requests\passwordStore;
use App\Models\City;
use App\Models\File;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Province;
use App\Models\User;
use App\Repositories\Skill\Skill as apiSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larabookir\Gateway\Enum;
use Larabookir\Gateway\Exceptions\RetryException;
use Larabookir\Gateway\Gateway;

class ProfileController extends Controller
{

    //*  account profile edit  *//
    public function account(Request $request)
    {
        $information = [
            'title' => trans('dash.panel.sidebar.profile.edit') ,
            'breadcrumb' => [
                trans('dash.panel.sidebar.profile.edit') => null
            ]
        ] ;

        $account = User::withCount( 'plan' , 'teams' ,'offers')->find( me()->id ) ;

        $cities  = City::whereHas("province" ,function ($q) use ($account) {
            $q->where("id" , $account->province_id);
        })->select(['id','name'])->get();

        $provinces = Province::select(['id','name'])->get() ;

        $log = $account->information() ;

        $count_skill = 0 ;
        if(!! $account->plan)
            $count_skill = $account->plan->count_skill ;

        return view('dash.user.profile.account' , compact('account' , 'count_skill' , 'information', 'log' , 'provinces' , 'cities') ) ;
    }

    public function accountStore(accountStore $request)
    {
        $account = me() ;

        File::pull($account , 'avatar', 'avatar' );

        File::pull($account , 'cover', 'cover' );

        $account->update([
            'name' => $request->input('name') ,
            'family' => $request->input('family') ,
            'username' => $request->input('username') ,
            'email' => $request->input('email') ,
            'mobile' => $request->input('mobile') ,
            'website' => $request->input('website') ,
            'phone' => $request->input('phone') ,
            'fax' => $request->input('fax') ,
            'instagram_account' => $request->input('instagram_account') ,
            'linkedin_account' => $request->input('linkedin_account') ,
            'gender' => $request->input('gender') ,
            'bio' => $request->input('bio') ,
            'province_id' => $request->input('province_id') ,
            'city_id' => $request->input('city_id') ,
        ]);

        return ResMessage( trans('dash.messages.success.profile.update') );
    }

    //*  password profile edit  *//
    public function password(Request $request)
    {
        $information = [
            'title' => trans('dash.panel.sidebar.profile.password') ,
            'breadcrumb' => [
                trans('dash.panel.sidebar.profile.password') => null
            ]
        ] ;

        return view("dash.user.profile.password" , compact('information')) ;
    }

    public function passwordStore(passwordStore $request)
    {
        me()->update([
            'password' => bcrypt( $request->input('password') )
        ]);

        return ResMessage(trans('dash.messages.success.profile.pass'));
    }

    //*  notification profile edit  *//
    public function notification(Request $request)
    {
        $information = [
            'title' => trans('dash.panel.sidebar.profile.notification') ,
            'breadcrumb' => [
                trans('dash.panel.sidebar.profile.notification') => null
            ]
        ] ;
        $user = me() ;

        return view("dash.user.profile.notification" , compact('information' , 'user') ) ;
    }

    public function notificationStore(Request $request)
    {
        $user = me() ;

        $user->porfileNotification()->update([
            'when_login' => $request->input('when_login' , false ) ,
            'when_create_team' => $request->input('when_create_team' , false ) ,
            'when_create_offer' => $request->input('when_create_offer' , false ) ,
            'when_edit_profile' => $request->input('when_edit_profile' , false ) ,
            'when_myteamhave_offer' => $request->input('when_myteamhave_offer' , false ) ,
            'when_expired_panel' => $request->input('when_expired_panel' , false ) ,
            'when_offer_confirmed' => $request->input('when_offer_confirmed' , false ) ,
        ]);

        return ResMessage( trans('dash.messages.success.profile.notification') );

    }

    //* panel profile edit *//
    public function plan(Request $request)
    {
        $information = [
            'title' => trans('dash.panel.sidebar.profile.changeplan') ,
            'breadcrumb' => [
                trans('dash.panel.sidebar.profile.changeplan') => null
            ]
        ] ;
        $plans = Plan::where('price' , '<>' , 0 )->orderBy("price" , 'desc')->take(4)->get() ;

        return view("dash.user.profile.plan" , compact('information' , 'plans') ) ;
    }

    public function planShow(Plan $plan)
    {
        return $this->planStore($plan) ;
    }

    public function planStore(Plan $plan)
    {
        $user = me()->id ;
        $gateway = Gateway::zarinpal() ;
        $gateway->setCallback( route('user.profile.plan.payment') );
        $gateway
            ->price( $plan->price )
            ->ready();

        // get authority

        Payment::create([
            'user_id' => me()->id ,
            'plan_id' => $plan->id ,
            'ref_id' => $gateway->refId() ,
            'transaction_id' => $gateway->transactionId() ,
        ]);

        // redirect payment page
        return $gateway->redirect();
    }

    public function planPayment(Request $request)
    {
        try{
            $gateway = Gateway::verify();
            $trackingCode = $gateway->trackingCode();
            $refId = $gateway->refId();
            Payment::where('ref_id' , $refId)
                ->update([
                    'tracking_code' => $trackingCode ,
                    'status' => Enum::TRANSACTION_SUCCEED
                ]) ;
        }
        catch (RetryException $e){ // کاربر دوباره صفحه فاکتور را رفرش کرده است !
            return $e->getMessage() ;
        }
        catch (\Exception $e) { // کاربر از پرداخت منصرف شده است .
            Payment::where('status' , Enum::TRANSACTION_INIT)
                ->where('transaction_id' , $request->input('transaction_id'))
                ->update(['status' => Enum::TRANSACTION_FAILED ]) ;

        }
    }


    //*  logout profile edit  *//
    public function logout(Request $request)
    {
        $guards = array_keys(config('auth.guards')) ;
        foreach ($guards as $guard)
            if (Auth::guard($guard)->check())
            {
                Auth::guard($guard)->logout() ;
                return ResMessage( trans('dash.messages.success.logout') );
            }
        return ResMessage( trans('dash.messages.error.logout') );
    }

}