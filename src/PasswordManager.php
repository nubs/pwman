<?php
namespace Nubs\PwMan;

use Exception;

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
     * @api
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
     * @api
     * @param string $application The application name.
     * @return array<array> The passwords for the applications that match the
     *     application.
     */
    public function matchingApplication($application)
    {
        $result = [];
        foreach ($this->_passwords as $key => $value) {
            if (preg_match("/{$application}/i", $key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Add the new application to the password list.
     *
     * @api
     * @param string $name The unique application name.
     * @param array $newApplication The application information.
     * @return void
     */
    public function addPassword($name, array $newApplication)
    {
        if (isset($this->_passwords[$name])) {
            throw new Exception("Password already exists for {$name}");
        }

        $this->_passwords[$name] = $newApplication;
    }

    /**
     * Remove the given password by unique name.
     *
     * @api
     * @param string $name The unique application name.
     * @return void
     */
    public function removePassword($name)
    {
        unset($this->_passwords[$name]);
    }

    /**
     * Replace one set of passwords with a new set.
     *
     * Removes all the passwords in $old, and adds all the passwords in $new.
     *
     * @api
     * @param array $old The old passwords to replace.
     * @param array $new The new passwords to use instead.
     * @return void
     */
    public function replacePasswords(array $old, array $new)
    {
        array_map([$this, 'removePassword'], array_keys($old));

        foreach ($new as $name => $spec) {
            $this->addPassword($name, $spec);
        }

        $this->sortPasswords();
    }

    /**
     * Get the passwords.
     *
     * @api
     * @return array<array> The passwords.
     */
    public function getPasswords()
    {
        return $this->_passwords;
    }

    /**
     * Sort the passwords.
     *
     * @api
     * @return void
     */
    public function sortPasswords()
    {
        ksort($this->_passwords);
    }
}
