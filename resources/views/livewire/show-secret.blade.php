<div>
    @if ($this->secret->secret_type === 'text')
        <div class="flex flex-col items-center justify-center mt-48">
            <p class="text-2xl font-bold mb-2">Shared secret:</p>
            <x-text-input value="{{ $this->decryptedMessage }}" class="w-1/2 h-36   text-center text-xl" />
        </div>
    @elseif ($this->secret->secret_type === 'file')
        <div class="flex flex-col items-center justify-center mt-48 p-10">
            <h2 class="text-2xl font-bold mb-2">Click the button below to download the file:</h2>
            <x-primary-button wire:click="decryptFile('{{ $messageKey }}')">
                Download file
            </x-primary-button>
        </div>
    @endif
</div>
