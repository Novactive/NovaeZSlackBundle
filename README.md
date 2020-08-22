# Novactive eZ Slack Bundle

----

This repository is what we call a "subtree split": a read-only copy of one directory of the main repository. 
It is used by Composer to allow developers to depend on specific bundles.

If you want to report or contribute, you should instead open your issue on the main repository: https://github.com/Novactive/Nova-eZPlatform-Bundles

Documentation is available in this repository via `.md` files but also packaged here: https://novactive.github.io/Nova-eZPlatform-Bundles/master/SlackBundle/README.md.html

----

[![Downloads](https://img.shields.io/packagist/dt/novactive/ezslackbundle.svg?style=flat-square)](https://packagist.org/packages/novactive/ezslackbundle)
[![Latest version](https://img.shields.io/github/release/Novactive/NovaeZSlackBundle.svg?style=flat-square)](https://github.com/Novactive/NovaeZSlackBundle/releases)
[![License](https://img.shields.io/packagist/l/novactive/ezslackbundle.svg?style=flat-square)](LICENSE)

Novactive eZ Slack Bundle is an eZ Platform bundle that provides a complete Slack integration.

This bundle provides a deep Slack integration.

It provides 5 high-level features that you can decline to build unlimited concrete collaboration features:

- **Slack Connect**: to allow user to login in eZ via Slack and to recognize a Slack user in eZ. (mapping with 
roles & permissions)
- **Incoming Webhooks**: to allow eZ to post Message (notifications) in one(or more) channel(s) in your Slack workspace.
- **Interactive Components**: to allow you to add Buttons or Selects (and more types) to your Message to create
interactive experiences for your users.
- **Slash Commands**: to allow your users to trigger actions from Slack into your eZ.
- **_(Soon)_**: Event Subscriptions: Your eZ will be able to listen channels and interact with you users.

> Yes, we can vulgarize and say that it is a Chat Bot

**This bundle simplify drastically the work you have to do to manage your own interactions between your eZ and your 
Slack workspace.** (see [usage](bundle/Resources/doc/USAGE.md) to learn how to do it)

By default, on top of those high-level features, this bundle provides interactions and commands. 
(see [features](bundle/Resources/doc/FEATURES.md))


DEMO: https://youtu.be/3DTe6pDCx1w


## Features

[Features](bundle/Resources/doc/FEATURES.md)

## Usage and installation instructions

[Usage](bundle/Resources/doc/USAGE.md)

[Installation](bundle/Resources/doc/INSTALL.md)

Change
------

[Changelog](bundle/Resources/doc/CHANGELOG.md)


Special Mentions and Credits
----------------------------

This bundle requires those awesome libs, BIG thank you to their mainteners!

- [jms/serializer-bundle](https://jmsyst.com/libs/serializer)
- [nesbot/carbon](http://carbon.nesbot.com/)
- [netgen/query-translator](https://github.com/netgen/query-translator)
- [knpuniversity/oauth2-client-bundle](https://github.com/knpuniversity/oauth2-client-bundle)
- [adam-paterson/oauth2-slack](https://github.com/adam-paterson/oauth2-slack)

Also this bundle was inspired by 2 great articles

- LA Times: [How Slack Controls Our CMS](https://source.opennews.org/articles/slack-buttons)
- Snowball Digital: [Using Slack for content approval workflow](https://snowball.digital/blog/using-slack-for-content-approval-workflow-in-ez-platform-symfony)
