(function () {
    'use strict';
    if (!!window.JCPickupPage)
        return;

    window.JCPickupPage = function (params) {

        this.container = document.getElementById(params.container);
        this.map = params.map || {}; //объект созданной карты
        this.points = params.points || {}; //object точки

        this.init();
    };

    window.JCPickupPage.prototype = {
        init: function () {

            if (!!this.container) {
                //point
                if (Object.keys(this.points).length > 0) {
                    this.setPointToMap(this.points, this.map);
                }

                var map =this.map;
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

                var textBaloon = obPoint[key].NAME;

                this.setPointOnMap(
                    myMap,
                    text,
                    [obPoint[key].position[0], obPoint[key].position[1]],
                    obPoint[key].NAME,
                    obPoint[key].METKA,
                    textBaloon,
                    );
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
        setPointOnMap: function (myMap, text, coord, name, metka, textBaloon) {
            myMap.geoObjects
                .add(new ymaps.Placemark(coord, {
                      balloonContent: text,
                      iconCaption: textBaloon,
                },{
                    preset: metka?metka:'islands#redIcon'
                }))
        },

    }
})();