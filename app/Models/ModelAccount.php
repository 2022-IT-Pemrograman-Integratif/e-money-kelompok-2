<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelAccount extends Model
{
    protected $table = "account";
    protected $primaryKey = "account_id";
    protected $allowedFields = [
        'account_username',
        'account_password',
        'account_pin',
        'account_money',
        'account_role'
    ];

    protected $validationRules = [
        'account_username'  => 'required|is_unique[account.account_username]',
        'account_password'  => 'required|is_unique[account.account_password]',
        'account_pin'       => 'required|is_unique[account.account_pin]'
    ];

    protected $validationMessages = [
        'account_username'  => [
            'required'  => 'Silahkan masukkan username',
            'is_unique' => 'username tersebut sudah terdaftar'
        ],
        'account_password'  => [
            'required'  => 'Silahkan masukkan password',
            'is_unique' => 'password tersebut sudah terpakai'
        ],
        'account_pin'       => [
            'required'  => 'Silahkan masukkan PIN',
            'is_unique' => 'PIN tersebut sudah ada'
        ]
    ];

    
}

?>