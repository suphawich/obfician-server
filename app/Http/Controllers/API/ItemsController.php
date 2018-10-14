<?php

namespace App\Http\Controllers\API;

use App\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Item::get();
        $items->map(function($item) {
            $item->link = $this->getLinkFromItem($item);
            return $item;
        });
        $result = [
            'data' => $items
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
        $name = $request->input('name');
        $description = $request->input('description');
        if ($description == null || $name == null || !$request->hasFile('image')) {
            $fault = [
                'message' => 'Not found data.'
            ];
            $json = json_encode($fault);
            return response($json, 400)
                    ->header('Content-Length', strlen($json))
                    ->header('Content-Type', 'application/json;charset=utf-8');
        }

        $path = Storage::putFile('public/images/items', $request->file('image'));

        $newItem = new Item;
        $newItem->name = $name;
        $newItem->description = $description;
        $newItem->path = $path;
        $newItem->token = str_random(64);
        $newItem->save();

        $item = $newItem;
        $item->link = $this->getLinkFromPath($path);
        $result = [
            'data' => $item
        ];
        return $this->send($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Item::find($id);
        if ($item == null) {
            $msg = 'Object not found.';
            $result = [
                'message' => $msg
            ];
            return $this->send($result, 400);
        }

        $item->link = $this->getLinkFromItem($item);
        $result = [
            'data' => $item
        ];
        return $this->send($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item = Item::find($id);
        if ($item == null) {
            $msg = 'Object not found.';
            $result = [
                'message' => $msg
            ];
            return $this->send($result, 400);
        }
        $name = $request->input('name');
        $description = $request->input('description');
        if ($name != null) {
            $item->name = $name;
            
        }
        if ($description != null) {
            $item->description = $description;
        }
        if ($request->hasFile('image')) {
            $path = Storage::putFile('public/images/items', $request->file('image'));
            $oldPath = $item->path;
            Storage::delete($oldPath);
            $item->path = $path;
        }

        $item->save();
        $item->link = $this->getLinkFromPath($item->path);
        $result = [
            'data' => $item
        ];
        return $this->send($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Item::find($id);
        if ($item == null) {
            $msg = 'Object not found.';
            $result = [
                'message' => $msg
            ];
            return $this->send($result, 400);
        }
        // $isDeleted = $post->delete();
        $isDeleted = $item->forceDelete();
        if (!$isDeleted) {
            $msg = 'Can not delete post.';
            $result = [
                'message' => $msg
            ];
            return $this->send($result, 400);
        }

        Storage::delete($item->path);
        $result = [
            'result' => $isDeleted
        ];
        return $this->send($result);
    }

    private function send($result, $status = 200)
    {
        $json = json_encode($result, JSON_UNESCAPED_UNICODE);
        return response($json, $status)
                ->header('Content-Length', strlen($json))
                ->header('Content-Type', 'application/json;charset=utf-8');
    }

    private function getLinkFromItem($item)
    {
        $path = $item->path;
        return $this->getLinkFromPath($path);
    }

    private function getLinkFromPath($path)
    {
        $filename = basename($path);
        $link = request()->root() . '/images/items/' . $filename;
        return $link;
    }
}
