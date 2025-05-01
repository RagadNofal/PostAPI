<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $data['users'] = User::query()->select(['id', 'name', 'email'])->get();
        return $this->api_response(true, 'Fetched successfully', $data);
    }

    public function show($id)
    {
        $data['user'] = User::query()->find($id);
        if (!isset($data['user'])) {
            return $this->api_response(false, 'User Not Found', [], 404);
        }
        return $this->api_response(true, 'Fetched successfully', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            $res = [
                'errors' => $validator->errors(),
            ];
            return $this->api_response(false, 'Validation Error', $res, 422);
        }

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $user = User::query()->create($data);
        return $this->api_response(true, 'Created successfully', ['user' => $user], 200);

    }

    public function update($id, Request $request)
    {
        $user = User::query()->find($id);
        if (!isset($user)) {
            return $this->api_response(false, 'User Not Found', [], 404);
        }

        if (isset($user)) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                "email" => 'required|unique:users,email,' . $user->id,
                "password" => 'required|min:8'
            ]);
            if ($validator->fails()) {
                return $this->api_response(false, 'Validation error', ['errors' => $validator->errors()], 422);
            }
            $data = $validator->validated();
            $data['password'] = Hash::make($data['password']);
            $user->update($data);
            $res['user'] = $user;
            return $this->api_response(true, 'update done ', $res);
        } else {
            return $this->api_response(false, 'error', [], 400);
        }

    }

    public function delete($id)
    {
        $user = User::query()->find($id);
        if (!isset($user)) {
            return $this->api_response(false, 'User Not Found', [], 404);
        }
        $user->delete();
        return $this->api_response(true, 'Deleted successfully');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8'
        ]);
        if ($validator->fails()) {
            $res = [
                'errors' => $validator->errors(),
            ];
            return $this->api_response(false, 'Validation Error', $res, 422);
        }


        $user = User::query()->where('email', $request->email)->first();

        $check = Hash::check($request->password, $user->password);


        if ($check) {

            $res['token'] = $user->createToken('api')->plainTextToken;
            $res['user'] = $user;
            return $this->api_response(true, 'Login Successful', $res);
            // login success
        } else {
            return $this->api_response(false, 'Wrong password', [], 400);
        }
    }

    public function logout()
    {
        $user = auth('api')->user();
        $user->currentAccessToken()->delete();

        return $this->api_response(true, 'Logout Successfully');

    }

    public function myData()
    {
        dd(auth('api')->user() , \request()->user());
    }
}