<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-slate-100">

        <!-- Page Heading -->

        <header>
            {{-- <div class="flex items-center gap-4">
                <x-application-logo class="w-auto h-12 text-white" />
                <h2 class="font-semibold text-2xl text-white">
                    Share a Secret
                </h2>
            </div> --}}
            @include('layouts.navigation')
            @isset($header)
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            @endisset
        </header>


        <!-- Page Content -->
        <main>
            @if (session('success'))
                <div class="container container--narrow mt-10">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" wire:key="success">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
            @endif
            @if (session('error'))
                <div class="container container--narrow">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>

</html>
