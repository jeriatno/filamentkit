<x-filament-breezy::grid-section md=1>
  <x-filament::card>
    <form wire:submit.prevent="submit" class="space-y-6">
      {{ $this->form }}
    </form>
  </x-filament::card>

  <div class="mt-6">

    <x-filament::button color="gray" type="button" wire:click="cancel" class="align-right">
      Cancel
    </x-filament::button>


    {{-- @if ($action = $this->getFooterActions()[0] ?? null)
      {{ $action->render() }}
    @endif --}}
  </div>
</x-filament-breezy::grid-section>
