@php
use Illuminate\Support\Facades\Auth;
@endphp

<div class="min-h-screen bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="/">
                        <span class="font-bold text-lg text-blue-700">ERP</span>
                    </a>
                </div>
                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <a href="/chart-of-accounts" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Chart of Accounts</a>
                    <!-- Add more links as needed -->
                </div>
                <!-- User Dropdown -->
                <div class="ml-3 relative">
                    @auth
                        <div class="flex items-center space-x-2">
                            <span class="text-gray-700 text-sm">{{ Auth::user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Logout</button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 text-sm">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>
</div> 