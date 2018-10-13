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
        $posts = Post::get();
        $posts->map(function($post) {
            $post->link = $this->getLinkFromPost($post);
            return $post;
        });
        $result = [
            'data' => $posts
        ];
        return $this->send($result);
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

        $path = Storage::putFile('public/images/posts/cover', $request->file('image'));

        $newPost = new Post;
        $newPost->title = $title;
        $newPost->description = $description;
        $newPost->path_cover = $path;
        $newPost->token = str_random(64);
        $newPost->save();

        $post = $newPost;
        $post->link = $this->getLinkFromPath($path);
        $result = [
            'data' => $post
        ];
        return $this->send($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $post->link = $this->getLinkFromPost($post);
        $result = [
            'data' => $post
        ];
        return $this->send($result);
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

    private function send($result)
    {
        $json = json_encode($result, JSON_UNESCAPED_UNICODE);
        return response($json)
                ->header('Content-Length', strlen($json))
                ->header('Content-Type', 'application/json;charset=utf-8');
    }

    private function getLinkFromPost($post)
    {
        $path = $post->path_cover;
        return $this->getLinkFromPath($path);
    }

    private function getLinkFromPath($path)
    {
        $filename = basename($path);
        $link = request()->root() . '/images/posts/cover/' . $filename;
        return $link;
    }
}
