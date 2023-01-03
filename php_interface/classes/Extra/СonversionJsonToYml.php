<?php

namespace Extra;

class СonversionJsonToYml
{
    public $filePath = "https://svarog-rf.ru/export/price-list?format=json";
    public const PATH_CONVERSION_EXPORT_DIP = "/local/conversion/export_dip.xml";

    public $dom;
    public $shop;
    public $categories;
    public $arrCategories=[];
    public $offers;

    public function __construct()
    {
        //Создаем объект xml
        $this->dom = new \domDocument("1.0", "utf-8");
        //Шапка файла всегда одна и таже
        $this->beginYml();
        //тянем файл с конвертацией из json
        $objData = $this->getDataCurl($this->filePath);
        //Вернет false если что то не так
        if ($objData) {
            //Нужно обратиться к каждому url в массиве и забрать данные
            if ($objResult = $this->getAllData($objData)) {
                //счетчик offer-ов т.к. других нет
                $count=1;
                foreach ($objResult as $item) {
                    //если что то не так то 10000
                    $keyCategory=10000;
                    //проверяем категории если нет то создаем первую
                    if(!empty($this->arrCategories)){
                        //если нет в массиве, то добавлемя новую, если есть то берем id
                        if(!in_array(trim($item->category),$this->arrCategories)){
                            $this->arrCategories[]=trim($item->category);

                            $keyCategory= array_search ($item->category,$this->arrCategories);
                            $obj=$this->appendChild($this->categories,"category");
                            $this->setAttribute($obj,'id',$keyCategory);
                            $this->createTextNode($obj,$item->category);
                        }else{
                            $keyCategory= array_search ($item->category,$this->arrCategories);
                        }
                    }else{
                        $keyCategory=1;
                        $obj=$this->appendChild($this->categories,"category");
                        $this->setAttribute($obj,'id',1);
                        $this->createTextNode($obj,$item->category);
                        $this->arrCategories[1]=trim($item->category);
                    }
                    //offer
                    $offer=$this->appendChild($this->offers,"offer");
                    $this->setAttribute($offer,'id',$count);
                    $this->setAttribute($offer,'available',"true");
                    $this->setAttribute($offer,'type',"vendor.model");
                    //url
                    $url=$this->appendChild($offer,"url");
                    $this->createTextNode($url,$item->url?$item->url:"");
                    //price
                    $price=$this->appendChild($offer,"price");
                    $this->createTextNode($price,$item->ma_price?$item->ma_price:"");
                    //currencyId
                    $currencyId=$this->appendChild($offer,"currencyId");
                    $this->createTextNode($currencyId,"RUB");
                    //categoryId
                    $categoryId=$this->appendChild($offer,"categoryId");
                    $this->createTextNode($categoryId, $keyCategory);
                    //picture
                    $img="";
                    if($item->images){
                        if(isset($item->images[0]) && !empty($item->images[0])){
                            $img=$item->images[0]->url;
                        }
                    }
                    $picture=$this->appendChild($offer,"picture");
                    $this->createTextNode($picture, $img);
                    //vendor
                    $vendor=$this->appendChild($offer,"vendor");
                    $this->createTextNode($vendor, "");
                    //model
                    $model=$this->appendChild($offer,"model");
                    $this->createTextNode($model, $item->title?$item->title:"");
                    //vendorCode
                    $vendorCode=$this->appendChild($offer,"vendorCode");
                    $this->createTextNode($vendorCode, $item->num?$item->num:"");
                    //description
                    $description=$this->appendChild($offer,"description");
                    $this->createTextNode($description, $item->description? "<![CDATA[".$item->description."]]>":"");
                    //params
                    if($item->spec_fields && false){
                        foreach ($item->spec_fields as $field){

                            if(isset($field->value[0]) && !empty($field->value[0]) ){
                                $param=$this->appendChild($offer,"param");
                                $this->createTextNode($param,$field->value[0]);
                                $this->setAttribute($param,'name',$field->title);

                                if(isset($field->units) && !empty($field->units)){
                                    $this->setAttribute($param,'unit',$field->units);
                                }
                            }

                            unset($param);

                        }
                    }

                    $count++;

                    unset($url,$price,$currencyId,$categoryId,$picture,$vendor,$description,$offer);
                }
            }
        }

        $this->endYml();
    }

