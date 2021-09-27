RunroomUserBundle
========================

[![Latest Stable Version](https://poser.pugx.org/runroom-packages/user-bundle/v/stable)](https://packagist.org/packages/runroom-packages/user-bundle)
[![Latest Unstable Version](https://poser.pugx.org/runroom-packages/user-bundle/v/unstable)](https://packagist.org/packages/runroom-packages/user-bundle)
[![License](https://poser.pugx.org/runroom-packages/user-bundle/license)](https://packagist.org/packages/runroom-packages/user-bundle)

[![Total Downloads](https://poser.pugx.org/runroom-packages/user-bundle/downloads)](https://packagist.org/packages/runroom-packages/user-bundle)
[![Monthly Downloads](https://poser.pugx.org/runroom-packages/user-bundle/d/monthly)](https://packagist.org/packages/runroom-packages/user-bundle)
[![Daily Downloads](https://poser.pugx.org/runroom-packages/user-bundle/d/daily)](https://packagist.org/packages/runroom-packages/user-bundle)

This bundle gives the ability to define and use translations directly on the Sonata Backoffice as a replacement for `yaml` translations of Symfony.

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```
composer require runroom-packages/user-bundle
```

### Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Runroom\UserBundle\RunroomUserBundle::class => ['all' => true],
];
```

### Update doctrine schema

Finally, execute doctrine schema update to create the new tables:

```
console doctrine:schema:update --force
```

## License

This bundle is under the [MIT license](LICENSE).