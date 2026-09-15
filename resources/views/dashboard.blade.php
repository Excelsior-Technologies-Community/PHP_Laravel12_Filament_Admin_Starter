<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Dashboard') }}
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Welcome back, {{ auth()->user()->name }}
                </p>
            </div>

            <a
                href="{{ route('login.activities') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
            >
                Login Activities
            </a>

        </div>

    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <p class="text-sm text-gray-500">
                            Account Status
                        </p>

                        <p class="mt-2 text-2xl font-bold
                            {{ auth()->user()->is_active ? 'text-green-600' : 'text-red-600' }}">

                            {{ auth()->user()->is_active ? 'Active' : 'Inactive' }}

                        </p>

                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <p class="text-sm text-gray-500">
                            Last Login
                        </p>

                        <p class="mt-2 text-lg font-bold text-gray-800">

                            @if(auth()->user()->last_login_at)
                                {{ auth()->user()->last_login_at->format('d M Y, h:i A') }}
                            @else
                                Never
                            @endif

                        </p>

                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <p class="text-sm text-gray-500">
                            Last Login IP
                        </p>

                        <p class="mt-2 text-lg font-bold text-gray-800">

                            {{ auth()->user()->last_login_ip ?? 'Not available' }}

                        </p>

                    </div>
                </div>

            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900">

                    <h3 class="text-lg font-semibold">
                        Account Overview
                    </h3>

                    <p class="mt-2 text-gray-600">
                        You are successfully logged in to the Laravel application.
                    </p>

                    @if(auth()->user()->hasAnyRole(['super_admin', 'admin']))

                        <div class="mt-6">

                            <a
                                href="/admin"
                                class="inline-flex items-center px-5 py-2.5 bg-gray-900 text-white rounded-md hover:bg-gray-700"
                            >
                                Open Admin Panel
                            </a>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</x-app-layout>