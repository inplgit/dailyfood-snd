<?php

namespace App\Http\Controllers\API\V1;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\UserVersion;

class RegisterController extends BaseController
{
    public function register(Request $request)
    {
        return $this->sendError('404', "Request Not Found");
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken(env('APP_NAME'))->plainTextToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User register successfully.');
    }

    public function login(Request $request)
    {

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = User::find(Auth::id());
            if ($user->tso && $user->tso->active == 1) {
                $user->tso->designation;
                $user->tso->department;
                $user->tso->cities;

  UserVersion::create([
                    'user_id' => $user->id,
                    'app_version' => $request->app_version,
                    'ip_address' => $request->ip()
                ]);

                $user['token'] =  $user->createToken(env('APP_NAME') ?? "S&D")->plainTextToken;
                // $user['token'] =  $user->createToken("S&D")->plainTextToken;
                return $this->sendResponse($user, 'User login successfully.');
            }
            Auth::logout();
            return $this->sendError('Unauthorised.', ['error'=>'Credential not found'], 200);
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Credential not found'], 200);
        }
    }
}
