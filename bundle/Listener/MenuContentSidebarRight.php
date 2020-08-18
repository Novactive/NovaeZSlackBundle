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

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;

/**
 * Class MenuContentSidebarRight.
 */
class MenuContentSidebarRight
{
    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu    = $event->getMenu();
        $options = $event->getOptions(); // options passed from the context (i.e. Content item in Content View)
        $menu->addChild(
            'novaezslack_shareonslack',
            [
                'label'           => 'admin.share.on.slack',
                'route'           => 'novactive_ezslack_callback_shareonslack',
                'routeParameters' => ['locationId' => $options['location']->id],
                'extras'          => [
                    'translation_domain' => 'slack',
                ],
            ]
        );
    }
}
