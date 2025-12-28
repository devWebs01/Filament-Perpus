<?php

namespace App\Livewire\Profile;

use App\Models\UserDetail;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class UpdateProfile extends Component
{
    use \Livewire\WithFileUploads;

    public $user;

    public $userDetail;

    public $name;

    public $email;

    public $nik;

    public $nis;

    public $nisn;

    public $class;

    public $address;

    public $phone_number;

    public $birth_date;

    public $birth_place;

    public $gender;

    public $religion;

    public $profile_photo;

    public function mount($user, $userDetail): void
    {
        $this->user = $user;
        $this->userDetail = $userDetail;

        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->nik = $userDetail?->nik ?? '';
        $this->nis = $userDetail?->nis ?? '';
        $this->nisn = $userDetail?->nisn ?? '';
        $this->class = $userDetail?->class ?? '';
        $this->address = $userDetail?->address ?? '';
        $this->phone_number = $userDetail?->phone_number ?? '';
        $this->birth_date = $userDetail?->birth_date?->format('Y-m-d') ?? '';
        $this->birth_place = $userDetail?->birth_place ?? '';
        $this->gender = $userDetail?->gender ?? '';
        $this->religion = $userDetail?->religion ?? '';
    }

    public function updateProfile(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$this->user->id,
            'nik' => 'nullable|string|max:20',
            'nis' => 'nullable|string|max:20',
            'nisn' => 'nullable|string|max:20',
            'class' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'phone_number' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'birth_place' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female',
            'religion' => 'nullable|string|in:islam,christian,catholic,hindu,buddhist,confucianism,other|max:50',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $this->validate($rules);

        // Update user
        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        // Handle profile photo upload
        $profilePhotoPath = null;
        if ($this->profile_photo) {
            $profilePhotoPath = $this->profile_photo->store('profile-photos', 'public');
        }

        // Update or create user detail
        $userDetailData = [
            'nik' => $this->nik ?: null,
            'nis' => $this->nis ?: null,
            'nisn' => $this->nisn ?: null,
            'class' => $this->class ?: null,
            'address' => $this->address ?: null,
            'phone_number' => $this->phone_number ?: null,
            'birth_date' => $this->birth_date ?: null,
            'birth_place' => $this->birth_place ?: null,
            'gender' => $this->gender ?: null,
            'religion' => $this->religion ?: null,
            'profile_photo' => $profilePhotoPath ?? ($this->userDetail?->profile_photo ?? null),
        ];

        if ($this->userDetail) {
            $this->userDetail->update($userDetailData);
        } else {
            $userDetailData['user_id'] = $this->user->id;
            UserDetail::create($userDetailData);
        }

        LivewireAlert::title('Berhasil')->text('Update Data pengguna berhasil!')->success()->show();

        $this->redirectRoute('profile');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.profile.update-profile');
    }
}
