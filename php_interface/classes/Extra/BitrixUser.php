<?php
namespace Extra;

class BitrixUser
{
    /**
     * Поиск юзера по номеру телефона
     *@param string $phone
     *
     * @return false|int
     */
    function getUserOnPhoneNumber($phoneNumber){
        $user = \Bitrix\Main\UserPhoneAuthTable::getList($parameters = array(
            'filter'=>array('PHONE_NUMBER' =>$phoneNumber) // выборка пользователя с подтвержденным номером
        ));
        $rows = $user->fetchAll();

        foreach ($rows as $row){
            return $row['USER_ID'];
        }

        return false;
    }
}
