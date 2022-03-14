<?php

namespace App\Security\Voters;

use App\Entity\User;
use App\Entity\VisitLog;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class VisitLogVoter extends Voter
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
        if (!$subject instanceof VisitLog) {
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

        // вы знаете, что $subject - это объект VisitLog, благодаря поддержке
        /** @var VisitLog $visit_log */
        $visit_log = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($visit_log, $user);
            case self::EDIT:
                return $this->canEdit($visit_log, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(VisitLog $visit_log, User $user)
    {
        // если они могут редактировать, то они могут просматривать
        if ($this->canEdit($visit_log, $user)) {
            return true;
        }

        return $visit_log->getExecutor() === $user;
    }

    private function canEdit(VisitLog $visit_log, User $user)
    {
        if ($user->checkRoles(['ROLE_ADMIN', 'ROLE_ADMIN_NSI'])) {
            return true;
        }

        if ($visit_log->getExecutor() == null) {
            throw new LogicException("Проверка полей обьекта до инициализации");
        }

        return $user === $visit_log->getExecutor();
    }
}