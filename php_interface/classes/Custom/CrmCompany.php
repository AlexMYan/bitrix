<?php
namespace Custom;
//вспомогательный класс, который достает из CRM нужнные данные для страницы заказа и личного кабинета

class CrmCompany{

    /**
     *  элементы нуженого объекта
     *
     * @param $type
     * @param $filter
     * @param array $select
     * @param string $limit
     * @param string $offset
     * @return array
     */
    public function getObjectItemsCRM($type,$filter,$select=[],$limit='',$offset=''): array
    {
        $factory=self::getObjectCRM($type);
        $arItems = $factory->getItems([
            'filter' => $filter,
            'select' => $select,
            'limit'=>$limit,
            'offset'=>$offset,

        ]);

        return $arItems;
    }

    /**
     * Кол-во позиций
     *
     * @param $type
     * @param $filter
     * @return mixed
     */
    public function getCountObjectItemsCRM($type,$filter)
    {
        $factory=self::getObjectCRM($type);

        $count = $factory->getItemsCount([
            $filter
        ]);

        return $count;
    }

    /**
     * достаем из CRM статусы сделок
     *
     * @param $type
     * @return array
     */
    public function getArrayOrdersStagesCRM($type):array
    {
        $factory=self::getObjectCRM($type);

        $obStages = $factory->getStages();
         foreach ($obStages as $stage){
             $arStages[$stage->get("CATEGORY_ID")][$stage->get("STATUS_ID")]=$stage->get("NAME");
         }
        return $arStages;
    }

    /**
     *  достаем из CRM счета
     *
     * @param $entityId
     * @return mixed
     */
    public function getArrayInvoiceStagesCRM($entityId)
    {
        $parent = new \Bitrix\Crm\ItemIdentifier(\CCrmOwnerType::Deal, $entityId);
        //все связанные сущности
        $childs = \Bitrix\Crm\Service\Container::getInstance()->getRelationManager()->getChildElements($parent);
        foreach($childs as $child) {
            if ($child->getEntityTypeId() == \CCrmOwnerType::SmartInvoice) {
                $data = $child->getEntityId();
            }
        }

        return $data;
    }

    /**
     * массив значений полей элементов
     *
     * @param $object
     * @return array
     */
    public function getArrayObjectItemsCRM($object): array
    {
        $arrFieldsOrderCrm=[];
        foreach ($object as $item) {
            $arrFieldsOrderCrm[$item->getId()]=$item->getCompatibleData();
        }
        return $arrFieldsOrderCrm;
    }

    /**
     * Товары
     *
     * @param $object
     * @return array
     */
    public function getArrayProductRow($object): array
    {
        return $object->getProductRows()->toArray();
    }

    /**
     * достаем из CRM объект
     *
     * @param $type
     * @return mixed
     */
    public function getObjectCRM($type)
    {
        $container = \Bitrix\Crm\Service\Container::getInstance();
        $factory = $container->getFactory($type);

        return $factory;
    }
}