<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = factory(App\User::class, 10)->create();
        foreach ($users as $value) {
            factory(App\UserProfile::class, 1)->create([
                'user_id' => $value->id
            ]);
        }
    }
}
