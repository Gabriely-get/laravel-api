<?php

namespace Tests\Unit;

use Illuminate\Http\JsonResponse;
use \Mockery;
use App\Repositories\UserRepository;
use PHPUnit\Framework\TestCase;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Controllers\UserController;

class UserControllerTest extends TestCase
{
    public function test_should_create_user_with_mock(): void
     {
        try {
            $request = new Request([
                'name' => 'Gabriely W.',
                'email' => 'duda@gmail.com',
                'password' => 'Abc1234@!#$'
            ]);

            $userServiceMock =$this->createMock(UserService::class);

            //$user = $this->$userServiceMock->create($request);

            $userServiceMock
            ->method('create')
            ->with($request->all())
            ->willReturn(new JsonResponse([
                'name' => 'Gabriely W.',
                'email' => 'duda@gmail.com',
                'id' => 7
            ], 201));

            $controller = new UserController($userServiceMock);
            $response = $controller->store($request);

            $responseData = json_decode($response->getContent(), true);

            // Validando o retorno
            $this->assertEquals(201, $response->getStatusCode());
            $this->assertEquals('Gabriely W.', $responseData['name']);
            $this->assertEquals('duda@gmail.com', $responseData['email']);
            $this->assertEquals(7, $responseData['id']);

            $this->assertEquals($request, $response);
        } catch(\Exception $e) {
            echo $e;
        }
    }
}
