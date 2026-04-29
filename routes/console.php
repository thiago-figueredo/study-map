<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('migrate:test', function () {
    $this->comment('Migrating...');
    Artisan::call('migrate');
    $this->comment(Artisan::output());

    $this->comment('Rolling back...');
    Artisan::call('migrate:rollback');
    $this->comment(Artisan::output());

    $this->comment('Migrating...');
    Artisan::call('migrate');
    $this->comment(Artisan::output());
})->purpose('Test migration commit and rollback ');
