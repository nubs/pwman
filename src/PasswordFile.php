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

    /** @type \Symfony\Component\Process\ProcessBuilder The process builder. */
    private $_processBuilder;

    /**
     * Initialize the password file.
     *
     * @api
     * @param string $passwordFile The file path to the password file.
     * @param \Symfony\Component\Process\ProcessBuilder $processBuilder The
     *     command creator for interacting with the process file.
     */
    public function __construct($passwordFile, ProcessBuilder $processBuilder)
    {
        $this->_passwordFile = $passwordFile;
        $this->_processBuilder = $processBuilder;
    }

    /**
     * Return all the application passwords out of the password file.
     *
     * @return array<array>|null The passwords in the file if the file could be
     *     loaded.
     */
    public function getPasswords()
    {
        $gpg = $this->_processBuilder->setPrefix('gpg')->setArguments(['--decrypt', $this->_passwordFile])->getProcess();

        $gpg->run();
        if (!$gpg->isSuccessful()) {
            return null;
        }

        return json_decode($gpg->getOutput(), true);
    }
}
