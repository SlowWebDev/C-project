<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Backend API Endpoints
|--------------------------------------------------------------------------
|
| API routes for external integrations and AJAX requests.
| All routes here are prefixed with '/api' and use 'api' middleware group.
| 
| @author SlowWebDev
|
*/

/*
|--------------------------------------------------------------------------
| Authenticated API Routes
|--------------------------------------------------------------------------
|
| Routes requiring Sanctum token authentication
|
*/

// User profile endpoint for authenticated users
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
|
| Future public endpoints can be added here without authentication
| Example: public project listings, contact forms, etc.
|
*/
