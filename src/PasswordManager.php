<?php
namespace Nubs\PwMan;

/**
 * Manage the collection of passwords.
 */
class PasswordManager
{
    /** @type array<array> The passwords. */
    private $_passwords;

    /**
     * Initialize the password manager.
     *
     * @param array<array> The passwords.
     */
    public function __construct(array $passwords)
    {
        $this->_passwords = $passwords;
    }

    /**
     * Find all the passwords that match the given application name.
     *
     * The application name is used as a regex.
     *
     * @param string $application The application name.
     * @return array<array> The passwords for the applications that match the
     *     application.
     */
    public function matchingApplication($application)
    {
        $passwordMatchesApplication = function($password) use($application) {
            return isset($password['application']) && preg_match("/{$application}/i", $password['application']);
        };

        return array_values(array_filter($this->_passwords, $passwordMatchesApplication));
    }
}
