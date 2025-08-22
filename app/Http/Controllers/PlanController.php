<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Inertia\Inertia;
use Inertia\Response;

class PlanController extends Controller
{
    /**
     * Display a listing of available plans (public).
     */
    public function index(): Response
    {
        $plans = Plan::query()->orderBy('price')->get();

        return Inertia::render('Plans', [
            'plans' => $plans,
        ]);
    }
}


