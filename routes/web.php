<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityAlternativeNameController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CityProviderController;
use App\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;
use Inertia\Response;

Route::middleware("guest")->group(function (): void {
    Route::post("/login", [AuthController::class, "login"])->name("login");
    Route::post("/register", [AuthController::class, "store"])->name("register");
});

Route::middleware("auth")->group(function (): void {
    Route::post("/logout", [AuthController::class, "logout"])->name("logout");
    Route::get("/dashboard", fn(): Response => inertia("Dashboard"))->name("dashboard");

    Route::middleware(["role:admin"])->group(function (): void {
        Route::resource("/admin/dashboard/countries", CountryController::class);
        Route::resource("/admin/dashboard/cities", CityController::class);
        Route::resource("/city-alternative-name", CityAlternativeNameController::class);
        Route::get("/admin", fn(): Response => inertia("Admin"))->name("admin");
        Route::patch("/update-city-providers/{city}", [CityProviderController::class, "update"]);
    });
});

Route::inertia("/", "Landing")->name("home");

Route::get("/run-importers", [CityProviderController::class, "runImporters"]);
