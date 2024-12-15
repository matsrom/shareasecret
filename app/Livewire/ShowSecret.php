<?php

namespace App\Livewire;

use App\Models\Secret;
use Livewire\Component;
use App\Models\SecretLog;
use Jenssegers\Agent\Facades\Agent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

class ShowSecret extends Component
{

    public $secret;
    public $decryptedMessage;
    public $messageKey;

    public $passwordProtected;
    public $manualDeletion;
    public $password;
    public $passwordError = false;

    public $modalVisible = false;


    protected $listeners = ['decryptText', 'decryptFile', 'createSecretLog'];

    public function mount($secret)
    {

        $currentDate = now();
        $expirationDate = $secret->created_at->addDays($secret->days_remaining);

        // If the secret has expired, get the location of the user and create a log
        if (($secret->clicks_expiration && $secret->clicks_remaining <= 0) || ($secret->days_expiration && $currentDate->greaterThan($expirationDate))) {
            $this->dispatch('getLogLocationAndCreateLog', false);

            $ip = request()->ip();
            $response = Http::withOptions(['verify' => false])->get('https://api.ip2location.io/', [
                'key' => '78738552C7CE2DF260F21C9EE99E9099',
                'ip' => $ip
            ]);

            $country = $response->json()['country_name'];
            $city = $response->json()['city_name'];
            $latitude = $response->json()['latitude'];
            $longitude = $response->json()['longitude'];

            $this->createSecretLog(false, $country, $city, $latitude, $longitude);

            return redirect(route('secrets.create'))->with('status', [
                'message' => 'The secret has expired',
                'class' => 'toast-danger',
            ]);
        }

        // If the secret is not expired, decrypt the secret
    
        $this->passwordProtected = $secret->is_password_protected; 
        $this->manualDeletion = $secret->allow_manual_deletion;

        if(!$this->passwordProtected && $secret->secret_type === 'text'){
            $this->dispatch('decryptSecret', data: $secret, master_key: auth()->user()->master_key);
            $this->updateSecretClicks($secret);
            $this->dispatch('getLogLocationAndCreateLog', true);
        }else if(!$secret->secret_type === 'text' && !$secret->secret_type === 'file'){
            abort(403, 'Unauthorized');
        }
    }

    public function decryptFile()
    {
        $this->dispatch('decryptSecret', data: $this->secret, message_key: $this->messageKey, master_key: auth()->user()->master_key);
    }

    public function showSecret()
    {
        if (Hash::check($this->password, $this->secret->password_hash)) {
            $this->passwordProtected = false;
            if($this->secret->secret_type === 'text'){
                $this->dispatch('decryptSecret', data: $this->secret, master_key: auth()->user()->master_key);
            } 
            
            if($this->secret->clicks_expiration){
                $this->updateSecretClicks($this->secret);
            }
            $this->dispatch('getLogLocationAndCreateLog', true);
            $this->render();
            
        }
        else{
            $this->dispatch('getLogLocationAndCreateLog', false);
            $this->passwordError = "Incorrect password";
        }
        
    }

    private function updateSecretClicks(Secret $secret): void
    {
        if($secret->clicks_expiration){
            $secret->clicks_remaining--;
            $secret->save();
        }
       
    }

    public function openDeleteModal()
    {
        $this->modalVisible = true;
        $this->render();
    }

    public function closeModal()
    {
        $this->modalVisible = false;
        $this->render();
    }

    public function deleteSecret()
    {
        $this->secret->clicks_remaining = 0;
        $this->secret->days_remaining = 0;
        $this->secret->save();

        $this->redirect(route('secrets.create'));
    }

    public function createSecretLog($success, $country, $city, $latitude, $longitude){;
        $secretLog = new SecretLog();
        $secretLog->secret_id = $this->secret->id;
        $secretLog->ip_address = request()->ip();
        $secretLog->browser = Agent::browser() . ' ' . Agent::version(Agent::browser());
        $secretLog->os = Agent::platform() . ' ' . Agent::version(Agent::platform());
        $secretLog->device = Agent::device();
        $secretLog->country = $country;
        $secretLog->city = $city;
        $secretLog->access_date = now();
        $secretLog->is_successful = $success;
        $secretLog->latitude = $latitude;
        $secretLog->longitude = $longitude;
        $secretLog->save();
    }

    public function render()
    {
        return view('livewire.show-secret');
    }
}