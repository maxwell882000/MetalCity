<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StringController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $strings_ru = Storage::disk('bot')->get('/core/resources/strings_ru.json');
        $strings_uz = Storage::disk('bot')->get('/core/resources/strings_uz.json');

        $data = [
            'strings_ru' => json_decode($strings_ru, true),
            'strings_uz' => json_decode($strings_uz, true)
        ];

        return view('admin.strings.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    public function edit($string)
    {
        $strings_ru = Storage::disk('bot')->get('/core/resources/strings_ru.json');
        $strings_uz = Storage::disk('bot')->get('/core/resources/strings_uz.json');

        $strings_ru = json_decode($strings_ru, true);
        $strings_uz = json_decode($strings_uz, true);

        if (strstr($string, '.', true) == 'uz')
            $string_lang = $strings_uz[substr($string, 3)];
        elseif (strstr($string, '.', true) == 'ru')
            $string_lang = $strings_ru[substr($string, 3)];

        return view('admin.strings.edit', compact('string', 'string_lang'));
    }


    public function update(Request $request, $string)
    {
        $request->validate([
            'string' => 'nullable|string',
        ]);
        $strings_ru = Storage::disk('bot')->get('/core/resources/strings_ru.json');
        $strings_uz = Storage::disk('bot')->get('/core/resources/strings_uz.json');

        $strings_ru = json_decode($strings_ru, true);
        $strings_uz = json_decode($strings_uz, true);
        if (strstr($string, '.', true) == 'uz') {
            $strings_uz[substr($string, 3)] = $request->input('string');
            $contents = json_encode($strings_uz, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            Storage::disk('bot')->put(
                '/core/resources/strings_uz.json', $contents
            );
        } elseif (strstr($string, '.', true) == 'ru') {
            $strings_ru[substr($string, 3)] = $request->input('string');
            $contents = json_encode($strings_ru, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            Storage::disk('bot')->put(
                '/core/resources/strings_ru.json', $contents
            );
        }
        return redirect()->route('admin.strings.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
