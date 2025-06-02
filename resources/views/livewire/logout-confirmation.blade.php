<div>
  <x-filament::modal id="logout-modal" :close-button="false" x-data="{ isOpen: false }"
    x-on:close-modal.window="if ($event.detail.id === 'logout-modal') isOpen = false"
    x-on:open-modal.window="if ($event.detail.id === 'logout-modal') isOpen = true"
    x-on:livewire:navigated="isOpen = false">

    <x-slot name="heading">
      <div class="fi-modal-header flex ps-5 pt-2 text-center justify-center">
        <h2 class="text-red-600 font-bold text-[22px]">
          Are you sure you want to logout?
        </h2>
      </div>
    </x-slot>

    <div class="text-gray-500 text-center">
      You will be signed out from your account. Make sure to save any ongoing work.
    </div>

    <x-slot name="footer">
      <div class="flex justify-center gap-4 w-full">
        <x-filament::button color="gray" class="w-1/2" @click="$dispatch('close-modal', { id: 'logout-modal' })">
          Cancel
        </x-filament::button>
        <x-filament::button color="danger" class="w-1/2" wire:click="logout">
          Logout
        </x-filament::button>
      </div>
    </x-slot>
  </x-filament::modal>
</div>
