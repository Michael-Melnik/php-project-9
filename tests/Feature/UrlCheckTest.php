<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UrlCheckTest extends TestCase
{
    protected int $id;

    protected function setUp(): void
    {
        parent::setUp();

        $this->id = DB::table('urls')->insertGetId(['name' => 'https://www.mail.ru', 'created_at' => Carbon::now()]);
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
            'status_code' => 200
        ];

        $response = $this->post(route('url.check', $this->id));
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('url_checks', $expectedData);
    }
}
