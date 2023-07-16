<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\loginRequest;
use App\Http\Requests\TmCreateRequest;
use Validator;
use Auth;
use App\Repositories\Interfaces\UserRepositoryInterface;

class RegisterController extends BaseController
{
    private $userRepository;
    public function __construct(UserRepositoryInterface $userRepository){
        $this->userRepository = $userRepository;
    }
    public function register(UserCreateRequest $request){

        $request['password'] =  bcrypt($request->password);
        $user = $this->userRepository->createUser($request->all());

        $success['token'] = $user->createToken('Register')->plainTextToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'User Registered Successfully');

    }

    public function login(loginRequest $request){
        
        if(Auth::attempt(['email' => $request->email,'password' => $request->password]) ){
            $user = Auth::user();
            $success['token'] = $user->createToken('Login')->plainTextToken;
            $success['name'] = $user->name;

            return $this->sendResponse($success, 'User Logged in Successfully');
        }else{
           return $this->sendError('Unauthorized',['error' => 'Unauthorized' ]);
        }
    }

    public function logout(){
        auth()->user()->tokens()->delete();
        return $this->sendResponse([], 'User logged out');
    }

}
