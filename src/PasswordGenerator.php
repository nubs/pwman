<?php
namespace Nubs\PwMan;

/**
 * Generate random passwords.
 */
class PasswordGenerator
{
    /**
     * Generate a password.
     *
     * @api
     * @return string The random password.
     */
    public function __invoke()
    {
        return 'password';
    }
}
