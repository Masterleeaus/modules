<?php

namespace Modules\TitanIntegrations\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ZoneApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $companyId = $request->attributes->get('api_company_id');

        if (class_exists(\Modules\ZoneManagement\Entities\Zone::class)) {
            $zones = \Modules\ZoneManagement\Entities\Zone::where('company_id', $companyId)
                ->where('is_active', 1)
                ->select(['id', 'name', 'center_lat', 'center_lng', 'radius', 'created_at'])
                ->get();

            return response()->json(['data' => $zones]);
        }

        return response()->json(['data' => [], 'message' => 'Zone management not available']);
    }
}
