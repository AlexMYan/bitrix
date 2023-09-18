(function () {
    'use strict';
    if (!!window.JCPickupPage)
        return;

    window.JCPickupPage = function (params) {

        this.container = document.getElementById(params.container);
        this.componentPath = params.componentPath || '';
        this.map = params.map || {}; //объект созданной карты
        this.points = params.points || {}; //object точки
        this.cities = params.cities || {}; //города
        this.pickupItems = ""; //Информация для городов

        this.init();
    };

    window.JCPickupPage.prototype = {
        init: function () {

            if (!!this.container) {
                //point
                if (Object.keys(this.points).length > 0) {
                    this.setPointToMap(this.points, this.map);
                }
                //нажатие на блок в списке точек
                this.setEventClickPickup();
                //Обертка списка магазинов
                this.pickupItems = this.container.querySelector('.pickup__items');

                //Изменения списка
                this.pickupSelect = this.container.querySelector('.js-pickup-select');

                if (!!this.pickupSelect) {
                    BX.bind(this.pickupSelect, 'change', BX.delegate(this.pickupSelectChange, this));
                }

                this.pickupItemsMob = this.container.querySelectorAll('.js-select-mob__option');

                if(!!this.pickupItemsMob){
                    for(let i = 0; i < this.pickupItemsMob.length; i++) {
                        BX.bind(this.pickupItemsMob[i], 'click', BX.delegate(this.pickupSelectChangeMob, this));
                    }
                }
                this.map.setBounds(this.map.geoObjects.getBounds(),{checkZoomRange:true/*, zoomMargin:9*/});

                var map =this.map;
                var pickupItems  = this.pickupItems;
                this.map.geoObjects.events.add('click', function (e) {
                    // Получим ссылку на геообъект, по которому кликнул пользователь.
                    var target = e.get('target');

                    window.JCPickupPage.prototype.setActivePickup(pickupItems,target.geometry.getCoordinates());
                    window.JCPickupPage.prototype.setPositionCenter(map,target.geometry.getCoordinates());

                });


            }
        },
        /**
         * Открываем модалку
         *
         * @param data
         * @returns {boolean}
         */
        clickPickupList:function (data){

            if (!BX.proxy_context)
            {
                return false;
            }

            var right = this.container.querySelector('.js-pickup__right');

            right.classList.add('open');

            this.setModalInfo(this.points,BX.proxy_context.getAttribute('data-item'));

        },
        /**
         * При нажатии на список точек, выделить пункт и установить центр карты в точке пункта самовывоза
         *
         * @returns {boolean}
         */
        clickPickupListDesctop:function (){

            if (!BX.proxy_context)
            {
                return false;
            }
            var pos =BX.proxy_context.getAttribute('data-item');
            this.setPositionCenter(this.map, [this.points[pos].position[0], this.points[pos].position[1]]);
            this.setActivePickup(this.pickupItems, [this.points[pos].position[0], this.points[pos].position[1]]);

        },
        /**
         * Установить класс для подсветки пункта по нажатию
         *
         * @param pickupItems
         * @param coord
         */
        setActivePickup:function (pickupItems,coord){

            if(!!pickupItems && coord){

                var listPickupItems = pickupItems.querySelectorAll('.pickup__item');

                if(!!listPickupItems){
                    for(let i = 0; i < listPickupItems.length; i++) {

                        if(listPickupItems[i].getAttribute('data-coord-lat')=== undefined){

                        }else{
                            if(coord[0]==listPickupItems[i].getAttribute('data-coord-lat')){
                                listPickupItems[i].classList.add('active');
                            }else{
                                listPickupItems[i].classList.remove('active');
                            }
                        }
                    }
                }
            }
        },


        /**
         * Установка события по нажатию на пункт
         *
         * @param obPoint
         */
        setEventClickPickup:function (){

            var pickupItems = this.container.querySelector('.pickup__items');

            var listPickupItems = pickupItems.querySelectorAll('.pickup__item');

            if(!!listPickupItems){
                for(let i = 0; i < listPickupItems.length; i++) {

                    BX.bind(listPickupItems[i], 'click', BX.proxy(this.clickPickupList, this));
                    BX.bind(listPickupItems[i], 'click', BX.proxy(this.clickPickupListDesctop, this));

                }
            }

        },

        /**
         * Изменения при выборе города
         *
         * @param item
         */
        pickupSelectChangeMob:function (item){

            let pos = item.target.getAttribute('data-value');
            if(!!pos){
                this.pickupSelectChange(pos);
            }
        },

        /**
         * Создание списка точек при выборе города
         *
         * @param obPoint
         */

        setInfoPickupItems: function (obPoint) {

            if (!!this.pickupItems) {
                this.pickupItems.innerHTML = "";

                for (var key in obPoint) {

                    this.pickupItems.appendChild(
                        BX.create('DIV', {
                            props: {
                                className: 'pickup__item js-pickup__item'
                            },
                            attrs: {
                                'data-item': key,
                                'data-coord-lat': obPoint[key].position[0],
                            },
                            children: [
                                BX.create('div', {
                                    props: {
                                        className: 'pickup__item-address'
                                    },
                                    html: obPoint[key].ADRESS
                                }),
                                BX.create('div', {
                                    props: {
                                        className: 'pickup__item-schedule'
                                    },
                                    html: obPoint[key].TIME_WORK
                                }),
                                BX.create('div', {
                                    props: {
                                        className: 'pickup__item-phones'
                                    },
                                    html: obPoint[key].PHONE
                                }),

                            ]
                        })
                    );
                }
            }
        },

        /**
         * Фильтруем данные по событию
         * Очищаем карту и наносим новые метки
         *
         * @param e
         */
        pickupSelectChange: function (e) {

            let value, city;
            if (typeof e === 'object') {
                value = e.target.value;
            } else {
                value = e;
            }

            if (value == "all") {
                city = value;
            } else if (this.cities[value] === undefined) {

            } else {
                city = this.cities[value];
            }

            BX.ajax({
                url: this.componentPath + '/ajax.php',
                method: 'POST',
                dataType: 'json',
                timeout: 60,
                data: {
                    action: 'getData',
                    city: city
                },
                onsuccess: BX.delegate(function (result) {

                    if (!!result.status) {
                        if (!!result.result) {
                            //Удаляем все метки
                            this.map.geoObjects.removeAll();
                            //обновляем список точек
                            this.points=result.result;
                            //Фильтрованные метки
                            this.setPointToMap(result.result, this.map);
                            //Фильтрованные метки
                            this.setInfoPickupItems(result.result);

                            this.setEventClickPickup(result.result);


                            this.map.setBounds(this.map.geoObjects.getBounds(),{checkZoomRange:true/*, zoomMargin:9*/});
                        }
                    }

                }, this)
            });
        },

        /**
         * Центрируем карту по нажатию в списке
         *
         * @param myMap
         * @param coord
         */
        setPositionCenter: function (myMap, coord) {
            myMap.setCenter(coord, 14);
        },
        /**
         * Для мобильной версии подгрузка данных
         *
         * @param data
         * @param pos
         */
        setModalInfo: function (data, pos) {

            if (data[pos] === undefined) {

            } else {
                //ADRESS
                let address = document.querySelector('.pickup__modal-address');
                if (!!address) {
                    address.innerHTML = data[pos].ADRESS;
                }
                //TIME_WORK
                let schedule = document.querySelector('.pickup__modal-schedule');
                if (!!schedule) {

                    var strWork="";
                    if(data[pos].TIME_WORK.length>0){
                        for(let i = 0; i < data[pos].TIME_WORK.length; i++) {
                            strWork+="<div>"+data[pos].TIME_WORK[i]+"</div>";
                        }
                    }

                    schedule.innerHTML = strWork;

                }
                let phone = document.querySelector('.pickup__modal-phones');
                if (!!phone) {
                    var strPhone="";
                    if(data[pos].PHONE.length>0){
                        for(let i = 0; i < data[pos].PHONE.length; i++) {

                            strPhone+="<a href='tel:"+data[pos].PHONE[i]+"'>"+data[pos].PHONE[i]+"</a>";

                        }
                    }

                    phone.innerHTML = strPhone;
                }
                let pay = document.querySelector('.pickup__modal-pay');
                if (!!pay) {
                    pay.innerHTML = data[pos].PAY;
                }
                let desc = document.querySelector('.pickup__modal-description');
                if (!!desc) {
                    desc.innerHTML = data[pos].DESCRIPTION_TEXT;
                }
            }
        },

        /**
         * Установка точек на карте
         *
         * @param obPoint
         * @param myMap
         */
        setPointToMap: function (obPoint, myMap) {

            for (var key in obPoint) {

                var text = obPoint[key].ADRESS

                var textBaloon = obPoint[key].ADRESS;

                this.setPointOnMap(
                    myMap,
                    text,
                    [obPoint[key].position[0], obPoint[key].position[1]],
                    "",
                    777,
                    textBaloon);
            }
        },

        /**
         * Точки на карте
         *
         * @param myMap
         * @param text
         * @param coord
         * @param name
         * @param id
         * @param count
         */
        setPointOnMap: function (myMap, text, coord, name, id, textBaloon) {
            myMap.geoObjects
                .add(new ymaps.Placemark(coord, {
                    //  balloonContent: text,
                    // iconCaption: textBaloon,
                },))
        },

    }
})();