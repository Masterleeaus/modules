<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CmsPageController extends Controller
{
    public function home(): View
    {
        return $this->renderSlug('home');
    }

    public function show(string $slug): View
    {
        return $this->renderSlug($slug);
    }

    protected function renderSlug(string $slug): View
    {
        $page = CmsPage::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (! $page || ! $page->isPublished()) {
            throw new NotFoundHttpException;
        }

        $settings = Schema::hasTable('platform_settings')
            ? DB::table('platform_settings')->first()
            : null;

        return view('cms.page', [
            'page' => $page,
            'settings' => $settings,
        ]);
    }
}
