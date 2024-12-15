    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-6 py-2 border-b text-center w-1/12 cursor-pointer" wire:click="sortBy('ip_address')">IP
                    </th>
                    <th class="px-6 py-2 border-b text-center w-2/12 cursor-pointer" wire:click="sortBy('browser')">
                        Browser
                    </th>
                    <th class="px-6 py-2 border-b text-center w-2/12 cursor-pointer" wire:click="sortBy('os')">OS</th>
                    <th class="px-6 py-2 border-b text-center w-2/12 cursor-pointer" wire:click="sortBy('country')">
                        Country
                    </th>
                    <th class="px-6 py-2 border-b text-center w-2/12 cursor-pointer" wire:click="sortBy('city')">City
                    </th>
                    <th class="px-6 py-2 border-b text-center w-2/12 cursor-pointer" wire:click="sortBy('access_date')">
                        Access date</th>
                    <th class="px-6 py-2 border-b text-center w-1/12 cursor-pointer"
                        wire:click="sortBy('is_successful')">
                        Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($secretLogs as $secretLog)
                    <tr class="text-gray-700">
                        <td class="px-6 py-2 border-b text-center">{{ $secretLog->ip_address }}</td>
                        <td class="px-6 py-2 border-b text-center">{{ $secretLog->browser }}</td>
                        <td class="px-6 py-2 border-b text-center">{{ $secretLog->os }}</td>
                        <td class="px-6 py-2 border-b text-center">{{ $secretLog->country }}</td>
                        <td class="px-6 py-2 border-b text-center">{{ $secretLog->city }}</td>
                        <td class="px-6 py-2 border-b text-center">
                            {{ \Carbon\Carbon::parse($secretLog->access_date)->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-2 border-b text-center">
                            {{ $secretLog->is_successful ? 'Success' : 'Failed' }}</td>
                    </tr>
                @empty
                    <tr class="text-gray-400 text-sm">
                        <td colspan="7" class="px-6 py-4 text-center">No logs found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $secretLogs->links() }}
        </div>
    </div>
