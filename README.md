# Laravel-Laratheme
[![Latest Stable Version](https://api.travis-ci.org/akbardwi/laratheme.svg?branch=master)](https://travis-ci.org/akbardwi/laratheme)
[![Latest Stable Version](https://poser.pugx.org/akbardwi/laratheme/v/stable)](https://packagist.org/packages/akbardwi/laratheme)
[![Latest Unstable Version](https://poser.pugx.org/akbardwi/laratheme/v/unstable)](https://packagist.org/packages/akbardwi/laratheme)
[![License](https://poser.pugx.org/akbardwi/laratheme/license)](https://packagist.org/packages/akbardwi/laratheme)

Laratheme is a Laravel theme and asset management package. You can easily integrate this package with any Laravel based project.

### Features

* Custom theme path
* Override theme
* Parent theme support
* Unlimited Parent view finding
* Asset Finding
* Theme translator support
* Multiple theme config extension
* Multiple theme changelog extension
* Artisan console commands
* Theme enable only Specific route via middleware
* Almost everything customizable 
* Also Laravel 5.5 to Laravel 10 Supported

## Installation

Laratheme is a Laravel package so you can install it via Composer. Run this command in your terminal from your project directory:

```sh
composer require akbardwi/laratheme
```

Wait for a while, Composer will automatically install Laratheme in your project.

## Configuration

Below **Laravel 5.5** you have to call this package service in `config/app.php` config file. To do that, add this line in `app.php` in `providers` array:

```php
Akbardwi\Laratheme\Providers\LarathemeServiceProvider::class,
```

Below **Laravel 5.5** version to use facade you have to add this line in `app.php` to the `aliases` array:

```php
'Theme' => Akbardwi\Laratheme\Facades\Theme::class,
```

Now run this command in your terminal to publish this package resources:

```
php artisan vendor:publish --provider="Akbardwi\Laratheme\Providers\LarathemeServiceProvider"
```

## Artisan Command
Run this command in your terminal from your project directory.

Create a theme directory:
```sh
php artisan theme:create your_theme_name


 What is theme title?:
 > 

 What is theme description? []:
 > 

 What is theme author name? []:
 >  

 What is theme version? []:
 > 

 Any parent theme? (yes/no) [no]:
 > y

 What is parent theme name?:
 > 

```
List of all themes:
```sh
php artisan theme:list

+----------+---------------------+---------+----------+
| Name     | Author              | Version | Parent   |
+----------+---------------------+---------+----------+
| themeone | Akbar Dwi Syahputra | 1.1.0   |          |
| themetwo | Akbar Dwi Syahputra | 1.0.0   | themeone |
+----------+---------------------+---------+----------+
```

## Example folder structure:
```
- app/
- ..
- ..
- themes/
    - themeone/
        - assets
            - css
                - app.css
            - img
            - js
        - lang
            - en
                -content.php
        - views/
            - layouts
                - master.blade.php
            - welcome.blade.php
        - changelog.yml        
        - theme.json
     - themetwo/   
```
You can change `theme.json` and `changelog.yml` name from `config/theme.php`

```php
// ..
'config' => [
    'name' => 'theme.json',
    'changelog' => 'changelog.yml'
],
// ..
```

`json`, `yml`, `yaml`, `php`, `ini`, `xml` extension supported.  

For example:
```php
// ..
'config' => [
    'name' => 'theme.json',
    'changelog' => 'changelog.json'
],
// ..
```
Then run `theme:create` command which describe above.

Now Please see the API List Doc.

## View Finding Flow:

Suppose you want find `welcome.blade.php` 
```
 - At first check your active theme 
 - If `welcome.blade.php not found in active theme then search parent recursively
 - If `welcome.blade.php not found in parents theme then search laravel default view folder resources/views
 ```

## API List
- [set](https://github.com/akbardwi/laratheme#set)
- [get](https://github.com/akbardwi/laratheme#get)
- [current](https://github.com/akbardwi/laratheme#current)
- [all](https://github.com/akbardwi/laratheme#all)
- [has](https://github.com/akbardwi/laratheme#has)
- [getThemeInfo](https://github.com/akbardwi/laratheme#getThemeInfo)
- [assets](https://github.com/akbardwi/laratheme#assets)
- [lang](https://github.com/akbardwi/laratheme#lang)

### set

For switching current theme you can use `set` method.

```php
Theme::set('theme-name');
```

### get

For getting current theme details you can use `get` method:

```php
Theme::get(); // return Array
```
You can also get particular theme details:
```php
Theme::get('theme-name'); // return Array
```

```php
Theme::get('theme-name', true); // return Collection
```

### current

Retrieve current theme's name:

```php
Theme::current(); // return string
```

### all

Retrieve all theme information:

```php
Theme::all(); // return Array
```

### has

For getting whether the theme exists or not:

```php
Theme::has(); // return bool
```

### getThemeInfo

For info about the specified theme:

```php
$themeInfo = Theme::getThemeInfo('theme-name'); // return Collection

$themeName = $themeInfo->get('name');
// or
$themeName = $themeInfo['name'];
```
Also fallback support:
```php
$themeInfo = Theme::getThemeInfo('theme-name'); // return Collection

$themeName = $themeInfo->get('changelog.versions');
// or
$themeName = $themeInfo['changelog.versions'];
// or you can also call like as multi dimension
$themeName = $themeInfo['changelog']['versions'];
```

### assets

For binding theme assets you can use the `assets` method:

```php
Theme::assets('your_asset_path'); // return string
```
It's generated at `BASE_URL/theme_roots/your_active_theme_name/assets/your_asset_path`

If `your_asset_path` does not exist then it's find to active theme immediate parent assets folder. Look like `BASE_URL/theme_roots/your_active_theme_parent_name/assets/your_asset_path`

When using helper you can also get assets path:
```php
themes('your_asset_path'); // return string
```

If you want to bind specific theme assets:
```php
Theme::assets('your_theme_name:your_asset_path'); // return string
// or 
themes('your_theme_name:your_asset_path'); // return string
```

**Suppose you want to bind `app.css` in your blade. Then below code can be applicable:**
```php
<link rel="stylesheet" href="{{ themes('app.css') }}">
```
Specific theme assets:
```php
<link rel="stylesheet" href="{{ themes('your_theme_name:app.css') }}">
```
### lang

The `lang` method translates the given language line using your current **theme** [localization files](https://laravel.com/docs/master/localization):
```php
echo Theme::lang('content.title'); // return string
// or
echo lang('content.title'); // return string
```
also support
```php
echo Theme::lang('content.title', [your replace array], 'your desire locale'); // return string
// or
echo lang('content.title', [your replace array], 'your desire locale'); // return string
```

If you want to bind specific theme assets:
```php
echo Theme::lang('your_theme_name::your_asset_path'); // return string
// or 
echo lang('your_theme_name::your_asset_path'); // return string
```

## How to use in Route
```php
Route::get('/', function () {
    Theme::set('your_theme_name');
    return view('welcome');
});
```
_**This will firstly check if there is a welcome.blade.php in current theme directory. If none is found then it checks parent theme, and finally falls back to default Laravel views location.**_

If you want to specific theme view:
```php
Route::get('/', function () {
    Theme::set('your_theme_name');
    return view('your_theme_name::welcome');
});
```

## Set theme using route middleware
A helper middleware is included out of the box if you want to define a theme per route. To use it:

First register it in app\Http\Kernel.php:

```php
protected $routeMiddleware = [
    // ...
    'theme' => \Akbardwi\Laratheme\Middleware\RouteMiddleware::class,
];
```
Now you can apply the middleware to a route or route-group. Eg:
```php
Route::group(['prefix' => 'admin', 'middleware'=>'theme:Your_theme_name'], function() {
    // ... Add your routes here 
    // The Your_theme_name will be applied.
});
```

## Set theme using web middleware
A helper middleware is included out of the box if you want to define a theme per route. To use it:

First register it in app\Http\Kernel.php:

```php
protected $middlewareGroups = [
    'web' => [
        // ...
        \Akbardwi\Laratheme\Middleware\WebMiddleware::class,
    ],
    // ...
];
```
Theme set from `config/theme.php` .

Then in your controller you can call your view as you would normally do:

```php

return view('home');  // This will load the home.blade.php from the the folder you set in your `config/theme.php`

```




### Dependency Injection
You can also inject theme instance using ThemeContract, eg:

``` php
use Akbardwi\Laratheme\Contracts\ThemeContract;

private $theme;

public function __construct(ThemeContract $theme)
{
    $this->theme = $theme
}
```
## Troubleshooting

Clear config after runing `vendor publish` (see [Config section](#configuration)) to save issues related to config caching by running:

`php artisan config:cache`

`php artisan config:clear`


## Credits

- [Shipu Ahamed](https://github.com/Shipu)
- [Akbar Dwi Syahputra](https://github.com/akbardwi)
- [All Contributors](../../contributors)