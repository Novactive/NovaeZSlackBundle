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
       new KnpU\OAuth2ClientBundle\KnpUOAuth2ClientBundle(),
       new Novactive\Bundle\eZSlackBundle\NovaeZSlackBundle(),
   );
   ...
}
```

> If you already have _JMSSerializerBundle_ or  _KnpUOAuth2ClientBundle_ do not add them a second time.


### Add routes

Make sure you add this route to your routing:

```yml
# app/config/routing.yml

_novaezslack_routes:
    resource: "@NovaeZSlackBundle/Controller"
    type:     annotation
    prefix:   /_novaezslack

_novaezslack_slack_oauth_check:
    path: /_novaezslack/auth/check
```

### Add configuration

Make sure you adapt the configuration

```yml
# app/config/config.yml

nova_ezslack:
    system:
        default:
            slack_client_id: "SLACK_APP_CLIENT ID"
            slack_client_secret: "SLACK_APP_CLIENT_SECRET"
            slack_verification_token: "SLACK_APP_VERIFICATION_TOKEN"
            site_name: "novactive.us"
            favicon: "https://assets.novactive.us/images/icos/favicon.ico"
            asset_prefix: "https://assets.novactive.us"
            notifications:
                channels:
                    - "https://hooks.slack.com/services/XXXX"

knpu_oauth2_client:
    clients:
        slack:
            type: slack
            redirect_route: _novaezslack_slack_oauth_check
            client_id: "#" # will be overridden by ConfigResolver - this value does not matter
            client_secret: "#" # will be overridden by ConfigResolver - this value does not matter

```

