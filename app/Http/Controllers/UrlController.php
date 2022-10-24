<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UrlController extends Controller
{
    public function showAll()
    {
        $urls = DB::table('urls')->paginate(10);
        $lastChek = DB::table('url_checks')
            ->orderBy('url_id')
            ->latest()
            ->distinct('url_id')
            ->get()
            ->keyBy('url_id');

        return view('urls', compact('urls', 'lastChek'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'url.name' => 'required|max:255|min:5|url'
        ]);

        $parsedUrl = parse_url($request->input('url.name'));
        $normalizeUrl = strtolower("{$parsedUrl['scheme']}://{$parsedUrl['host']}");
        $url = DB::table('urls')->where('name', $normalizeUrl);

        if ($url->exists()) {
            flash('Страница уже существует');
            $id = $url->first()->id;
        } else {
            $id = DB::table('urls')->insertGetId([
                'name' => $normalizeUrl,
                'created_at' => Carbon::now()->toDateTimeString()
            ]);
            flash('Страница успешно добавлена')->success();
        }
        return redirect()->route('url.show', ['id' => $id]);
    }

    public function show(int $id)
    {
        $url = DB::table('urls')->find($id);
        $checks = DB::table('url_checks')->where('url_id', $id)->orderBy('id', 'desc')->get();
        return view('show', ['url' => $url, 'checks' => $checks]);
    }
    public function check(Request $request, int $id)
    {

        DB::table('url_checks')->insert([
                'url_id' => $id,
                'created_at' => Carbon::now()]);

        flash('Страница успешно проверена')->success();

        return redirect()->route('url.show', [$id]);
    }
}
