<?php
namespace Nubs\PwMan;

use RandomLib\Factory as RandomFactory;

/**
 * Generate random passwords.
 */
class PasswordGenerator
{
    /** @type string The characters to use in the password. */
    private $_characters;

    /** @type int The length of the password. */
    private $_length;

    /**
     * Construct the password generator with the desired settings.
     *
     * @api
     */
    public function __construct()
    {
        $this->_characters = join(range(chr(32), chr(126)));
        $this->_length = 32;
    }

    /**
     * Generate a password.
     *
     * @api
     * @return string The random password.
     */
    public function __invoke()
    {
        $generator = (new RandomFactory())->getMediumStrengthGenerator();
        return $generator->generateString($this->_length, $this->_characters);
    }
}
