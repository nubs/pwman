<?php
namespace Nubs\PwMan;

use GnuPG;

/**
 * Manage the password file including encryption and encoding.
 */
class PasswordFile
{
    /** @type string The file path to the password file. */
    private $_passwordFile;

    /** @type \GnuPG The gpg resource. */
    private $_gpg;

    /**
     * Initialize the password file.
     *
     * @api
     * @param string $passwordFile The file path to the password file.
     * @param \GnuPG $gpg The gpg resource for interacting with the password file.
     */
    public function __construct($passwordFile, GnuPG $gpg)
    {
        $this->_passwordFile = $passwordFile;
        $this->_gpg = $gpg;
    }

    /**
     * Return all the application passwords out of the password file.
     *
     * @return array<array>|null The passwords in the file if the file could be
     *     loaded.
     */
    public function getPasswords()
    {
        $contents = file_get_contents($this->_passwordFile);
        if ($contents === false) {
            return null;
        }

        $decryptedContents = $this->_gpg->decrypt($contents);
        if ($decryptedContents === false) {
            return null;
        }

        return json_decode($decryptedContents, true);
    }
}
