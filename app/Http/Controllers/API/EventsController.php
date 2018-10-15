<?php

namespace App\Http\Controllers\API;

use App\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Event::get();
        $result = [
            'data' => $events
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
        $description = $request->input('description');
        $date = $request->input('date');
        if ($description == null || $date == null) {
            $fault = [
                'message' => 'Not found data.'
            ];
            $json = json_encode($fault);
            return response($json, 400)
                    ->header('Content-Length', strlen($json))
                    ->header('Content-Type', 'application/json;charset=utf-8');
        }

        $newEvent = new Event;
        $newEvent->date = $date;
        $newEvent->description = $description;
        $newEvent->token = str_random(64);
        $newEvent->save();

        $result = [
            'data' => Event::find($newEvent->id)
        ];
        return $this->send($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::find($id);
        if ($event == null) {
            $msg = 'Object not found.';
            $result = [
                'message' => $msg
            ];
            return $this->send($result, 400);
        }

        $result = [
            'data' => $event
        ];
        return $this->send($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $event = Event::find($id);
        if ($event == null) {
            $msg = 'Object not found.';
            $result = [
                'message' => $msg
            ];
            return $this->send($result, 400);
        }

        $description = $request->input('description');
        $date = $request->input('date');
        if ($description != null) {
            $event->description = $description;
        }
        if ($date != null) {
            $event->date = $date;
        }

        $event->save();
        $result = [
            'data' => $event
        ];
        return $this->send($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::find($id);
        if ($event == null) {
            $msg = 'Object not found.';
            $result = [
                'message' => $msg
            ];
            return $this->send($result, 400);
        }
        // $isDeleted = $event->delete();
        $isDeleted = $event->forceDelete();
        if (!$isDeleted) {
            $msg = 'Can not delete event.';
            $result = [
                'message' => $msg
            ];
            return $this->send($result, 400);
        }

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
}
