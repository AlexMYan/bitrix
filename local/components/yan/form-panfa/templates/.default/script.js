(function () {
    'use strict';

    if (!!window.JCFormEvenetComponent)
        return;

    window.JCFormEvenetComponent = function (params) {

        this.props = params.jsProps || '';
        this.container = BX(params.container);
        this.id=params.container;
        BX.ready(BX.delegate(this.init, this));

    };

    window.JCFormEvenetComponent.prototype = {
        init: function () {
            //форма
            this.form = this.container.querySelector('form');
            this.group = this.form?.querySelector('.js-form-add-group');

            //Сообщение об успешной отправке
            this.formSuccess = this.form.querySelector('.form__group_success');


            //Кнопка регистрации на форме
            this.buttonSubmit = this.form.querySelector('.btn-send-form');
            if (!!this.buttonSubmit) {
                //Подписываемся на событие нажатия кнопки
                BX.bind(this.buttonSubmit, 'click', BX.delegate(this.sendRequest, this));
            }




        },




        sendRequest: function (e) {


            //отменяем submit
            e.preventDefault();

            var data = {},
                flag = true;



            data["ACTION"] = e.target.getAttribute("data-action");
            //убираем сообщение об успешной отправке
            if (!!this.formSuccess) {
                if (this.formSuccess.classList.contains("form_success")) {
                    BX.removeClass(this.formSuccess, 'form_success');
                }
            }

            //Список всех полей формы (что то будет всегда)
            var inputs = this.form.querySelectorAll('input');
            //Проверка на обязательность
            for (var i = 0; i < inputs.length; i++) {

                //проверка на обязательность полей у которых в инфоблоке стоит каглка
                if (this.checkMainFields(inputs[i].name)) {
                    if (inputs[i].value == "") {
                        flag = false;
                        this.showError(inputs[i]);
                    } else {
                        this.hiddenError(inputs[i]);
                    }
                }
                data[inputs[i].name] = inputs[i].value;
            }

            //Если нет ошибок делаем запрос
            if (flag) {
                BX.ajax({
                    url: this.form.action,
                    method: 'POST',
                    timeout: 60,
                    data: data,
                    onsuccess: BX.delegate(function (result) {

                       var processed = BX.processHTML(result);
                       BX('modalWrapSuccess').innerHTML = processed.HTML;

                        let target = document.getElementById("modalWrap");
                        if (target) {
                            target.classList.add('open');
                            document.body.style.overflow = 'hidden';
                        }
                    }, this)
                });
            }

        },

        /**
         * Проверяем обязательные поля (обязательность проставляется в настройках инфоблока для самого свойства)
         *
         * @param inputCode
         * @returns {boolean}
         */
        checkMainFields: function (inputCode) {
            if (!!this.props) {
                for (var i in this.props) {
                    if (this.props.hasOwnProperty(i)) {
                        if (this.props[i].CODE == inputCode) {
                            if (this.props[i].REQUIRED == 'Y') {
                                return true;
                            }
                        }
                    }
                }
            }
            return false;
        },

        /**
         * Показываем ошибки
         *
         * @param element
         */
        showError: function (element) {

            var parentNode = element.parentNode;
            if (parentNode.classList.contains("error")) {

            } else {
                console.log(parentNode);
                BX.addClass(parentNode, 'error');

                let divErrore= document.createElement("div");
                divErrore.className='field__error';
                divErrore.innerHTML=BX.message('FORMS_ALERT_ERROR');

                parentNode.append(divErrore);
            }
        },

        /**
         * Чистим сообщения ошибки
         *
         * @param element
         */
        hiddenError: function (element) {
            var parentNode = element.parentNode;
            if (parentNode.classList.contains("error")) {
                BX.removeClass(parentNode, 'error');
                var childNode = parentNode.querySelector('.field__error');
                childNode.parentNode.removeChild(childNode);
            }

        }


    }
})();