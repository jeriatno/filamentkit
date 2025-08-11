<x-filament::page>
  {{-- Konten detail user, misalnya --}}
  <div class="space-y-4">
    {{-- Render form atau komponen detail lainnya --}}
    {{ $this->form }}
  </div>

  {{-- Footer Actions: tombol Edit dan Cancel --}}
  <div class="mt-0 flex gap-x-3">
    @foreach ($this->getFooterActions() as $action)
      {{ $action->render() }}
    @endforeach
  </div>
</x-filament::page>
