# Overloader

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Code Climate][ico-code-climate]][link-code-climate]
[![Issue Count][ico-code-climate-issues]][link-code-climate-issues]
[![Test Coverage][ico-code-climate-tests]][link-code-climate-tests]
[![Total Downloads][ico-downloads]][link-downloads]

Overload object methods and properties

## Install

Via Composer

``` bash
$ composer require nixxxon/overloader
```

## Usage

``` php
class Foo
{
    protected $bar = 'bar';

    protected function getBar()
    {
        return $this->getBarForReal();
    }

    private function getBarForReal()
    {
        return $this->bar;

    }
}

$object = new Foo;
$overload = \Nixxxon\Overloader\Overloader::init($object);
$overload->method('getBarForReal', function () {
    return 'NOT BAR';
});
echo $overload->getBar(); // NOT BAR
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email niclas.aberg@gmail.com instead of using the issue tracker.

## Credits

- [Niclas Åberg][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/nixxxon/overloader.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/nixxxon/overloader/master.svg?style=flat-square
[ico-code-climate]: https://codeclimate.com/github/nixxxon/overloader/badges/gpa.svg
[ico-code-climate-issues]: https://codeclimate.com/github/nixxxon/overloader/badges/issue_count.svg
[ico-code-climate-tests]: https://codeclimate.com/github/nixxxon/overloader/badges/coverage.svg
[ico-downloads]: https://img.shields.io/packagist/dt/nixxxon/overloader.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/nixxxon/overloader
[link-travis]: https://travis-ci.org/nixxxon/overloader
[link-code-climate]: https://codeclimate.com/github/nixxxon/overloader
[link-code-climate-issues]: https://codeclimate.com/github/nixxxon/overloader
[link-code-climate-tests]: https://codeclimate.com/github/nixxxon/overloader/coverage
[link-downloads]: https://packagist.org/packages/nixxxon/overloader
[link-author]: https://github.com/nixxxon
[link-contributors]: ../../contributors
