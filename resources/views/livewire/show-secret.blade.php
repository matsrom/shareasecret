<div>
    @if ($this->secret->secret_type === 'text')
        <p>Secreto:</p>
        <p>{{ $this->decryptedMessage }}</p>
    @elseif ($this->secret->secret_type === 'file')
        <p>Archivo:</p>
        <button wire:click="decryptFile('{{ $messageKey }}')"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            Desencriptar archivo
        </button>
    @endif
</div>
