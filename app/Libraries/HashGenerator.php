<?php

namespace App\Libraries;

use LogicException;

final class HashGenerator {

    private const array ALGORITHMS = [
        'md5'    => 'MD5',
        'sha1'   => 'SHA-1',
        'sha256' => 'SHA-256',
        'sha384' => 'SHA-384',
        'sha512' => 'SHA-512',
    ];

    /**
     * @return array<string, string>
     */
    public function algorithms(): array {
        return self::ALGORITHMS;
    }

    /**
     * @return array<string, string>
     */
    public function generate(string $value): array {
        $availableAlgorithms = hash_algos();
        $hashes              = [];

        foreach (array_keys(self::ALGORITHMS) as $algorithm) {
            if (! in_array($algorithm, $availableAlgorithms, true)) {
                throw new LogicException(sprintf('Hash algorithm "%s" is unavailable.', $algorithm));
            }

            $hashes[$algorithm] = hash($algorithm, $value);
        }

        return $hashes;
    }
}
