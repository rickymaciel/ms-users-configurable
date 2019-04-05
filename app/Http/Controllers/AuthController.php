<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ConfigController;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\User;
use App\UserProfile;
use App\Role;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;
    protected $config;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(JWTAuth $jwt, ConfigController $config)
    {
        $this->jwt = $jwt;
        $this->config = $config;
    }

    /**
     * Make login.
     *
     * @return token
     */
    public function Login(Request $request)
    {
        $config = json_decode($this->config->getConfig()->getContent());
        $content = $config->status ? $config->content : null;

        $inputs = $request->all();

        $validator = Validator::make($inputs, [
            $content->mode_auth => 'required|max:60|'.$content->type,
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($this->error($validator->errors()->first()));
        }

        return $this->getToken($content,$request->only($content->mode_auth, 'password'));
    }

    /**
     * Get token.
     *
     * @return token
     */
    public function getToken($content,$credentials)
    {
        try {
            \Log::info('credentials', [$credentials]);
            if ( ! $token = $this->jwt->attempt( [
                    $content->mode_auth=>$credentials[$content->mode_auth],
                    'password'=>$credentials['password'],
                    'active'=>User::ACTIVE,
                    'banned'=>User::BANNED,
                    'confirmed'=>User::CONFIRMED
                ] ) ) {
                
                return response()->json($this->error('invalid_credentials'), 404);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                return response()->json($this->error(['token_expired' => $e->getMessage()], 500));

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return response()->json($this->error(['token_invalid' => $e->getMessage()], 500));

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                return response()->json($this->error(['token_absent' => $e->getMessage()], 500));
        }

        return $this->success('Bearer '.$token);
    }
}