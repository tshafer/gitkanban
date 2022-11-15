<?php

namespace App\Http\Livewire\Projects;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use WireElements\Pro\Components\Modal\Modal;
use WireUi\Traits\Actions;

class Create extends Modal
{
    use AuthorizesRequests;
    use Actions;


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
     * The user
     */
    public function getUserProperty(): mixed
    {
        return auth()->user();
    }

    /**
     * Mount the component.
     */
    public function mount(): void
    {
    }

    /**
     * Update the project
     *
     * @return void
     */
    public function create(): void
    {
        $this->validate();

        $this->user->currentTeam->projects()->create($this->form);

        $this->reset('form');

        $this->close(andEmit: [
            'refresh',
        ]);

        $this->dialog()->success(__('Project Created!'));
    }

    /**
     * Render the component view.
     *
     * @return View
     */
    public function render(): View
    {
        $properties = [
            'action' => 'create',
            'title' => __('Create Project'),
            'id' => 'create-project-modal',
        ];

        return view('projects.form', compact('properties'));
    }
}
