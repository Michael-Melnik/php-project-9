<?php

namespace Tests\Feature;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UrlControllerTest extends TestCase
{
    protected int $id;
    protected string $nameUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $db = DB::table('urls');
        $this->id = $db->insertGetId(['name' => 'https://www.yandex.ru', 'created_at' => Carbon::now()]);
        $this->nameUrl = $db->find($this->id)->name;
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
        $this->post(route('urls.store'), $data);
        $this->assertDatabaseMissing('urls', ['name' => "yandex"]);
    }

    public function testStoreValidExistsToDataBase()
    {
        $response = $this->post(route('urls.store'), ['url' => ['name' => "https://www.yandex.ru"]]);
        $response->assertRedirect(route('urls.show', ['url' => $this->id]));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('urls', ['name' => 'https://www.yandex.ru']);
    }

    public function testShow()
    {
        $response = $this->get(route('urls.show', $this->id));
        $response->assertOk();
        $response->assertSee($this->nameUrl);
        $response->assertViewIs('show');
    }
}
