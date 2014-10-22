<?php
namespace Nubs\PwMan;

use Exception;
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

    /**
     * Add the given encryption key.
     *
     * @param string $key The uid or fingerprint of the key to add.
     * @return void
     */
    public function addEncryptKey($key)
    {
        $keyInfo = $this->_gpg->keyinfo($key);
        if (count($keyInfo) !== 1) {
            throw new Exception('Could not find a unique key');
        }

        if (!$keyInfo[0]['can_encrypt']) {
            throw new Exception('Key not a valid encryption key');
        }

        $isEncryptionKey = function($subKey) {
            return $subKey['can_encrypt'];
        };

        $encryptionKeys = array_values(array_filter($keyInfo[0]['subkeys'], $isEncryptionKey));
        if (!$this->_gpg->addencryptkey($encryptionKeys[0]['fingerprint'])) {
            throw new Exception('Failed to add the encryption key');
        }
    }

    /**
     * Save the passwords to the password file.
     *
     * This requires an encryption key to have been added.
     *
     * @see addEncryptKey
     * @param array<array> The passwords to save in the file.
     * @return void
     */
    public function setPasswords(array $passwords)
    {
        $encryptedContents = $this->_gpg->encrypt(json_encode($passwords, JSON_PRETTY_PRINT));
        if ($encryptedContents === false) {
            throw new Exception($this->_gpg->geterror());
        }

        $successfullyWritten = file_put_contents($this->_passwordFile, $encryptedContents);
        if (!$successfullyWritten) {
            throw new Exception('Failed to write to the password file.');
        }
    }
}
