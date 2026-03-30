<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Mise à jour automatique des statuts — chaque jour à 01h00 ────
Schedule::command('misalo:update-statuts')
    ->dailyAt('01:00')
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// ── Relance des réservations en attente — chaque jour à 09h00 ───
Schedule::command('misalo:relancer-attente')
    ->dailyAt('09:00')
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// Mise à jour automatique des réservations terminées — 01h00
Schedule::command('misalo:update-statuts')
    ->dailyAt('01:00');

// Rappels et alertes — toutes les heures
Schedule::command('misalo:rappels-quotidiens')
    ->hourly();

// Rapport revenus J-1 — chaque matin à 08h00
Schedule::command('misalo:rapport-revenus')
    ->dailyAt('08:00');
