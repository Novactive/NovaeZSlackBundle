# Novactive eZ Slack Bundle

# Usage

> All the configuration is SiteAccessAware then you can have different one depending on the SiteAccess

## Basic Configuration

```yaml

# config.yml
nova_ezslack:
    system:
        default:
            slack_client_id: "" # The Client Id of your Slack App
            slack_client_secret: "" #The Client Secret of your Slack App
            slack_verification_token: "" #The Verification Token of your Slack app
            slackconnect_usergroup_content_id: 12 # When to create Slack connect Users if they do not exist
            slackconnect_contenttype_identifier: 'user' # The Content Type of the Users
            site_name: "" # Used to display in the Message
            asset_prefix: "" # Prefix where to load the assets
            notifications:
                channels:
                    - "" # Incoming Webhook URL #1
                    - "" # Incoming Webhook URL #2
                    - "" # Incoming Webhook URL #N


# DO NOT TOUCH THAT UNLESS YOU KNOW WHAT YOUR ARE DOING
knpu_oauth2_client:
    clients:
        slack:
            type: slack
            redirect_route: _novaezslack_slack_oauth_check
            client_id: "#" # will be overridden by ConfigResolver - this value does not matter
            client_secret: "#" # will be overridden by ConfigResolver - this value does not matter


```

## Create your own interactions

You have to understands some Slack concepts first: https://api.slack.com/interactive-messages

To sum up:
- A Message is a collection of Attachments
- A Attachment can own Action(s) (Button or Select)
- When, from whithin Slack, a user click on an Action, the callback is called and this callback can update the Message.

This bundle provides 2 concepts to manage that:

- `AttachmentProvider`: to provide an Attachment to a Message
- `ActionProvider`: to provide an Action to a Attachment

Now look at this Message:

![Message2-tech]

Now look at the configuration of services and tag to generate it.

```yaml
 novaezslack.attachment.provider.content.main:
        class: "%novaezslack.attachment.provider.content.class%"
        tags:
            - { name: "novaezslack.attachment.provider", alias: "main"}

    novaezslack.attachment.provider.content.details:
        class: "%novaezslack.attachment.provider.content.class%"
        tags:
            - { name: "novaezslack.attachment.provider", alias: "details"}

    Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Attachment\BasicActions:
        tags:
            - { name: "novaezslack.attachment.provider", alias: "basic-actions"}

    Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Attachment\States:
        tags:
            - { name: "novaezslack.attachment.provider", alias: "states"}

    Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Action\Hide:
        tags:
            - { name: "novaezslack.action.provider", alias: "hide", attachment: "basic-actions" }

    Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Action\Unhide:
        tags:
            - { name: "novaezslack.action.provider", alias: "unhide", attachment: "basic-actions" }

    Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Action\Trash:
        tags:
            - { name: "novaezslack.action.provider", alias: "trash", attachment: "basic-actions" }

    Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Action\Recover:
        tags:
            - { name: "novaezslack.action.provider", alias: "recover", attachment: "basic-actions" }

    Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Action\PublicationChainChangeState:
        tags:
            - { name: "novaezslack.action.provider", alias: "publication_chain.change_state", attachment: "states" }

```

So basically:

- you can inject an Attachment if you define a Service tagged `novaezslack.attachment.provider` that implements
AttachmentProviderInterface

- inject an Action to an Attachment if you define a Service tagged `novaezslack.action.provider` that implements
ActionProviderInterface. Here you also need to provide the Attachment name with `attachment` key.

Wanna add an attachment "MyAttachmentPart" and "Button1" and "Select2"?

```yaml
    YOUR_FQDN_CLASS_FOR_MyAttachmentPart_AttachmentProviderInterface:
        tags:
            - { name: "novaezslack.attachment.provider", alias: "MyAttachmentPart"}

    YOUR_FQDN_CLASS_FOR_Button1_ActionProviderInterface:
        tags:
            - { name: "novaezslack.action.provider", alias: "Button1", attachment: "MyAttachmentPart" }
    
    YOUR_FQDN_CLASS_FOR_Select2_ActionProviderInterface:
        tags:
            - { name: "novaezslack.action.provider", alias: "Select2", attachment: "MyAttachmentPart" }
    
```

That is it! Now your turn to code!

## Create your own command

You have to understands some Slack concepts first: https://api.slack.com/slash-commands

To sump up:

- A command is trigger from Slack and consist of Text placed after the `/command`
- A callback URLs is called by Slack to get the Message to display!

This bundle provides a concept of `Responder` to manage that, it is pretty simple you just have to implement the
ResponderInterface and tag your service:

```yaml
    YOUR_FQDN_CLASS_FOR_MyAttachmentPart_AttachmentProviderInterface:
        tags:
            - { name: "novaezslack.command.responder"}
```

The trigger of this Responder will be the ClassName, for instance 
`Novactive\Bundle\eZSlackBundle\Core\Slack\Responder\Search` will provide the `/command search` responder.

In your Slack you can then do:

```
/command classname whatever you want etc --12 --hello=world 
```
And you will get an array of arguments (without `/command` and `classname`) in the 
`public function respond(array $arguments = []): Message;` function 

That's it!

[Message2-tech]: images/Message2-tech.png
