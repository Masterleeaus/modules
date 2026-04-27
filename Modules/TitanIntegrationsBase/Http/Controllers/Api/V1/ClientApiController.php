<?php

namespace Modules\TitanIntegrations\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ClientApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $companyId = $request->attributes->get('api_company_id');

        $query = User::where('company_id', $companyId)
            ->whereHas('roles', fn($q) => $q->where('name', 'client'));

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(fn($q) => $q
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
            );
        }

        $clients = $query->select(['id', 'name', 'email', 'created_at'])
            ->latest()
            ->paginate(50);

        return response()->json($clients);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $companyId = $request->attributes->get('api_company_id');

        $client = User::where('company_id', $companyId)
            ->whereHas('roles', fn($q) => $q->where('name', 'client'))
            ->findOrFail($id);

        return response()->json($client->only(['id', 'name', 'email', 'created_at', 'mobile']));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $companyId = $request->attributes->get('api_company_id');

        $client = User::create([
            'name'       => $request->input('name'),
            'email'      => $request->input('email'),
            'mobile'     => $request->input('mobile'),
            'company_id' => $companyId,
            'password'   => bcrypt(\Illuminate\Support\Str::random(16)),
        ]);

        $clientRole = \Spatie\Permission\Models\Role::where('name', 'client')
            ->where('company_id', $companyId)
            ->first();

        if ($clientRole) {
            $client->assignRole($clientRole);
        }

        // Send password-reset notification so the client can set their own password
        $client->sendPasswordResetNotification(
            app(\Illuminate\Auth\Passwords\PasswordBroker::class)->createToken($client)
        );

        return response()->json($client->only(['id', 'name', 'email', 'created_at']), 201);
    }
}
