<?php

namespace App\Security\Voters;

use App\Entity\User;
use App\Entity\VisitInfo;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class VisitInfoVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
        // если это не один из поддерживаемых атрибутов, возвращается false
        if (!in_array($attribute, array(self::VIEW, self::EDIT))) {
            return false;
        }

        // голосовать только по объектам VisitInfo внутри этого избирателя
        if (!$subject instanceof VisitInfo) {
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

        // вы знаете, что $subject - это объект VisitInfo, благодаря поддержке
        /** @var VisitInfo $visit_info */
        $visit_info = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($visit_info, $user);
            case self::EDIT:
                return $this->canEdit($visit_info, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(VisitInfo $visit_info, User $user)
    {
        // если они могут редактировать, то они могут просматривать
        if ($this->canEdit($visit_info, $user)) {
            return true;
        }

        return $visit_info->getVisitLog()->getExecutor() === $user;
    }

    private function canEdit(VisitInfo $visit_info, User $user)
    {
        if ($user->checkRoles(['ROLE_ADMIN', 'ROLE_ADMIN_NSI'])) {
            return true;
        }

        if ($visit_info->getVisitLog() == null) {
          throw new LogicException("Проверка полей обьекта до инициализации");
        }
        return $visit_info->getVisitLog()->getExecutor() === $user;
    }

}