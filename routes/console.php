<?php

use App\Jobs\KirimPengingatSewa;
use Illuminate\Support\Facades\Schedule;

// Kirim pengingat H-1 setiap hari jam 08.00 pagi
Schedule::job(new KirimPengingatSewa)->dailyAt('08:00');