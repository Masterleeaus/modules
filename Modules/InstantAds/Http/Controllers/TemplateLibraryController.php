<?php

namespace Modules\InstantAds\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\InstantAds\Entities\InstantAdsBrandKit;
use Modules\InstantAds\Entities\InstantAdsTemplate;

class TemplateLibraryController extends Controller
{
    /**
     * List all active templates grouped by category.
     */
    public function index(): JsonResponse
    {
        $templates = InstantAdsTemplate::active()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return response()->json([
            'success'   => true,
            'templates' => $templates,
        ]);
    }

    /**
     * Pre-fill the ad generator with a template prompt, optionally merged with a brand kit.
     */
    public function applyTemplate(InstantAdsTemplate $template, Request $request): JsonResponse
    {
        $brandKitId = $request->input('brand_kit_id');
        $prompt     = $template->prompt_template;

        if ($brandKitId) {
            $kit = InstantAdsBrandKit::find($brandKitId);

            if ($kit) {
                $prompt = $template->applyBrandKit($kit);
            }
        }

        return response()->json([
            'success'         => true,
            'template'        => $template,
            'resolved_prompt' => $prompt,
        ]);
    }
}
