<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CleaningTask;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    /**
     * Get system health and statistics.
     */
    public function health()
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'database' => [
                'connection' => config('database.default'),
                'connected' => $this->checkDatabaseConnection(),
            ],
            'stats' => [
                'users' => User::count(),
                'hotels' => Hotel::count(),
                'rooms' => Room::count(),
                'bookings' => Booking::count(),
                'tasks' => CleaningTask::count(),
            ],
        ]);
    }

    /**
     * List all database tables and row counts.
     */
    public function database()
    {
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");

        $data = [];
        foreach ($tables as $table) {
            $tableName = $table->name;
            if ($tableName === 'sqlite_sequence') {
                continue;
            }

            try {
                $count = DB::table($tableName)->count();
                $data[$tableName] = [
                    'rows' => $count,
                    'columns' => count(DB::select("PRAGMA table_info($tableName)")),
                ];
            } catch (\Exception $e) {
                $data[$tableName] = ['error' => $e->getMessage()];
            }
        }

        return response()->json([
            'tables' => $data,
            'total_tables' => count($data),
        ]);
    }

    /**
     * Get recent logs.
     */
    public function logs(Request $request)
    {
        $lines = $request->query('lines', 50);
        $logPath = storage_path('logs/laravel.log');

        if (! file_exists($logPath)) {
            return response()->json(['error' => 'Log file not found'], 404);
        }

        $logs = $this->tailFile($logPath, $lines);

        return response()->json([
            'lines' => $lines,
            'content' => $logs,
        ]);
    }

    /**
     * Run artisan command (be careful with this).
     */
    public function artisan(Request $request)
    {
        $command = $request->query('command');

        if (! $command) {
            return response()->json(['error' => 'Command parameter required'], 400);
        }

        // Whitelist of allowed commands for safety
        $allowedCommands = [
            'migrate:status',
            'route:list',
            'cache:clear',
            'config:clear',
            'view:clear',
            'optimize:clear',
            'about',
            'db:show',
            'test:booking-flow',
            'housekeepr:ensure-capacities',
            'housekeepr:refresh-bookings',
            'housekeepr:debug-schedule',
            'housekeepr:trigger-booking-events',
        ];

        if (! in_array($command, $allowedCommands)) {
            return response()->json([
                'error' => 'Command not allowed',
                'allowed_commands' => $allowedCommands,
            ], 403);
        }

        try {
            Artisan::call($command);
            $output = Artisan::output();

            return response()->json([
                'command' => $command,
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test booking creation flow.
     */
    public function testBooking(Request $request)
    {
        // Find or create test data
        $hotel = Hotel::first();
        $room = $hotel ? $hotel->rooms()->first() : null;

        if (! $hotel || ! $room) {
            return response()->json([
                'error' => 'No hotel or room found. Run seeders first.',
            ], 400);
        }

        // Create test booking
        try {
            $checkInDate = now()->addDays(2);
            $checkOutDate = now()->addDays(3);

            $booking = Booking::create([
                'room_id' => $room->id,
                'guest_name' => 'Test Guest',
                'check_in' => $checkInDate->toDateString(),
                'check_out' => $checkOutDate->toDateString(),
                'check_in_datetime' => $checkInDate->setTime(14, 0),
                'check_out_datetime' => $checkOutDate->setTime(11, 0),
                'notes' => 'Test booking created via diagnostic endpoint',
            ]);

            $booking->load('cleaningTask');

            return response()->json([
                'success' => true,
                'booking' => $booking,
                'cleaning_task_created' => $booking->cleaningTask ? true : false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    /**
     * Get configuration values (sanitized).
     */
    public function config()
    {
        return response()->json([
            'app' => [
                'name' => config('app.name'),
                'env' => config('app.env'),
                'debug' => config('app.debug'),
                'url' => config('app.url'),
                'timezone' => config('app.timezone'),
                'locale' => config('app.locale'),
            ],
            'database' => [
                'connection' => config('database.default'),
                'database' => config('database.connections.sqlite.database'),
            ],
            'session' => [
                'driver' => config('session.driver'),
                'lifetime' => config('session.lifetime'),
            ],
            'cache' => [
                'default' => config('cache.default'),
            ],
        ]);
    }

    /**
     * Run PHP code (Tinker-like). Use with extreme caution!
     */
    public function tinker(Request $request)
    {
        // ONLY enable this in development or with extreme caution
        if (app()->environment('production')) {
            return response()->json([
                'error' => 'Tinker disabled in production for security',
            ], 403);
        }

        $code = $request->input('code');

        if (! $code) {
            return response()->json(['error' => 'Code parameter required'], 400);
        }

        try {
            ob_start();
            $result = eval($code);
            $output = ob_get_clean();

            return response()->json([
                'result' => $result,
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            ob_get_clean();

            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check database connection.
     */
    private function checkDatabaseConnection(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get last N lines from a file.
     */
    private function tailFile(string $filepath, int $lines = 50): string
    {
        $file = new \SplFileObject($filepath, 'r');
        $file->seek(PHP_INT_MAX);
        $lastLine = $file->key();
        $startLine = max(0, $lastLine - $lines);

        $output = [];
        $file->seek($startLine);
        while (! $file->eof()) {
            $output[] = $file->current();
            $file->next();
        }

        return implode('', $output);
    }
}
