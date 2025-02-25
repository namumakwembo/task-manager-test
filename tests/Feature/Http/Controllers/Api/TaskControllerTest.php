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
