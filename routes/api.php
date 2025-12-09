<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CleaningTaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token,
        'token_type' => 'Bearer'
    ]);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user()->load(['hotel', 'cleaner.hotel']);
    });

    // Logout
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    });

    // Bookings (for owners)
    Route::apiResource('bookings', BookingController::class);

    // Cleaning Tasks
    Route::apiResource('cleaning-tasks', CleaningTaskController::class)->only(['index', 'show']);

    // Task actions for cleaners
    Route::post('/cleaning-tasks/{cleaningTask}/start', [CleaningTaskController::class, 'start']);
    Route::post('/cleaning-tasks/{cleaningTask}/stop', [CleaningTaskController::class, 'stop']);
    Route::post('/cleaning-tasks/{cleaningTask}/complete', [CleaningTaskController::class, 'complete']);
});
