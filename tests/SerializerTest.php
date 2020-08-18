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

        $container        = self::$kernel->getContainer();
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

        $actual   = $this->serializer->serialize($message, 'json');
        $expected = <<<END
{"text":"MessageText","attachments":[{"fallback":"Fallback","callback_id":"create_content_1","color":"Color","pretext":"Pretext","author_name":"Plopix","author_link":"http:\/\/plopix.net","author_icon":"https:\/\/avatars2.githubusercontent.com\/u\/313532","title":"Title","title_link":"http:\/\/titlelink.com","text":"AttachmentText","fields":[{"title":"ShortFieldTitle","value":"ShortFieldValue","short":true},{"title":"LongFieldTitle","value":"LongFieldValue","short":false}],"image_url":"http:\/\/imgurl.com","thumb_url":"http:\/\/thumburl.com","footer":"Footer","footer_icon":"http:\/\/footer.ico","ts":1518308103,"actions":[{"type":"button","name":"action","text":"Hide it","value":"hide","confirm":{"title":"Hide it","text":"Do you really want to trash it?"},"style":"default"},{"type":"button","name":"action","text":"Trash it","value":"trash","style":"danger"}]}]}
END;

        $this->assertEquals($expected, $actual, 'JSON Serialization is incorrect.');

        return $message;
    }

    /**
     * @depends testSerialization
     */
    public function testDeSerialization(Message $message): void
    {
        $received = <<<END
{"type":"interactive_message","actions":[{"name":"action","type":"button","value":"trash"}],"callback_id":"create_content_1","team":{"id":"T02NHT8E7","domain":"novactive"},"channel":{"id":"C969BKHH9","name":"novaezslackbundle"},"user":{"id":"U7KAANP8S","name":"plopix"},"action_ts":"1518408524.462839","message_ts":"1518408508.000120","attachment_id":"1","token":"EO6DzTy53tmYVndBn2uow6PS","is_app_unfurl":false,"original_message":{"text":"MessageText","bot_id":"B97E0DYF4","attachments":[{"author_name":"Plopix","callback_id":"create_content_1","fallback":"Fallback","text":"AttachmentText","pretext":"Pretext","title":"Title","footer":"Footer","id":1,"title_link":"http:\\/\\/titlelink.com","author_link":"http:\\/\\/plopix.net","author_icon":"https:\\/\\/avatars2.githubusercontent.com\\/u\\/313532","ts":1518308103,"fields":[{"title":"ShortFieldTitle","value":"ShortFieldValue","short":true},{"title":"LongFieldTitle","value":"LongFieldValue","short":false}],"actions":[{"id":"1","name":"action","text":"Hide it","type":"button","value":"hide","style":"default","confirm":{"text":"Do you really want to trash it?","title":"Hide it","ok_text":"Okay","dismiss_text":"Cancel"}},{"id":"2","name":"action","text":"Trash it","type":"button","value":"trash","style":"danger"}]}],"type":"message","subtype":"bot_message","ts":"1518408508.000120"},"response_url":"https:\\/\\/hooks.slack.com\\/actions\\/T02NHT8E7\\/313496070642\\/2Psx8CpJyoN8ADRrwRe8GYPI","trigger_id":"314349496102.2765926483.99f522b133ee5dacf24f552ba81e32b1"}
END;

        $messageInteractive = $this->serializer->deserialize($received, InteractiveMessage::class, 'json');
        /** @var InteractiveMessage $messageInteractive */
        $originalMessage = $messageInteractive->getOriginalMessage();
        $this->assertEquals($originalMessage->getText(), $message->getText());

        $attachment = $message->getAttachments()[0];
        $attach     = $originalMessage->getAttachments()[0];

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
