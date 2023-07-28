<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrganisationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'contact'
], function () {

    Route::get("/", [ContactController::class, "index"]);
    Route::get("/show/{id}", [ContactController::class, "show"]);
    Route::post("/store", [ContactController::class, "store"]);
    Route::put("/update/{id}", [ContactController::class, "update"]);
    Route::delete("/destroy/{id}", [ContactController::class, "destroy"]);
    Route::post("/isAlreadyExist", [ContactController::class, "isAlreadyExist"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'organisation'
], function () {

    Route::get("/", [OrganisationController::class, "index"]);
    Route::get("/show/{id}", [OrganisationController::class, "show"]);
    Route::post("/store", [OrganisationController::class, "store"]);
    Route::put("/update/{id}", [OrganisationController::class, "update"]);
    Route::delete("/destroy/{id}", [OrganisationController::class, "destroy"]);
    Route::post("/isAlreadyExist", [OrganisationController::class, "isAlreadyExist"]);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'contact-organisation'
], function () {
    Route::get("/", [ContactController::class, "contactWithOrganisation"]);
    Route::get("/{contactId}", [ContactController::class, "contactIDWithOrganisation"]);
});
