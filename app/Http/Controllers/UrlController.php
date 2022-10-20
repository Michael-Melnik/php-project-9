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
        return view('urls', compact('urls'));
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
        return view('show', ['url' => $url]);
    }
}
