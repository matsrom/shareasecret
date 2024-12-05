<x-app-layout>
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold mb-6 text-center mt-10">Dashboard</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-6 py-3 border-b text-center">Type</th>
                        <th class="px-6 py-3 border-b text-center">Alias</th>
                        <th class="px-6 py-3 border-b text-center">URL</th>
                        <th class="px-6 py-3 border-b text-center">Days left</th>
                        <th class="px-6 py-3 border-b text-center">Clicks left</th>
                        <th class="px-6 py-3 border-b text-center">Creation date</th>
                        <th class="px-6 py-3 border-b text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($secrets as $secret)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 border-b capitalize text-center">{{ $secret->secret_type }}</td>
                            <td class="px-6 py-4 border-b text-center">
                                <span title="{{ $secret->alias }}">
                                    {{ Str::limit($secret->alias, 15, '...') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 border-b text-center">
                                <button onclick="copyToClipboard('{{ $secret->url_identifier }}')"
                                    class="text-blue-600 hover:text-blue-800">
                                    Copiar URL
                                </button>
                            </td>
                            <td class="px-6 py-4 border-b text-center">{{ $secret->days_remaining }}</td>
                            <td class="px-6 py-4 border-b text-center">{{ $secret->clicks_remaining }}</td>
                            <td class="px-6 py-4 border-b text-center">{{ $secret->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 border-b text-center">
                                <button class="text-red-600 hover:text-red-800">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>
