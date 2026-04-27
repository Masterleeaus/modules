<?php

namespace Modules\ProShots\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Modules\ProShots\Entities\UserPebblely;
use Modules\ProShots\Http\Requests\ProShotsRequest;
use Modules\ProShots\Services\PebblelyService;

class ProShotsController extends Controller
{
    public function __construct(
        protected PebblelyService $service
    ) {}

    public function index(): \Illuminate\Contracts\View\View
    {
        $userId = Auth::id();

        return view('proshots::index', [
            'last'   => UserPebblely::where('user_id', $userId)->latest()->first(),
            'images' => UserPebblely::where('user_id', $userId)->latest()->get(),
            'themes' => $this->service->getThemes(),
        ]);
    }

    public function store(ProShotsRequest $request): JsonResponse|RedirectResponse
    {
        $removedImage = $this->service->removeBg($request->file('image'));

        if (is_array($removedImage) && isset($removedImage['error'])) {
            return redirect()->back()->with([
                'message' => $removedImage['message'] ?? __('Background removal failed.'),
                'type'    => 'error',
            ]);
        }

        $response = $this->service->createBg($removedImage, $request->get('background'));

        if (is_array($response) && isset($response['error'])) {
            return redirect()->back()->with([
                'message' => $response['message'] ?? __('ProShots API error.'),
                'type'    => 'error',
            ]);
        }

        UserPebblely::create([
            'user_id' => Auth::id(),
            'image'   => $response,
        ]);

        return redirect()->back()->with([
            'message' => __('Professional shot created successfully.'),
            'type'    => 'success',
        ]);
    }

    public function destroy(string $id): RedirectResponse
    {
        $record = UserPebblely::where('user_id', Auth::id())->findOrFail($id);
        $record->delete();

        return redirect()->back()->with([
            'message' => __('Deleted successfully.'),
            'type'    => 'success',
        ]);
    }
}
