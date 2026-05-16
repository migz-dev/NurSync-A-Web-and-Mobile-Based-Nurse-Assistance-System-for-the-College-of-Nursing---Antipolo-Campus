<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Faculty\DrugApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Note: If you want to protect these with tokens later, wrap the group with
| ->middleware('auth:sanctum') and issue tokens to your mobile app users.
*/

Route::get('/status', function () {
    return response()->json([
        'status' => 'ok',
        'time'   => now()->toIso8601String(),
    ]);
});

// Read-only Drug Guide API for mobile / external clients
Route::prefix('faculty')->name('api.faculty.')->group(function () {
    // List with search/filters/pagination
    Route::get('/drugs', [DrugApiController::class, 'index'])->name('drugs.index');

    // Full monograph details
    Route::get('/drugs/{id}', [DrugApiController::class, 'show'])
        ->whereNumber('id')
        ->name('drugs.show');
});
