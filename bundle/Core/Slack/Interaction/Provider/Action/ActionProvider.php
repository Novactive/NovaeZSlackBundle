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

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Action;

use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\AliasTrait;

/**
 * Class ActionProvider.
 */
abstract class ActionProvider implements ActionProviderInterface
{
    use AliasTrait;

    /**
     * {@inheritdoc}
     */
    public function supports($alias): bool
    {
        return substr($alias, 0, \strlen($this->getAlias())) === $this->getAlias();
    }
}
