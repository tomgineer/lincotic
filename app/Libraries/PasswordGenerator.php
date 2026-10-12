<?php

namespace App\Libraries;

use InvalidArgumentException;

final class PasswordGenerator {

    public const int MIN_LENGTH = 5;
    public const int MAX_LENGTH = 128;

    private const string LOWERCASE = 'abcdefghijkmnopqrstuvwxyz';
    private const string UPPERCASE = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    private const string DIGITS    = '23456789';
    private const string SYMBOLS   = '!@#$%^&*()-_=+[]{};:,.?';

    /**
     * Generates a password with at least one character from every active pool.
     */
    public function generate(int $length = 8, bool $strong = false): string {
        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf(
                'Password length must be between %d and %d characters.',
                self::MIN_LENGTH,
                self::MAX_LENGTH,
            ));
        }

        $pools = [self::LOWERCASE, self::UPPERCASE, self::DIGITS];

        if ($strong) {
            $pools[] = self::SYMBOLS;
        }

        $characters = array_map(
            fn(string $pool): string => $this->randomCharacter($pool),
            $pools,
        );
        $pool = implode('', $pools);

        while (count($characters) < $length) {
            $characters[] = $this->randomCharacter($pool);
        }

        return implode('', $this->secureShuffle($characters));
    }

    private function randomCharacter(string $pool): string {
        return $pool[random_int(0, strlen($pool) - 1)];
    }

    /**
     * @param list<string> $characters
     *
     * @return list<string>
     */
    private function secureShuffle(array $characters): array {
        for ($index = count($characters) - 1; $index > 0; $index--) {
            $swapIndex = random_int(0, $index);

            [$characters[$index], $characters[$swapIndex]] = [
                $characters[$swapIndex],
                $characters[$index],
            ];
        }

        return $characters;
    }
}
