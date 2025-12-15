<?php

// Quick script to delete all owner users
// Run with: php delete_owners.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Hotel;
use Illuminate\Support\Facades\DB;

echo "🔍 Searching for owner users...\n";

$owners = User::where('role', 'owner')->get();

if ($owners->isEmpty()) {
    echo "✅ No owner users found.\n";
    exit(0);
}

echo "Found {$owners->count()} owner user(s):\n\n";

foreach ($owners as $owner) {
    $hotel = $owner->hotel;
    $hotelInfo = $hotel ? " (Hotel: {$hotel->name})" : " (No hotel)";
    echo "  - {$owner->email}{$hotelInfo}\n";
}

echo "\n⚠️  This will DELETE all owners and their data!\n";
echo "Press ENTER to continue or Ctrl+C to cancel...\n";
fgets(STDIN);

echo "\n🗑️  Starting deletion...\n";

DB::beginTransaction();

try {
    $deletedCount = 0;

    foreach ($owners as $owner) {
        echo "Deleting: {$owner->email}\n";

        $hotel = $owner->hotel;

        if ($hotel) {
            echo "  └─ Deleting hotel: {$hotel->name}\n";

            $roomsCount = $hotel->rooms()->count();
            $cleanersCount = $hotel->cleaners()->count();

            if ($roomsCount > 0) {
                echo "     └─ Deleting {$roomsCount} room(s)\n";
            }
            if ($cleanersCount > 0) {
                echo "     └─ Deleting {$cleanersCount} cleaner(s)\n";
            }

            $hotel->delete();
        }

        $owner->delete();
        $deletedCount++;
    }

    DB::commit();

    echo "\n✅ Successfully deleted {$deletedCount} owner user(s)!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "All changes rolled back.\n";
    exit(1);
}

