<?php
/**
 * NovaeZSlackBundle Bundle.
 *
 * @package   Novactive\Bundle\eZSlackBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSlackBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZSlackBundle\Core\Slack;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class InteractiveMessage.
 */
class InteractiveMessage
{
    /**
     * An array of actions that were clicked, including the name and value of the actions, as you prepared when
     * creating your message buttons. Though presented as an array, at this time you'll only receive a single action
     * per incoming invocation.
     *
     * @var Action[]
     * @Serializer\Type("array<Novactive\Bundle\eZSlackBundle\Core\Slack\Action>")
     */
    private $actions;

    /**
     * The string you provided in the original message attachment as the callback_id. Use this to identify the
     * specific set of actions/buttons originally posed. If the value of an action is the answer, callback_id is the
     * specific question that was asked. No more than 200 or so characters please.
     *
     * @var string
     * @Serializer\SerializedName("callback_id")
     * @Serializer\Type("string")
     */
    private $callbackId;

    /**
     * A small set of string attributes about the workspace/team where this action occurred.
     *
     * @var Team
     * @Serializer\Type("Novactive\Bundle\eZSlackBundle\Core\Slack\Team")
     */
    private $team;

    /**
     * Where it all happened — the user inciting this action clicked a button on a message contained within a channel,
     * and this hash presents attributed about that channel.
     *
     * @var Channel
     * @Serializer\Type("Novactive\Bundle\eZSlackBundle\Core\Slack\Channel")
     */
    private $channel;

    /**
     * The clicker! The action-invoker! The button-presser! These attributes tell you all about the user who decided to
     * interact your message.
     *
     * @var User
     * @Serializer\Type("Novactive\Bundle\eZSlackBundle\Core\Slack\User")
     */
    private $user;

    /**
     *The time when the message containing the action was posted, expressed in decimal epoch time, wrapped in a string.
     * Like "1458170917.164398".
     *
     * @var string
     * @Serializer\SerializedName("message_ts")
     * @Serializer\Type("string")
     */
    private $messageTimestamp;

    /**
     * The time when the action occurred, expressed in decimal epoch time, wrapped in a string.
     * Like "1458170917.164398".
     *
     * @var string
     * @Serializer\SerializedName("action_ts")
     * @Serializer\Type("string")
     */
    private $actionTimestamp;

    /**
     * A 1-indexed identifier for the specific attachment within a message that contained this action. In case you were
     * curious or building messages containing buttons within many attachments.
     *
     * @var string
     * @Serializer\SerializedName("attachment_id")
     * @Serializer\Type("string")
     */
    private $attachmentIndex;

    /**
     * This is the same string you received when configuring your application for interactive message support,
     * presented to you on an app details page. Validate this to ensure the request is coming to you from Slack.
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $token;

    /**
     * A object hash containing JSON expressing the original message that triggered this action. This is especially
     * useful if you don't retain state or need to know the message's message_ts for use with chat.update This value is
     * not provided for ephemeral messages.
     *
     * @var Message
     * @Serializer\SerializedName("original_message")
     * @Serializer\Type("Novactive\Bundle\eZSlackBundle\Core\Slack\Message")
     */
    private $originalMessage;

    /**
     * A string containing a URL, used to respond to this invocation independently from the triggering of your action
     * URL.
     *
     * @var string
     * @Serializer\SerializedName("response_url")
     * @Serializer\Type("string")
     */
    private $responseURL;

    /**
     * @return Action[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param Action[] $actions
     *
     * @return InteractiveMessage
     */
    public function setActions(array $actions): self
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @return Action
     */
    public function getAction(): Action
    {
        return $this->actions[0];
    }

    /**
     * @return string
     */
    public function getCallbackId(): string
    {
        return $this->callbackId;
    }

    /**
     * @param string $callbackId
     *
     * @return InteractiveMessage
     */
    public function setCallbackId(string $callbackId): self
    {
        $this->callbackId = $callbackId;

        return $this;
    }

    /**
     * @return Team
     */
    public function getTeam(): Team
    {
        return $this->team;
    }

    /**
     * @param Team $team
     *
     * @return InteractiveMessage
     */
    public function setTeam(Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @return Channel
     */
    public function getChannel(): Channel
    {
        return $this->channel;
    }

    /**
     * @param Channel $channel
     *
     * @return InteractiveMessage
     */
    public function setChannel(Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return InteractiveMessage
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessageTimestamp(): string
    {
        return $this->messageTimestamp;
    }

    /**
     * @param string $messageTimestamp
     *
     * @return InteractiveMessage
     */
    public function setMessageTimestamp(string $messageTimestamp): self
    {
        $this->messageTimestamp = $messageTimestamp;

        return $this;
    }

    /**
     * @return string
     */
    public function getActionTimestamp(): string
    {
        return $this->actionTimestamp;
    }

    /**
     * @param string $actionTimestamp
     *
     * @return InteractiveMessage
     */
    public function setActionTimestamp(string $actionTimestamp): self
    {
        $this->actionTimestamp = $actionTimestamp;

        return $this;
    }

    /**
     * @return string
     */
    public function getAttachmentIndex(): string
    {
        return $this->attachmentIndex;
    }

    /**
     * @param string $attachmentIndex
     *
     * @return InteractiveMessage
     */
    public function setAttachmentIndex(string $attachmentIndex): self
    {
        $this->attachmentIndex = $attachmentIndex;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return InteractiveMessage
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Message|null
     */
    public function getOriginalMessage(): ?Message
    {
        return $this->originalMessage;
    }

    /**
     * @param Message $originalMessage
     *
     * @return InteractiveMessage
     */
    public function setOriginalMessage(Message $originalMessage): self
    {
        $this->originalMessage = $originalMessage;

        return $this;
    }

    /**
     * @return string
     */
    public function getResponseURL(): string
    {
        return $this->responseURL;
    }

    /**
     * @param string $responseURL
     *
     * @return InteractiveMessage
     */
    public function setResponseURL(string $responseURL): self
    {
        $this->responseURL = $responseURL;

        return $this;
    }
}
