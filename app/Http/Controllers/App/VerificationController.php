<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VerificationController
{
    public function index(Request $request): Response
    {
        // Placeholder props similar to previous Upload page
        return Inertia::render('app/Verification', [
            'remainingUploads' => 58,
            'cycleResetDate' => now()->addDays(12)->toDateString(),
            'queue' => [],
            'atLimit' => false,
            'upgradeUrl' => route('plans.index'),
        ]);
    }
}
