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

namespace Novactive\Bundle\eZSlackBundle\Tests;

use JMS\Serializer\Serializer;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Author;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Button;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Confirmation;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Field;
use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Message;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SerializerTest extends KernelTestCase
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * Set up the Test.
     */
    public function setUp()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();
        $this->serializer = $container->get('jms_serializer');
    }

    public function testSerialization(): Message
    {
        $message = new Message();
        $message->setText('MessageText');
        $attach = new Attachment();
        $attach->setText('AttachmentText');
        $attach->setColor('Color');
        $attach->setTimestamp(1518308103);
        $attach->setFallback('Fallback');
        $attach->setTitle('Title');
        $attach->setTitleLink('http://titlelink.com');
        $attach->setThumbURL('http://thumburl.com');
        $attach->setImageURL('http://imgurl.com');
        $attach->setPretext('Pretext');
        $attach->setFooter('Footer');
        $attach->setFooterIcon('http://footer.ico');
        $author = new Author();
        $author->setLink('http://plopix.net');
        $author->setName('Plopix');
        $author->setIcon('https://avatars2.githubusercontent.com/u/313532');
        $message->addAttachment($attach);
        $attach->setAuthor($author);
        $attach->addField(new Field('ShortFieldTitle', 'ShortFieldValue'));
        $attach->addField(new Field('LongFieldTitle', 'LongFieldValue', false));
        $hideButton = new Button('action', 'Hide it', 'hide');
        $hideButton->setStyle(Button::DEFAULT_STYLE);
        $moveToTrashButton = new Button('action', 'Trash it', 'trash');
        $moveToTrashButton->setStyle(Button::DANGER_STYLE);
        $confirmation = new Confirmation('Do you really want to trash it?');
        $hideButton->setConfirmation($confirmation);
        $attach->setActions([$hideButton, $moveToTrashButton]);
        $attach->setCallbackId('create_content_1');

        $actual = $this->serializer->serialize($message, 'json');
        $expected = file_get_contents(__DIR__.'/../serialized1.json');
        $this->assertEquals($expected, $actual, 'JSON Serialization is incorrect.');

        return $message;
    }

    /**
     * @depends testSerialization
     */
    public function testDeSerialization(Message $message): void
    {
        $received = file_get_contents(__DIR__.'/../serialized2.json');
        $messageInteractive = $this->serializer->deserialize($received, InteractiveMessage::class, 'json');
        /** @var InteractiveMessage $messageInteractive */
        $originalMessage = $messageInteractive->getOriginalMessage();
        $this->assertEquals($originalMessage->getText(), $message->getText());

        $attachment = $message->getAttachments()[0];
        $attach = $originalMessage->getAttachments()[0];

        // Not provided back
        $attachment->setAuthor(null);
        $attachment->setColor(null);
        $attachment->setImageURL(null);
        $attachment->setThumbURL(null);
        $attachment->setFooterIcon(null);
        $attachment->setActions([]);
        $attach->setActions([]);

        $this->assertEquals($attachment, $attach);
    }
}
