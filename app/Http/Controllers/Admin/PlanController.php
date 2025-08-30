<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $plans = Plan::query()
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Plans/Index', [
            'plans' => $plans,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Plans/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'stripe_plan_id' => ['required', 'string', 'max:255'],
            'anet_plan_id' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'image_limit' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'verifications_included' => ['nullable', 'integer', 'min:0'],
            'features' => ['nullable'], // can be array or string from form
            'cta_label' => ['nullable', 'string', 'max:255'],
            'cta_route' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'visibility' => ['required', 'in:Public,Private'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        // Normalize features if provided as textarea string
        if (isset($validated['features']) && is_string($validated['features'])) {
            $features = preg_split('/\r?\n/', $validated['features']);
            $validated['features'] = array_values(array_filter(array_map('trim', $features)));
        }

        // Compute unique slug from provided slug or name
        $base = $validated['slug'] ?? $validated['name'];
        $validated['slug'] = Plan::uniqueSlug($base);

        Plan::create($validated);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Plan $plan): Response
    {
        return Inertia::render('Admin/Plans/Show', [
            'plan' => $plan,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plan $plan): Response
    {
        return Inertia::render('Admin/Plans/Edit', [
            'plan' => $plan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'stripe_plan_id' => ['required', 'string', 'max:255'],
            'anet_plan_id' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'image_limit' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'verifications_included' => ['nullable', 'integer', 'min:0'],
            'features' => ['nullable'],
            'cta_label' => ['nullable', 'string', 'max:255'],
            'cta_route' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'visibility' => ['required', 'in:Public,Private'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (isset($validated['features']) && is_string($validated['features'])) {
            $features = preg_split('/\r?\n/', $validated['features']);
            $validated['features'] = array_values(array_filter(array_map('trim', $features)));
        }

        // Compute unique slug. If slug empty, derive from name; ignore current record when checking uniqueness.
        $base = $validated['slug'] ?? $validated['name'];
        $validated['slug'] = Plan::uniqueSlug($base, $plan->id);

        $plan->update($validated);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $plan): RedirectResponse
    {
        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan deleted');
    }

    public function duplicate(Plan $plan): RedirectResponse
    {
        $copy = $plan->replicate();
        $copy->name = trim(($plan->name ?? 'Untitled') . ' - copy');
        $copy->is_active = false;

        // compute unique slug from new name
        $copy->slug = Plan::uniqueSlug($copy->name);

        $copy->save();

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan cloned');
    }
}


