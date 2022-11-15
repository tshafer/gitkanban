<?php

namespace App\Http\Livewire\Projects;

use App\Models\Project;
use Illuminate\View\View;
use Livewire\Component;
use WireElements\Pro\Concerns\InteractsWithConfirmationModal;
use WireUi\Traits\Actions;

class Index extends Component
{
    use InteractsWithConfirmationModal;
    use Actions;

    /**
     * The components listeners.
     *
     * @var array
     */
    protected $listeners = ['refresh' => 'render'];



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
     * Confirm deletion of a provider
     */
    public function confirmDestroy(int $project): void
    {
        $this->dialog()->confirm([
            'title' => __('Are you sure?'),
            'description' => __('Deleting this Project will remove it from the system.'),
            'icon' => 'error',
            'accept' => [
                'label' => 'Yes, Delete it',
                'method' => 'destroy',
                'params' => $project,
            ],
            'reject' => [
                'label' => 'No, cancel',
            ],
        ]);
    }

    /**
     * Refresh the provider
     *
     * @param  Project  $project
     * @return void
     */
    public function refresh(Project $project): void
    {
        $project->refresh();

        $this->notification()->success(__('Project has been refreshed!'));
    }

    /**
     * Destroy the Project
     */
    public function destroy(Project $project): void
    {
        $project->delete();

        $this->notification()->success(__('Project Deleted Successfully!'));

        $this->emitSelf('refresh');
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        $projects = $this->user->currentTeam->projects()->get();

        return view('projects.index', compact('projects'));
    }
}
