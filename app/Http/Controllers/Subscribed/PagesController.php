<?php

namespace App\Http\Controllers\Subscribed;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PagesController extends Controller
{
    public function home()
    {
        $plans = Plan::query()
            ->where('is_active', true)
            ->where('visibility', 'Public')
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get([
                'id', 'name', 'slug', 'price', 'image_limit', 'description',
                'verifications_included', 'features', 'cta_label', 'cta_route'
            ]);

        return Inertia::render('Home', [
            'plans' => $plans,
        ]);
    }

    public function features()
    {
        return Inertia::render('Features');
    }

    public function about()
    {
        return Inertia::render('About');
    }

    public function docs()
    {
        return Inertia::render('Docs');
    }

    public function contact()
    {
        return Inertia::render('Contact');
    }

    public function submitContact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        Log::info('Marketing contact submission', $data);

        return back()->with('success', 'Thanks! We\'ll get back to you shortly.');
    }

    public function privacy()
    {
        return Inertia::render('PrivacyPolicy');
    }

    public function terms()
    {
        return Inertia::render('TermsOfService');
    }

    public function signup()
    {
        return Inertia::render('Signup');
    }
}
