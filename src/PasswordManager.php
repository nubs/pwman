<?php
namespace Nubs\PwMan;

class PasswordManager
{
    private $_passwords;

    public function __construct(array $passwords)
    {
        $this->_passwords = $passwords;
    }

    public function matchingApplication($application)
    {
        $passwordMatchesApplication = function($password) use($application) {
            return isset($password['application']) && preg_match("/{$application}/i", $password['application']);
        };

        return array_values(array_filter($this->_passwords, $passwordMatchesApplication));
    }
}
