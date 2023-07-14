<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CityAlternativeNameController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProviderController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

Route::middleware("guest")->group(function (): void {
    Route::get("/signup", fn(): Response => Inertia::render("Auth/Register"));
    Route::get("/login", [LoginController::class, "create"])->name("login");
    Route::post("/login", [LoginController::class, "login"])->name("login");
    Route::post("/register", [RegisterController::class, "store"])->name("register");
});

Route::middleware("auth")->group(function (): void {
    Route::post("/logout", [LogoutController::class, "logout"])->name("logout");
    Route::get("/dashboard", fn(): Response => inertia("Dashboard"))->name("dashboard");
    Route::get("/notes", [NoteController::class, "index"])->name("notes.index");
    Route::post("/notes", [NoteController::class, "store"])->name("notes.store");
    Route::delete("/notes/{note}", [NoteController::class, "destroy"])->name("notes.destroy");

    Route::middleware(["role:admin"])->group(function (): void {
        Route::resource("/admin/dashboard/countries", CountryController::class);
        Route::resource("/admin/dashboard/cities", CityController::class);
        Route::resource("/city-alternative-name", CityAlternativeNameController::class);
        Route::get("/admin", fn(): Response => inertia("Admin"))->name("admin");
        Route::patch("/update-city-providers/{city}", [ProviderController::class, "update"]);
    });
});

Route::get("/", fn(): Response => inertia("Landing"))->name("home");

Route::get("/run-importers", [ProviderController::class, "runImporters"]);
