<?php

declare(strict_types=1);

namespace League\Flysystem;

use function array_pop;
use function explode;
use function implode;
use function preg_match;
use function str_replace;

class WhitespacePathNormalizer implements PathNormalizer
{
    private bool $allowRelativePaths;

    public function __construct(bool $allowRelativePathTraversal = true)
    {
        $this->allowRelativePaths = $allowRelativePathTraversal;
    }

    public function normalizePath(string $path): string
    {
        $unixPath = str_replace('\\', '/', $path);
<<<<<<< HEAD
        $match = preg_match('#\p{C}+#u', $unixPath);

        if ($match !== 0) {
=======

        if (preg_match('#\p{C}+#u', $unixPath)) {
>>>>>>> 9bc3608ef7b9c5a82d5f569b8ea6535b05e0f93b
            throw CorruptedPathDetected::forPath($path);
        }

        $parts = [];

        foreach (explode('/', $unixPath) as $part) {
            switch ($part) {
                case '':
                case '.':
                    break;

                case '..':
                    if ($this->allowRelativePaths === false || empty($parts)) {
                        throw PathTraversalDetected::forPath($path);
                    }

                    array_pop($parts);
                    break;

                default:
                    $parts[] = $part;
                    break;
            }
        }

        return implode('/', $parts);
    }
}
