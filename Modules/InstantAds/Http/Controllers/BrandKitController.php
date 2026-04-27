<?php

namespace Modules\InstantAds\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\InstantAds\Entities\InstantAdsBrandKit;

class BrandKitController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $companyId = $this->resolveCompanyId($request);

        $kits = InstantAdsBrandKit::forCompany($companyId)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'kits'    => $kits,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'primary_color'   => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo_path'       => 'nullable|string',
            'tagline'         => 'nullable|string|max:255',
            'is_default'      => 'boolean',
        ]);

        $companyId = $this->resolveCompanyId($request);

        // Only one default per company
        if (! empty($data['is_default'])) {
            InstantAdsBrandKit::forCompany($companyId)->where('is_default', true)->update(['is_default' => false]);
        }

        $kit = InstantAdsBrandKit::create(array_merge($data, [
            'company_id' => $companyId,
            'created_by' => auth()->id(),
        ]));

        return response()->json([
            'success' => true,
            'kit'     => $kit,
        ], 201);
    }

    public function setDefault(InstantAdsBrandKit $kit): JsonResponse
    {
        InstantAdsBrandKit::forCompany($kit->company_id)->where('is_default', true)->update(['is_default' => false]);

        $kit->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'kit'     => $kit->fresh(),
        ]);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function resolveCompanyId(Request $request): int
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'getAttribute') && $user->company_id) {
            return (int) $user->company_id;
        }

        return (int) $request->input('company_id', 1);
    }
}
