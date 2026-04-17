<?php


namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

class Modal extends Component
{
    public string $id;
    public string $title;

    public function __construct(string $id = null, string $title = '')
    {
        // Auto-generate ID if not provided
        $this->id = $id ?? 'modal-' . uniqid();

        $this->title = $title;
    }

    public function render(): View
    {
        return view('admin.components.modal');
    }
}
