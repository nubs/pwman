<?php
namespace Nubs\PwMan;

use Symfony\Component\Console\Application as SymfonyApplication;
use Nubs\PwMan\Command\Get;

/**
 * The symfony application wrapper for PwMan.
 */
class Application extends SymfonyApplication
{
    /**
     * Initialize the pwman application with all of the different commands.
     */
    public function __construct()
    {
        parent::__construct('pwman', '0.1.0');

        $getCommand = new Get();
        $this->add($getCommand);
        $this->setDefaultCommand($getCommand);
    }
}
