# colornames-php

Find a human-friendly color name for a hex or RGB color value.

This package is useful when your app stores colors as technical values, but you want to show readable names to users.

## Practical Use Cases

### 1. Show a readable color name in a UI

```php
use wwaz\Colornames\ColornameService;

$colors = new ColornameService();

echo $colors->fromHex('#f00'); // Red
```

You can use this in design tools, admin panels, product editors, theme builders, or any place where users pick colors.

### 2. Match a color against your own catalog

```php
use wwaz\Colornames\ColornameService;

$colors = new ColornameService('/path/to/your/colornames.json');

echo $colors->fromHex('#f00'); // Brand Red
```

This is helpful when you need localized color names or a product-specific list of approved color names.

## Installation

Install the package with Composer:

```bash
composer require wwaz/colornames-php
```

The package requires PHP 8.2 or newer.

## Basic Usage

Use `ColornameService` for the common case:

```php
use wwaz\Colornames\ColornameService;

$colors = new ColornameService();

$name = $colors->fromHex('#f0f8ff');

echo $name; // Alice Blue
```

The service accepts short and long hex values, with or without `#`:

```php
$colors->fromHex('f00');
$colors->fromHex('#f00');
$colors->fromHex('ff0000');
$colors->fromHex('#ff0000');
```

## Custom Catalogs

Create a catalog from an array:

```php
use wwaz\Colornames\ColornameService;

$catalog = [
    ['name' => 'Brand Red', 'hex' => '#ff0000'],
    ['name' => 'Brand Blue', 'rgb' => [0, 0, 255]],
];

$colors = new ColornameService($catalog);

echo $colors->fromHex('#f00'); // Brand Red
```

Or load a catalog from a JSON file:

```php
use wwaz\Colornames\ColornameService;

$colors = new ColornameService('/path/to/your/colornames.json');
```

Custom catalog file paths should be absolute.

The JSON file should contain a list of entries:

```json
[
  {
    "name": "Brand Red",
    "hex": "#ff0000"
  },
  {
    "name": "Brand Blue",
    "rgb": [0, 0, 255]
  }
]
```

Each entry needs a `name` and either `hex` or `rgb`. If both are present, `hex` is used.

## RGB Matching

The service also accepts RGB values:

```php
use wwaz\Colornames\ColornameService;

$colors = new ColornameService();

echo $colors->fromRgb('255,0,0'); // Red
echo $colors->fromRgb([255, 0, 0]); // Red
```

`fromRgb()` accepts:

- An RGB string like `255,0,0`
- An RGB array like `[255, 0, 0]`
- An object with a `toArray()` method that returns RGB values

## How Matching Works

The package compares the input color with every color in the selected catalog.

It returns the color name with the smallest RGB distance. This means exact matches work, and near matches return the closest available name.

## Development

Install dependencies:

```bash
composer install
```

Run the test suite:

```bash
composer test
```

## License

MIT
