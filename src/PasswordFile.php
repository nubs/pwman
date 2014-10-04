<?php
namespace Nubs\PwMan;

use Symfony\Component\Process\ProcessBuilder;

class PasswordFile
{
    private $_passwordFile;

    public function __construct($passwordFile)
    {
        $this->_passwordFile = $passwordFile;
    }

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
