<?php

namespace App\Http\Controllers\Subscribed;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Library\Meta;

class PagesController extends Controller
{
    public function home()
    {
        // SEO meta (Home)
        $title = 'InsureVerifyAI — Fast License & Insurance Verification for Car Rentals';
        $description = 'Verify renter driver licenses and insurance in seconds. Reduce fraud, save time, and scale with our developer-friendly API.';
        $url = url('/');
        $image = url('/images/about-2.png');

        Meta::addTitle($title);
        Meta::addMeta('description', $description);
        Meta::addMeta('og:url', $url);
        Meta::addMeta('og:type', 'website');
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
                'verifications_included', 'features', 'cta_label', 'cta_route'
            ]);

        return Inertia::render('Home', [
            'plans' => $plans,
        ]);
    }

    public function features()
    {
        // SEO meta (Features)
        $title = 'Features — InsureVerifyAI';
        $description = 'Instant license and insurance checks, developer-friendly APIs, strong encryption, and scalable infrastructure.';
        $url = url('/features');
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

        return Inertia::render('Features');
    }

    public function about()
    {
        // SEO meta (About)
        $title = 'About InsureVerifyAI';
        $description = 'We help car rental businesses verify licenses and insurance fast and securely. Trusted partnerships and coverage up to $300,000 in the rare event of identity-only verification failure.';
        $url = url('/about-us');
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

    public function faq()
    {
        // SEO meta (FAQ)
        $title = 'FAQ — InsureVerifyAI';
        $description = 'Answers to common questions about identity, license, and insurance verification, security, and getting started.';
        $url = url('/faq');
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

        return Inertia::render('FAQ');
    }
}
