<?php

namespace Modules\Recruit\View\Components;

use Illuminate\View\Component;

class CustomQuestionField extends Component
{
    public $fields;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($fields)
    {
        $this->fields = $fields;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('Recruit::components.cards.custom-question-field');
    }
}
