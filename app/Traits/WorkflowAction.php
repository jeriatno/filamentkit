<?php

namespace App\Traits;

trait WorkflowAction
{
    /**
     * Get the workflow mapping from the class constants.
     *
     * @return array
     */
    protected static function getWorkflowMapping(): array
    {
        return defined('static::EVENTS') ? static::EVENTS : [];
    }

    /**
     * Get the next workflow mapping from the class constants.
     *
     * @return array
     */
    protected static function getNextWorkflowMapping(): array
    {
        return defined('static::NEXT_ACTION') ? static::NEXT_ACTION : [];
    }

    /**
     * Get workflow actions based on the model's status mapping.
     *
     * @param $action
     * @param $isReverse
     * @return array|string|null
     */
    public static function getWorkflowAction($action, $isReverse = true)
    {
        if (!$isReverse) {
            $workflow = array_reverse(self::getWorkflowMapping(), true);
            return $workflow[$action] ?? null;
        }

        $workflow = array_reverse(self::getNextWorkflowMapping(), true);
        return $workflow[$action] ?? null;
    }
}
