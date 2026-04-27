<?php

namespace Modules\Recruit\View\Components;

use Illuminate\View\Component;

class JobCard extends Component
{
    public $jobApplication;

    public $applicationLength;

    public $draggable;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($jobApplication, $draggable, $applicationLength)
    {
        $this->jobApplication = $jobApplication;
        $this->draggable = $draggable;
        $this->applicationLength = $applicationLength;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('Recruit::components.cards.job-card');
    }
}
