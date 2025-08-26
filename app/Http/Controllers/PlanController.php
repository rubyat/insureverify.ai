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
        $plans = Plan::query()
            ->where('is_active', true)
            ->where('visibility', 'Public')
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get([
                'id', 'name', 'slug', 'price', 'image_limit', 'description',
                'verifications_included', 'features', 'cta_label', 'cta_route', 'anet_plan_id'
            ]);

        return Inertia::render('Plans', [
            'plans' => $plans,
        ]);
    }

    /**
     * Show a single plan by slug for signup page.
     */
    public function show(string $slug): Response
    {
        $plan = Plan::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('visibility', 'Public')
            ->firstOrFail();

        $plans = Plan::query()
            ->where('is_active', true)
            ->where('visibility', 'Public')
            ->orderBy('price')
            ->get(['id', 'name', 'slug', 'price', 'verifications_included']);

        return Inertia::render('PlanSignup', [
            'plan' => $plan,
            'plans' => $plans,
        ]);
    }
}


