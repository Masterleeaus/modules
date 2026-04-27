<?php
namespace Modules\OnboardingPro\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\OnboardingPro\Entities\Banner;

class BannerController extends AccountBaseController
{
    public function index()
    {
        $this->pageTitle = __('onboardingpro::onboardingpro.banners');
        $banners = Banner::orderBy('order')->get();
        return view('onboardingpro::admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $this->pageTitle = __('onboardingpro::onboardingpro.add_banner');
        return view('onboardingpro::admin.banners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|string|max:500',
            'cta_text'    => 'nullable|string|max:100',
            'cta_url'     => 'nullable|url|max:500',
            'role'        => 'required|in:admin,employee,client,all',
            'order'       => 'required|integer|min:0',
            'active'      => 'boolean',
        ]);

        Banner::create($data);

        return Reply::success(__('onboardingpro::onboardingpro.banner_created'));
    }

    public function edit(Banner $banner)
    {
        $this->pageTitle = __('onboardingpro::onboardingpro.edit_banner');
        return view('onboardingpro::admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|string|max:500',
            'cta_text'    => 'nullable|string|max:100',
            'cta_url'     => 'nullable|url|max:500',
            'role'        => 'required|in:admin,employee,client,all',
            'order'       => 'required|integer|min:0',
            'active'      => 'boolean',
        ]);

        $banner->update($data);

        return Reply::success(__('onboardingpro::onboardingpro.banner_updated'));
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return Reply::success(__('onboardingpro::onboardingpro.banner_deleted'));
    }

    public function show(Banner $banner)
    {
        return view('onboardingpro::admin.banners.edit', compact('banner'));
    }
}
