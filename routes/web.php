<?php
use Illuminate\Support\Facades\Route;
Route::get('/', function () {
  return response()->json([
    'app'=>config('app.name'), 'url'=>config('app.url'),
    'roles'=>\Spatie\Permission\Models\Role::count(),
    'users'=>\App\Models\User::count(),
    'ok'=>true
  ]);
});
