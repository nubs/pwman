<?php
namespace Nubs\PwMan;

use Symfony\Component\Console\Application as SymfonyApplication;
use Nubs\PwMan\Command\Get;

class Application extends SymfonyApplication
{
    public function __construct()
    {
        parent::__construct('pwman', '0.1.0');

        $getCommand = new Get();
        $this->add($getCommand);
        $this->setDefaultCommand($getCommand);
    }
}
