## About

[![Continuous Integration](https://github.com/dklisiarchis/Faker2/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/dklisiarchis/Faker2/actions/workflows/continuous-integration.yml)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg?style=flat-square)](https://php.net/)
[![Latest Stable Version](http://poser.pugx.org/dklis/faker2/v)](https://packagist.org/packages/dklis/faker2)
[![License](http://poser.pugx.org/dklis/faker2/license)](https://packagist.org/packages/dklis/faker2)


Faker2 is a fork of the [(archived) original Faker PHP library](https://github.com/fzaninotto/Faker).
More information on the reasons the original creator decided to archive the library can be found [in this article](https://marmelab.com/blog/2020/10/21/sunsetting-faker.html).

Faker2 differences from the original library:
 - PHP requirement upgraded to ^8.0.
 - PHPUnit upgraded to version 9.5.
 - All locales, except en_US, are removed (along with their tests).
 - All ORM-related classes are removed.
 - Guesser classes are removed.
 - Type hinting added on all methods.
 - Test suite refactored for PHPUnit 9.5, type-related tests removed.
 - Psalm added


## Requirements

Faker2 requires PHP version 8.0 or greater.

## Installation

The easiest way to get started with Faker2 is through composer:
```
# Install as dependency
composer require "dklis/faker2"
```


## Usage
For usage refer to original Faker readme
[Faker repo README](https://github.com/fzaninotto/Faker/blob/master/readme.md)

[Faker README archived](docs/readme.md)

## Issues
Bug reports and feature requests can be submitted on the [Github Issue Tracker](https://github.com/dklisiarchis/faker2/issues).