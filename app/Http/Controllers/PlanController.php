<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Inertia\Inertia;
use Inertia\Response;
use App\Library\Meta;

class PlanController extends Controller
{
    /**
     * Display a listing of available plans (public).
     */
    public function index(): Response
    {
        // SEO meta (Pricing)
        $title = 'Insurance Verification API Pricing & Plans';
        $description = 'See InsureVerify AI’s transparent pricing tiers. Choose a plan that fits—real-time license checks, renters insurance verification, and fraud prevention tools.';
        $url = url('/plans');
        $image = url('/images/about-2.png');

        Meta::addTitle($title);
        Meta::addMeta('description', $description);
        Meta::addMeta('og:url', $url);
        Meta::addMeta('og:type', 'article');
        Meta::addMeta('og:title', $title);
        Meta::addMeta('og:description', $description);
        Meta::addMeta('og:image', $image);
        Meta::addMeta('og:site_name', 'InsureVerifyAI');
        Meta::addMeta('twitter:card', 'summary_large_image');
        Meta::addMeta('twitter:title', $title);
        Meta::addMeta('twitter:description', $description);
        Meta::addMeta('twitter:image', $image);

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


