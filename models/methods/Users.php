<?php

namespace iAmirNet\HesabFa\Models\Methods;

use Azarinweb\Minimall\Models\User;

trait Users
{
    public function getUser($user) {
        return $this->user = is_numeric($user) ? User::findOrFail($user) : $user;
    }
}