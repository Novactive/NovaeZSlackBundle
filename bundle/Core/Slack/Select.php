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
use RuntimeException;

/**
 * Class Select.
 */
class Select extends Action
{
    /**
     * A collection of option fields.
     *
     * @var Option[]
     * @Serializer\Type("array<Novactive\Bundle\eZSlackBundle\Core\Slack\Option>")
     */
    private $options;

    /**
     * {@inheritdoc}
     */
    public function getObjectType(): string
    {
        return 'select';
    }

    /**
     * @return Option[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param Option[] $options
     *
     * @return Select
     */
    public function setOptions(array $options): self
    {
        foreach ($options as $option) {
            if (!$option instanceof Option) {
                throw new RuntimeException(sprintf('Provided Option is not an %s', Option::class));
            }
        }
        $this->options = $options;

        return $this;
    }

    /**
     * @return Select
     */
    public function addOption(Option $option): self
    {
        $this->options[] = $option;

        return $this;
    }
}
