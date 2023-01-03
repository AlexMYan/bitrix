<?php
namespace Extra;

class СonversionYmlToKml
{
    public $filePath = "/bitrix/catalog_export/export_dip.xml";
    public const PATH_CONVERSION_KML_FILE = "/local/conversion/kml.xml";

    public $dom;
    public $records;

    public function __construct(){
        //тянем файл с конвертацией из json
        if($objData = $this->getDataXml($_SERVER["DOCUMENT_ROOT"].$this->filePath)){

            //Создаем объект xml
            $this->dom = new \domDocument("1.0", "utf-8");
            //Шапка файла всегда одна и таже
            $this->beginKml();
            //создаем поля для вставки
            $this->addData($objData);

            $this->endKml();
        }
    }

    /**
     *  Интерпретирует XML-файл в объект
     *
     * @param $path
     * @return false|mixed
     */
    public function getDataXml($path)
    {

        if (!empty($path)) {

            $xml = simplexml_load_file($path/*,'SimpleXMLElement', LIBXML_NOCDATA*/);

            if($xml)
                return $xml;
        }

        return false;

    }

    /**
     * Шапка файла всегда одинкова
     *
     * @return void
     * @throws \DOMException
     */
    public function beginKml(){

        $root = $this->dom->appendChild($this->dom->createElement('uedb'));
        $this->records = $root->appendChild($this->dom->createElement('records'));

    }

    /**
     * Создаем массив для вставки
     *
     * @param $objResult
     * @return void
     * @throws \DOMException
     */
    public function addData($objResult){


        $contactPersonValue=$objResult->shop->company;

        foreach ($objResult->shop->offers->offer as $offer){

            //record
            $record=$this->appendChild($this->records,"record");
            //unid
            $unid=$this->appendChild($record,"unid");
            $this->createTextNode($unid,$offer["id"]?$offer["id"]:"");
            //subject
            $subject=$this->appendChild($record,"subject");
            $this->createTextNode($subject,'<![CDATA['.$offer->model.']]>');
            //price
            $price=$this->appendChild($record,"price");
            $this->createTextNode($price,$offer->price?$offer->price:"");
            //currency
            $currency=$this->appendChild($record,"currency");
            $this->createTextNode($currency,$offer->currencyId?$offer->currencyId:"");
            //link
            $link=$this->appendChild($record,"link");
            $this->createTextNode($link,'<![CDATA['.$offer->url.']]>');
            //photos
            $photos=$this->appendChild($record,"photos");
            $photo=$this->appendChild($photos,"photo");
            $this->setAttribute($photo,'picture',$offer->picture?$offer->picture:"");
            //type
            $type=$this->appendChild($record,"type");
            $this->createTextNode($type,"sell");
            //contact_person
            $contact_person=$this->appendChild($record,"contact_person");
            $this->createTextNode($contact_person,'<![CDATA['.$contactPersonValue.']]>');
            //remuneration_type
            $remuneration_type=$this->appendChild($record,"remuneration_type");
            $this->createTextNode($remuneration_type,"1");
            //condition
            $condition=$this->appendChild($record,"condition");
            $this->createTextNode($condition,"0");
            //confiscated
            $confiscated=$this->appendChild($record,"confiscated");
            $this->createTextNode($confiscated,"0");
            //shop_guarantee
            $shop_guarantee=$this->appendChild($record,"shop_guarantee");
            $this->createTextNode($shop_guarantee,"0");
            //possible_exchange
            $possible_exchange=$this->appendChild($record,"possible_exchange");
            $this->createTextNode($possible_exchange,"0");
            //delivery_price
            $delivery_price=$this->appendChild($record,"delivery_price");
            $this->createTextNode($delivery_price,'<![CDATA[]]>');
            //body
            $body=$this->appendChild($record,"body");
            $bodyValue=$offer->description."<br>";
            if($offer->param){
                $bodyValue.="<ul>";
                foreach ($offer->param as $param){
                    $bodyValue.="<li>".$param["name"]." - ".$param."</li>";
                }
                $bodyValue.="</ul>";
            }
            $this->createTextNode($body,'<![CDATA['.$bodyValue.']]>');

            unset($record,$unid,$subject,$price,$currency,$link,$photos,$type,$contact_person,$remuneration_type,
                $condition,$confiscated,$shop_guarantee,$possible_exchange,$delivery_price,$body,$bodyValue);
        }

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
     * Создание файла
     *
     * @return void
     */
    public function endKml(){
        $this->dom->formatOutput = true;
        $this->dom->save($_SERVER["DOCUMENT_ROOT"].self::PATH_CONVERSION_KML_FILE);
    }
}