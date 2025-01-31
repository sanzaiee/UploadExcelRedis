<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Mobile Numbers') }}
        </h2>

    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="max-w-xl">
                <header>
                    <x-message />

                    <form onsubmit="return confirm('Are you sure?')"
                            action="{{ route('mobile.destroy') }}" method="post">
                        @csrf
                        @method('DELETE')
                        <x-danger-button class="ms-3">
                        {{ __('Delete All Numbers') }}
                    </x-danger-button>
                    </form>
                </header>
            </div>

            <div class="container mx-auto px-4 sm:px-8">
                <div class="py-8">
                    <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                        <div class="inline-block min-w-full shadow-md rounded-lg overflow-hidden">
                            <table class="min-w-full leading-normal px-5 py-5">
                                <thead>
                                    <tr>
                                        <th class="uppercase px-5 py-5 border-b border-gray-200 bg-white text-sm">S.N</th>
                                        <th class="uppercase px-5 py-5 border-b border-gray-200 bg-white text-sm"> Created At</th>
                                        <th class="uppercase px-5 py-5 border-b border-gray-200 bg-white text-sm"> Mobile Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($numbers as $index => $item)
                                        <tr>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ ++ $index }}</td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                {{ $item->created_at->format('M d Y, H:i s') }}
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                {{ $item->mobile_number }}
                                            </td>

                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>

                            {{ $numbers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
