<?php

namespace App\Services;

class PasswordValidator
{
    public function isStrong(string $password): bool
    {
        return strlen($password) >= 8 
               && preg_match('/[A-Z]/', $password) 
               && preg_match('/[a-z]/', $password) 
               && preg_match('/[0-9]/', $password) 
               && preg_match('/[^A-Za-z0-9]/', $password);
    }
}