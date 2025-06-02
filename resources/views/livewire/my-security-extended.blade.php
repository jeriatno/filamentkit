<x-filament-breezy::grid-section md=1>
  <form wire:submit.prevent="submit" class="space-y-6">
    <x-filament::card>

      {{ $this->form->render() }}

    </x-filament::card>
    <div class="mt-6 flex space-x-2">
      {{-- <x-filament::button type="submit">
        Save
      </x-filament::button> --}}

      {{ $this->updatePassword }}

      <x-filament::button color="gray" type="button" wire:click="cancel">
        Cancel
      </x-filament::button>

    </div>
  </form>

  <x-filament-actions::modals />

</x-filament-breezy::grid-section>
