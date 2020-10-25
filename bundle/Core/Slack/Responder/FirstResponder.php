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

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\Responder;

use Novactive\Bundle\eZSlackBundle\Core\Slack\Message;
use RuntimeException;

class FirstResponder
{
    /**
     * Array with all responders.
     *
     * @var Responder[]
     */
    protected $responders;

    /**
     * FirstResponder constructor.
     */
    public function __construct(iterable $responders)
    {
        foreach ($responders as $responder) {
            $this->addResponder($responder);
        }
    }

    /**
     * Add a responder.
     */
    public function addResponder(ResponderInterface $responder): void
    {
        $this->responders[$responder->getName()] = $responder;
    }

    /**
     * Get the Responder.
     *
     * @param string $name
     */
    public function getResponder($name): Responder
    {
        $name = strtolower($name);
        if (isset($this->responders[$name])) {
            return $this->responders[$name];
        }

        throw new RuntimeException("No Responder with the name '{$name}''.");
    }

    public function __invoke(string $args): ?Message
    {
        $argsArray = explode(' ', $args);
        $name = array_shift($argsArray);
        $responder = $this->getResponder($name);

        return $responder->respond($this->parseArgs($argsArray));
    }

    /**
     * Thank you here: https://gist.github.com/jadb/3949954.
     *
     * @author              Patrick Fisher <patrick@pwfisher.com>
     * @author              Sebastien Morel <morel.seb@gmail.com>
     *
     * @since               August 21, 2009
     * @see                 http://www.php.net/manual/en/features.commandline.php
     *                      #81042 function arguments($argv) by technorati at gmail dot com, 12-Feb-2008
     *                      #78651 function getArgs($args) by B Crawford, 22-Oct-2007
     * @see                 Adaptation php7.2, best practices etc. (2/2018)
     */
    private function parseArgs($argv): array
    {
        $o = [];
        foreach ($argv as $a) {
            if ('--' === substr($a, 0, 2)) {
                $equal = strpos($a, '=');
                if (false !== $equal) {
                    $o[substr($a, 2, $equal - 2)] = substr($a, $equal + 1);
                } else {
                    $k = substr($a, 2);
                    if (!isset($o[$k])) {
                        $o[$k] = true;
                    }
                }
            } else {
                if ('-' === $a[0]) {
                    if ('=' === $a[2]) {
                        $o[$a[1]] = substr($a, 3);
                    } else {
                        foreach (str_split(substr($a, 1)) as $k) {
                            if (!isset($o[$k])) {
                                $o[$k] = true;
                            }
                        }
                    }
                } else {
                    $o[] = $a;
                }
            }
        }

        return $o;
    }
}
