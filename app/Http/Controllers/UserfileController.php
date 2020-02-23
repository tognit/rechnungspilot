<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Userfile;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return UserFile::with(['fileable', 'tags'])
                ->search($request->input('searchtext'))
                ->type($request->input('fileable_type'))
                ->withAllTags($request->input('tags'), 'dateien')
                ->orderBy('created_at', 'DESC')
                ->paginate(15);
        }

        return view('userfile.index')
            ->with('tags', Tag::withType('dateien')->get());
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|max:51200|mimes:' . join(',', UserFile::MIME_TYPES),
        ]);

        $userfiles = [];
        foreach ($validatedData['files'] as $key => $file) {
            $userfiles[] = Userfile::fromUploadedFile($file);
        }

        if ($request->wantsJson()) {
            return $userfiles;
        }

        return back()
            ->with('status', 'Datei hochgeladen!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Userfiles  $userfiles
     * @return \Illuminate\Http\Response
     */
    public function show(Userfile $userfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Userfile  $userfile
     * @return \Illuminate\Http\Response
     */
    public function edit(Userfile $userfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Userfile  $userfile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Userfile $userfile)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
        ]);

        $userfile->update($validatedData);

        return $userfile->load('fileable');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Userfile  $userfile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Userfile $userfile)
    {
        if (Storage::disk(config('app.storage_disk_userfiles'))->delete($userfile->filename))
        {
            $userfile->delete();
        }

        if ($request->wantsJson())
        {
            return;
        }

        return back()
            ->with('status', 'Datei gelöscht!');
    }
}
