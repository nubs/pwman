<?php
namespace Nubs\PwMan\Command;

use Nubs\PwMan\PasswordFile;
use Nubs\PwMan\PasswordManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Get extends Command
{
    /**
     * Configures the command's options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('get')
            ->setDescription('Get password(s) for the specified application(s)')
            ->addArgument('password-file', InputArgument::REQUIRED, 'The path to the encrypted password file')
            ->addArgument('application', InputArgument::OPTIONAL, 'The application(s) to query');
    }

    /**
     * Gets the password(s) for the specified application(s).
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input The command input.
     * @param \Symfony\Component\Console\Output\OutputInterface $output The command output.
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $passwordFile = new PasswordFile($input->getArgument('password-file'));
        $passwords = $passwordFile->getPasswords();
        if ($passwords === null) {
            $stderr = $output instanceof ConsoleOutput ? $output->getErrorOutput() : $output;
            $stderr->writeln('<error>Failed to load passwords from file!</error>');
            return 1;
        }

        $passwordManager = new PasswordManager($passwords);
        $application = $input->getArgument('application') ?: '';

        $matchingPasswords = $passwordManager->matchingApplication($application);

        $output->writeln(json_encode($matchingPasswords, JSON_PRETTY_PRINT));
    }
}
