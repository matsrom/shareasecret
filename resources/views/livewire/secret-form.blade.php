@php
    use App\Enums\SecretType;
@endphp

<form action="" class="flex flex-col lg:flex-row lg:justify-between mt-10" wire:submit.prevent='createSecret'>

    {{-- Column 1 --}}
    <div class="lg:w-1/3 flex flex-col px-10  gap-4 lg:pl-480">
        <h2 class="text-xl font-medium text-gray-900">1- Select Type</h2>
        <div><x-primary-button wire:click.prevent="selectType('text')"
                @class([
                    'bg-gray-700' => $secret_type === SecretType::Text,
                    'w-20 justify-center',
                ])>Text</x-primary-button>
            <x-primary-button wire:click.prevent="selectType('file')"
                @class([
                    'bg-gray-700' => $secret_type === SecretType::File,
                    'w-20 justify-center',
                ])>File</x-primary-button>
        </div>
    </div>

    {{-- Column 2 --}}
    <div class="lg:w-1/3 flex flex-col px-10  gap-4 mt-10 lg:mt-0 max-w-lg">
        <h2 class="text-xl font-medium text-gray-900">2- Enter Secret</h2>
        @if ($this->secret_type == SecretType::Text)
            {{-- Toggle --}}
            <div class="flex flex-row items-center">
                <label class="flex gap-4 items-center cursor-pointer">
                    <span class="text-sm font-medium text-gray-900">Manual</span>
                    <input type="checkbox" wire:click="toggle" value="automatic" class="sr-only peer">
                    <div
                        class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                    </div>
                    <span class="text-sm font-medium text-gray-900">Automatic</span>
                </label>
                <div class="group relative">
                    <svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 -960 960 960" width="24px"
                        fill="#1D4ED8" class="ml-2 cursor-pointer">
                        <path
                            d="M478-240q21 0 35.5-14.5T528-290q0-21-14.5-35.5T478-340q-21 0-35.5 14.5T428-290q0 21 14.5 35.5T478-240Zm-36-154h74q0-33 7.5-52t42.5-52q26-26 41-49.5t15-56.5q0-56-41-86t-97-30q-57 0-92.5 30T342-618l66 26q5-18 22.5-39t53.5-21q32 0 48 17.5t16 38.5q0 20-12 37.5T506-526q-44 39-54 59t-10 73Zm38 314q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
                    </svg>

                    <div class="absolute top-full left-0 mt-2 w-48 bg-gray-700 text-white text-xs rounded py-2 px-4 shadow-lg hidden group-hover:block z-10"
                        id="tooltip">
                        <p><strong>Manual:</strong> you write the text to share.</p>
                        <p><strong>Automatic:</strong> the text is generated automatically with the selected options.
                            Perfect for random
                            secure passwords.</p>
                    </div>
                </div>
            </div>



            @if ($this->text_type == 'manual')
                {{-- Manual text --}}
                <div>
                    <x-input-label for="manual_secret" :value="__('Write the text to share')" />
                    <textarea id="manual_secret" class="border-gray-300 focus:border-blue-700  rounded-lg shadow-sm w-full h-48"
                        wire:model="secret"></textarea>
                    @error('secret')
                        <livewire:show-alert :message="$message" />
                    @enderror
                </div>
            @else
                {{-- Automatic text --}}
                {{-- Length --}}
                <div class="mt-2">
                    <x-input-label for="length" :value="__('Length')" />
                    <x-text-input id="length" class="block mt-1 w-24  h-9" type="text" wire:model="length"
                        :value="old('length')" />
                    @error('length')
                        <livewire:show-alert :message="$message" />
                    @enderror
                </div>
                {{-- Use Capitals --}}
                <div class="flex flex-row gap-2 mt-2">
                    <input id="useCapitals" type="checkbox" wire:model="useCapitals"
                        class="rounded border-gray-400 focus:ring-0 focus:ring-offset-0">
                    <x-input-label for="useCapitals" :value="__('Capitals')" />
                </div>
                {{-- Use Numbers --}}
                <div class="flex flex-row gap-2 mt-2">
                    <input id="useNumbers" type="checkbox" wire:model="useNumbers"
                        class="rounded border-gray-400 focus:ring-0 focus:ring-offset-0">
                    <x-input-label for="useNumbers" :value="__('Numbers')" />
                </div>
                {{-- Use Symbols --}}
                <div class="flex flex-row gap-2 mt-2">
                    <input id="useSymbols" type="checkbox" wire:model="useSymbols"
                        class="rounded border-gray-400 focus:ring-0 focus:ring-offset-0">
                    <x-input-label for="useSymbols" :value="__('Symbols')" />
                </div>
                {{-- Generate --}}
                <x-primary-button class="justify-center mt-2 w-60" wire:click.prevent="createAutomaticText">
                    Generate
                </x-primary-button>

                {{-- Result --}}
                <div>
                    <x-input-label class="mt-2 lg:mt-5" :value="__('Result')" for="automatic_secret" />
                    <div class="flex items-center">
                        <textarea id="automatic_secret" class="border-gray-300 focus:border-blue-700  rounded-lg shadow-sm w-full h-30"
                            wire:model="secret" readonly></textarea>

                        <button class="pl-2 text-blue-700 hover:text-gray-700 flex" type="button"
                            onclick="copyToClipboard()" title="Copiar al portapapeles">

                            <span class="material-symbols-outlined" id="copyIcon">
                                content_copy
                            </span>
                        </button>
                    </div>
                    @error('secret')
                        <livewire:show-alert :message="$message" />
                    @enderror
                </div>
            @endif
        @else
            <input type="file" wire:model="secret" id="file_secret"
                class="block w-full text-sm file:!bg-blue-700 border-gray-300 rounded-lg cursor-pointer bg-gray-50  focus:outline-none ">

            @error('secret')
                <livewire:show-alert :message="$message" />
            @enderror
        @endif
    </div>

    {{-- Column 3 --}}
    <div class="lg:w-1/3 flex flex-col px-10  gap-4 my-10 lg:my-0 max-w-lg">
        <h2 class="text-xl font-medium text-gray-900">3- Sharing Settings</h2>
        <div>
            <div class="flex flex-col">
                {{-- Days until expiration --}}
                <div class="flex flex-row items-center relative group">
                    <x-input-label for="daysLeft" :value="__('Days until expiration')" />


                    <svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 -960 960 960" width="24px"
                        fill="#1D4ED8" class="ml-2 cursor-pointer">
                        <path
                            d="M478-240q21 0 35.5-14.5T528-290q0-21-14.5-35.5T478-340q-21 0-35.5 14.5T428-290q0 21 14.5 35.5T478-240Zm-36-154h74q0-33 7.5-52t42.5-52q26-26 41-49.5t15-56.5q0-56-41-86t-97-30q-57 0-92.5 30T342-618l66 26q5-18 22.5-39t53.5-21q32 0 48 17.5t16 38.5q0 20-12 37.5T506-526q-44 39-54 59t-10 73Zm38 314q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
                    </svg>

                    <div
                        class="absolute top-0 left-10 mt-6 w-48 bg-gray-700 text-white text-xs rounded py-2 px-4 shadow-lg hidden group-hover:block z-10">
                        Number of days the secret can be viewed before it expires.
                    </div>
                </div>

                <x-text-input id="daysLeft" class="block w-24 h-9 mt-1" type="number" wire:model="daysLeft"
                    :value="old('daysLeft')" />
                @error('daysLeft')
                    <livewire:show-alert :message="$message" />
                @enderror
            </div>
        </div>
        <div>

            <div class="flex flex-col">
                {{-- Clicks until expiration --}}
                <div class="flex flex-row items-center relative group">
                    <x-input-label for="daysLeft" :value="__('Clicks until expiration')" />

                    <svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 -960 960 960" width="24px"
                        fill="#1D4ED8" class="ml-2 cursor-pointer">
                        <path
                            d="M478-240q21 0 35.5-14.5T528-290q0-21-14.5-35.5T478-340q-21 0-35.5 14.5T428-290q0 21 14.5 35.5T478-240Zm-36-154h74q0-33 7.5-52t42.5-52q26-26 41-49.5t15-56.5q0-56-41-86t-97-30q-57 0-92.5 30T342-618l66 26q5-18 22.5-39t53.5-21q32 0 48 17.5t16 38.5q0 20-12 37.5T506-526q-44 39-54 59t-10 73Zm38 314q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
                    </svg>


                    <div
                        class="absolute top-0 left-10 mt-6 w-48 bg-gray-700 text-white text-xs rounded py-2 px-4 shadow-lg hidden group-hover:block z-10">
                        Number of times the secret can be viewed before it expires.
                    </div>
                </div>


                <x-text-input id="clicksLeft" class="block w-24 h-9 mt-1" type="number" wire:model="clicksLeft"
                    :value="old('clicksLeft')" />
                @error('clicksLeft')
                    <livewire:show-alert :message="$message" />
                @enderror
            </div>
        </div>
        {{-- Allow manual delete --}}
        <div class="flex flex-row gap-2 mt-2">
            <input id="allowManualDelete" type="checkbox" wire:model="allowManualDelete"
                class="rounded border-gray-400 focus:ring-0 focus:ring-offset-0">
            <div class="flex flex-row  relative group">
                <x-input-label for="allowManualDelete" :value="__('Allow manual delete')" />
                <svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 -960 960 960" width="24px"
                    fill="#1D4ED8" class="ml-2 cursor-pointer">
                    <path
                        d="M478-240q21 0 35.5-14.5T528-290q0-21-14.5-35.5T478-340q-21 0-35.5 14.5T428-290q0 21 14.5 35.5T478-240Zm-36-154h74q0-33 7.5-52t42.5-52q26-26 41-49.5t15-56.5q0-56-41-86t-97-30q-57 0-92.5 30T342-618l66 26q5-18 22.5-39t53.5-21q32 0 48 17.5t16 38.5q0 20-12 37.5T506-526q-44 39-54 59t-10 73Zm38 314q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
                </svg>

                <div
                    class="absolute top-0 left-10 mt-6 w-48 bg-gray-700 text-white text-xs rounded py-2 px-4 shadow-lg hidden group-hover:block z-10">
                    When the secret is displayed, there will be a button that will allow to delete it instantly.
                </div>
            </div>
        </div>
        {{-- Protect with password --}}
        <div class="mt-2">
            <div class="flex flex-row relative group">
                <x-input-label for="password" :value="__('Protect with password')" />
                <svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 -960 960 960" width="24px"
                    fill="#1D4ED8" class="ml-2 cursor-pointer">
                    <path
                        d="M478-240q21 0 35.5-14.5T528-290q0-21-14.5-35.5T478-340q-21 0-35.5 14.5T428-290q0 21 14.5 35.5T478-240Zm-36-154h74q0-33 7.5-52t42.5-52q26-26 41-49.5t15-56.5q0-56-41-86t-97-30q-57 0-92.5 30T342-618l66 26q5-18 22.5-39t53.5-21q32 0 48 17.5t16 38.5q0 20-12 37.5T506-526q-44 39-54 59t-10 73Zm38 314q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
                </svg>
                <div
                    class="absolute top-0 left-10 mt-6 w-48 bg-gray-700 text-white text-xs rounded py-2 px-4 shadow-lg hidden group-hover:block  z-10">
                    Before the secret is displayed, a password will be requested.
                </div>
            </div>
            <x-text-input id="password" class="block mt-1 w-full lg:w-60  h-9" type="text" wire:model="password"
                :value="old('password')" />
            @error('password')
                <livewire:show-alert :message="$message" />
            @enderror
        </div>
        {{-- Keep track of this secret --}}
        <div class="flex flex-col gap-4">
            <div class="flex flex-row gap-2 mt-2">
                <input id="keepTrack" type="checkbox" wire:click="toggleKeepTrack"
                    class="rounded border-gray-400 focus:ring-0 focus:ring-offset-0 {{ auth()->check() ? '' : 'bg-gray-100 ' }}"
                    {{ auth()->check() ? '' : 'disabled ' }}>

                <div class="flex flex-row relative group">
                    <x-input-label for="keepTrack" :value="__('Keep track of this secret')" />
                    <svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 -960 960 960" width="24px"
                        fill="#1D4ED8" class="ml-2 cursor-pointer">
                        <path
                            d="M478-240q21 0 35.5-14.5T528-290q0-21-14.5-35.5T478-340q-21 0-35.5 14.5T428-290q0 21 14.5 35.5T478-240Zm-36-154h74q0-33 7.5-52t42.5-52q26-26 41-49.5t15-56.5q0-56-41-86t-97-30q-57 0-92.5 30T342-618l66 26q5-18 22.5-39t53.5-21q32 0 48 17.5t16 38.5q0 20-12 37.5T506-526q-44 39-54 59t-10 73Zm38 314q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
                    </svg>
                    <div
                        class="absolute top-0 left-10 mt-6 w-48 bg-gray-700 text-white text-xs rounded py-2 px-4 shadow-lg hidden group-hover:block  z-10">
                        Registered users will be able to view and manage tracked secrets on the profile dashboard.
                    </div>
                </div>
            </div>
            {{-- Alias --}}
            <div class="flex flex-col">
                <div class="flex flex-row items-center relative group">
                    <x-input-label for="daysLeft" :value="__('Secret alias')" />

                    <svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 -960 960 960" width="24px"
                        fill="#1D4ED8" class="ml-2 cursor-pointer">
                        <path
                            d="M478-240q21 0 35.5-14.5T528-290q0-21-14.5-35.5T478-340q-21 0-35.5 14.5T428-290q0 21 14.5 35.5T478-240Zm-36-154h74q0-33 7.5-52t42.5-52q26-26 41-49.5t15-56.5q0-56-41-86t-97-30q-57 0-92.5 30T342-618l66 26q5-18 22.5-39t53.5-21q32 0 48 17.5t16 38.5q0 20-12 37.5T506-526q-44 39-54 59t-10 73Zm38 314q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z" />
                    </svg>


                    <div
                        class="absolute top-0 left-10 mt-6 w-48 bg-gray-700 text-white text-xs rounded py-2 px-4 shadow-lg hidden group-hover:block z-10">
                        A name for the secret that will be displayed in the dashboard.
                    </div>
                </div>
                <x-text-input id="alias" class="block w-full lg:w-60 h-9 mt-2" type="text" wire:model="alias"
                    :value="old('alias')" disabled="{{ !$keepTrack }}" />

            </div>
            @error('alias')
                <livewire:show-alert :message="$message" />
            @enderror
        </div>
        <x-primary-button class="justify-center mt-2 w-full	lg:w-60">
            Share Secret
        </x-primary-button>
    </div>
    @script
        <script>
            $wire.on('encryptSecret', async (event) => {
                // Get secretdata
                const data = event.data;
                const messageKey = Math.random().toString(36).substring(2, 18);
                console.log("messageKey", messageKey);
                const encodedMessageKey = btoa(messageKey);
                console.log("encodedMessageKey", encodedMessageKey);

                // Get keys for encryption
                if (data.keep_track == 1) {
                    const encryptedMasterKey = event.masterKey;
                    console.log("encryptedMasterKey", encryptedMasterKey);
                    const derivedKey = localStorage.getItem('derivedKey');
                    console.log("derivedKey", derivedKey);
                    const masterKey = await aesDecrypt(encryptedMasterKey, derivedKey);
                    console.log("masterKey", masterKey);
                    const encryptedMessageKey = await aesEncrypt(encodedMessageKey, masterKey);
                    console.log("encryptedEncodedMessageKey", encryptedMessageKey);
                    data.message_key = encryptedMessageKey;
                } else {
                    data.message_key = encodedMessageKey;
                }

                if (data.secret_type === 'text' && data.text_type === 'manual') {
                    const text = document.getElementById("manual_secret").value;
                    data.message = await aesEncrypt(text, encodedMessageKey);
                } else if (data.secret_type === 'text' && data.text_type === 'automatic') {
                    const text = document.getElementById("automatic_secret").value;
                    data.message = await aesEncrypt(text, encodedMessageKey);
                } else if (data.secret_type === 'file') {
                    const file = document.getElementById("file_secret").files[0];
                    data.message = await aesEncryptFile(file, encodedMessageKey);
                    const file_name = file.name;
                    const original_filename = await aesEncrypt(file_name, encodedMessageKey);

                    data.original_filename = original_filename;
                }

                sessionStorage.setItem('urlKey', encodedMessageKey);


                data.message_iv = "";
                data.message_tag = "";

                $wire.dispatch('storeSecret', [data]);
            });


            window.copyToClipboard = function() {

                const secretInput = document.getElementById("automatic_secret");
                const copyIcon = document.getElementById("copyIcon");

                // Seleccionar y copiar el texto al portapapeles
                secretInput.select();
                secretInput.setSelectionRange(0, 99999); // Para dispositivos móviles

                navigator.clipboard.writeText(secretInput.value);
                copyIcon.classList.add('text-green-600');


                // Eliminar clase después de 1 segundo
                setTimeout(() => {
                    copyIcon.classList.remove('text-green-600');
                }, 1000);



                Livewire.dispatch('show-toast', [{
                    message: "Text copied to clipboard", // Mensaje que se mostrará en el toast
                    class: "toast-success" // Clase CSS asociada al toast
                }]);
            }
        </script>
    @endscript

</form>
