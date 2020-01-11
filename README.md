# Bios
The Nova tool for configurations storage

##### Table of Contents   

* [Introduction](#introduction)      
* [Installation](#installation)      
* [Resources](#resources)              



## Introduction
Bios is a well-designed UI based on the `armincms/option` package for store the configurations data.

## Installation

To get started with Bios run the below command:

```
    composer require armincms/bios
```

After configuring the *`armincms/option`* package; you need to register the Bios tool. 
for doing this refer [here](https://nova.laravel.com/docs/2.0/customization/tools.html#registering-tools)

## Resources

The Bios tool; will detect the configurable options by the resources fields. 
Bios resources are like Nova sources, but easier. to create new settings, you need to create a Bios resource. for doing this run the below command:

```
php artisan bios resource

```

this command makes a new resource in the `app/Nova` directory. this resource never displayed in the Nova resource nav but will display in the Bios resources.

If you have correctly registered the Bios tool, you will see a new Nav item containing your new resources.

**Attention 1 :** 
    By default, the Bios will read the storage driver from the `armincms/option` configurations.

**Attention 2:** 
    For use custom storage you can change the `public static $store = null`  property of the resource.

**Attention 3:** 
    For authorizing each config option you can define a policy for the `Armincms\Bios\Option`  model or create your model and change the resource `public static $model` property  and make a policy for it.

**Attention 4:**
    It's possible to access your stored data by the `static method `option` of the resource.
    so if you created the resource 'General'; you can get all data by the `General::options()` method. 
    In the same way; you can retrieve the specific data by the `General::option(key, default)` method.

**Attention 5:**
    Methods that defined in `attention 4` are just helpers and also you can use 
    `armincms/option` helpers method instead of it.  


 