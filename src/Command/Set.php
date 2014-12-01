<?php
namespace Nubs\PwMan\Command;

use GnuPG;
use Nubs\PwMan\PasswordFile;
use Nubs\PwMan\PasswordGenerator;
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
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'The password for the application')
            ->addOption('length', 'l', InputOption::VALUE_REQUIRED, 'The length of the random passwords for the application');
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
            return $this->_error('Failed to load passwords from file!');
        }

        $application = $input->getOption('application') ?: '';
        $username = $input->getOption('username') ?: '';
        $password = $input->getOption('password');
        if (!$password) {
            $passwordGenerator = new PasswordGenerator(null, $input->getOption('length') ?: 32);
            $password = $passwordGenerator();
        }

        $passwordManager = new PasswordManager($passwords);
        $existingPasswords = $passwordManager->matchingApplication($application);

        $passwordsToEdit = empty($existingPasswords) ?
            [$application => ['application' => $application, 'username' => $username, 'password' => $password]] :
            $existingPasswords;

        $commandLocatorFactory = new WhichLocatorFactory();
        $editorFactory = new EditorFactory($commandLocatorFactory->create());
        $editor = $editorFactory->create();

        $updates = json_decode($editor->editData(new ProcessBuilder(), json_encode($passwordsToEdit, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT)), true);
        if ($updates === null) {
            return $this->_error('Invalid json for application!');
        }

        array_map([$passwordManager, 'removePassword'], array_keys($existingPasswords));

        foreach ($updates as $name => $spec) {
            $passwordManager->addPassword($name, $spec);
        }

        $passwordManager->sortPasswords();

        $passwordFile->addEncryptKey($input->getOption('encrypt-key') ?: '');
        $passwordFile->setPasswords($passwordManager->getPasswords());
    }

    /**
     * Prints an error message and returns the given error code.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output The command output.
     * @param string $message The message to output.
     * @param int $code The return status.
     * @return int The return status
     */
    private function _error(OutputInterface $output, $message, $code = 1)
    {
        $stderr = $output instanceof ConsoleOutput ? $output->getErrorOutput() : $output;
        $stderr->writeln("<error>{$message}</error>");

        return $code;
    }
}
