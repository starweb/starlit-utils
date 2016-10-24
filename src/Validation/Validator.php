<?php
/**
 * Utils.
 *
 * @copyright Copyright (c) 2016 Starweb / Ehandelslogik i Lund AB
 * @license   BSD 3-Clause
 */

namespace Starlit\Utils\Validation;

use Symfony\Component\Translation\TranslatorInterface as SymfonyTranslatorInterface;

/**
 * A crude validator.
 */
class Validator
{
    /**
     * @var array
     */
    protected static $validRuleProperties = [
        'required',
        'nonEmpty',
        'min',
        'max',
        'minLength',
        'maxLength',
        'regexp',
        'regexpExpl',
        'email',
        'textKey',
        'date',
        'dateTime',
        'custom'
    ];

    /**
     * @var ValidatorTranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    protected $validatedData = [];

    /**
     * @var array
     */
    protected $fieldsRuleProperties = [];

    /**
     * Constructor.
     *
     * @param array                                                        $fieldsRuleProperties
     * @param ValidatorTranslatorInterface|SymfonyTranslatorInterface|null $translator
     */
    public function __construct(array $fieldsRuleProperties = [], $translator = null)
    {
        $this->fieldsRuleProperties = $fieldsRuleProperties;

        if (!$translator) {
            $this->translator = new DefaultValidatorTranslator();
        } elseif ($translator instanceof ValidatorTranslatorInterface) {
            $this->translator = $translator;
        } elseif ($translator instanceof SymfonyTranslatorInterface) {
            $this->translator = new SymfonyTranslatorProxy($translator);
        } else {
            throw new \InvalidArgumentException("Translator must implement ValidatorTranslatorInterface");
        }
    }

    /**
     * @param array $newFieldsRuleProperties
     */
    public function addFieldsRuleProperties(array $newFieldsRuleProperties)
    {
        foreach ($newFieldsRuleProperties as $fieldName => $newRuleProperties) {
            if (isset($this->fieldsRuleProperties[$fieldName])) {
                $this->fieldsRuleProperties[$fieldName] = array_merge(
                    $this->fieldsRuleProperties[$fieldName],
                    $newRuleProperties
                );
            } else {
                $this->fieldsRuleProperties[$fieldName] = $newRuleProperties;
            }
        }
    }

    /**
     * @param string $fieldName
     */
    public function removeFieldRuleProperties($fieldName)
    {
        unset($this->fieldsRuleProperties[$fieldName]);
    }

    /**
     * @param string $fieldName
     * @return array
     */
    public function getFieldRuleProperties($fieldName)
    {
        if (isset($this->fieldsRuleProperties[$fieldName])) {
            return $this->fieldsRuleProperties[$fieldName];
        }

        return [];
    }

    /**
     * Validate and return error messages (if any).
     *
     * @param array|null $data The data (e.g. from a form post) to be validated and set
     * @return array An array with all (if any) of error messages
     */
    public function validate($data)
    {
        $errorMsgs = [];
        foreach ($this->fieldsRuleProperties as $fieldName => $ruleProperties) {
            // Get value to validate
            if (isset($data[$fieldName])) {
                $value = $data[$fieldName];

                // Trim all values unless explicitly set to not
                if (is_string($value) && (!isset($ruleProperties['trim']) || $ruleProperties['trim'] === true)) {
                    $value = trim($value);
                }
            // Don't validate empty values that are not set and not required
            } elseif (empty($ruleProperties['required']) && empty($ruleProperties['nonEmpty'])) {
                continue;
            } else {
                // Empty string as default (not null because that adds complexity to checks)
                $value = '';
            }

            // Validate value against each of the fields' rules
            $errorMsg = $this->validateValue($value, $ruleProperties);

            // If field has any error messages, add to error array, otherwise add value to validated data
            if ($errorMsg) {
                $errorMsgs[$fieldName] = $errorMsg;
            } else {
                $this->validatedData[$fieldName] = $value;
            }
        }

        return $errorMsgs;
    }

