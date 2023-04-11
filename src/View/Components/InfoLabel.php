<?php

namespace App\View\Components\Facegen;

use Illuminate\View\Component;

class InfoLabel extends Component
{
    public $label;
    public $value;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($label, $value)
    {
        $this->label = $label;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.crudify.info-label');
    }
}
