<?php

namespace Starlit\Utils\Validation;

class DefaultValidatorTranslatorTest extends \PHPUnit_Framework_TestCase
{
    public function testTrans()
    {
        $translator = new DefaultValidatorTranslator();
        $validator = new Validator([], $translator);

        $result = $validator->validateValue(null, ['required' => true]);
        $this->assertEquals('Field must be filled in.', $result);
    }

    public function testTransNonExistantTextId()
    {
        $translator = new DefaultValidatorTranslator();
        $this->assertEquals('lemmeltag', $translator->trans('lemmeltag'));
    }
}
