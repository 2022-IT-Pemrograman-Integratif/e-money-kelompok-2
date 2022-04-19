<?php

namespace App\Models;

use CodeIgniter\Model;
use Codeigniter\API\ResponseTrait;
use Exception;

class ModelAccount extends Model
{
    use ResponseTrait;
    protected $table = "account";
    protected $primaryKey = "account_id";
    protected $allowedFields = [
        'account_username',
        'account_password',
        'account_pin',
        'account_money',
        'account_role',
        'nomer_hp'
    ];

    protected $validationRules = [
        'account_username'  => 'required|is_unique[account.account_username]',
        'account_password'  => 'required|is_unique[account.account_password]',
        'account_pin'       => 'required|is_unique[account.account_pin]',
        'nomer_hp'          => 'required|is_unique[account.nomer_hp]'
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
        ],
        'nomer_hp'       => [
            'required'  => 'Silahkan masukkan nomer hp',
            'is_unique' => 'nomer hp tersebut sudah terdaftar'
        ]
    ];

    function verifyLogin($username, $password)
    {
        $builder = $this->table('account');
        $builder->where('account_username', $username);
        $builder->where('account_password', $password);
        $data = $builder->first();
        if (empty($data)) {
            return "username / password salah";
        }
        return $data;
    }

    function getDataWhere($where, $whering)
    {
        $builder = $this->table('account');
        $builder->where($where, $whering);
        $data = $builder->first();
        if (empty($data)) {
            return "data tidak ada";
        }
        return $data;
    }
    function updateData($where, $whering, $set, $seting)
    {
        $builder = $this->table('account');
        $builder->set($set, $seting);
        $builder->where($where, $whering);
        $builder->update();
    }
}
