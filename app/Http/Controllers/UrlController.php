<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DiDom\Document;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

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

        $validator = Validator::make($request->all(), [
            'url.name' => 'required|max:255|url'
        ]);

        if ($validator->fails()) {
            $validator = $validator->errors();
            return response(View::make('index', ['error' => $validator, 'url' => $request->input('url.name')]), 422);
        }

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
            $h1 = Str::limit(optional($document->first('h1'))->text(), 100, '...');
            $title = Str::limit(optional($document->first('title'))->text(), 100, '...');
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
        } catch (Exception $exception) {
            flash('Не удалось выполнить проверку')->error();
            return back();
        }
        return redirect()->route('urls.show', ['url' => $id]);
    }
}
