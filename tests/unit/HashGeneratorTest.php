<?php

use App\Libraries\HashGenerator;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class HashGeneratorTest extends CIUnitTestCase
{
    public function testGeneratesEverySupportedHash(): void
    {
        $generator = new HashGenerator();
        $hashes    = $generator->generate('hello');

        $this->assertSame(array_keys($generator->algorithms()), array_keys($hashes));
        $this->assertSame('5d41402abc4b2a76b9719d911017c592', $hashes['md5']);
        $this->assertSame('aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d', $hashes['sha1']);
        $this->assertSame(hash('sha256', 'hello'), $hashes['sha256']);
        $this->assertSame(hash('sha384', 'hello'), $hashes['sha384']);
        $this->assertSame(hash('sha512', 'hello'), $hashes['sha512']);
    }

    public function testPreservesWhitespaceInTheInput(): void
    {
        $generator = new HashGenerator();

        $this->assertNotSame(
            $generator->generate('hello')['sha256'],
            $generator->generate(' hello ')['sha256'],
        );
    }
}
