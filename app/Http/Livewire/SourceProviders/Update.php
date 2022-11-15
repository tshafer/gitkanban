<?php

namespace App\Http\Livewire\SourceProviders;

use App\Models\SourceProvider;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use WireElements\Pro\Components\Modal\Modal;
use WireUi\Traits\Actions;

class Update extends Modal
{
    use AuthorizesRequests;
    use Actions;

    public $source_provider;

    /**
     * @var array
     */
    public $form = [
        'label' => '',
    ];

    /**
     * @var array
     */
    protected $messages = [
        'form.label' => 'Label is required.',
    ];

    /**
     * @var array
     */
    protected $rules = [
        'form.label' => 'required',
    ];

    /**
     * Mount the component.
     */
    public function mount(SourceProvider $source_provider): void
    {
        $this->source_provider = $source_provider;

        $this->form = [
            'label' => $this->source_provider->label,
        ];
    }

    /**
     * Update the source provider.
     *
     * @return void
     */
    public function update(): void
    {
        $this->validate();

        $this->source_provider->update($this->form);

        $this->reset('form');

        $this->close(andEmit: [
            'refreshSourceProviders',
        ]);

        $this->dialog()->success(__('Source Provider Updated!'));
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
            'title' => __('Update Source Provider'),
            'id' => 'update-source-provider-modal',
        ];

        return view('source-providers.form', compact('properties'));
    }
}
