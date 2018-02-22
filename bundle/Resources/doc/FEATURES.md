# Features

This bundle provides a deep Slack integration with eZ Platform.

It provides 5 high-level features that you can declined to build unlimited concrete collaboration features:

- **Slack Connect**: to allow user to login in eZ via Slack and to recognize a Slack user in eZ. (mapping with 
role & permissions)
- **Incoming Webhooks**: to allow eZ to post Message (notifications) in one(or more) channel(s) in your Slack workspace.
- **Interactive Components**: to allow you to add Button (and more types) to your Message to create interactive 
experiences for your users.
- **Slash Commands**: to allow your users to trigger actions from Slack into your eZ.
- **_(Soon)_**: Event Subscriptions: Your eZ will be able to listen channels and interact with you users.

> Yes, we can vulgarize and say that it is a Chat Bot

**This bundle simplify drastically the work you have to do to manage your own interactions between your eZ and your 
Slack workspace.**

On top of those high-level features, this bundle provides default interactions and commands:

## Interactive Messages 

When an Content is `published`, `hid`, `unhid`, `trashed`, `recovered` or change of `states` (extendable by 
configuration) a Message will be sent to the configured Channels.

If you are using eZ Platform Enterprise, this bundle is wired to the Form Builder and the Notification Center of eZ to 
send Interactive Message on events. 

![Message1]

> Here is an example, data and thumb image are pulled from eZ and come from the Content Repository.

As you can see Messages contain a set of "Interactions" (extendable via Services and tags), and by default you 
can hide/unhide, trash/recover and change states.

[See here if you want to create your own interaction](TODO)


## Slash Command

For within you workspace you can trigger action on your eZ.

> Let's assume the `/command` set up in your Slack app is `/ez`

### Search

`/ez search QUERY` will allow to search in the Content Repository for Contents. The QUERY can be really complex, it is
powered by [netgen/query-translator](https://github.com/netgen/query-translator), we mapped it to the eZ query/criterion
system allowing you to do crazy search like:

- `/ez search plop`
- `/ez search (banana OR apple) -red`
- `/ez search contentType:article published:>today +lang:eng-US|fre-FR`
- `/ez search contentType:blog_post|article published:>yesterday`
- `/ez search contentType:blog_post|article modified:<today`
- `/ez search id:12|42`

> If only one result is found then the interactions are displayed allowing you to interact with the content.


[See here if you want to create your own command](TODO)

## Screenshots

### Slack Connect

![slackconnect]

### Share from the BO

![shareonslack]

### Messages

![Message1]
![Message2]

### Confirmation

![Confirmation]

### Selects

![PublicationChain]

### Search Command

![Search]



[Confirmation]: images/Confirmation.png
[Message1]: images/Message1.png
[Message2]: images/Message2.png
[PublicationChain]: images/PublicationChain.png
[Search]: images/search.png
[shareonslack]: images/shareonslack.png
[slackconnect]: images/slackconnect.png

