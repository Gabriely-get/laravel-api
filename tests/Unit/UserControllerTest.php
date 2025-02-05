<?php

namespace Tests\Unit;
use Mockery\Undefined;
use Tests\TestCase;
use Illuminate\Http\JsonResponse;
use \Mockery;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Controllers\UserController;

class UserControllerTest extends TestCase
{
    public function test_should_create_user_with_mock(): void
     {
        ///prepare Mock
        $request = new Request([
            'name' => 'Teste W.',
            'email' => 'teste@gmail.com',
            'password' => 'Abc1234@!#$'
        ]);

        $userServiceMock =$this->createMock(UserService::class);
        $userServiceMock
        ->expects($this->once())
        ->method('create')
        ->with($request->all())
        ->willReturn(new JsonResponse([
            'name' => 'Teste W.',
            'email' => 'teste@gmail.com',
            'id' => 7
        ], 201));

        //instance
        $controller = new UserController($userServiceMock);
        $response = $controller->store($request);

        //assert
        $responseData = json_decode($response->getContent(), true)['original'];

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('Teste W.', $responseData['name']);
        $this->assertEquals('teste@gmail.com', $responseData['email']);
        $this->assertEquals(7, $responseData['id']);
    }
}
