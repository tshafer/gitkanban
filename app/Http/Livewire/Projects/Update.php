<?php

namespace App\Http\Livewire\Projects;

use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use WireElements\Pro\Components\Modal\Modal;
use WireUi\Traits\Actions;

class Update extends Modal
{
    use AuthorizesRequests;
    use Actions;

    public $project;

    /**
     * @var array
     */
    public $form = [
        'name' => '',
    ];

    /**
     * @var array
     */
    protected $messages = [
        'form.name' => 'Name is required.',
    ];

    /**
     * @var array
     */
    protected $rules = [
        'form.name' => 'required',
    ];

    /**
     * Mount the component.
     */
    public function mount(Project $project): void
    {
        $this->project = $project;

        $this->form = [
            'name' => $this->project->name,
        ];
    }

    /**
     * Update the project.
     *
     * @return void
     */
    public function update(): void
    {
        $this->validate();

        $this->project->update($this->form);

        $this->reset('form');

        $this->close(andEmit: [
            'refresh',
        ]);

        $this->dialog()->success(__('Project Updated!'));
    }

    /**
     * Render the component view.
     *
     * @return View
     */
    public function render(): View
    {
        $properties = [
            'action' => 'update',
            'title' => __('Update Project'),
            'id' => 'update-project-modal',
        ];

        return view('projects.form', compact('properties'));
    }
}
