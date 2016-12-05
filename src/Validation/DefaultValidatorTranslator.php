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
class DefaultValidatorTranslator implements ValidatorTranslatorInterface
{
    /**
     * @var array
     */
    protected $texts = [
        'errorTheNoneSpecifiedField' => 'Field',
        'errorFieldXIsRequired' => '%field% must be filled in.',
        'errorFieldMustBeMinNumber' => '%field% must be minimum %number%.',
        'errorFieldMustBeMaxNumber' => '%field% must be maximum %number%.',
        'errorFieldMustBeMinXLength' => '%field% must include minimum %numberOf% characters.',
        'errorFieldMustBeMaxXLength' => '%field% must include maximum %numberOf% characters.',
        'errorFieldMustBeXLength' => '%field% must consist of %numberOf% characters.',
        'errorFieldInvalidFormat' => '%field% has invalid format.',
        'errorFieldValidCharactersAreX' => 'Valid characters are %characters%.',
        'errorInvalidEmail' => 'Email address is invalid.',
        'errorInvalidDate' => 'Date is invalid.',
        'errorInvalidDateTime' => 'Date/time is invalid.',
    ];

    /**
     * {@inheritdoc}
     */
    public function trans($id, array $parameters = [])
    {
        if (isset($this->texts[$id])) {
            return strtr($this->texts[$id], $parameters);
        }

        return $id;
    }
}
