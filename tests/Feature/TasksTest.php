<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TasksTest extends TestCase
{
    /** @test  */
    public function a_user_can_read_all_the_tasks()
    {

        //Given we have task in the database
        $task = Task::factory()->create();

        //When user visit the tasks page
        $response = $this->get('/tasks');


        //He should be able to read the task
        $response->assertSee($task->title);
    }
    /** @test  */
    public function a_user_can_see_single_page()
    {
        $task = Task::factory()->create();

        $res = $this->get('/tasks/'.$task->id);
        $res->assertSee($task->title)
            ->assertSee($task->description);
    }
    /** @test */
    public function authenticated_users_can_create_a_new_task()
    {
//        $this->withExceptionHandling();
//        $this->withoutExceptionHandling();
        //Given we have an authenticated user
       // $this->actingAs(facto('App\User')->create());

        $user = User::factory()->create();
        $this->actingAs($user ,'web');
        //Given we have a task object
        $task = Task::factory()->make();

        //When user submits post request to create task endpoint
        $this->post('/tasks/create',$task->toArray());
        //It gets stored in the database
        $this->assertEquals(1000,Task::all()->count());
    }
}
