<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Auth\AuthenticateUserAction;
use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request, AuthenticateUserAction $action)
    {
        $result = $action->execute(
            $request->validated('email'),
            $request->validated('password'),
        );

        return $this->success([
            'token' => $result['token'],
            'user' => new UserResource($result['user']),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    public function me(Request $request)
    {
        return $this->success(new UserResource($request->user()));
    }
}
