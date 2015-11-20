<?php
namespace Nubs\PwMan\Command;

use GnuPG;
use Nubs\PwMan\PasswordFile;
use Nubs\PwMan\PasswordManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A symonfy console command to get passwords from a password file.
 */
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
            ->addArgument('application', InputArgument::OPTIONAL, 'The application(s) to query')
            ->addOption('decrypt-key', 'd', InputOption::VALUE_REQUIRED, 'The uid or fingerprint for the decryption key')
            ->addOption('decrypt-passphrase', 'y', InputOption::VALUE_REQUIRED, 'The passphrase for the decryption key')
            ->addOption('output-format', 'f', InputOption::VALUE_REQUIRED, 'The output format (valid: json)', 'json');
    }

    /**
     * Gets the password(s) for the specified application(s).
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input The command input.
     * @param \Symfony\Component\Console\Output\OutputInterface $output The command output.
     * @return int The return status
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $passwordFile = new PasswordFile($input->getArgument('password-file'), new GnuPG());
        $passwordFile->addDecryptKey($input->getOption('decrypt-key') ?: '', $input->getOption('decrypt-passphrase') ?: '');
        $passwords = $passwordFile->getPasswords();
        if ($passwords === null) {
            return $this->_error($output, 'Failed to load passwords from file!');
        }

        $passwordManager = new PasswordManager($passwords);
        $application = $input->getArgument('application') ?: '';

        $matchingPasswords = $passwordManager->matchingApplication($application);

        $output->writeln($this->_formatPasswords($matchingPasswords, $input->getOption('output-format')));
    }

    /**
     * Format the passwords according to the output format.
     *
     * @param array<array> $passwords The passwords to format.
     * @param string $outputFormat The output format (valid: json).
     * @return string The formatted passwords for display.
     */
    private function _formatPasswords(array $passwords, $outputFormat)
    {
        switch ($outputFormat) {
            case 'json':
                return json_encode($passwords, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT);
            default:
                throw new InvalidArgumentException("Invalid format: {$outputFormat}");
        }
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
