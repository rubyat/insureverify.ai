<?php

namespace App\Http\Controllers\Subscribed;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Library\Meta;
use App\Library\Settings;

class PagesController extends Controller
{
    public function home()
    {
        // SEO meta (Home)
        $title = 'InsureVerify AI – Automated Insurance Verification';
        $description = 'Verify renters’ licenses and insurance in seconds with InsureVerify AI. Reduce risk, save time, and boost ROI through our secure, scalable API solution.';
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

        // Default Organization schema with dynamic settings where available
        Meta::addDefaultOrganizationSchema();

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
        $title = 'Key Features of InsureVerify AI';
        $description = 'Discover how InsureVerify AI verifies renters’ licenses and insurance in seconds. Explore core features that cut risk, save time, and boost ROI.';
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

        // WebPage + SoftwareApplication schema (dynamic where possible)
        $baseUrl = rtrim(config('app.url', url('/')), '/');
        $siteName = Settings::get('site_title', 'InsureVerify AI');
        $logo = Settings::get('site_logo_url', url('/favicon.png'));
        Meta::addSchema([
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'url' => $baseUrl . '/features',
            'name' => 'InsureVerify AI – Features',
            'description' => "Explore InsureVerify AI’s core features, including instant renter’s license and insurance verification, fraud detection, and scalable API integration.",
            'mainEntity' => [
                '@type' => 'SoftwareApplication',
                'name' => 'InsureVerify AI',
                'operatingSystem' => 'Cloud-based',
                'applicationCategory' => 'FinanceApplication',
                'offers' => [
                    '@type' => 'Offer',
                    'url' => $baseUrl . '/features',
                    'price' => 'Contact for pricing',
                    'priceCurrency' => 'USD',
                ],
                'featureList' => [
                    'Driver’s license verification API',
                    'Renters insurance verification',
                    'Fraud prevention tools',
                    'Pre-visit eligibility checks',
                    'Secure & Encrypted',
                    'Fast Verification',
                ],
                'description' => 'Verify renters’ licenses and insurance instantly with InsureVerify AI. Reduce fraud, save time, and scale operations with our secure verification API.',
                'url' => $baseUrl . '/',
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => $siteName,
                    'url' => $baseUrl . '/',
                    'logo' => $logo,
                ],
            ],
        ]);

        return Inertia::render('Features');
    }

    public function about()
    {
        // SEO meta (About)
        $title = 'Meet Our Team – Insure Verify AI About Us';
        $description = 'Explore Insure Verify AI, our mission, and dedicated team working to offer fast, reliable, and secure insurance verification powered by AI.';
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
        // SEO meta (Contact)
        $title = 'Connect with Insure Verify AI – Contact Us Today';
        $description = 'Reach out to Insure Verify AI for any queries, support, or collaboration. Fast and reliable assistance for insurance verification needs.';
        $url = url('/contact');
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
        // SEO meta (Privacy Policy)
        $title = 'Insure Verify AI Privacy Policy & Data Protection';
        $description = 'Learn how Insure Verify AI safeguards your personal information and ensures secure and compliant insurance verification services for all users.';
        $url = url('/privacy-policy');
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

        return Inertia::render('PrivacyPolicy');
    }

    public function terms()
    {
        // SEO meta (Terms)
        $title = 'Terms of Service – Insure Verify AI';
        $description = 'Read Insure Verify AI’s Terms of Service to understand the rules, user agreement, and guidelines for using our secure insurance verification platform.';
        $url = url('/terms-of-service');
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
