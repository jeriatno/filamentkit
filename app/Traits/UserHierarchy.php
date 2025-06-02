<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait UserHierarchy
{
    /**
     * Get all superiors (up to the top) of a user
     *
     * @param $depth
     * @param $isArray
     * @param $column
     * @return array|Collection
     */
    public function getSuperiors($depth = 2, $isArray = false, $column = null)
    {
        $superiors = collect();
        $user = $this;

        while ($user->parentUser && $depth > 0) {
            $superiors->push($user->parentUser);
            $user = $user->parentUser;
            $depth--;
        }

        if($isArray) {
            if($column) {
                return array_column($superiors->toArray(), $column);
            }

            return $superiors->toArray();
        }

        return $superiors;
    }

    /**
     * Get all subordinates (down to the bottom) of a user
     *
     * @param $depth
     * @param $isArray
     * @param $column
     * @param $showRole
     * @return array|Collection
     */
    public function getSubordinates($depth = 2, $isArray = false, $column = null, $showRole = false)
    {
        $subordinates = collect();
        $user = $this;

        if ($user) {
            $subordinates = $user->subordinates;

            // If depth is greater than 1, recursively get subordinates of subordinates
            if ($depth > 1) {
                foreach ($subordinates as $subordinate) {
                    $subordinates = $subordinates->merge($subordinate->getSubordinates($depth - 1, false, null, $showRole));
                }
            }
        }

        if ($showRole) {
            $subordinates = $subordinates->map(function ($subordinate) {
                return [
                    'id' => $subordinate->id,
                    'name' => $subordinate->name,
                    'email' => $subordinate->email,
                    'roles' => $subordinate->role->pluck('name')->toArray(),
                    'parentUser' => [
                        'id' => $subordinate->parentUser->id,
                        'email' => $subordinate->parentUser->email
                    ]
                ];
            });
        }

        if($isArray) {
            if ($showRole) {
                return $subordinates->toArray();
            }

            if ($column) {
                return array_column($subordinates->toArray(), $column);
            }

            return $subordinates->pluck('id')->toArray();
        }

        return $subordinates;
    }
}
