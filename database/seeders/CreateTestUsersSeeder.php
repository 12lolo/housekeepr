<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Cleaner;
use App\Models\DayCapacity;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CreateTestUsersSeeder extends Seeder {
  public function run(): void {
    // Create Admin
    $admin = User::firstOrCreate(['email'=>'admin@housekeepr.nl'],[
      'name'=>'Admin User',
      'password'=>Hash::make('password'),
      'role'=>'admin',
      'status'=>'active',
      'email_verified_at'=>now(),
    ]);

    // Create Hotel Owner
    $owner = User::firstOrCreate(['email'=>'owner@housekeepr.nl'],[
      'name'=>'Hotel Owner',
      'password'=>Hash::make('password'),
      'role'=>'owner',
      'status'=>'active',
      'email_verified_at'=>now(),
    ]);

    // Create Hotel
    $hotel = Hotel::firstOrCreate(['owner_id'=>$owner->id],[
      'name'=>'Test Hotel',
    ]);

    // Create Rooms
    $rooms = [
      ['room_number'=>'101','room_type'=>'Standard','standard_duration'=>60],
      ['room_number'=>'102','room_type'=>'Standard','standard_duration'=>60],
      ['room_number'=>'201','room_type'=>'Deluxe','standard_duration'=>90],
      ['room_number'=>'202','room_type'=>'Deluxe','standard_duration'=>90],
      ['room_number'=>'301','room_type'=>'Suite','standard_duration'=>120],
    ];

    foreach ($rooms as $roomData) {
      Room::firstOrCreate(
        ['hotel_id'=>$hotel->id,'room_number'=>$roomData['room_number']],
        $roomData
      );
    }

    // Create Cleaner Users
    $cleaner1 = User::firstOrCreate(['email'=>'cleaner1@housekeepr.nl'],[
      'name'=>'Maria Janssen',
      'password'=>Hash::make('password'),
      'role'=>'cleaner',
      'status'=>'active',
      'email_verified_at'=>now(),
    ]);

    $cleaner2 = User::firstOrCreate(['email'=>'cleaner2@housekeepr.nl'],[
      'name'=>'Jan de Vries',
      'password'=>Hash::make('password'),
      'role'=>'cleaner',
      'status'=>'active',
      'email_verified_at'=>now(),
    ]);

    // Link Cleaners to Hotel
    Cleaner::firstOrCreate(
      ['user_id'=>$cleaner1->id,'hotel_id'=>$hotel->id],
      ['status'=>'active']
    );

    Cleaner::firstOrCreate(
      ['user_id'=>$cleaner2->id,'hotel_id'=>$hotel->id],
      ['status'=>'active']
    );

    // Set default capacity for next 7 days
    for ($i = 0; $i < 7; $i++) {
      $date = now()->addDays($i)->toDateString();
      DayCapacity::firstOrCreate(
        ['hotel_id'=>$hotel->id,'date'=>$date],
        ['capacity'=>2]
      );
    }
  }
}
