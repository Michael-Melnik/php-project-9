<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DiDom\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UrlCheckController extends Controller
{
    public function store(Request $request, int $id)
    {
        $url = DB::table('urls')->find($id);

        try {
            $response = Http::get($url->name);
            $status = $response->status();
            $document = new Document($response->body());
            $h1 = Str::limit(optional($document->first('h1'))->text(), 255, '...');
            $title = Str::limit(optional($document->first('title'))->text(), 255, '...');
            $description = optional($document->first('meta[name=description]'))->getAttribute('content');
            DB::table('url_checks')->insert([
                'url_id' => $id,
                'status_code' => $status,
                'title' => $title,
                'h1' => $h1,
                'description' => $description,
                'created_at' => Carbon::now()->toDateTimeString()
            ]);
            flash('Страница успешно проверена')->success();
        } catch (\Exception $exception) {
            flash('Не удалось выполнить проверку')->error();
            return back();
        }
        return redirect()->route('urls.show', ['url' => $id]);
    }
}
