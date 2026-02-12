<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class UserManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $user = null;
    public $form = [
        'name' => '',
        'email' => '',
        'password' => ''
    ];

    protected $rules = [
        'form.name' => 'required|string|max:255',
        'form.email' => 'required|string|max:255',
        'form.password' => 'required|string|min:8|max:255'
    ];

    public function render()
    {
        $query = User::orderBy('id');

        return view('livewire.user-manager', [
            'items' => $query->paginate(50)
        ]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(User $user)
    {
        $this->user = $user;
        $this->form = $user->only('name', 'email', 'password');

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        $formData = $this->form;

        if ($this->user) {
            $this->user->update($formData);
            $this->dispatch('notify', message: 'User aktualisiert');
        } else {
            $user = User::create([
            'name' => $formData['name'],
            'email' => $formData['email'],
            'password' => Hash::make($formData['password']),
            'role' => 'admin',
            'is_active' => true,
        ]);
            $this->dispatch('notify', message: 'User erstellt');
        }
        
        $this->closeModal();
        $this->resetPage();
    }

    public function delete(User $user)
    {
        $user->delete();
        $this->dispatch('notify', message: 'User gelöscht');
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->form = [
        'name' => '',
        'email' => '',
        'password' => ''
        ];
        $this->user = null;
    }
}
