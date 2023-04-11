<?php

namespace App\Assert;

use Symfony\Component\Validator\Constraints as Assert;

class SecretAssert
{
    public static function getConstraints(): Assert\Collection
    {
        return new Assert\Collection([
            'secret' => [
                new Assert\NotBlank()
            ],
            'expireAfterViews' => [
                new Assert\NotBlank(),
                new Assert\Regex(array(
                        'pattern' => '/^[0-9]\d*$/',
                        'message' => 'Please use only positive numbers.'
                    )
                ),
                new Assert\GreaterThan(0)
            ],
            'expireAfter' => [
                new Assert\NotBlank(),
                new Assert\Regex(array(
                        'pattern' => '/^[0-9]\d*$/',
                        'message' => 'Please use only positive numbers.'
                    )
                ),
            ],
        ]);
    }
}