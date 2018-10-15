<?php

namespace App\Http\Controllers\API;

use App\User;
use App\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::get();
        $users->map(function($user) {
            $user->profile;
            $user->profile->link = $this->getLinkFromUser($user);
            return $user;
        });
        $result = [
            'data' => $users
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
        $email = $request->input('email');
        $name = $request->input('name');
        $password = $request->input('password');
        if ($email == null || $name == null || $password == null) {
            $fault = [
                'message' => 'Not found data.'
            ];
            $json = json_encode($fault);
            return response($json, 400)
                    ->header('Content-Length', strlen($json))
                    ->header('Content-Type', 'application/json;charset=utf-8');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $newUser = new User;
        $newUser->email = $email;
        $newUser->name = $name;
        $newUser->password = $hash;
        $newUser->token = str_random(64);
        $newUser->save();

        $newUserProfile = new UserProfile;
        $newUserProfile->user_id = $newUser->id;
        $newUserProfile->save();

        $newUser->profile;
        $result = [
            'data' => $newUser
        ];
        return $this->send($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if ($user == null) {
            $msg = 'Object not found.';
            $result = [
                'message' => $msg
            ];
            return $this->send($result, 400);
        }

        $user->profile;
        $user->profile->link = $this->getLinkFromUser($user);
        $result = [
            'data' => $user
        ];
        return $this->send($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if ($user == null) {
            $msg = 'Object not found.';
            $result = [
                'message' => $msg
            ];
            return $this->send($result, 400);
        }

        $email = $request->input('email');
        $mobile = $request->input('mobile');
        $name = $request->input('name');
        $password = $request->input('password');
        $profile = $request->input('profile');
        if ($email != null) {
            $user->email = $email;
        }
        if ($mobile != null) {
            $user->mobile = $mobile;
        }
        if ($name != null) {
            $user->name = $name;
        }
        if ($profile != null) {
            $dob = $profile['dob'] ?? null;
            $hometown = $profile['hometown'] ?? null;
            if ($dob != null) {
                $user->profile->dob = $dob;
            }
            if ($hometown != null) {
                $user->profile->hometown = $hometown;
            }
            $user->profile->save();
        }

        $user->save();
        $user->profile->link = $this->getLinkFromUser($user);
        $result = [
            'data' => $user
        ];
        return $this->send($result);
    }

    public function updatePwd(Request $request, $id) {
        $user = User::find($id);
        if ($user == null) {
            $msg = 'Object not found.';
            $result = [
                'message' => $msg
            ];
            return $this->send($result, 400);
        }

        $old_pwd = $request->input('old_password');
        $new_pwd = $request->input('new_password');
        if ($old_pwd == null || $new_pwd == null) {
            $fault = [
                'message' => 'Not found data.'
            ];
            $json = json_encode($fault);
            return response($json, 400)
                    ->header('Content-Length', strlen($json))
                    ->header('Content-Type', 'application/json;charset=utf-8');
        }

        $isMatch = password_verify($old_pwd, $user->password);
        if (!$isMatch) {
            $fault = [
                'message' => 'Current password is not match..'
            ];
            $json = json_encode($fault);
            return response($json, 400)
                    ->header('Content-Length', strlen($json))
                    ->header('Content-Type', 'application/json;charset=utf-8'); 
        }

        $hash = password_hash($new_pwd, PASSWORD_DEFAULT);
        $user->password = $hash;
        $user->save();

        $result = [
            'result' => $isMatch
        ];
        return $this->send($result);
    }

    public function verifyEmail(Request $request, $id) {
        $user = User::find($id);
        if ($user == null) {
            $msg = 'Object not found.';
            $result = [
                'message' => $msg
            ];
            return $this->send($result, 400);
        }

        $verify = $user->email_verified_at;
        if ($verify != null) {
            $msg = 'User has been verified.';
            $result = [
                'message' => $msg
            ];
            return $this->send($result, 400);
        }

        $user->email_verified_at = Carbon::now()->toDateTimeString();
        $user->save();
        $result = [
            'result' => true
        ];
        return $this->send($result);
    }

    public function updateAvatar(Request $request, $id) {
        $user = User::find($id);
        if ($user == null) {
            $msg = 'Object not found.';
            $result = [
                'message' => $msg
            ];
            return $this->send($result, 400);
        }

        if (!$request->hasFile('image')) {
            $msg = 'Not found image file.';
            $result = [
                'message' => $msg
            ];
            return $this->send($result, 400);
        }

        $path = Storage::putFile('public/images/users/avatar', $request->file('image'));
        $oldPath = $user->profile->path_avatar;
        if ($oldPath != null) {
            Storage::delete($oldPath);
        }
        $user->profile->path_avatar = $path;
        $user->profile->save();
        
        $user->profile->link = $this->getLinkFromUser($user);
        $result = [
            'data' => $user
        ];
        return $this->send($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user == null) {
            $msg = 'Object not found.';
            $result = [
                'message' => $msg
            ];
            return $this->send($result, 400);
        }
        $isDeleted = $user->profile->forceDelete();
        $isDeleted = $user->forceDelete();
        if (!$isDeleted) {
            $msg = 'Can not delete user.';
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

    private function getLinkFromUser($user)
    {
        $path = $user->profile->path_avatar;
        if ($path == null) {
            return null;
        }
        return $this->getLinkFromPath($path);
    }

    private function getLinkFromPath($path)
    {
        $filename = basename($path);
        $link = request()->root() . '/images/users/avatar/' . $filename;
        return $link;
    }
}
