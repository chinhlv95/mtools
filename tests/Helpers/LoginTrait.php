<?php

namespace Tests\Helpers;

trait LoginTrait
{
    protected function login($credential)
    {
        return $this->visit('login')
            ->type(array_get($credential, 'email'), 'email')
            ->type(array_get($credential, 'password'), 'password')
            ->press('loginButton');
    }
}
