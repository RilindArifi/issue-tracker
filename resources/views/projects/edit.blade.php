<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit project') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('projects.update', $project) }}">
                    @csrf
                    @method('PUT')
                    @include('projects._form')

                    <div class="mt-6 flex items-center gap-4">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                        <a href="{{ route('projects.show', $project) }}"
                           class="text-sm text-gray-600 hover:text-gray-900">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-medium text-gray-900">{{ __('Danger zone') }}</h3>
                <form method="POST" action="{{ route('projects.destroy', $project) }}" class="mt-4"
                      onsubmit="return confirm('{{ __('Delete this project and all its issues?') }}')">
                    @csrf
                    @method('DELETE')
                    <x-danger-button>{{ __('Delete project') }}</x-danger-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
