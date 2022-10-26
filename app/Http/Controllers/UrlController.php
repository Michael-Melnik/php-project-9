<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DiDom\Document;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UrlController extends Controller
{
    public function index()
    {
        $urls = DB::table('urls')->paginate(10);
        $lastCheck = DB::table('url_checks')
            ->orderBy('url_id')
            ->latest()
            ->distinct('url_id')
            ->get()
            ->keyBy('url_id');

        return view('urls', compact('urls', 'lastCheck'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'url.name' => 'required|max:255|min:5|url'
        ]);

        $parsedUrl = parse_url($request->input('url.name'));
        $normalizeUrl = strtolower("{$parsedUrl['scheme']}://{$parsedUrl['host']}");
        $url = DB::table('urls')->where('name', $normalizeUrl)->first();

        if (isset($url)) {
            flash('Страница уже существует');
            return redirect()->route('urls.show', $url->id);
        }

        $id = DB::table('urls')
            ->insertGetId([
                'name' => $normalizeUrl,
                "created_at" =>  Carbon::now()->toDateTimeString(),
            ]);

        flash('Страница успешно добавлена')->success();
        return redirect()->route('urls.show', $id);
    }

    public function show(int $id)
    {
        $url = DB::table('urls')->find($id);
        abort_unless($url, 404);
        $checks = DB::table('url_checks')->where('url_id', $id)->get();
        return view('show', compact('url', 'checks'));
    }
    public function check(Request $request, int $id)
    {
        $url = DB::table('urls')->find($id);

        try {
            $response = Http::get($url->name);
            $status = $response->status();
            $document = new Document($response->body());
            $h1 = optional($document->first('h1'))->text();
            $title = optional($document->first('title'))->text();
            $description = optional($document->first('meta[name=description]'))->getAttribute('content');
            DB::table('url_checks')->insert([
                'url_id' => $id, 'status_code' => $status,
                'title' => $title, 'h1' => $h1,
                'description' => $description,
                'created_at' => Carbon::now()->toDateTimeString()
            ]);
            flash('Страница успешно проверена')->success();
        } catch (HttpClientException $exception) {
            $request->session()->flash('message', $exception->getMessage());
        }
        return redirect()->route('urls.show', ['url' => $id]);
    }
}
