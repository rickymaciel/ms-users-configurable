<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Authorizable;
use App\User;
use App\UserProfile;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:'.Authorizable::CREATE_USER, ['only' => ['createUser']]);
        $this->middleware('permission:'.Authorizable::EDIT_USER, ['only' => ['updateUser']]);
    }

    /**
     * Get user from token
     *
     * @return user
     */
    public function getUserFromToken() {
        return User::getUserFromToken();
    }

    /**
     * Retrieving user data from token.
     *
     * @return data
     */
    public function retrievingUser(){
        try {
            $user=$this->getUserFromToken();

            if (!$user) {
                throw new Exception('user_not_found', 404);
            }

            return response()->json($this->success($user->getUserData()));

        } catch(Exception $e){
            return response()->json($this->error($e->getMessage()));
        }
    }

    /**
     * Retrieving premissions data from token.
     *
     * @return data
     */
    public function retrievingPermissions(){
        try {
            $user=$this->getUserFromToken();

            if (!$user) {
                throw new Exception('user_not_found', 404);
            }

            return $this->success($user->getPermissions());

        } catch(Exception $e){
            return response()->json($this->error($e->getMessage()));
        }
    }



    /**
     * Create user data
     *
     * @return data
     */
    public function createUser(Request $request)
    {
        $inputs = $request->all();

        $validator = Validator::make($inputs, [
            'document_number' => 'required|string|max:60|unique:users',
            'password' => 'required|min:6|confirmed',
            'email' => 'required|email|max:60|unique:users'
        ]);

        if ($validator->fails()) {
            return response()->json($this->error($validator->errors()->first()));
        }

        try {

            DB::beginTransaction();

            $inputs['password'] = $this->setPassword($inputs['password']);
            $inputs['name'] = ucwords( $this->sanear_string($inputs['profile']['name']) )." ".ucwords( $this->sanear_string($inputs['profile']['last_name']) );

            $user = User::create($inputs);

            $user_formated = $this->formatUserData($inputs);

            $user->profile()->create($user_formated['profile']);

            $user->roles()->attach($inputs['role']['id']);

            DB::commit();
            unset($inputs);

            $user->profile;
            return response()->json($this->success($user));

        } catch (Exception $e) {
            unset($inputs);
            DB::rollBack();
            return response()->json($this->error($e->getMessage()));
        }
    }



    /**
     * Update user data
     *
     * @return data
     */
    public function updateUser(Request $request)
    {

        $user = $this->getUserFromToken();

        if (!$user) {
            throw new Exception('user_not_found', 404);
        }

        $inputs = $request->all();

        if ( isset($inputs['email']) && $inputs['email'] == $user->email) {
            unset($inputs['email']);
        }

        if ( isset($inputs['document_number']) && $inputs['document_number'] == $user->document_number) {
            unset($inputs['document_number']);
        }

        $validator = Validator::make($inputs, [
            'document_number' => 'string|max:60|unique:users',
            'email' => 'email|max:60|unique:users'
        ]);

        if ($validator->fails()) {
            return response()->json($this->error($validator->errors()->first()));
        }

        try {

            if ($this->validateFields($inputs)) {

                DB::beginTransaction();

                if (!empty($inputs['password'])) {
                    $user->password = $this->setPassword($inputs['password']);
                    $user->save();
                    unset($inputs['password']);
                }

                $user_formated = $this->formatUserData($inputs);

                $user->update($user_formated);
                $user->profile->update($user_formated['profile']);

                DB::commit();
                unset($inputs);

                $user->profile;
                return response()->json($this->success($user));
            }

            return $this->error('Algunos campos son requeridos');

        } catch(Exception $e){
            unset($inputs);
            DB::rollBack();
            return response()->json($this->error($e->getMessage()));
        }
    }



    /**
     * Validate user data
     *
     * @return bool
     */
    public function validateFields($data)
    {
        if ( (isset($data['profile']['name']) && !empty($data['profile']['name'])) && (isset($data['profile']['last_name']) && !empty($data['profile']['last_name'])) ) {
            return true;
        }
        return false;
    }



    /**
     * Update password hash.
     *
     * @return hash
     */
    public function setPassword($password)
    {
        return app('hash')->make(trim($password));
    }




    /**
     * Format data user.
     *
     * @return data
     */
    public function formatUserData($data)
    {
        $data['profile']['name'] = ucwords( $this->sanear_string($data['profile']['name']) );
        $data['profile']['last_name'] = ucwords( $this->sanear_string($data['profile']['last_name']) );
        $data['name'] = $data['profile']['name']." ".$data['profile']['last_name'];

        $data['profile']['nickname'] = $this->getNickName(null, $data['name']);

        if (isset($data['profile']['company'])) {
            $data['profile']['company'] = ucwords( $this->sanear_string($data['profile']['company']) );
        }

        $data['profile']['avatar'] = $this->getAvatarsLetters($data['name'], '605ca8', 'ffffff');

        return $data;
    }


    /**
     * Prepare nickname from social response (nickname or name)
     *
     * @return string
     */
    public function getNickName($nickname = null, $name)
    {

        $nickname = (!is_null($nickname) && !empty($nickname)) ? $nickname : $name;

        if (strlen($nickname) > 12) {
            $nickname = substr($nickname,0,12);
        }

        return $this->limpia_espacios(strtolower(( $this->sanear_string($nickname) )));
    }

    
    /**
     * Get avatar from name
     * @return url
     *
     */
    public function getAvatarsLetters($name, $background, $color)
    {
        return env('AVATAR_URL').'?&background='.$background.'&size=128&color='.$color.'&size=120&name='.$name;
    }



    /**
     * Remove space from string.
     *
     * @return string
     */
    public function limpia_espacios($cadena){
        return trim(str_replace(' ', '', $cadena));
    }
}
