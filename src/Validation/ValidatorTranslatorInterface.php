<?php
/**
 * Utils.
 *
 * @copyright Copyright (c) 2016 Starweb AB
 * @license   BSD 3-Clause
 */

namespace Starlit\Utils\Validation;

/**
 */
interface ValidatorTranslatorInterface
{
    /**
     * Translates the given message.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param array       $parameters An array of parameters for the message
     * @return string The translated string
     */
    public function trans($id, array $parameters = array());
}