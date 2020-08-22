# Novactive eZ Slack Bundle

# Installation

## Installation steps

There are 2 phases:

- Create the Slack Application for your workspace.
- Install the Bundle and configure it according to the Slack application.


### Slack Application

First you need to create a Slack app here: https://api.slack.com/apps

Once created you need to go through some steps to connect it to your eZ Platform, you should see this screen.

![Step1]

Let's go on the first item in the middle of the page "Add features and functionality".

![Step2]

You will have to do something in each of this circled items above.

#### Incoming Webhooks

![Step3]

That is basically here that you are going to configure how eZ will be able to send Message in your channel(s). 

`1 webhook` **==** `1 channel`, of course you can create multiple Incoming Webhooks and setup the bundle to send Message
in each of them.

> Config in the `notifications.channels` array

#### Interactive Components

On the left select "Interactive Components" and provide an URL to your website(a callback). That is the first URL on which 
Slack will communicate with eZ.

Just change the "HOST" in the following, this bundle provides the routes, you should fill in something like:

`https://HOST/_novaezslack/message`

![Step4]

> keep the `/_novaezslack/message` suffix that is mandatory.

#### Slash Commands

![Step5]

Still on the left "Slash Commands" and provide an URL to your website(a callback). That is the second URL on which Slack
will communicate with eZ (for Commands this time).
 
![Step6]

The command `/command` is up to you, it is not mandatory to use `/ez`. 
For the **Request URL**, just change the "HOST" in the following, this bundle provides the routes, you should fill in 
something like:
                        
`https://HOST/_novaezslack/command`


#### OAuth & Permissions

One more step to set the OAuth authentication, on the left "OAuth & Permissions", make sure to provide a valid URL.

![Step7]

> The real callback is `https://HOST/_novaezslack/auth/check` but you don't want to set that up as you want to manage
multiple SiteAccess then it is fine just to mention the base url here.

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
       // NovaeZSlackBundle
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

### Add a Guard Authenticator to your Firewall

In order to enable the Slack Connect you need to change your Firewall

```yaml
# app/config/security.yml

        ezpublish_front:
            guard:
                authenticators:
                    - Novactive\Bundle\eZSlackBundle\Security\SlackAuthenticator
```


### Add Rebuild the CSS etc.

A simplication to run all the good commands (assets, dumps etc.).

```bash
composer install
```

Awesome! Your are done!

[Step1]: images/NovaeZSlack-Step1.png
[Step2]: images/NovaeZSlack-Step2.png
[Step3]: images/NovaeZSlack-Step3.png
[Step4]: images/NovaeZSlack-Step4.png
[Step5]: images/NovaeZSlack-Step5.png
[Step6]: images/NovaeZSlack-Step6.png
[Step7]: images/NovaeZSlack-Step7.png
