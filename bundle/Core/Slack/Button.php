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
 * Class Button.
 */
class Button extends Action
{
    /**
     * Used only with message buttons, this decorates buttons with extra visual importance, which is especially useful
     * when providing logical default action or highlighting a destructive activity.
     *    default — Yes, it's the default. Buttons will look simple.
     *    primary — Use this sparingly, when the button represents a key action to accomplish. You should probably only
     *              ever have one primary button within a set.
     *    danger —  Use this when the consequence of the button click will result in the destruction of something,
     *              like a piece of data stored on your servers. Use even more sparingly than primary.
     *
     * @var string
     * @Serializer\Type("string")
     */
    private $style;

    public const DEFAULT_STYLE = 'default';
    public const PRIMARY_STYLE = 'primary';
    public const DANGER_STYLE  = 'danger';

    /**
     * @var array
     */
    private const STYLES = [self::DEFAULT_STYLE, self::PRIMARY_STYLE, self::DANGER_STYLE];

    /**
     * Button constructor.
     *
     * @param string $name
     * @param string $text
     * @param string $value
     */
    public function __construct(string $name, string $text, string $value)
    {
        parent::__construct($name, $text, $value);
        $this->style = self::DEFAULT_STYLE;
    }

    /**
     * @return string
     */
    public function getStyle(): string
    {
        return $this->style;
    }

    /**
     * @param string $style
     *
     * @return Button
     */
    public function setStyle(string $style): self
    {
        if (!\in_array($style, self::STYLES)) {
            throw new \RuntimeException("Style {$style} is not allowed.");
        }
        $this->style = $style;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectType(): string
    {
        return 'button';
    }
}
