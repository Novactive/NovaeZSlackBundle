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

namespace Novactive\Bundle\eZSlackBundle\Listener;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class Response.
 *
 * Tricks to inject js in the login page.. which is not extensible yet :( And does not comply with the doc...
 */
class Response
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ClientRegistry
     */
    private $clientRegistry;

    /**
     * Response constructor.
     */
    public function __construct(RouterInterface $router, ClientRegistry $clientRegistry)
    {
        $this->router = $router;
        $this->clientRegistry = $clientRegistry;
    }

    public function onKernelResponse(FilterResponseEvent $event): void
    {
        $response = $event->getResponse();
        $connectURL = $this->clientRegistry->getClient('slack')->redirect(
            ['team:read', 'users.profile:read', 'users:read', 'users:read.email']
        )->getTargetUrl();
        $slackAssetPrefix = 'https://platform.slack-edge.com';
        $code = <<<END
<script type="text/javascript">
$(function () {
"use strict";
var slackButton = $("<a/>").attr({href:"{$connectURL}", class: "", style:"margin-top:0.95rem; display:inline-block"});
var slackImage = $("<img/>").attr({
src: "{$slackAssetPrefix}/img/sign_in_with_slack.png",
srcset: "{$slackAssetPrefix}/img/sign_in_with_slack.png 1x, {$slackAssetPrefix}/img/sign_in_with_slack@2x.png 2x",
height: 40,
width: 172
});
slackButton.append(slackImage);
slackButton.insertBefore(".ez-login__form-wrapper form button[type=submit]:first");
});
</script>
END;
        $response->setContent(str_replace('</body>', "{$code}</body>", $response->getContent()));
    }
}
