<?php

namespace App\JsonApi;

use Tobscure\JsonApi\AbstractSerializer;

class UserJsonApiSerializer extends AbstractSerializer
{
    protected $type = 'user';

    public function getId($user)
    {
        return $user->id;
    }

    public function getAttributes($user, array $fields = null)
    {
        $attributes = [];

        if (isset($user->email)) {
            $attributes['email'] = $user->email;
        }
        if (isset($user->name)) {
            $attributes['name'] = $user->name;
        }

        return $attributes;
    }
}
