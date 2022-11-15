<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UrlCheckControllerTest extends TestCase
{
    protected int $id;
    protected string $date;
    protected function setUp(): void
    {
        parent::setUp();
        $this->date = Carbon::now();
        $this->id = DB::table('urls')->insertGetId(['name' => 'https://www.mail.ru', 'created_at' => $this->date]);
    }

    public function testCheck()
    {
        $testHtml = file_get_contents(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', "Fixtures", 'test.html']));

        Http::fake(['https://www.mail.ru' => Http::response($testHtml, 200)]);

        $expectedData = [
            'h1' => 'Проанализировать страницу',
            'title' => 'Анализатор страниц',
            'description' => 'Description',
            'url_id' => $this->id,
            'status_code' => 200,
            'created_at' => $this->date
        ];

        $response = $this->post(route('checks.store', $this->id));
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('url_checks', $expectedData);
    }
}
