<?php

namespace App\Http\Livewire\SourceProviders;

use App\Models\SourceProvider;
use Illuminate\Support\Collection;
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
    protected $listeners = ['refreshSourceProviders' => 'render'];

    /**
     * The source providers.
     */
    public Collection $source_providers;

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
        $this->source_providers = collect([
            'GitHub' => [
                'icon' => 'fab-github',
                'type' => 'github',
                'description' => 'Github',
                'url' => route('auth.social', 'github'),
            ],
            'GitLab' => [
                'icon' => 'fab-gitlab',
                'type' => 'gitlab',
                'description' => 'gitlab',
                'url' => route('auth.social', 'gitlab'),
            ],
            'BitBucket' => [
                'icon' => 'fab-bitbucket',
                'type' => 'bitbucket',
                'description' => 'bitbucket',
                'url' => route('auth.social', 'bitbucket'),
            ],
            // 'Gogs' =>
            // [
            //     'icon' => 'bitbucket',
            //     'type' => 'bitbucket',
            //     'description' => 'bitbucket',
            //     'url' => ''
            // ],
            // 'Gitea' =>
            // [
            //     'icon' => 'Gitea',
            //     'type' => 'Gitea',
            //     'description' => 'Gitea',
            //     'url' => ''
            // ],
        ]);
    }

    /**
     * Confirm deletion of a provider
     */
    public function confirmDestroy(int $source_provider): void
    {
        $this->dialog()->confirm([
            'title' => __('Are you sure?'),
            'description' => __('Deleting this Source Provider will remove it from the system.'),
            'icon' => 'error',
            'accept' => [
                'label' => 'Yes, Delete it',
                'method' => 'destroy',
                'params' => $source_provider,
            ],
            'reject' => [
                'label' => 'No, cancel',
            ],
        ]);
    }

    /**
     * Destroy the Source Provider
     */
    public function destroy(SourceProvider $source_provider): void
    {
        $source_provider->delete();

        $this->notification()->success(__('Source Provider Deleted Successfully!'));

        $this->emit('refreshSourceProviders');
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        $providers = $this->user->currentTeam->sourceProviders->groupBy('type')->all();

        return view('source-providers.index', compact('providers'));
    }
}
