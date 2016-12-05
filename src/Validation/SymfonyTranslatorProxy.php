<?php
/**
 * Utils.
 *
 * @copyright Copyright (c) 2016 Starweb AB
 * @license   BSD 3-Clause
 */

namespace Starlit\Utils\Validation;

use Symfony\Component\Translation\TranslatorInterface as SymfonyTranslatorInterface;

/**
 */
class SymfonyTranslatorProxy implements ValidatorTranslatorInterface
{
    /**
     * @var SymfonyTranslatorInterface
     */
    protected $symfonyTranslator;

    /**
     * @param SymfonyTranslatorInterface $symfonyTranslator
     */
    public function __construct(SymfonyTranslatorInterface  $symfonyTranslator)
    {
        $this->symfonyTranslator = $symfonyTranslator;
    }
    
    /**
     * {@inheritdoc}
     */
    public function trans($id, array $parameters = [])
    {
        return $this->symfonyTranslator->trans($id, $parameters);
    }
}
