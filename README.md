PasswordMeterPHP
====================

A port of the Javascript used on [passwordmeter.com](http://www.passwordmeter.com). It is a Little Apps script and is coded in PHP. 

### Integration ###
This code is a PHP class and meant to be included in a PHP project using one of the following methods:

#### Composer ####

You can install the bindings via [Composer](http://getcomposer.org/). Add this to your `composer.json`:

    {
      "require": {
        "little-apps/PasswordMeterPHP": "0.*"
      }
    }

Then install via:

    composer install

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/00-intro.md#autoloading):

    require_once('vendor/autoload.php');

#### Manually ####
If you do not wish to use Composer, you can [download the latest release from GitHub](https://github.com/little-apps/PasswordMeterPHP/archive/master.zip). Then, include the ``PasswordMeter`` class.

    require_once( '/path/to/PasswordMeterPHP/passwordmeter.class.php' );
    
### Example ###
An example of how to use PasswordMeterPHP is shown in ``examples/cli.php``. This file can be executed by executing the following in the command line:

    php '/path/to/PasswordMeterPHP/examples/cli.php'

### License ###

    PasswordMeterPHP
    Copyright (C) 2008 Little Apps (https://www.little-apps.com)
    
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Lesser General Public License for more details.
    
    You should have received a copy of the GNU Lesser General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

### Show Your Support ###

Little Apps relies on people like you to keep our software running. If you would like to show your support for PasswordMeterPHP, then you can [make a donation](https://www.little-apps.com/?donate). Please note that any amount helps (even just $1).
