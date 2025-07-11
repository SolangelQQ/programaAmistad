<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    // Verificación de atributos básicos del modelo User
    public function test_user_model_has_required_attributes()
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password') 
        ]);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertNotNull($user->password);
    }

    // Validación de formato de email
    public function test_email_validation()
    {
        $validEmails = [
            'user@example.com',
            'firstname.lastname@example.com',
            'user+tag@example.com'
        ];

        $invalidEmails = [
            'plainaddress',
            '@missingusername.com',
            'username@.com'
        ];

        foreach ($validEmails as $email) {
            $this->assertTrue(filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
        }

        foreach ($invalidEmails as $email) {
            $this->assertFalse(filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
        }
    }

    // Validación de fortaleza de contraseña
    public function test_password_strength_validation()
    {
        $validatePassword = function($password) {
            $length = strlen($password) >= 8;
            $uppercase = preg_match('/[A-Z]/', $password);
            $lowercase = preg_match('/[a-z]/', $password);
            $number = preg_match('/[0-9]/', $password);
            $special = preg_match('/[^A-Za-z0-9]/', $password);
            
            return $length && $uppercase && $lowercase && $number && $special;
        };
        
        $this->assertFalse($validatePassword('weak'));
        $this->assertFalse($validatePassword('Weak1'));
        $this->assertFalse($validatePassword('NoSpecialChars123'));
        $this->assertTrue($validatePassword('StrongPass123!'));
    }

    // Verificación de remember token
    public function test_remember_token_functionality()
    {
        $user = new User();
        $token = 'sample-token-123';
        
        $user->setRememberToken($token);
        $this->assertEquals($token, $user->getRememberToken());
    }

    // Verificación de campos requeridos
    public function test_login_form_validation_rules()
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ];

        $validator = validator([], $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }
}