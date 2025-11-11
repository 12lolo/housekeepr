<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
class CreateTestUsersSeeder extends Seeder {
  public function run(): void {
    foreach ([
      ['name'=>'Admin','email'=>'admin@local.test','role'=>'admin'],
      ['name'=>'Owner','email'=>'owner@local.test','role'=>'owner'],
      ['name'=>'Cleaner','email'=>'cleaner@local.test','role'=>'cleaner'],
    ] as $u) {
      $user = User::firstOrCreate(['email'=>$u['email']],[
        'name'=>$u['name'],'password'=>Hash::make('password'),
        'email_verified_at'=>now(),'remember_token'=>Str::random(10),
      ]);
      $user->assignRole($u['role']);
    }
  }
}
