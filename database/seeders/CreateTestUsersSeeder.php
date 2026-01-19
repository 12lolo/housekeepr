<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Cleaner;
use App\Models\DayCapacity;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateTestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding test data...');

        // ==========================================
        // ADMIN USER
        // ==========================================
        $admin = User::firstOrCreate(['email' => 'admin@housekeepr.nl'], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $this->command->info('✅ Admin user created');

        // ==========================================
        // HOTEL OWNERS & HOTELS
        // ==========================================

        // Owner 1: Test Hotel Amsterdam
        $owner1 = User::firstOrCreate(['email' => 'owner@housekeepr.nl'], [
            'name' => 'Jan Vermeer',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $hotel1 = Hotel::firstOrCreate(['owner_id' => $owner1->id], [
            'name' => 'Hotel Amsterdam Central',
        ]);

        // Owner 2: Boutique Hotel Rotterdam
        $owner2 = User::firstOrCreate(['email' => 'owner2@housekeepr.nl'], [
            'name' => 'Sophie de Boer',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $hotel2 = Hotel::firstOrCreate(['owner_id' => $owner2->id], [
            'name' => 'Boutique Hotel Rotterdam',
        ]);

        // Owner 3: Pending new owner (hasn't completed setup)
        $owner3 = User::firstOrCreate(['email' => 'newowner@housekeepr.nl'], [
            'name' => '',
            'password' => Hash::make('TemporaryPass123'),
            'role' => 'owner',
            'status' => 'pending',
            'email_verified_at' => null,
        ]);

        // Owner 4: Senne Visser (specific test data)
        $owner4 = User::firstOrCreate(['email' => 'sennevisser@outlook.com'], [
            'name' => 'Senne Visser',
            'password' => Hash::make('Password1!'),
            'role' => 'owner',
            'status' => 'active',
            'email_verified_at' => '2025-12-09 10:45:02',
            'created_at' => '2025-12-09 10:45:02',
            'updated_at' => '2025-12-09 10:49:32',
        ]);

        $hotel3 = Hotel::firstOrCreate(['owner_id' => $owner4->id], [
            'name' => 'Hotel Senne',
            'created_at' => '2025-12-09 10:49:32',
            'updated_at' => '2025-12-09 10:49:32',
        ]);

        $this->command->info('✅ 4 owners created (1 pending)');

        // ==========================================
        // ROOMS FOR HOTEL 1
        // ==========================================
        $hotel1Rooms = [
            ['room_number' => '101', 'room_type' => 'Standard', 'standard_duration' => 60],
            ['room_number' => '102', 'room_type' => 'Standard', 'standard_duration' => 60],
            ['room_number' => '103', 'room_type' => 'Standard', 'standard_duration' => 60],
            ['room_number' => '201', 'room_type' => 'Deluxe', 'standard_duration' => 90],
            ['room_number' => '202', 'room_type' => 'Deluxe', 'standard_duration' => 90],
            ['room_number' => '203', 'room_type' => 'Deluxe', 'standard_duration' => 90],
            ['room_number' => '301', 'room_type' => 'Suite', 'standard_duration' => 120],
            ['room_number' => '302', 'room_type' => 'Suite', 'standard_duration' => 120],
        ];

        $hotel1RoomModels = [];
        foreach ($hotel1Rooms as $roomData) {
            $room = Room::firstOrCreate(
                ['hotel_id' => $hotel1->id, 'room_number' => $roomData['room_number']],
                $roomData
            );
            $hotel1RoomModels[] = $room;
        }

        // ==========================================
        // ROOMS FOR HOTEL 2
        // ==========================================
        $hotel2Rooms = [
            ['room_number' => '1', 'room_type' => 'Standard', 'standard_duration' => 60],
            ['room_number' => '2', 'room_type' => 'Standard', 'standard_duration' => 60],
            ['room_number' => '3', 'room_type' => 'Deluxe', 'standard_duration' => 90],
            ['room_number' => '4', 'room_type' => 'Deluxe', 'standard_duration' => 90],
            ['room_number' => '5', 'room_type' => 'Suite', 'standard_duration' => 120],
        ];

        $hotel2RoomModels = [];
        foreach ($hotel2Rooms as $roomData) {
            $room = Room::firstOrCreate(
                ['hotel_id' => $hotel2->id, 'room_number' => $roomData['room_number']],
                $roomData
            );
            $hotel2RoomModels[] = $room;
        }

        // ==========================================
        // ROOMS FOR HOTEL 3 (Senne's Hotel)
        // ==========================================
        $hotel3Rooms = [
            ['room_number' => 'A1', 'room_type' => 'Standard', 'standard_duration' => 60],
            ['room_number' => 'A2', 'room_type' => 'Deluxe', 'standard_duration' => 90],
            ['room_number' => 'B1', 'room_type' => 'Deluxe', 'standard_duration' => 90],
            ['room_number' => 'B2', 'room_type' => 'Suite', 'standard_duration' => 120],
        ];

        $hotel3RoomModels = [];
        foreach ($hotel3Rooms as $roomData) {
            $room = Room::firstOrCreate(
                ['hotel_id' => $hotel3->id, 'room_number' => $roomData['room_number']],
                $roomData
            );
            $hotel3RoomModels[] = $room;
        }

        $this->command->info('✅ Rooms created (8 for Hotel 1, 5 for Hotel 2, 4 for Hotel 3)');

        // ==========================================
        // CLEANERS FOR HOTEL 1
        // ==========================================
        $cleaner1 = User::firstOrCreate(['email' => 'cleaner1@housekeepr.nl'], [
            'name' => 'Maria Janssen',
            'password' => Hash::make('password'),
            'role' => 'cleaner',
            'status' => 'active',
            'email_verified_at' => now(),
            'phone' => '+31612345678',
        ]);

        $cleaner2 = User::firstOrCreate(['email' => 'cleaner2@housekeepr.nl'], [
            'name' => 'Jan de Vries',
            'password' => Hash::make('password'),
            'role' => 'cleaner',
            'status' => 'active',
            'email_verified_at' => now(),
            'phone' => '+31687654321',
        ]);

        $cleaner3 = User::firstOrCreate(['email' => 'cleaner3@housekeepr.nl'], [
            'name' => 'Anna Bakker',
            'password' => Hash::make('password'),
            'role' => 'cleaner',
            'status' => 'active',
            'email_verified_at' => now(),
            'phone' => '+31698765432',
        ]);

        // Pending cleaner (hasn't logged in yet)
        $cleaner4 = User::firstOrCreate(['email' => 'cleaner4@housekeepr.nl'], [
            'name' => 'Peter Smit',
            'password' => Hash::make('password'),
            'role' => 'cleaner',
            'status' => 'pending',
            'email_verified_at' => null,
            'phone' => '+31611223344',
        ]);

        // Link cleaners to Hotel 1
        $hotel1Cleaner1 = Cleaner::firstOrCreate(
            ['user_id' => $cleaner1->id, 'hotel_id' => $hotel1->id],
            [
                'status' => 'active',
                'works_monday' => true,
                'works_tuesday' => true,
                'works_wednesday' => true,
                'works_thursday' => true,
                'works_friday' => true,
                'works_saturday' => false,
                'works_sunday' => false,
            ]
        );

        $hotel1Cleaner2 = Cleaner::firstOrCreate(
            ['user_id' => $cleaner2->id, 'hotel_id' => $hotel1->id],
            [
                'status' => 'active',
                'works_monday' => true,
                'works_tuesday' => true,
                'works_wednesday' => true,
                'works_thursday' => true,
                'works_friday' => true,
                'works_saturday' => false,
                'works_sunday' => false,
            ]
        );

        $hotel1Cleaner3 = Cleaner::firstOrCreate(
            ['user_id' => $cleaner3->id, 'hotel_id' => $hotel1->id],
            [
                'status' => 'active',
                'works_monday' => false,
                'works_tuesday' => true,
                'works_wednesday' => true,
                'works_thursday' => true,
                'works_friday' => true,
                'works_saturday' => true,
                'works_sunday' => false,
            ]
        );

        $hotel1Cleaner4 = Cleaner::firstOrCreate(
            ['user_id' => $cleaner4->id, 'hotel_id' => $hotel1->id],
            [
                'status' => 'active',
                'works_monday' => true,
                'works_tuesday' => false,
                'works_wednesday' => true,
                'works_thursday' => false,
                'works_friday' => true,
                'works_saturday' => false,
                'works_sunday' => false,
            ]
        );

        // ==========================================
        // CLEANERS FOR HOTEL 2
        // ==========================================
        $cleaner5 = User::firstOrCreate(['email' => 'cleaner5@housekeepr.nl'], [
            'name' => 'Lisa van Dam',
            'password' => Hash::make('password'),
            'role' => 'cleaner',
            'status' => 'active',
            'email_verified_at' => now(),
            'phone' => '+31655443322',
        ]);

        $hotel2Cleaner1 = Cleaner::firstOrCreate(
            ['user_id' => $cleaner5->id, 'hotel_id' => $hotel2->id],
            [
                'status' => 'active',
                'works_monday' => true,
                'works_tuesday' => true,
                'works_wednesday' => true,
                'works_thursday' => true,
                'works_friday' => true,
                'works_saturday' => true,
                'works_sunday' => true,
            ]
        );

        // ==========================================
        // CLEANERS FOR HOTEL 3 (Senne's Hotel)
        // ==========================================
        $cleaner6 = User::firstOrCreate(['email' => 'cleaner6@housekeepr.nl'], [
            'name' => 'Emma de Groot',
            'password' => Hash::make('password'),
            'role' => 'cleaner',
            'status' => 'active',
            'email_verified_at' => now(),
            'phone' => '+31633445566',
        ]);

        $hotel3Cleaner1 = Cleaner::firstOrCreate(
            ['user_id' => $cleaner6->id, 'hotel_id' => $hotel3->id],
            [
                'status' => 'active',
                'works_monday' => true,
                'works_tuesday' => true,
                'works_wednesday' => true,
                'works_thursday' => true,
                'works_friday' => true,
                'works_saturday' => false,
                'works_sunday' => false,
            ]
        );

        $cleaner7 = User::firstOrCreate(['email' => 'cleaner7@housekeepr.nl'], [
            'name' => 'Tom Hendriks',
            'password' => Hash::make('password'),
            'role' => 'cleaner',
            'status' => 'active',
            'email_verified_at' => now(),
            'phone' => '+31644556677',
        ]);

        $hotel3Cleaner2 = Cleaner::firstOrCreate(
            ['user_id' => $cleaner7->id, 'hotel_id' => $hotel3->id],
            [
                'status' => 'active',
                'works_monday' => true,
                'works_tuesday' => false,
                'works_wednesday' => false,
                'works_thursday' => true,
                'works_friday' => true,
                'works_saturday' => true,
                'works_sunday' => true,
            ]
        );

        $this->command->info('✅ Cleaners created (4 for Hotel 1, 1 for Hotel 2, 2 for Hotel 3)');

        // ==========================================
        // CAPACITY SETTINGS (30 days)
        // ==========================================
        for ($i = -7; $i < 30; $i++) {
            $date = now()->addDays($i)->toDateString();
            DayCapacity::firstOrCreate(
                ['hotel_id' => $hotel1->id, 'date' => $date],
                ['capacity' => 3]
            );
            DayCapacity::firstOrCreate(
                ['hotel_id' => $hotel2->id, 'date' => $date],
                ['capacity' => 1]
            );
            DayCapacity::firstOrCreate(
                ['hotel_id' => $hotel3->id, 'date' => $date],
                ['capacity' => 2]
            );
        }

        $this->command->info('✅ Daily capacity set for 30 days (all hotels)');

        // ==========================================
        // BOOKINGS FOR HOTEL 1
        // ==========================================

        // Past bookings (completed)
        $this->createBooking($hotel1RoomModels[0], now()->subDays(3), now()->subDays(2), 'Guest A', 'completed');
        $this->createBooking($hotel1RoomModels[1], now()->subDays(5), now()->subDays(3), 'Guest B', 'completed');

        // Yesterday checkout (should have cleaning tasks)
        $this->createBooking($hotel1RoomModels[2], now()->subDays(2), now()->subDays(1), 'Guest C', 'completed');

        // Current stays (checked in)
        $this->createBooking($hotel1RoomModels[0], now()->subDay(), now()->addDays(2), 'Guest D', 'checked_in');
        $this->createBooking($hotel1RoomModels[3], now()->subDay(), now()->addDays(3), 'Guest E', 'checked_in');

        // Checking out today
        $this->createBooking($hotel1RoomModels[4], now()->subDays(2), now(), 'Guest F', 'checked_in');

        // Upcoming bookings
        $this->createBooking($hotel1RoomModels[1], now()->addDay(), now()->addDays(3), 'Guest G', 'confirmed');
        $this->createBooking($hotel1RoomModels[5], now()->addDays(2), now()->addDays(5), 'Guest H', 'confirmed');
        $this->createBooking($hotel1RoomModels[6], now()->addDays(3), now()->addDays(6), 'Guest I', 'confirmed');
        $this->createBooking($hotel1RoomModels[7], now()->addDays(5), now()->addDays(7), 'Guest J', 'confirmed');

        // ==========================================
        // BOOKINGS FOR HOTEL 2
        // ==========================================
        $this->createBooking($hotel2RoomModels[0], now()->subDay(), now()->addDays(2), 'Guest K', 'checked_in');
        $this->createBooking($hotel2RoomModels[1], now()->addDay(), now()->addDays(4), 'Guest L', 'confirmed');
        $this->createBooking($hotel2RoomModels[3], now()->addDays(3), now()->addDays(5), 'Guest M', 'confirmed');

        // ==========================================
        // BOOKINGS FOR HOTEL 3 (Senne's Hotel)
        // ==========================================
        // Past booking (completed)
        $this->createBooking($hotel3RoomModels[0], now()->subDays(4), now()->subDays(2), 'John Doe', 'completed');

        // Current stay (checked in)
        $this->createBooking($hotel3RoomModels[1], now()->subDay(), now()->addDays(2), 'Jane Smith', 'checked_in');

        // Checking out today
        $this->createBooking($hotel3RoomModels[2], now()->subDays(2), now(), 'Robert Johnson', 'checked_in');

        // Upcoming bookings
        $this->createBooking($hotel3RoomModels[0], now()->addDay(), now()->addDays(3), 'Sarah Williams', 'confirmed');
        $this->createBooking($hotel3RoomModels[3], now()->addDays(2), now()->addDays(5), 'Michael Brown', 'confirmed');
        $this->createBooking($hotel3RoomModels[1], now()->addDays(4), now()->addDays(6), 'Emily Davis', 'confirmed');
        $this->createBooking($hotel3RoomModels[2], now()->addDays(3), now()->addDays(7), 'David Wilson', 'confirmed');

        $this->command->info('✅ Bookings created (10 for Hotel 1, 3 for Hotel 2, 7 for Hotel 3)');

        // ==========================================
        // SUMMARY
        // ==========================================
        $this->command->info('');
        $this->command->info('🎉 Database seeding completed!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('  • Admins: 1');
        $this->command->info('  • Owners: 4 (1 pending setup)');
        $this->command->info('  • Hotels: 3');
        $this->command->info('  • Rooms: 17');
        $this->command->info('  • Cleaners: 7 (1 pending)');
        $this->command->info('  • Bookings: 20');
        $this->command->info('');
        $this->command->info('🔑 Login credentials:');
        $this->command->info('  • Most users: password');
        $this->command->info('  • sennevisser@outlook.com: Password1!');
        $this->command->info('');
    }

    /**
     * Helper to create bookings
     */
    private function createBooking($room, $checkin, $checkout, $guestName, $status)
    {
        return Booking::firstOrCreate(
            [
                'room_id' => $room->id,
                'check_in' => $checkin->toDateString(),
                'check_out' => $checkout->toDateString(),
            ],
            [
                'guest_name' => $guestName,
                'check_in_datetime' => $checkin,
                'check_out_datetime' => $checkout,
            ]
        );
    }
}
