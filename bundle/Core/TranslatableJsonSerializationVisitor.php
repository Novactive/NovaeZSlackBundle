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

namespace Novactive\Bundle\eZSlackBundle\Core;

use JMS\Serializer\Context;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class TranslatableJsonSerializationVisitor.
 */
class TranslatableJsonSerializationVisitor extends JsonSerializationVisitor
{
    /**
     * @var TranslatorInterface;
     */
    private $translator;

    /**
     * {@inheritdoc}
     */
    public function visitString($data, array $type, Context $context)
    {
        if ('string' !== $type['name']) {
            return $data;
        }

        if (0 === \count($type['params'])) {
            return $data;
        }

        foreach ($type['params'] as $param) {
            if ('translatable' === $param['name']) {
                if ('_t:' === substr($data, 0, 3)) {
                    $data = $this->translator->trans(substr($data, 3), [], 'slack');
                }
            }
        }

        return $data;
    }

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }
}
