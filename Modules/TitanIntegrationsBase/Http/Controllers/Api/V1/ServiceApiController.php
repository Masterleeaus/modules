<?php

namespace Modules\TitanIntegrations\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ServiceApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $companyId = $request->attributes->get('api_company_id');

        // ServiceManagement module may not be active on all instances
        if (class_exists(\Modules\ServiceManagement\Entities\Service::class)) {
            $services = \Modules\ServiceManagement\Entities\Service::where('company_id', $companyId)
                ->where('status', 1)
                ->select(['id', 'name', 'slug', 'description', 'discount', 'tax', 'created_at'])
                ->paginate(50);

            return response()->json($services);
        }

        return response()->json(['data' => [], 'message' => 'Service catalogue not available']);
    }
}
