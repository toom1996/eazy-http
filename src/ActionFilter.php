<?php

namespace eazy\http;

class ActionFilter extends Behavior
{
    public $only;

    public $except = [];


    /**
     * {@inheritdoc}
     */
    public function attach($owner)
    {
        $this->owner = $owner;
        $owner->on(Controller::EVENT_BEFORE_ACTION, [$this, 'beforeFilter']);
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        if ($this->owner) {
            $this->owner->off(Controller::EVENT_BEFORE_ACTION, [$this, 'beforeFilter']);
            $this->owner->off(Controller::EVENT_AFTER_ACTION, [$this, 'afterFilter']);
            $this->owner = null;
        }
    }

    /**
     * @param ActionEvent $event
     */
    public function beforeFilter($event)
    {
        if (!$this->isActive($event->action)) {
            return;
        }

        $event->isValid = $this->beforeAction($event->action);
        if ($event->isValid) {
            // call afterFilter only if beforeFilter succeeds
            // beforeFilter and afterFilter should be properly nested
            $this->owner->on(Controller::EVENT_AFTER_ACTION, [$this, 'afterFilter'], null, false);
        } else {
            $event->handled = true;
        }
    }

    /**
     * @param ActionEvent $event
     */
    public function afterFilter($event)
    {
        $event->result = $this->afterAction($event->action, $event->result);
        $this->owner->off(Controller::EVENT_AFTER_ACTION, [$this, 'afterFilter']);
    }

    /**
     * This method is invoked right before an action is to be executed (after all possible filters.)
     * You may override this method to do last-minute preparation for the action.
     * @param Action $action the action to be executed.
     * @return bool whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        return true;
    }

    /**
     * This method is invoked right after an action is executed.
     * You may override this method to do some postprocessing for the action.
     * @param Action $action the action just executed.
     * @param mixed $result the action execution result
     * @return mixed the processed action result.
     */
    public function afterAction($action, $result)
    {
        return $result;
    }

    /**
     * Returns an action ID by converting [[Action::$uniqueId]] into an ID relative to the module.
     * @param Action $action
     * @return string
     * @since 2.0.7
     */
    protected function getActionId($action)
    {
        if ($this->owner instanceof Module) {
            $mid = $this->owner->getUniqueId();
            $id = $action->getUniqueId();
            if ($mid !== '' && strpos($id, $mid) === 0) {
                $id = substr($id, strlen($mid) + 1);
            }
        } else {
            $id = $action->id;
        }

        return $id;
    }

    /**
     * Returns a value indicating whether the filter is active for the given action.
     * @param Action $action the action being filtered
     * @return bool whether the filter is active for the given action.
     */
    protected function isActive($action)
    {
        $id = $this->getActionId($action);

        if (empty($this->only)) {
            $onlyMatch = true;
        } else {
            $onlyMatch = false;
            foreach ($this->only as $pattern) {
                if (StringHelper::matchWildcard($pattern, $id)) {
                    $onlyMatch = true;
                    break;
                }
            }
        }

        $exceptMatch = false;
        foreach ($this->except as $pattern) {
            if (StringHelper::matchWildcard($pattern, $id)) {
                $exceptMatch = true;
                break;
            }
        }

        return !$exceptMatch && $onlyMatch;
    }
}