<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller {
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function store(Request $userRequest): JsonResponse
    {
        try {
            $messages = [
                'name.required' => 'O campo nome é obrigatório.',
                'password.required' => 'O campo senha é obrigatório',
                'email.required' => 'O campo email é obrigatório.',
                'email.email' => 'O email deve ser válido.',
                'email.unique' => 'Este email já está cadastrado.'
            ];

            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => 'required|email|unique:users,email',
                'password'=> 'required',
            ];

            $validator = Validator::make($userRequest->all(), $rules, $messages);
            if ($validator->fails()) {
                return new JsonResponse(['errors'=> $validator->errors()],400);
            }

            $result = $this->userService->create($userRequest->all());
            return new JsonResponse($result, 201);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), status: 400);
            //logging
        }
    }

    public function all()
    {
        try {
            $users = $this->userService->all();
            return response()->json($users, 200);
        }
        catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }
}
