<?php

namespace App\Http\Controllers;

use App\Repositories\User\UserRepositoryInterface;

class ProfilesController extends Controller

{
    public function __construct(UserRepositoryInterface $userRepository){
        $this->userRepository = $userRepository;
    }

    //function index profiles
    public function index() {
        return view('profiles.index');
    }
}
