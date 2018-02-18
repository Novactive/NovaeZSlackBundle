# Install


## Requirements

* eZ Platform 2+
* PHP 7.2+


## Installation steps

There are 2 phases:

- create the Slack Application for your workspace
- install the Bundle and configure it according to the Slack application


### Slack Application


### Bundle

Run `composer require novactive/ezslackbundle` to install the bundle and its dependencies:

### Register the bundle

Activate the bundle in `app\AppKernel.php` file.

```php
// app\AppKernel.php

public function registerBundles()
{
   ...
   $bundles = array(
       new FrameworkBundle(),
       ...
       new JMS\SerializerBundle\JMSSerializerBundle(),
       new Novactive\Bundle\eZSlackBundle\NovaeZSlackBundle(),
   );
   ...
}
```

> If you already have _JMSSerializerBundle_ do not add it one more time.


### Add routes

Make sure you add this route to your routing:

```yml
# app/config/routing.yml

_novaezslack_routes:
    resource: "@NovaeZSlackBundle/Controller"
    type:     annotation
    prefix:   /_novaezslack
```

