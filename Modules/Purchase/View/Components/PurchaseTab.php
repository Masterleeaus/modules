<?php

namespace Modules\Purchase\View\Components;

use Illuminate\View\Component;

class PurchaseTab extends Component
{

    public $href;
    public $text;
    public $ajax;
    public $count;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($href, $text, $count = 0, $ajax = 'true')
    {
        $this->href = $href;
        $this->text = $text;
        $this->ajax = $ajax;
        $this->count = $count;

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('purchase::components.purchase-tab');
    }

}
