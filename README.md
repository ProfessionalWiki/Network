# Network

[![Build Status](https://travis-ci.org/ProfessionalWiki/Network.svg?branch=master)](https://travis-ci.org/ProfessionalWiki/Network)
[![Latest Stable Version](https://poser.pugx.org/professional-wiki/network/version.png)](https://packagist.org/packages/professional-wiki/network)
[![Download count](https://poser.pugx.org/professional-wiki/network/d/total.png)](https://packagist.org/packages/professional-wiki/network)

The **Network** extension allows visualizing connections between wiki pages via an interactive network graph.

It was created by [Professional.Wiki](https://professional.wiki/) and funded by
[KDZ - Centre for Public Administration Research](https://www.kdz.eu/).

Example network

TODO

## Platform requirements

* PHP 7.1 or later
* MediaWiki 1.31.x up to 1.35.x

See the [release notes](#release-notes) for more information on the different versions of Network.

## Installation

The recommended way to install Network is using [Composer](https://getcomposer.org) with
[MediaWiki's built-in support for Composer](https://professional.wiki/en/articles/installing-mediawiki-extensions-with-composer).

On the commandline, go to your wikis root directory. Then run these two commands:

```shell script
COMPOSER=composer.local.json composer require --no-update professional-wiki/network:dev-master
composer update professional-wiki/network --no-dev -o
```

Then enable the extension by adding the following to the bottom of your wikis `LocalSettings.php` file:

```php
wfLoadExtension( 'Network' );
```

You can verify the extension was enabled successfully by opening your wikis Special:Version page in your browser.

## Usage

```
{{#network:}}
```

```
{{#network:Page1 | Page2 | Page3
 | class = col-lg-3 mt-0
}}
```

### Parameters

<table>
	<tr>
		<th></th>
		<th>Default</th>
		<th>Example value</th>
		<th>Description</th>
	</tr>
	<tr>
	    <th>(page)</th>
	    <td><i>The current page</i></td>
	    <td>MyPage</td>
	    <td>The name of the page to show connections for. Can be specified multiple times. The parameter name is optional.</td>
	</tr>
	<tr>
        <th>class</th>
        <td></td>
        <td>col-lg-3 mt-0</td>
        <td>Extra css class(es) to add to the network graph</td>
    </tr>
	<tr>
        <th>exclude</th>
        <td></td>
        <td>Sitemap; Main Page</td>
        <td>Pages to exclude from the network graph, separated by semicolon</td>
    </tr>
</table>

### Layout CSS

The network graphs are located in a div with class `network-visualization`. The default css for this class is

```css
.network-visualization {
	width: 100%;
	height: 600px;
}
```

You can add extra CSS in [MediaWiki:Common.css]. You can also add extra classes to the div via the `class` parameter.

### Configuration

The default value of all parameters can be changed by placing configuration in "LocalSettings.php".
These configuration settings are available:

* `$wgTODO` – 

Default values of these configuration settings can be found in "extension.json". Do not change "extension.json".

Example of changing one of the configuration settings:

```php
$wgTODO = '500px';
```

## Performance / caching

This extension bypasses the MediaWiki page cache. This means that your network graphs will always be up to date,
without needing to purge the page cache.

## Limitations

* External links are not shown

[Professional MediaWiki development](https://professional.wiki/en/services#development) is available via
[Professional.Wiki](https://professional.wiki/).

## Contribution and support

If you want to contribute work to the project please subscribe to the developers mailing list and
have a look at the contribution guideline.

* [File an issue](https://github.com/ProfessionalWiki/Network/issues)
* [Submit a pull request](https://github.com/ProfessionalWiki/Network/pulls)
* Ask a question on [the mailing list](https://www.semantic-mediawiki.org/wiki/Mailing_list)

[Professional MediaWiki support](https://professional.wiki/en/support) is available via
[Professional.Wiki](https://professional.wiki/).

## Development

Tests, style checks and static analysis can be run via Make. You can execute these commands on the command line
in the `extensions/Network` directory:

* `make ci` - run everything
* `make cs` - run style checks
* `make test` - run the tests 

For more details see the `Makefile`.

The JavaScript tests can only be run by going to the [`Special:JavaScriptTest` page][JS tests].

## License

[GNU General Public License v2.0 or later (GPL-2.0-or-later)](/COPYING).

## Release notes

### Version 1.0.0

TODO

Initial release

## Examples

TODO

[MediaWiki:Common.css]: https://www.mediawiki.org/wiki/Manual:Interface/Stylesheets
[JS tests]: https://www.mediawiki.org/wiki/Manual:JavaScript_unit_testing
