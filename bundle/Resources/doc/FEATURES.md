# Features

This bundle provides a deep Slack integration with eZ Platform.

It provides 4 high-level features that you can declined to build unlimited concrete collaboration features:

- **Incoming Webhooks**: to allow eZ to post Message (notifications) in one(or more) channel(s) in your workspace.
- **Interactive Components**: to allow you to add Button (and more type) to your Message to create interactive 
experiences for your users.
- **Slash Commands**: to allow you users to trigger actions from Slack on your eZ.
- _(Soon)_: Event Subscriptions: Your eZ will be able to listen channels and interact with you users.

> Yes, we can vulgarize and say that it is a Chat Bot

**This bundle simplify drastically the work you have to do to manage your own interactions between your eZ and your Slack 
workspace.**

Nevertheless, on top of those high-level features, this bundle provides by default interactions and commands:

## Interactive Messages 

When an Content is `published`, `hid`, `unhid`, `trashed`, `recovered` or change of `states` (extendable by 
configuration) a Message will be sent to the configured Channels to alert users.
![Message1]

> Here is an example, data and thumb image are pulled from eZ and come from the Content Repository.

As you can see Messages contain a set of "Interactions" (extendable via Services and tags), and by default you can you 
can hide/unhide, trash/recover and change the state 

[See here if you want to create your own interaction](TODO)


## Slash Command

For within you workspace you can trigger action on your eZ.

> Let's assume the command set up in your Slack app is `/ez`

### Search

`/ez search QUERY` will allow to search in the Content Repository for Contents. The QUERY can be really complex, it is
powered by [netgen/query-translator](https://github.com/netgen/query-translator), we mapped it to the eZ query system
allowing you to crazy search like:

- `/ez search plop`
- `/ez search (banana OR apple) -red`
- `/ez search contentType:article published:>today +lang:eng-US|fre-FR`
- `/ez search contentType:blog_post|article published:>yesterday`
- `/ez search contentType:blog_post|article modified:<today`
- `/ez search id:12|42`

> If only result is found then the interactions are displayed allowing you to interact with the content.


[See here if you want to create your own command](TODO)

## Screenshots

![Confirmation]
![Message1]
![Message2]
![PublicationChain]
![Search]



[Confirmation]: images/Confirmation.png.png
[Message1]: images/Message1.png
[Message2]: images/Message2.png
[PublicationChain]: images/PublicationChain.png
[Search]: images/search.png.png
[Doc-Commands]: images/Commands.png
[Doc-Interactions]: images/Interactions.png
[Doc-Models]: images/Models.png
