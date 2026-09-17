<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
            <x-filament::button type="submit" color="primary" icon="heroicon-o-check">
                Save All Changes
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
