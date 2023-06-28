<?php
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\VaultController;
use App\Http\Controllers\Api\v1\TeamsController;
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



Route::prefix('v1')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);
    //create Super Admin account
    Route::post('/register', [AuthController::class, 'createAccount']);

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::group(['middleware' => ['auth:sanctum']], function () {
        //team routes
        Route::post('/createTeam', [TeamsController::class, 'createTeam']);
        Route::post('/updateTeamName', [TeamsController::class, 'updateTeamName']);
        Route::get('/deleteTeam', [TeamsController::class, 'deleteTeam']);
        Route::post('/addTeamMember', [TeamsController::class, 'addTeamMember']);
        Route::post('/addTeamMemberByLeader', [TeamsController::class, 'addTeamMemberByLeader']);
        Route::post('/removeTeamMember', [TeamsController::class, 'removeTeamMember']);
        Route::get('/getUserTeams', [TeamsController::class, 'getUserTeams']);
        Route::post('/makeTeamLeader', [TeamsController::class, 'makeTeamLeader']);
        Route::post('/removeTeamLeader', [TeamsController::class, 'removeTeamLeader']);
        Route::get('/getTeamLeaders', [TeamsController::class, 'getTeamLeaders']);
        Route::get('/getTeamMembersWithRoles', [TeamsController::class, 'getTeamMembersWithRoles']);

        //vault routes
        //addTeam
        Route::post('/createVault', [VaultController::class, 'createVault']);
        Route::post('/updateVault', [VaultController::class, 'updateVault']);
        Route::delete('/deleteVault', [VaultController::class, 'deleteVault']);
        Route::post('/addTeam', [VaultController::class, 'addTeam']);
        Route::post('/removeTeam', [VaultController::class, 'removeTeam']);
        Route::get('/getTeamVaults', [VaultController::class, 'getTeamVaults']);
        Route::post('/getVault', [VaultController::class, 'getVault']);

        //socialAuth
        Route::post('/socialAuth', [AuthController::class, 'socialAuth']);

        // routes accessible only to SuperAdmin
        Route::group(['middleware' => 'superadmin'], function () {
            //user/admin registration
            Route::post('/createAdminUser', [AuthController::class, 'createAdminUser']);
        });

        // routes accessible only to Admin and SuperAdmin
        Route::group(['middleware' => 'teamlead'], function () {
            //
        });

        Route::group(['middleware' => 'staff'], function () {
            //
        });
    });
});
