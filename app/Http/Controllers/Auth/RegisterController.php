<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array<string, string> $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data,
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'join_code' => ['string', 'nullable', 'exists:user_groups,join_code'],
            ],
            [
                'name.required' => 'Please enter a name.',
                'name.max' => 'Your name is too long, please shorten it.',
                'email.required' => 'Please enter an email address.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'Your email address is too long, please shorten it.',
                'email.unique' => 'This email address is already in use, please try again.',
                'password.required' => 'Please enter a password.',
                'password.confirmed' => 'Your passwords do not match, please try again.',
                'password.min' => 'Your password is too short, please enter at least 8 characters.',
                'join_code.exists' => 'This family code is invalid, please check it and try again.',
            ]
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array<string, string> $data
     * @return \App\Models\User
     */
    protected function create(array $data): User
    {
        if (isset($data['join_code'])) {
            $userGroup = UserGroup::where('join_code', $data['join_code'])->first();
        }
        /** @var User $user */
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_group_id' => $userGroup?->getKey() ?? null
        ]);

        return $user;
    }
}
