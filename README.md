# Bios
The nova tool for configurations storage

##### Table of Contents   

* [Introduction](#introduction)      
* [Installation](#installation)      
* [Resources](#resources)              



## Introduction
Bios is a well designed UI for `armincms/option` package.

## Installation

To get started with Bios run below command:

```
    composer require armincms/bios
```

After configure `armincms/option`; you need to register Bios tool. for this reffer here(tool configuration)

## Resources
Bios resources is like the Nova resources; but simplest. for create new configurations; need create new bios resource. for this run below command:
```
php artisan bios resource

```

this command make new resource in `app/Nova` directory. this resource never show 
the Nova in resource nav; but will display in bios Nav.

if you registered bios tool correct; you will new nav contained your new resource.

**Attention 1 :** 
    The default option store is the default of `armincms/store` config file. 

**Attention 2:** 
    For use custom store you can override `public static $store = null` property 
    of your resource.

**Attention 3:** 
    For authorization each config option you can make policy for `Armincms\Bios\Option` 
    model or create your model and override resource `public static $model` property.

**Attention 4:**
    It's possible to access your stored option by `static method option` of your resource.
    So if you created resource 'General'; you can get coresponds options by call 
    the `General::options()` or `General::option(key, default)` static methods; 
    or use deafult `armincms/option` helper methods;


 