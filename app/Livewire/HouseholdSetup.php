<?php

namespace App\Livewire;

use App\Models\Household;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class HouseholdSetup extends Component
{
    public $mode = 'choose'; // choose, create, join
    public $householdName = '';
    public $inviteCode = '';

    protected $rules = [
        'householdName' => 'required_if:mode,create|string|max:255',
        'inviteCode' => 'required_if:mode,join|string|size:8',
    ];

    public function createHousehold()
    {
        $this->mode = 'create';
    }

    public function joinHousehold()
    {
        $this->mode = 'join';
    }

    public function back()
    {
        $this->mode = 'choose';
        $this->reset(['householdName', 'inviteCode']);
    }

    public function submitCreate()
    {
        $this->validate([
            'householdName' => 'required|string|max:255',
        ]);

        $household = Household::create([
            'name' => $this->householdName,
        ]);

        Auth::user()->update(['household_id' => $household->id]);

        session()->flash('message', 'Household created! Share the invite code with your partner: ' . $household->invite_code);
        
        return redirect()->route('dashboard');
    }

    public function submitJoin()
    {
        $this->validate([
            'inviteCode' => 'required|string|size:8',
        ]);

        $household = Household::where('invite_code', $this->inviteCode)->first();

        if (!$household) {
            $this->addError('inviteCode', 'Invalid invite code.');
            return;
        }

        if ($household->users()->count() >= 2) {
            $this->addError('inviteCode', 'This household already has two members.');
            return;
        }

        Auth::user()->update(['household_id' => $household->id]);

        session()->flash('message', 'You joined the household: ' . $household->name);
        
        return redirect()->route('dashboard');
    }

    public function render()
    {
        $user = Auth::user();
        
        return view('livewire.household-setup', [
            'household' => $user->household,
            'partner' => $user->partner,
        ]);
    }
}
