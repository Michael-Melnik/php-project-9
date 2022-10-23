<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlController;

class UrlControllerTest extends TestCase
{
    protected int $id;

    protected function setUp(): void
    {
        parent::setUp();

        $this->id = DB::table('urls')->insertGetId(['name' => 'https://www.yandex.ru', 'created_at' => Carbon::now()]);
    }

    public function testUrlsRequest()
    {
        $response = $this->get(route('url.showAll'));
        $response->assertOk();
    }

    public function testIndex()
    {
        $response = $this->get(route('url.showAll'));
        $response->assertOk();
        $response->assertViewIs('urls');
    }
//
    public function testStore()
    {
        $data = ['url' => ['name' => "https://www.PrevedMedved.ru"]];
        $response = $this->post(route('url.store'), $data);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('urls', ['name' => 'https://www.prevedmedved.ru']);
    }
//
    public function testStoreInvalid()
    {
        $data = ['url' => ['name' => "yandex"]];
        $response = $this->post(route('url.store'), $data);
        $response->assertRedirect();
        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('urls', ['name' => "yandex"]);
    }
//
    public function testStoreValidExistsToDataBase()
    {
        $id = DB::table('urls')->where('name', 'https://www.yandex.ru')->value('id');
        $response = $this->post(route('url.store'), ['url' => ['name' => "https://www.yandex.ru"]]);
        $response->assertRedirect(route('url.show', ['id' => $id]));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('urls', ['name' => 'https://www.yandex.ru']);
    }
//
    public function testShow()
    {
        $id = DB::table('urls')->where('id', $this->id)->value('id');
        $response = $this->get(route('url.show', $id));
        $response->assertOk();
        $nameUrl = DB::table('urls')->find($id)->name;
        $response->assertSee($nameUrl);
        $response->assertViewIs('show');
    }
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
