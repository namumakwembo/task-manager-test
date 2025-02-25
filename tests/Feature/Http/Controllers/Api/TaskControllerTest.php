<?php

use App\Models\Task;
use App\Models\User;

describe("Index", function () {


    describe("Validations", function () {

        test('Guest cannot access index', function () {

            $user = User::factory()->create();

            $token =    null;

            $this->withHeaders(['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'])->getJson(route('tasks.index'),)->assertStatus(401);
        });


        test('Auth user can  access index', function () {

            $user = User::factory()->hasTasks(3)->create();

            $token =    $user->createToken('secret')->plainTextToken;

            $this->withHeaders(['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'])->getJson(route('tasks.index'),)->assertOK();
        });
    });


    describe("Action:", function () {

        test('It returns all tasks for the authenticated user', function () {

            $user = User::factory()->hasTasks(3)->create();

            $token =   $user->createToken('secret')->plainTextToken;

            $this->withHeaders(['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'])->getJson(route('tasks.index'))->assertJsonCount(3);
        });


        test('It only returns tasks that belong to correct user', function () {

            //Craeate random tasks 
            Task::factory()->count(10)->create();

            //Create tasks for user
            $user = User::factory()->hasTasks(3)->create();


            //assert database has total of 13 tasks
            expect(Task::count())->toBe(13);

            $token =   $user->createToken('secret')->plainTextToken;

            $this->withHeaders(['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'])
                ->getJson(route('tasks.index'),)

                //assert that only 3 tasks are returned
                ->assertJsonCount(3);
        });



        test('It can paginate', function () {

            //Create 20 tasks
            $user =  User::factory()->hasTasks(17)->create();


            //First request
            $data = ['page' => 1];
            $response =  $this->actingAs($user, 'sanctum')->getJson(route('tasks.index', $data))->assertOk();
            $response = $response->decodeResponseJson();

            expect($response['meta']['current_page'])->tobe(1);
            expect(count($response['data']))->toBe(10);

            //Second request
            $data = ['page' => 2];
            $response =  $this->actingAs($user, 'sanctum')->getJson(route('tasks.index', $data))->assertOk();
            $response = $response->decodeResponseJson();
            expect($response['meta']['current_page'])->tobe(2);
            expect(count($response['data']))->toBe(7);
        });
    });


});


describe("store", function () {


    describe("Validations", function () {

        test('Guest cannot access index', function () {

            $user = User::factory()->create();

            $token =    null;

            $this->withHeaders(['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'])->postJson(route('tasks.store'),)->assertStatus(401);
        });


 
        //name
        test('name is required', function () {

            $user = User::factory()->hasTasks(3)->create();

            $token =    $user->createToken('secret')->plainTextToken;

            $data = ['name' => null];
            $response =  $this->actingAs($user, 'sanctum')->postJson(route('tasks.store', $data))->assertStatus(422);

            $response->assertJsonValidationErrors(['name' => 'required']);
        });

    

        test('name must be maximum 255 characters', function () {

            $user = User::factory()->hasTasks(3)->create();

            $token =    $user->createToken('secret')->plainTextToken;

            $data = ['name' => fake()->text(600)];

            $response =  $this->actingAs($user, 'sanctum')->postJson(route('tasks.store', $data))->assertStatus(422);

            $response->assertJsonValidationErrors(['name' => '255']);
        });


        //Status

        test('status is required', function () {

            $user = User::factory()->hasTasks(3)->create();

            $token =    $user->createToken('secret')->plainTextToken;

            $data = ['status' => null];
            $response =  $this->actingAs($user, 'sanctum')->postJson(route('tasks.store', $data))->assertStatus(422);

            $response->assertJsonValidationErrors(['status' => 'required']);
        });


        test('status must only be completed or pending', function () {

            $user = User::factory()->hasTasks(3)->create();

            $data = ['status' => 'random'];
            $response =  $this->actingAs($user, 'sanctum')->postJson(route('tasks.store', $data))->assertStatus(422);

            $response->assertJsonValidationErrors(['status' => 'The selected status is invalid.']);
        });



        
        //Description
        test('description can be nullable', function () {

            $user = User::factory()->hasTasks(3)->create();


            $data = ['description' => null];
            $response =  $this->actingAs($user, 'sanctum')->postJson(route('tasks.store', $data))->assertStatus(422);

            $response->assertJsonMissingValidationErrors(['description']);
        });


        test('description must be maximum 500 characters', function () {

            $user = User::factory()->hasTasks(3)->create();

            $data = ['description' => str_repeat('A', 600)];


            $response =  $this->actingAs($user, 'sanctum')->postJson(route('tasks.store', $data))->assertStatus(422);

            $response->assertJsonValidationErrors(['description']);
        });
    });

    describe("Action", function () {



           
        //Description
        test('it can create task ', function () {

            $user = User::factory()->hasTasks(3)->create();

            $data = [
                'name'=> 'Task 1',
                'description' => 'Task 1 description',
                'status' => 'pending'];

            $response =  $this->actingAs($user, 'sanctum')->postJson(route('tasks.store', $data))->assertStatus(201);

            $this->assertDatabaseHas('tasks', ['name' => 'Task 1']);

        });




    });


});


