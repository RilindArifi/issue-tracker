{{-- Shared fields for create/edit. Expects optional $project. --}}
@php($project = $project ?? null)

<div class="space-y-6">
    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                      :value="old('name', $project?->name)" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" :value="__('Description')" />
        <textarea id="description" name="description" rows="4"
                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $project?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
            <x-input-label for="start_date" :value="__('Start date')" />
            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full"
                          :value="old('start_date', $project?->start_date?->format('Y-m-d'))" />
            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="deadline" :value="__('Deadline')" />
            <x-text-input id="deadline" name="deadline" type="date" class="mt-1 block w-full"
                          :value="old('deadline', $project?->deadline?->format('Y-m-d'))" />
            <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
        </div>
    </div>
</div>
