<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    $initialDbConfig = config('database');
    
    $exportDbConnection = env('EXPORT_DB_CONNECTION');
    config([
        "database.connections.{$exportDbConnection}.host"     => env('EXPORT_DB_HOST'),
        "database.connections.{$exportDbConnection}.port"     => env('EXPORT_DB_PORT'),
        "database.connections.{$exportDbConnection}.username" => env('EXPORT_DB_USERNAME'),
        "database.connections.{$exportDbConnection}.password" => env('EXPORT_DB_PASSWORD'),
        "database.connections.{$exportDbConnection}.database" => env('EXPORT_DB_DATABASE'),
        'database.default'                                   => $exportDbConnection,
    ]);
    $tables = Schema::getTables(env('EXPORT_DB_DATABASE'));
    $exportTableNames = array_column($tables, 'name');


    dump($exportTableNames);


    // set back to initial database configuration
    config(['database' => $initialDbConfig]);

    Route::get('dashboard', function ()  use ($exportTableNames) {
        return Inertia::render('dashboard', [
            'exportTableNames' => $exportTableNames,
        ]);
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