describe("Show", function () {
    test('Guest cannot view task', function () {
        $task = Task::factory()->create();

        $this->getJson(route('tasks.show', $task->id))
            ->assertStatus(401);
    });

    test('User can view their own task', function () {
        $user = User::factory()->hasTasks(1)->create();
        $task = $user->tasks()->first();

        $response=  $this->actingAs($user, 'sanctum')
        ->getJson(route('tasks.update', $task->id))
        ->assertStatus(200);


    $response = $response->decodeResponseJson();
        expect($response['data']['id'])->tobe($task->id);
    });

    test('User cannot view someone else’s task', function () {
        $user1 = User::factory()->hasTasks(1)->create();
        $user2 = User::factory()->create();

        $task = $user1->tasks()->first();

        $this->actingAs($user2, 'sanctum')
            ->getJson(route('tasks.show', $task->id))
            ->assertStatus(403);
    });
});


describe("Update", function () {
    test('Guest cannot update a task', function () {
        $task = Task::factory()->create();

        $this->putJson(route('tasks.update', $task->id), ['name' => 'Updated Task'])
            ->assertStatus(401);
    });

    test('User can update their own task', function () {
        $user = User::factory()->hasTasks(1)->create();
        $task = $user->tasks()->first();

        $data = ['name' => 'Updated Task', 'status' => 'completed'];

        $response=  $this->actingAs($user, 'sanctum')
            ->putJson(route('tasks.update', $task->id), $data)
            ->assertStatus(200);


        $response = $response->decodeResponseJson();



            expect($response['data']['name'])->tobe('Updated Task');


        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'name' => 'Updated Task']);
    });

    test('User cannot update someone else’s task', function () {
        $user1 = User::factory()->hasTasks(1)->create();
        $user2 = User::factory()->create();
        $task = $user1->tasks()->first();

        $this->actingAs($user2, 'sanctum')
            ->putJson(route('tasks.update', $task->id), ['name' => 'Hacked Task','status' => 'completed'])
            ->assertStatus(403);
    });
});



describe("Destroy", function () {
    test('Guest cannot delete a task', function () {
        $task = Task::factory()->create();

        $this->deleteJson(route('tasks.destroy', $task->id))
            ->assertStatus(401);
    });

    test('User can delete their own task', function () {
        $user = User::factory()->hasTasks(1)->create();
        $task = $user->tasks()->first();

        $this->actingAs($user, 'sanctum')
            ->deleteJson(route('tasks.destroy', $task->id))
            ->assertStatus(204);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    });

    test('User cannot delete someone else’s task', function () {
        $user1 = User::factory()->hasTasks(1)->create();
        $user2 = User::factory()->create();
        $task = $user1->tasks()->first();

        $this->actingAs($user2, 'sanctum')
            ->deleteJson(route('tasks.destroy', $task->id))
            ->assertStatus(403);
    });
});
