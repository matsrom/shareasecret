<x-app-layout>
    <div class="container">
        <h1>Dashboard</h1>
    </div>
    @foreach ($secrets as $secret)
        <div class="secret">
            <h2>{{ $secret->url_identifier }}</h2>
        </div>
    @endforeach
</x-app-layout>
