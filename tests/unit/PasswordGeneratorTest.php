<?php

use App\Libraries\PasswordGenerator;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class PasswordGeneratorTest extends CIUnitTestCase
{
    public function testStandardPasswordHasRequestedLengthAndEveryCharacterType(): void
    {
        $password = (new PasswordGenerator())->generate(32);

        $this->assertSame(32, strlen($password));
        $this->assertMatchesRegularExpression('/[a-z]/', $password);
        $this->assertMatchesRegularExpression('/[A-Z]/', $password);
        $this->assertMatchesRegularExpression('/[0-9]/', $password);
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $password);
    }

    public function testStrongPasswordAlsoContainsASymbol(): void
    {
        $password = (new PasswordGenerator())->generate(32, true);

        $this->assertSame(32, strlen($password));
        $this->assertMatchesRegularExpression('/[a-z]/', $password);
        $this->assertMatchesRegularExpression('/[A-Z]/', $password);
        $this->assertMatchesRegularExpression('/[0-9]/', $password);
        $this->assertNotFalse(strpbrk($password, '!@#$%^&*()-_=+[]{};:,.?'));
    }

    public function testRejectsLengthsOutsideTheSupportedRange(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new PasswordGenerator())->generate(PasswordGenerator::MIN_LENGTH - 1);
    }
}