    /**
     * Собираем все товары по ссылкам
     *
     * @param $objData
     * @return array
     */
    public function getAllData($objData)
    {
        $objResult = [];

        foreach ($objData as $data) {

            if (!empty($data->data_url) && $data->in_stock==1) {

                if ($obj = $this->getDataCurl($data->data_url)) {
                    $objResult[] = $obj;
                }
            }
        }

        return $objResult;
    }

    /**
     * CURL
     *
     * @param $path
     * @return false|mixed
     */
    public function getDataCurl($path)
    {

        if (!empty($path)) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $path);
            $result = curl_exec($ch);
            curl_close($ch);

            $obj = json_decode($result);

            if ($obj) {
                return $obj;
            }
        }

        return false;

    }

    /**
     * Шапка файла всегда одинкова
     *
     * @return void
     * @throws \DOMException
     */
    public function beginYml()
    {
        $root = $this->dom->appendChild($this->dom->createElement('yml_catalog'));
        $this->setAttribute($root,'date',$this->getTime());

        $this->shop = $root->appendChild($this->dom->createElement('shop'));

        $name = $this->shop->appendChild($this->dom->createElement('name'));
        $this->createTextNode($name,"zid.by");

        $company = $this->shop->appendChild($this->dom->createElement('company'));
        $this->createTextNode($company,'ООО "Зид Бай"');

        $url = $this->shop->appendChild($this->dom->createElement('url'));
        $this->createTextNode($url,'https://zid.by');

        $platform = $this->shop->appendChild($this->dom->createElement('platform'));
        $this->createTextNode($platform,'BSM/Yandex/Market');

        $version = $this->shop->appendChild($this->dom->createElement('version'));
        $this->createTextNode($version,'2.4.7');

        $cpa = $this->shop->appendChild($this->dom->createElement('cpa'));
        $this->createTextNode($cpa,1);

        $currencies = $this->shop->appendChild($this->dom->createElement('currencies'));
        $currency = $currencies->appendChild($this->dom->createElement('currency'));
        $this->setAttribute($currency,'id',"RUB");
        $this->setAttribute($currency,'rate',1);

        $this->categories = $this->shop->appendChild($this->dom->createElement('categories'));

        $enable_auto_discounts=$this->appendChild($this->shop,"enable_auto_discounts");
        $this->createTextNode($enable_auto_discounts,"true");

        $this->offers=$this->appendChild($this->shop,"offers");
    }

    /**
     * Создание файла
     *
     * @return void
     */
    public function endYml(){
        $this->dom->formatOutput = true;
        $this->dom->save($_SERVER["DOCUMENT_ROOT"].self::PATH_CONVERSION_EXPORT_DIP);
    }

    /**
     * Вставка  атрибута в объект
     *
     * @param $object
     * @param $attribute
     * @param $value
     * @return void
     */
    public function setAttribute($object, $attribute, $value)
    {
        $object->setAttribute($attribute, $value);
    }

    /**
     * Вставка текста в объект
     *
     * @param $object
     * @param $value
     * @return void
     */
    public function createTextNode($object, $value){
        $object->appendChild($this->dom->createTextNode($value));
    }

    /**
     * Вставка объекта в объект
     *
     * @param $object
     * @param $name
     * @return mixed
     * @throws \DOMException
     */
    public function appendChild($object,$name){
        return $object->appendChild($this->dom->createElement($name));
    }

    /**
     * Время в нужном формате
     *
     * @return false|string
     */
    public function getTime()
    {
        $date = date_create(date("Y-m-d"), timezone_open('Europe/Minsk'));
        return date_format($date, "Y-m-d\TH:i:sP");
    }

}