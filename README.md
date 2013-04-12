# Rampage Development Tools

This module offers an enhancement for rampage-php to support
DB profiling on all adapters in [ZendDeveloperTools](https://github.com/zendframework/ZendDeveloperTools) without installing [BjyProfiler](https://github.com/bjyoungblood/BjyProfiler).

__NOTE__: In fact it will conflict with [BjyProfiler](https://github.com/bjyoungblood/BjyProfiler)


# Installation

## Manual

1. Download or clone this module 
2. Drop it in the `modules/rampage.devtools` directory.
3. Add `rampage.devtools = true` to your modules.conf

> **NOTE:** The pathnames may differ depending on your application layout.


## Composer

Simply add a dependency to your composer.json:

    php composer.phar require-dev rampage-php/dev-tools
