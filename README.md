# pwman
A password manager than handles encrypted passwords.

[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/nubs/pwman.svg?style=flat)](https://scrutinizer-ci.com/g/nubs/pwman/)

[![Latest Stable Version](http://img.shields.io/packagist/v/nubs/pwman.svg?style=flat)](https://packagist.org/packages/nubs/pwman)
[![Total Downloads](http://img.shields.io/packagist/dt/nubs/pwman.svg?style=flat)](https://packagist.org/packages/nubs/pwman)
[![License](http://img.shields.io/packagist/l/nubs/pwman.svg?style=flat)](https://packagist.org/packages/nubs/pwman)

[![Dependency Status](https://www.versioneye.com/user/projects/5565185363613000187c0900/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5565185363613000187c0900)

## Requirements
This package requires PHP 5.5, or newer.

## Installation
This package uses [composer][composer] so you can install it using composer.
Composer can install the command globally using:
```bash
composer global require nubs/pwman
```

This will install it to your `$COMPOSER_HOME` directory (typically
`$HOME/.composer`).  The `pwman` binary will be symlinked to
`$COMPOSER_HOME/vendor/bin/pwman` (e.g., `$HOME/.composer/vendor/bin/pwman`).

## Usage
The included `pwman` executable uses subcommands for its different actions.
The subcommands include `pwman get` and `pwman set`.

### Getting passwords

> `pwman get password-file [application]`

### Setting passwords

> `pwman set [-a|--application="..."] [-e|--encrypt-key="..."] [-u|--username="..."] [-p|--password="..."] [-l|--length="..."] [-c|--characters="..."] [-x|--exclude-characters="..."] password-file`

## License
pwman is licensed under the MIT license.  See [LICENSE](LICENSE) for the full
license text.

[composer]: https://getcomposer.org
