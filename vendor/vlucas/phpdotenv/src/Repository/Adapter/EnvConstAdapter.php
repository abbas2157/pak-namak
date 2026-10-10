<?php

declare(strict_types=1);

namespace Dotenv\Repository\Adapter;

use PhpOption\Option;
use PhpOption\Some;

final class EnvConstAdapter implements AdapterInterface
{
    /**
     * Create a new env const adapter instance.
     *
     * @return void
     */
    private function __construct()
    {
        //
    }

    /**
     * Create a new instance of the adapter, if it is available.
     *
     * @return \PhpOption\Option<self>
     */
    public static function create()
    {
        return Some::create(new self());
    }

    /**
     * Read an environment variable, if it exists.
     *
     * @param non-empty-string $name
     *
     * @return \PhpOption\Option<string>
     */
    public function read(string $name)
    {
        /** @var \PhpOption\Option<scalar> */
        $value = Option::fromArraysValue($_ENV, $name)
            ->filter(static function ($value) {
                return \is_scalar($value);
<<<<<<< HEAD
=======
            })
            ->map(static function ($value) {
                if ($value === false) {
                    return 'false';
                }

                if ($value === true) {
                    return 'true';
                }

                return (string) $value;
>>>>>>> 9bc3608ef7b9c5a82d5f569b8ea6535b05e0f93b
            });

        return $value->map(static function ($value) {
            if ($value === false) {
                return 'false';
            }

            if ($value === true) {
                return 'true';
            }

            return (string) $value;
        });
    }

    /**
     * Write to an environment variable, if possible.
     *
     * @param non-empty-string $name
     * @param string           $value
     *
     * @return bool
     */
    public function write(string $name, string $value)
    {
        $_ENV[$name] = $value;

        return true;
    }

    /**
     * Delete an environment variable, if possible.
     *
     * @param non-empty-string $name
     *
     * @return bool
     */
    public function delete(string $name)
    {
        unset($_ENV[$name]);

        return true;
    }
}
