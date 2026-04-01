<?php

use App\Http\Controllers\Api\Admin\AgencyController as AdminAgencyController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\SystemController as AdminSystemController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Agency\DashboardController as AgencyDashboardController;
use App\Http\Controllers\Api\Agency\ProfileController as AgencyProfileController;
use App\Http\Controllers\Api\Agency\PurchaseRequestController as AgencyPurchaseRequestController;
use App\Http\Controllers\Api\Agency\ReservationController as AgencyReservationController;
use App\Http\Controllers\Api\Agency\VehicleController as AgencyVehicleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Client\AlertController;
use App\Http\Controllers\Api\Client\ProfileController;
use App\Http\Controllers\Api\Client\PurchaseRequestController;
use App\Http\Controllers\Api\Client\ReservationController;
use App\Http\Controllers\Api\Public\AgencyController;
use App\Http\Controllers\Api\Public\VehicleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::prefix('catalogue')->group(function (): void {
        Route::get('/vehicules/filtres', [VehicleController::class, 'listCatalogueVehicleFilters']);
        Route::get('/vehicules', [VehicleController::class, 'listCatalogueVehicles']);
        Route::get('/vehicules/{vehicle}', [VehicleController::class, 'showCatalogueVehicle']);
        Route::get('/vehicules/{vehicle}/verifier-disponibilite', [VehicleController::class, 'checkCatalogueVehicleAvailability']);
        Route::get('/agences', [AgencyController::class, 'listCatalogueAgencies']);
        Route::get('/agences/{agency:slug}', [AgencyController::class, 'showCatalogueAgency']);
    });

    Route::post('/authentification/client/inscription', [AuthController::class, 'registerClient']);
    Route::post('/authentification/agence/inscription', [AuthController::class, 'registerAgency']);
    Route::post('/authentification/connexion', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/authentification/moi', [AuthController::class, 'me']);
        Route::post('/authentification/deconnexion', [AuthController::class, 'logout']);

        Route::middleware('role:client')->prefix('client')->group(function (): void {
            Route::get('/profil', [ProfileController::class, 'show']);
            Route::put('/profil', [ProfileController::class, 'update']);
            Route::get('/reservations', [ReservationController::class, 'index']);
            Route::post('/reservations', [ReservationController::class, 'store']);
            Route::get('/demandes-achat', [PurchaseRequestController::class, 'index']);
            Route::post('/demandes-achat', [PurchaseRequestController::class, 'store']);
            Route::get('/alertes', [AlertController::class, 'index']);
            Route::patch('/alertes/{alert}/lire', [AlertController::class, 'markAsRead']);
        });

        Route::middleware('role:agency')->prefix('agence')->group(function (): void {
            Route::get('/tableau-de-bord', AgencyDashboardController::class);
            Route::get('/vehicules', [AgencyVehicleController::class, 'index']);
            Route::post('/vehicules', [AgencyVehicleController::class, 'store']);
            Route::put('/vehicules/{vehicle}', [AgencyVehicleController::class, 'update']);
            Route::get('/reservations', [AgencyReservationController::class, 'index']);
            Route::patch('/reservations/{reservation}/statut', [AgencyReservationController::class, 'updateStatus']);
            Route::get('/demandes-achat', [AgencyPurchaseRequestController::class, 'index']);
            Route::patch('/demandes-achat/{purchaseRequest}/statut', [AgencyPurchaseRequestController::class, 'updateStatus']);
            Route::get('/profil', [AgencyProfileController::class, 'show']);
            Route::put('/profil', [AgencyProfileController::class, 'update']);
        });

        Route::middleware('role:admin')->prefix('administration')->group(function (): void {
            Route::get('/tableau-de-bord', AdminDashboardController::class);
            Route::get('/agences', [AdminAgencyController::class, 'index']);
            Route::post('/agences', [AdminAgencyController::class, 'store']);
            Route::patch('/agences/{agency}/statut', [AdminAgencyController::class, 'updateStatus']);
            Route::get('/utilisateurs', [AdminUserController::class, 'index']);
            Route::get('/systeme', AdminSystemController::class);
        });
    });
});
