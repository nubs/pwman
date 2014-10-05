<?php
namespace Nubs\PwMan;

use Symfony\Component\Process\ProcessBuilder;

/**
 * Manage the password file including encryption and encoding.
 */
class PasswordFile
{
    /** @type string The file path to the password file. */
    private $_passwordFile;

    /**
     * Initialize the password file.
     *
     * @api
     * @param string $passwordFile The file path to the password file.
     */
    public function __construct($passwordFile)
    {
        $this->_passwordFile = $passwordFile;
    }

    /**
     * Return all the application passwords out of the password file.
     *
     * @return array<array>|null The passwords in the file if the file could be
     *     loaded.
     */
    public function getPasswords()
    {
        $gpgBuilder = new ProcessBuilder();
        $gpg = $gpgBuilder->setPrefix('gpg')->setArguments(['--decrypt', $this->_passwordFile])->getProcess();

        $gpg->run();
        if (!$gpg->isSuccessful()) {
            return null;
        }

        return json_decode($gpg->getOutput(), true);
    }
}
