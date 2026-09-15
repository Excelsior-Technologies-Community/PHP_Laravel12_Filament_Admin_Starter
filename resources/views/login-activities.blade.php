<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Login Activities') }}
            </h2>

            <a
                href="{{ route('dashboard') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
            >
                Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6">

                    <form
                        method="GET"
                        action="{{ route('login.activities') }}"
                        class="mb-6"
                    >

                        <div class="flex gap-3">

                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Search name, email or IP..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >

                            <button
                                type="submit"
                                class="px-5 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                            >
                                Search
                            </button>

                            @if(request('search'))
                                <a
                                    href="{{ route('login.activities') }}"
                                    class="px-5 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                                >
                                    Clear
                                </a>
                            @endif

                        </div>

                    </form>

                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">

                                <tr>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        User
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Email
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        IP Address
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Browser
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Login Time
                                    </th>

                                </tr>

                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">

                                @forelse($activities as $activity)

                                    <tr>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $activity->user?->name ?? 'Deleted User' }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $activity->user?->email ?? '-' }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $activity->ip_address ?? '-' }}
                                        </td>

                                        <td class="px-6 py-4 max-w-md truncate">
                                            {{ $activity->user_agent ?? '-' }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $activity->login_at?->format('d M Y, h:i A') ?? '-' }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td
                                            colspan="5"
                                            class="px-6 py-8 text-center text-gray-500"
                                        >
                                            No login activities found.
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    <div class="mt-6">
                        {{ $activities->links() }}
                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>