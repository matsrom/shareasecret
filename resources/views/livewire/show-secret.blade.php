<div>
    @if ($this->passwordProtected)
        <div class="flex flex-col items-center justify-center mt-48 px-4">
            <h2 class="text-2xl font-bold mb-2">Enter the password to view the secret:</h2>
            <form wire:submit.prevent="showSecret" class="flex flex-col items-center justify-center gap-2 w-full">
                <x-text-input wire:model="password" type="password" placeholder="Enter password" class="w-2/3 md:w-64" />
                <x-primary-button type="submit" class="w-2/3 md:w-64 justify-center">Show secret</x-primary-button>
            </form>
            @if ($this->passwordError)
                <p class="text-red-500">Incorrect password</p>
            @endif
        </div>
    @else
        @if ($this->secret->secret_type === 'text')
            <div class="flex flex-col items-center justify-center mt-32 md:mt-48 px-4">
                <p class="text-2xl font-bold mb-2">Shared secret:</p>
                <x-text-input id="secret-text" value="{{ $this->decryptedMessage }}"
                    class="w-full md:w-1/2 h-36 text-center text-xl bg-gray-200" readonly />
            </div>
        @elseif ($this->secret->secret_type === 'file')
            <div class="flex flex-col items-center justify-center mt-48 p-10">
                <h2 class="text-2xl font-bold mb-2">Click the button below to download the file:</h2>
                <x-primary-button wire:click="decryptFile()">
                    Download file
                </x-primary-button>
            </div>
        @endif
        @if ($this->manualDeletion)
            <div class="flex flex-col items-center justify-center mt-8">
                <x-primary-button class="bg-red-600 hover:bg-red-800" wire:click="openDeleteModal">Delete
                    secret</x-primary-button>
                <p class="text-sm text-gray-500 mt-2">This secret won't be available anymore</p>
            </div>

            @if ($this->modalVisible)
                <div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true"
                    wire:click="closeModal">
                    <!--
                  Background backdrop, show/hide based on modal state.
              
                  Entering: "ease-out duration-300"
                    From: "opacity-0"
                    To: "opacity-100"
                  Leaving: "ease-in duration-200"
                    From: "opacity-100"
                    To: "opacity-0"
                -->
                    <div class="fixed inset-0 bg-gray-900/50 transition-opacity" aria-hidden="true">
                    </div>

                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                            <!--
                      Modal panel, show/hide based on modal state.
              
                      Entering: "ease-out duration-300"
                        From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        To: "opacity-100 translate-y-0 sm:scale-100"
                      Leaving: "ease-in duration-200"
                        From: "opacity-100 translate-y-0 sm:scale-100"
                        To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    -->
                            <div class="relative bg-white rounded-lg shadow dark:bg-neutral4">
                                <button type="button"
                                    class="absolute top-3 end-2.5 text-red bg-transparent hover:bg-gray-200 hover:text-neutral4 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-neutral4 dark:hover:text-white">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                                <div class="p-4 md:p-5 text-center">
                                    <svg class="mx-auto mb-4 text-neutral4 w-12 h-12 dark:text-gray-200"
                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <h3 class="mb-5 text-lg font-normal text-neutral4 dark:text-neutral4">Are you sure
                                        you want to
                                        delete
                                        this secret?</h3>
                                    <button wire:click="deleteSecret" type="button"
                                        class="text-white bg-red-600 hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                        Delete
                                    </button>
                                    <button type="button"
                                        class="py-2.5 px-5 ms-3 text-sm font-medium text-neutral4 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-neutral4 hover:text-white hover:bg-gray-700 focus:z-10 focus:ring-4 focus:ring-gray-100">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif



    @endif




    @script
        <script>
            Livewire.on('decryptSecret', async function(event) {
                // Obtener datos del secreto
                const secret = event.data;
                const messageKey = new URL(window.location.href).searchParams.get('key');

                // Obtener claves para la desencriptación
                const encryptedMasterKey = event.master_key;
                const derivedKey = localStorage.getItem('derivedKey');
                const masterKey = await aesDecrypt(encryptedMasterKey, derivedKey);

                // Desencriptar el mensaje


                if (secret.secret_type === 'text') {
                    const decryptedSecret = await aesDecrypt(secret.message, masterKey);
                    console.log("decryptedSecret", decryptedSecret);
                    document.getElementById('secret-text').value = decryptedSecret;
                } else if (secret.secret_type === 'file') {
                    const originalFilename = await aesDecrypt(secret.original_filename, masterKey);
                    const blob = await aesDecryptFile(secret.message, masterKey, originalFilename);
                }
            });


            Livewire.on('getLogLocationAndCreateLog', async function(success) {
                const response = await fetch('/proxy/ip-location?ip=81.47.137.137');
                const data = await response.json();

                const country = data.country_name;
                const city = data.city_name;
                const latitude = data.latitude;
                const longitude = data.longitude;

                // console.log(country);
                // console.log(city);
                // console.log(success[0]);

                Livewire.dispatch('createSecretLog', {
                    success: success[0],
                    country: country,
                    city: city,
                    latitude: latitude,
                    longitude: longitude
                });
            });
        </script>
    @endscript
</div>
