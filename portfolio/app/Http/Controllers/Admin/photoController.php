<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use Illuminate\Http\Request;

class photoController extends Controller
{
    public function index()
    {
        $photos = Photo::where('photo_status', 'approved')->paginate(20);
        return view('admin/Photo.photo', compact('photos'));
    }

    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        //
    }
    public function show(string $id)
    {
        //
    }
    public function edit(string $id)
    {
        //
    }
    public function update(Request $request, string $id)
    {
        //
    }
    public function destroy(string $id)
    {
        //
    }
    public function photoPending()
    {
        $photoPending = Photo::where('photo_status', 'pending')->paginate(10);
        return view('admin/Photo.photoPending', compact('photoPending'));
    }
    public function photoRejected()
    {
        $photoRejected = Photo::where('photo_status', 'photo')->paginate(10);
        return view('admin/Photo.photoRejected', compact('photoRejected'));
    }
}
