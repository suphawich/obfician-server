<?php

namespace App\Http\Controllers\API;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = [
            'data' => Post::get()
        ];
        $json = json_encode($result, JSON_UNESCAPED_UNICODE);
        return response($json)
                ->header('Content-Length', strlen($json))
                ->header('Content-Type', 'application/json');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $title = $request->input('title');
        $description = $request->input('description');
        if ($description == null || $title == null || !$request->hasFile('image')) {
            $fault = [
                'message' => 'Not found data.'
            ];
            $json = json_encode($fault);
            return response($json, 400)
                    ->header('Content-Length', strlen($json))
                    ->header('Content-Type', 'application/json;charset=utf-8');
        }

        $path = Storage::putFile('public/images/posts', $request->file('image'));
        $filename = basename($path);
        $link = $request->root() . '/images/posts/' . $filename;

        $newPost = new Post;
        $newPost->title = $title;
        $newPost->description = $description;
        $newPost->link = $path;
        $newPost->token = str_random(64);
        $newPost->save();

        $result = [
            'data' => $newPost
        ];
        $json = json_encode($result, JSON_UNESCAPED_UNICODE);
        return response($json)
                ->header('Content-Length', strlen($json))
                ->header('Content-Type', 'application/json;charset=utf-8');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $path = $post->link;
        $filename = basename($path);
        error_log(request()->root());
        $result = [
            'data' => $post
        ];
        $json = json_encode($result, JSON_UNESCAPED_UNICODE);
        return response($json)
                ->header('Content-Length', strlen($json))
                ->header('Content-Type', 'application/json;charset=utf-8');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }
}
