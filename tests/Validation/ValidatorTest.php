<?php

namespace Starlit\Utils\Validation;

use Symfony\Component\Translation\TranslatorInterface;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Validator
     */
    private $validator;

    public function setUp()
    {
        $rules = [];
        $rules['someField']['minLength'] = 5;
        $rules['nonRequiredField'] = [];

        $this->validator = new Validator($rules);
    }

    public function testOtherValidConstructions()
    {
        new Validator();

        $validatorTranslatorMock = $this->createMock(ValidatorTranslatorInterface::class);
        new Validator([], $validatorTranslatorMock);

        $symfonyTranslatorMock = $this->createMock(TranslatorInterface::class);
        new Validator([], $symfonyTranslatorMock);
    }

    public function testInvalidConstruction()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Validator([], 123);
    }

    public function testAddFieldsRuleProperties()
    {
        $newFieldRules = [
            'someField' => ['maxLength' => 8],  // Will be merged
            'otherField' => ['min' => 2], // New
        ];

        $this->validator->addFieldsRuleProperties($newFieldRules);

        $this->assertNotEmpty($this->validator->getFieldRuleProperties('someField'));
        $this->assertNotEmpty($this->validator->getFieldRuleProperties('otherField'));
    }

    public function testRemoveFieldRuleProperties()
    {
        $newFieldRules = [
            'fieldToBeRemoved' => ['required' => true],
        ];

        $this->validator->addFieldsRuleProperties($newFieldRules);
        $this->assertNotEmpty($this->validator->getFieldRuleProperties('fieldToBeRemoved'));
        $this->validator->removeFieldRuleProperties('fieldToBeRemoved');
        $this->assertEmpty($this->validator->getFieldRuleProperties('fieldToBeRemoved'));
    }

    public function testValidateSuccess()
    {
        $data = [
            'someField' => '12345',
        ];

        $errorMsgs = $this->validator->validate($data);
        $this->assertEmpty($errorMsgs);
    }

    public function testGetValidatedData()
    {
        $data = [
            'someField' => ' trimmed ',
        ];

        $this->validator->validate($data);

        $this->assertCount(1, $this->validator->getValidatedData());
        $this->assertEquals('trimmed', $this->validator->getValidatedData()['someField']);
        $this->assertEquals('trimmed', $this->validator->getValidatedData('someField'));
    }

    public function testValidateValue()
    {
        $newFieldRules = [
            'otherField' => ['required' => true],
            'otherField2' => ['min' => 5],
            'otherField3' => ['max' => 5],
            'otherField4' => ['minLength' => 5],
            'otherField5' => ['maxLength' => 5],
            'otherField6' => ['regexp' => '[A-Z]', 'regexpExpl' => 'A-Z'],
            'otherField7' => ['email' => true],
            'otherField8' => ['custom' => function ($v) {
                return ($v != 5) ? 'error' : '';
            }],
        ];
        $this->validator->addFieldsRuleProperties($newFieldRules);

        $data = [
            'otherField2' => 4,
            'otherField3' => 6,
            'otherField4' => 's',
            'otherField5' => '123456',
            'otherField6' => 5,
            'otherField7' => 's',
            'otherField8' => 4,
        ];
        $errorMsgs = $this->validator->validate($data);
        $this->assertArrayHasKey('otherField', $errorMsgs);
        $this->assertArrayHasKey('otherField2', $errorMsgs);
        $this->assertArrayHasKey('otherField3', $errorMsgs);
        $this->assertArrayHasKey('otherField4', $errorMsgs);
        $this->assertArrayHasKey('otherField5', $errorMsgs);
        $this->assertArrayHasKey('otherField6', $errorMsgs);
        $this->assertArrayHasKey('otherField7', $errorMsgs);
        $this->assertArrayHasKey('otherField8', $errorMsgs);
    }

    public function testValidateValueRulesReturnsNoErrorOnEmptyValue()
    {
        $ruleProperties = [
            'min' => 5,
            'max' => 10,
            'minLength' => 1,
            'maxLength' => 2,
            'length' => 2,
            'regexp' => '[0-9]',
            'email' => true,
            'date' => true,
            'dateTime' => true,
        ];

        $errorMsgs = $this->validator->validateValue(null, $ruleProperties);
        $this->assertEmpty($errorMsgs);
    }

    public function testValidateRequiredValueReturnsErrorMsg()
    {
        $errorMsgs = $this->validator->validateValue(null, ['required' => true]);
        $this->assertNotEmpty($errorMsgs);
        $errorMsgs = $this->validator->validateValue('', ['required' => true]);
        $this->assertNotEmpty($errorMsgs);
    }


    public function testValidateRequiredValueDoesNotReturnErrorMsg()
    {
        $errorMsgs = $this->validator->validateValue(null, ['required' => false]);
        $this->assertEmpty($errorMsgs);
        $errorMsgs = $this->validator->validateValue('0', ['required' => true]);
        $this->assertEmpty($errorMsgs);
        $errorMsgs = $this->validator->validateValue(0, ['required' => true]);
        $this->assertEmpty($errorMsgs);
    }


    public function testValidateNonEmptyValueReturnsErrorMsg()
    {
        $errorMsgs = $this->validator->validateValue(null, ['nonEmpty' => true]);
        $this->assertNotEmpty($errorMsgs);
        $errorMsgs = $this->validator->validateValue('', ['nonEmpty' => true]);
        $this->assertNotEmpty($errorMsgs);
        $errorMsgs = $this->validator->validateValue('0', ['nonEmpty' => true]);
        $this->assertNotEmpty($errorMsgs);
        $errorMsgs = $this->validator->validateValue(0, ['nonEmpty' => true]);
        $this->assertNotEmpty($errorMsgs);
    }


    public function testValidateNonEmptyValueDoesNotReturnErrorMsg()
    {
        $errorMsgs = $this->validator->validateValue(null, ['nonEmpty' => false]);
        $this->assertEmpty($errorMsgs);
        $errorMsgs = $this->validator->validateValue(true, ['nonEmpty' => true]);
        $this->assertEmpty($errorMsgs);
        $errorMsgs = $this->validator->validateValue('abc', ['nonEmpty' => true]);
        $this->assertEmpty($errorMsgs);
    }

    public function testInvalidRuleRequired()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->validateValue(null, ['required' =>  's']);
    }

    public function testInvalidRuleNonEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->validateValue(null, ['nonEmpty' =>  's']);
    }

    public function testRuleNonEmpty()
    {
        $errorMsgs = $this->validator->validateValue(null, ['nonEmpty' =>  true]);
        $this->assertNotEmpty($errorMsgs);

        $errorMsgs = $this->validator->validateValue(0, ['nonEmpty' =>  true]);
        $this->assertNotEmpty($errorMsgs);

        $errorMsgs = $this->validator->validateValue(null, ['nonEmpty' =>  false]);
        $this->assertEmpty($errorMsgs);
    }

    public function testInvalidRuleMin()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->validateValue(null, ['min' => 's']);
    }

    public function testInvalidRuleMax()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->validateValue(null, ['max' => 's']);
    }

    public function testInvalidRuleMinLength()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->validateValue(null, ['minLength' => 's']);
    }

    public function testInvalidRuleMaxLength()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->validateValue(null, ['maxLength' => 's']);
    }

    public function testInvalidRuleLength()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->validateValue(null, ['length' => 's']);
    }

    public function testRuleLength()
    {
        $errorMsgs = $this->validator->validateValue('asd', ['length' =>  5]);
        $this->assertNotEmpty($errorMsgs);

        $errorMsgs = $this->validator->validateValue('asd', ['length' =>  3]);
        $this->assertEmpty($errorMsgs);

        $errorMsgs = $this->validator->validateValue('', ['length' =>  5]);
        $this->assertEmpty($errorMsgs);
    }

    public function testInvalidRuleRegexp()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->validateValue(null, ['regexp' => null]);
    }

    public function testInvalidRuleEmail()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->validateValue(null, ['email' => 's']);
    }

    public function testInvalidEmailValueType()
    {
        $errorMsgs = $this->validator->validateValue(['abc' => 1], ['email' => true]);
        // Invalid value type should count in value not at all, ie. no error message
        // without required or nonEmpty rule
        $this->assertEmpty($errorMsgs);
    }

    public function testInvalidDateTimeRule()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->validateValue(null, ['dateTime' => 's']);
    }

    public function testValidDate()
    {
        $errorMsgs = $this->validator->validateValue('2015-10-21', ['date' => true]);
        $this->assertEmpty($errorMsgs);

        $errorMsgs = $this->validator->validateValue(null, ['date' => true]);
        $this->assertEmpty($errorMsgs);
    }

    public function testInvalidDate()
    {
        $errorMsgs = $this->validator->validateValue('2000-42-00', ['date' => true]);
        $this->assertNotEmpty($errorMsgs);
    }

    public function testValidDateTime()
    {
        $errorMsgs = $this->validator->validateValue('2015-10-21 15:00', ['dateTime' => true]);
        $this->assertEmpty($errorMsgs);

        $errorMsgs = $this->validator->validateValue('2015-10-21 15:00:00', ['dateTime' => true]);
        $this->assertEmpty($errorMsgs);

        $errorMsgs = $this->validator->validateValue(null, ['dateTime' => true]);
        $this->assertEmpty($errorMsgs);
    }

    public function testInvalidDateTime()
    {
        $errorMsgs = $this->validator->validateValue('2015-10-21 99:99', ['dateTime' => true]);
        $this->assertNotEmpty($errorMsgs);
    }

    public function testInvalidRuleCustom()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->validateValue(null, ['custom' => 's']);
    }

    public function testInvalidRuleNone()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->validateValue(null, ['nonExistantRule' => null]);
    }

    public function testTextKeyErrorMsg()
    {
        $mockTranslator = $this->createMock(TranslatorInterface::class);

        $mockTranslator->expects($this->exactly(2))
            ->method('trans')
            ->withConsecutive(['testField'], ['errorFieldXIsRequired'])
            ->will($this->onConsecutiveCalls('Test field', 'Test field required.'));

        $validator = new Validator([], $mockTranslator);

        $errorMsgs = $validator->validateValue(null, ['required' =>  true, 'textKey' => 'testField']);
        $this->assertEquals('Test field required.', $errorMsgs);
    }

    public function testGetValidRuleProperties()
    {
        $this->assertContains('required', $this->validator->getValidRuleProperties());
    }
}
