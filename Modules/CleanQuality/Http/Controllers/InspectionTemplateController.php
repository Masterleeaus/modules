<?php

namespace Modules\CleanQuality\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CleanQuality\Entities\InspectionTemplate;
use Modules\CleanQuality\Http\Requests\StoreInspectionTemplateRequest;
use Modules\CleanQuality\Http\Requests\UpdateInspectionTemplateRequest;

class InspectionTemplateController extends Controller
{
    public function index()
    {
        $templates = InspectionTemplate::query()
            ->orderByDesc('id')
            ->paginate(20);

        return view('quality_control::templates.index', compact('templates'));
    }

    public function create()
    {
        return view('quality_control::templates.create');
    }

    public function store(StoreInspectionTemplateRequest $request)
    {
        $template = InspectionTemplate::create($request->validated());

        return redirect()
            ->route('inspection-templates.edit', $template->id)
            ->with('success', __('quality_control::messages.template_created'));
    }

    public function show($id)
    {
        $template = InspectionTemplate::with('items')->findOrFail($id);

        return view('quality_control::templates.show', compact('template'));
    }

    public function edit($id)
    {
        $template = InspectionTemplate::with('items')->findOrFail($id);

        return view('quality_control::templates.edit', compact('template'));
    }

    public function update(UpdateInspectionTemplateRequest $request, $id)
    {
        $template = InspectionTemplate::findOrFail($id);
        $template->update($request->validated());

        return back()->with('success', __('quality_control::messages.template_updated'));
    }

    public function destroy($id)
    {
        $template = InspectionTemplate::findOrFail($id);
        $template->delete();

        return redirect()
            ->route('inspection-templates.index')
            ->with('success', __('quality_control::messages.template_deleted'));
    }
}
