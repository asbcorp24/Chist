<x-filament::page>
    <div class="grid gap-6 xl:grid-cols-5">
        <div class="xl:col-span-2">
            <div class="rounded-xl bg-white p-6 shadow">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Импорт расписания</h2>
                    <p class="mt-1 text-sm text-gray-500">XML Rector-College. Учебный год и полугодие определяются по датам файла. Старые журналы не удаляются.</p>
                </div>
                <form wire:submit.prevent="import" class="space-y-6">
                    {{ $this->form }}
                    <x-filament::button type="submit" icon="heroicon-o-upload" class="w-full">
                        Загрузить и сформировать занятия
                    </x-filament::button>
                </form>
            </div>
        </div>
        <div class="xl:col-span-3">
            <div class="rounded-xl bg-white shadow">
                <div class="border-b px-6 py-4">
                    <h2 class="text-lg font-bold text-gray-900">История импортов</h2>
                    <p class="text-sm text-gray-500">Последние 30 загрузок расписания</p>
                </div>
                <div class="divide-y">
                    @forelse($runs as $run)
                        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
                            <div class="min-w-0">
                                <div class="truncate font-semibold text-gray-900">{{ $run['filename'] ?: 'XML-файл' }}</div>
                                <div class="mt-1 text-sm text-gray-500">{{ $run['year_name'] ?: '—' }} · {{ $run['period_name'] ?: '—' }} · {{ $run['created_at'] }}</div>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $run['status'] === 'done' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $run['status'] === 'done' ? 'Готово' : $run['status'] }}
                            </span>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-gray-500">Импортов ещё нет.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-filament::page>
