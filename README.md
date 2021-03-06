## API Documentation

Check out [Localization API Documentation](http://imsamurai.github.io/CakePHP-Localization-Plugin/docs/master/)

## Abstract

[![Build Status](https://travis-ci.org/imsamurai/CakePHP-Localization-Plugin.png)](https://travis-ci.org/imsamurai/CakePHP-Localization-Plugin) [![Coverage Status](https://coveralls.io/repos/imsamurai/CakePHP-Localization-Plugin/badge.png?branch=master)](https://coveralls.io/r/imsamurai/CakePHP-Localization-Plugin?branch=master) [![Latest Stable Version](https://poser.pugx.org/imsamurai/cakephp-localization/v/stable.png)](https://packagist.org/packages/imsamurai/cakephp-localization) [![Total Downloads](https://poser.pugx.org/imsamurai/cakephp-localization/downloads.png)](https://packagist.org/packages/imsamurai/cakephp-localization) [![Latest Unstable Version](https://poser.pugx.org/imsamurai/cakephp-localization/v/unstable.png)](https://packagist.org/packages/imsamurai/cakephp-localization) [![License](https://poser.pugx.org/imsamurai/cakephp-localization/license.png)](https://packagist.org/packages/imsamurai/cakephp-localization)

Coordinator for any checker scripts.
With this plugin you can unify periodic checkers for some of your services/data/etc,
get mail in case of failure, store checker logs in DB.

## Installation

	cd my_cake_app/app
	git clone git://github.com/imsamurai/CakePHP-Localization-Plugin.git Plugin/Localization

or if you use git add as submodule:

	cd my_cake_app
	git submodule add "git://github.com/imsamurai/CakePHP-Localization-Plugin.git" "app/Plugin/Localization"

then add plugin loading in Config/bootstrap.php

	CakePlugin::load('Localization', array('bootstrap' => true, 'routes' => true));

add tables from `Config/Schema/localization.sql` and configure datasource `localization`

Plugin requires squizlabs/php_codesniffer": "~1" for tokenize javascript

## Configuration

Write global config if you need to change plugin config (see plugin bootstrap.php)

## Usage

Use `Localization` model for manage localization variables in DB.
Plugin works with `__*` style functions, also in javascript. Inline javascript are not supported (only \*.js files).
For php export from db will create/replace files in `app/Locale/*` and for js in `webroot/js/Locale/*` (you can change this in config, see bootstrap.php of the plugin).
Just open `example.com/localization` for edit/translate/export to db

For import/update all messages that used with `__*` functions (in php and js) use:

	Console/cake Localization.messages extract --output db <other params from i18n shell>
