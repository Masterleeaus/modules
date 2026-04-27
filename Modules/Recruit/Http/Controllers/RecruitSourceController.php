<?php

namespace Modules\Recruit\Http\Controllers;

use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Recruit\Entities\ApplicationSource;
use Modules\Recruit\Entities\RecruitJobApplication;
use Modules\Recruit\Http\Requests\StoreSourceRequest;
use Modules\Recruit\Http\Requests\UpdateSourceRequest;

class RecruitSourceController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('recruit::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('recruit::job-applications.source.create_source');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSourceRequest $request)
    {
        $company_id = auth()->user()->company_id;

        $source = new ApplicationSource();
        $source->application_source = $request->source;
        $source->company_id = $company_id;
        $source->is_predefined = false;
        $source->save();
        return Reply::success(__('messages.recordSaved'));

    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('recruit::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $source = ApplicationSource::findOrFail($id);
        return view('recruit::job-applications.source.edit_source', ['source' => $source]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSourceRequest $request, $id)
    {
        $company_id = auth()->user()->company_id;

        $source = ApplicationSource::findOrFail($id);
        $source->application_source = $request->source;
        $source->update();
        return Reply::successWithData(('messages.updateSuccess'), ['redirectUrl' => route('source-setting.edit', $id)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $source = ApplicationSource::findOrFail($id);

        $otherSource = ApplicationSource::where('application_source', 'Other')->first();

        $jobApplications = RecruitJobApplication::where('application_source_id', $id)->update(['application_source_id' => $otherSource->id]);

        $source->delete();

        return Reply::successWithData(__('messages.deleteSuccess'), ['redirectUrl' => route('source-setting.index')]);
    }

}
