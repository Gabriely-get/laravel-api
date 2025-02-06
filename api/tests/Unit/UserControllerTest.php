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
    public function test_should_get_all_users_empty(): void
    {
        $userServiceMock =$this->createMock(UserService::class);
        $userServiceMock
        ->expects($this->once())
        ->method('all')
        ->willReturn(new JsonResponse([]));

        $controller = new UserController($userServiceMock);
        $response = $controller->all();

        //assert
        $responseData = json_decode($response->getContent(), true)['original'];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([], $responseData);
    }

    public function test_should_get_all_users(): void
    {
        $userServiceMock =$this->createMock(UserService::class);
        $userServiceMock
        ->expects($this->once())
        ->method('all')
        ->willReturn(new JsonResponse([
            'name' => 'Teste W.',
            'email' => 'teste@gmail.com',
            'id' => 7
        ], 200));

        $controller = new UserController($userServiceMock);
        $response = $controller->all();

        //assert
        $responseData = json_decode($response->getContent(), true)['original'];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Teste W.', $responseData['name']);
        $this->assertEquals('teste@gmail.com', $responseData['email']);
        $this->assertEquals(7, $responseData['id']);
    }

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

    public function test_should_return_error_when_name_is_missing(): void
    {
        $request = new Request([
            'name' => '',
            'email' => 'teste@gmail.com',
            'password' => 'Abc1234@!#$'
        ]);

        $userServiceMock =$this->createMock(UserService::class);
        $userServiceMock
        ->expects($this->never())
        ->method('create');

        $controller = new UserController($userServiceMock);
        $response = $controller->store($request);

        $responseData = json_decode($response->getContent(), true)['errors'];

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('O campo nome é obrigatório.', $responseData['name'][0]);
    }

    public function test_should_return_error_when_email_is_missing(): void
    {
        $request = new Request([
            'name' => 'Teste',
            'email' => '',
            'password' => 'Abc1234@!#$'
        ]);

        $userServiceMock =$this->createMock(UserService::class);
        $userServiceMock
        ->expects($this->never())
        ->method('create');

        $controller = new UserController($userServiceMock);
        $response = $controller->store($request);

        $responseData = json_decode($response->getContent(), true)['errors'];

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('O campo email é obrigatório.', $responseData['email'][0]);
    }

    public function test_should_return_error_when_password_is_missing(): void
    {
        $request = new Request([
            'name' => 'Teste',
            'email' => 'teste@gmail.com',
            'password' => ''
        ]);

        $userServiceMock =$this->createMock(UserService::class);
        $userServiceMock
        ->expects($this->never())
        ->method('create');

        $controller = new UserController($userServiceMock);
        $response = $controller->store($request);

        $responseData = json_decode($response->getContent(), true)['errors'];

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('O campo senha é obrigatório', $responseData['password'][0]);
    }

    public function test_should_return_error_when_all_fields_is_missing(): void
    {
        $request = new Request([
            'name' => '',
            'email' => '',
            'password' => ''
        ]);

        $userServiceMock =$this->createMock(UserService::class);
        $userServiceMock
        ->expects($this->never())
        ->method('create');

        $controller = new UserController($userServiceMock);
        $response = $controller->store($request);

        $responseData = json_decode($response->getContent(), true)['errors'];

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('O campo nome é obrigatório.', $responseData['name'][0]);
        $this->assertEquals('O campo email é obrigatório.', $responseData['email'][0]);
        $this->assertEquals('O campo senha é obrigatório', $responseData['password'][0]);
    }

}
