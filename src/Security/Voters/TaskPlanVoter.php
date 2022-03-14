<?php

namespace App\Security\Voters;

use App\Entity\TaskPlan;
use App\Entity\User;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskPlanVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
        // если это не один из поддерживаемых атрибутов, возвращается false
        if (!in_array($attribute, array(self::VIEW, self::EDIT))) {
            return false;
        }

        // голосовать только по объектам TaskPlan внутри этого избирателя
        if (!$subject instanceof TaskPlan) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // пользователь должен быть в системе; если нет - отказать в доступе
            return false;
        }

        // вы знаете, что $subject - это объект TaskPlan, благодаря поддержке
        /** @var TaskPlan $task_plan */
        $task_plan = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($task_plan, $user);
            case self::EDIT:
                return $this->canEdit($task_plan, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(TaskPlan $task_plan, User $user)
    {

        // если они могут редактировать, то они могут просматривать
        if ($this->canEdit($task_plan, $user)) {
            return true;
        }

        return $task_plan->getExecutor() === $user;
    }

    private function canEdit(TaskPlan $task_plan, User $user)
    {
        if ($user->checkRoles(['ROLE_ADMIN', 'ROLE_ADMIN_NSI'])) {
            return true;
        }

        if ($task_plan->getExecutor() == null) {
            throw new LogicException("Проверка полей обьекта до инициализации");
        }

        return $user === $task_plan->getExecutor();
    }
}