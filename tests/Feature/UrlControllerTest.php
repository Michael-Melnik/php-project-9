<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Http;

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
        $response = $this->get(route('urls.index'));
        $response->assertOk();
    }

    public function testIndex()
    {
        $response = $this->get(route('urls.index'));
        $response->assertOk();
        $response->assertViewIs('urls');
    }

    public function testStore()
    {
        $data = ['url' => ['name' => "https://www.PrevedMedved.ru"]];
        $response = $this->post(route('urls.store'), $data);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('urls', ['name' => 'https://www.prevedmedved.ru']);
    }

    public function testStoreInvalid()
    {
        $data = ['url' => ['name' => "yandex"]];
        $response = $this->post(route('urls.store'), $data);
        $this->assertDatabaseMissing('urls', ['name' => "yandex"]);
    }

    public function testStoreValidExistsToDataBase()
    {
        $id = DB::table('urls')->where('name', 'https://www.yandex.ru')->value('id');
        $response = $this->post(route('urls.store'), ['url' => ['name' => "https://www.yandex.ru"]]);
        $response->assertRedirect(route('urls.show', ['url' => $id]));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('urls', ['name' => 'https://www.yandex.ru']);
    }

    public function testShow()
    {
        $id = DB::table('urls')->where('id', $this->id)->value('id');
        $response = $this->get(route('urls.show', $id));
        $response->assertOk();
        $nameUrl = DB::table('urls')->find($id)->name;
        $response->assertSee($nameUrl);
        $response->assertViewIs('show');
    }
    public function testCheck()
    {
        $testHtml = file_get_contents(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', "Fixtures", 'test.html']));

        Http::fake(['https://www.yandex.ru' => Http::response($testHtml, 200)]);

        $expectedData = [
            'h1' => 'Проанализировать страницу',
            'title' => 'Анализатор страниц',
            'description' => 'Description',
            'url_id' => $this->id,
            'status_code' => 200
        ];

        $response = $this->post(route('url.check', $this->id));
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('url_checks', $expectedData);
    }
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
