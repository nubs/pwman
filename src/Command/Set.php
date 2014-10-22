<?php
namespace Nubs\PwMan\Command;

use GnuPG;
use Nubs\PwMan\PasswordFile;
use Nubs\PwMan\PasswordManager;
use Nubs\Sensible\CommandFactory\EditorFactory;
use Nubs\Which\LocatorFactory\PlatformLocatorFactory as WhichLocatorFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

/**
 * A symonfy console command to set passwords in a password file.
 */
class Set extends Command
{
    /**
     * Configures the command's options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('set')
            ->setDescription('Sets the password for the specified application')
            ->addArgument('password-file', InputArgument::REQUIRED, 'The path to the encrypted password file')
            ->addOption('application', 'a', InputOption::VALUE_REQUIRED, 'The application to configure')
            ->addOption('encrypt-key', 'e', InputOption::VALUE_REQUIRED, 'The uid or fingerprint for the encryption key')
            ->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'The username for the application')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'The password for the application');
    }

    /**
     * Sets the password for the specified application.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input The command input.
     * @param \Symfony\Component\Console\Output\OutputInterface $output The command output.
     * @return int The return status
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $passwordFile = new PasswordFile($input->getArgument('password-file'), new GnuPG());
        $passwords = $passwordFile->getPasswords();
        if ($passwords === null) {
            $stderr = $output instanceof ConsoleOutput ? $output->getErrorOutput() : $output;
            $stderr->writeln('<error>Failed to load passwords from file!</error>');
            return 1;
        }

        $application = $input->getOption('application') ?: '';
        $username = $input->getOption('username') ?: '';
        $password = $input->getOption('password') ?: '';
        $newApplication = ['application' => $application, 'username' => $username, 'password' => $password];

        $passwordManager = new PasswordManager($passwords);
        $matchingPasswords = $passwordManager->matchingApplication($application);

        $commandLocatorFactory = new WhichLocatorFactory();
        $editorFactory = new EditorFactory($commandLocatorFactory->create());
        $editor = $editorFactory->create();

        if (empty($matchingPasswords)) {
            $newApplication = json_decode($editor->editData(new ProcessBuilder(), json_encode($newApplication, JSON_PRETTY_PRINT)), true);
            if ($newApplication === null) {
                $output->writeln('<error>Invalid json for application!</error>');
                return 1;
            }

            $passwordManager->addPassword($newApplication);

            $passwordFile->addEncryptKey($input->getOption('encrypt-key') ?: '');
            $passwordFile->setPasswords($passwordManager->getPasswords());
        } else {
            $stderr = $output instanceof ConsoleOutput ? $output->getErrorOutput() : $output;
            $stderr->writeln('<error>There already exists an application matching this one.</error>');
            return 1;
        }
    }
}