    /**
     * @param mixed $value
     * @param array $ruleProperties
     * @return string|null
     */
    public function validateValue($value, array $ruleProperties)
    {
        // Field name
        if (isset($ruleProperties['textKey'])) {
            $fieldName = $this->translator->trans($ruleProperties['textKey']);
            unset($ruleProperties['textKey']);
        } else {
            $fieldName = $this->translator->trans('errorTheNoneSpecifiedField');
        }

        // Unset rule properties that are not rules
        $rules = $ruleProperties;
        unset($rules['textKey']);
        unset($rules['regexpExpl']);
        unset($rules['trim']);

        $isValueSet = (is_scalar($value) || is_null($value)) && ((string) $value) !== '';

        foreach ($rules as $rule => $ruleContents) {
            $errorMsg = null;

            switch ($rule) {
                case 'required':
                    if (!is_bool($ruleContents)) {
                        throw new \InvalidArgumentException("Invalid required validation rule[{$rule}]");
                    }

                    if ($ruleContents && !$isValueSet) {
                        $errorMsg = $this->translator->trans('errorFieldXIsRequired', ['%field%' => $fieldName]);
                    }

                    break;
                case 'nonEmpty':
                    if (!is_bool($ruleContents)) {
                        throw new \InvalidArgumentException("Invalid nonEmpty validation rule[{$rule}]");
                    }

                    if ($ruleContents && empty($value)) {
                        $errorMsg = $this->translator->trans('errorFieldXIsRequired', ['%field%' => $fieldName]);
                    }

                    break;
                case 'min':
                    if (!is_numeric($ruleContents)) {
                        throw new \InvalidArgumentException("Invalid min validation rule[{$ruleContents}]");
                    }

                    if ($isValueSet && (!is_numeric($value) || $value < $ruleContents)) {
                        $fieldName =
                        $errorMsg = $this->translator->trans(
                            'errorFieldMustBeMinNumber',
                            ['%field%' => $fieldName, '%number%' => $ruleContents]
                        );
                    }

                    break;
                case 'max':
                    if (!is_numeric($ruleContents)) {
                        throw new \InvalidArgumentException("Invalid max validation rule[{$ruleContents}]");
                    }

                    if ($isValueSet && (!is_numeric($value) || $value > $ruleContents)) {
                        $errorMsg = $this->translator->trans(
                            'errorFieldMustBeMaxNumber',
                            [
                                '%field%'  => $fieldName,
                                '%number%' => $ruleContents
                            ]
                        );
                    }

                    break;
                case 'minLength':
                    if (!is_int($ruleContents) || $ruleContents < 1) {
                        throw new \InvalidArgumentException("Invalid min length validation rule[{$ruleContents}]");
                    }

                    if ($isValueSet && mb_strlen($value) < $ruleContents) {
                        $errorMsg = $this->translator->trans(
                            'errorFieldMustBeMinXLength',
                            ['%field%' => $fieldName, '%numberOf%' => $ruleContents]
                        );
                    }

                    break;
                case 'maxLength':
                    if (!is_int($ruleContents) || $ruleContents < 1) {
                        throw new \InvalidArgumentException("Invalid max length validation rule[{$ruleContents}]");
                    }

                    if ($isValueSet && mb_strlen($value) > $ruleContents) {
                        $errorMsg = $this->translator->trans(
                            'errorFieldMustBeMaxXLength',
                            ['%field%' => $fieldName, '%numberOf%' => $ruleContents]
                        );
                    }

                    break;
                case 'length':
                    if (!is_int($ruleContents) || $ruleContents < 1) {
                        throw new \InvalidArgumentException("Invalid length validation rule[{$ruleContents}]");
                    }

                    if ($isValueSet && mb_strlen($value) !== $ruleContents) {
                        $errorMsg = $this->translator->trans(
                            'errorFieldMustBeXLength',
                            ['%field%' => $fieldName, '%numberOf%' => $ruleContents]
                        );
                    }

                    break;
                case 'regexp':
                    if (!$ruleContents) {
                        throw new \InvalidArgumentException("Invalid regexp validation rule[{$ruleContents}]");
                    }

                    if ($isValueSet && !preg_match('/' . $ruleContents . '/', $value)) {
                        $errorMsg = $this->translator->trans(
                            'errorFieldInvalidFormat',
                            ['%field%' => $fieldName]
                        );

                        if (isset($ruleProperties['regexpExpl'])) {
                            $errorMsg .= $this->translator->trans(
                                'errorFieldValidCharactersAreX',
                                ['%characters%' => $ruleProperties['regexpExpl']]
                            );
                        }
                    }

                    break;
                case 'email':
                    if (!is_bool($ruleContents)) {
                        throw new \InvalidArgumentException("Invalid email validation rule[{$rule}]");
                    }

                    if ($ruleContents && $isValueSet && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errorMsg = $this->translator->trans('errorInvalidEmail');
                    }

                    break;
                case 'date':
                    // No break
                case 'dateTime':
                    if (!is_bool($ruleContents)) {
                        throw new \InvalidArgumentException("Invalid date validation rule[{$rule}]");
                    }

                    if ($ruleContents && $isValueSet) {
                        $isValueOk = function ($format) use ($value) {
                            return (\DateTime::createFromFormat($format, $value) !== false
                                && !\DateTime::getLastErrors()["warning_count"]
                                && !\DateTime::getLastErrors()["error_count"]);
                        };

                        // Allow datetime with and without seconds
                        if (($rule === 'date' && !$isValueOk('Y-m-d'))
                            || ($rule === 'dateTime' && !$isValueOk('Y-m-d H:i')
                                && !$isValueOk('Y-m-d H:i:s'))
                        ) {
                            $errorMsg = $this->translator->trans(
                                ($rule === 'date') ? 'errorInvalidDate' : 'errorInvalidDateTime'
                            );
                        }
                    }

                    break;
                case 'custom':
                    if (!is_callable($ruleContents)) {
                        throw new \InvalidArgumentException("Invalid custom validation rule[{$rule}]");
                    }

                    $errorMsg = $ruleContents($value);

                    break;
                default:
                    throw new \InvalidArgumentException("Unknown validation rule[{$rule}]");
            }

            if ($errorMsg) {
                return $errorMsg;
            }
        }

        return null;
    }

    /**
     * @param string $key
     * @param mixed  $default
     * @return array|mixed
     */
    public function getValidatedData($key = null, $default = null)
    {
        if ($key !== null) {
            return isset($this->validatedData[$key]) ? $this->validatedData[$key] : $default;
        } else {
            return $this->validatedData;
        }
    }

    /**
     * @return array
     */
    public static function getValidRuleProperties()
    {
        return static::$validRuleProperties;
    }
}
