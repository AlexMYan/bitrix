(function () {
    'use strict';

    if (!!window.JCFormEvenetComponent)
        return;

    window.JCFormEvenetComponent = function (params) {

        this.props = params.jsProps || '';
        this.container = BX(params.container);
        BX.ready(BX.delegate(this.init, this));
    };

    window.JCFormEvenetComponent.prototype = {
        init: function () {
            //форма
            this.form = this.container.querySelector('form');
            this.group = this.form?.querySelector('.js-form-add-group');
            //Для показа ошибки  Политику конфиденциальности
            this.errorPP = this.form.querySelector('.policy_privacy_error');
            //Сообщение об успешной отправке
            this.formSuccess = this.form.querySelector('.form__group_success');
            //Блок куда буду складываться новые юзеры
            this.wrapNewUsers = this.form.querySelector('.form__group_users');
            //new-user-count
            this.userCount = this.form.querySelector('.new-user-count');
            //обертка над кнопкой
            this.wrapbuttonSubmit = this.form.querySelector('#REGISTRATION_USER');
            //Кнопка регистрации на форме
            this.buttonSubmit = this.form.querySelector('.form__submit');
            if (!!this.buttonSubmit) {
                //Подписываемся на событие нажатия кнопки
                BX.bind(this.buttonSubmit, 'click', BX.delegate(this.sendRequest, this));
            }
            this.wrapBtnAddCancel = this.form.querySelector('#EXTRA_ADD');

            //кнопка добавить нового юзера (прикрепить)
            this.btnExtraAdd = this.form.querySelector('.js-form-add-btn');
            if (!! this.btnExtraAdd) {
                //Подписываемся на событие нажатия кнопки
                BX.bind( this.btnExtraAdd, 'click', BX.delegate(this.addExtraUser, this));
            }

            var mainPhone = this.form.querySelector('.tel');
            if (!!mainPhone) {
                //Подписываемся на событие нажатия кнопки
              //  BX.bind(mainPhone, 'click', BX.delegate(this.maskPhone, false));
              //  BX.bind(mainPhone, 'focus', BX.delegate(this.maskPhone, false));
              //  BX.bind(mainPhone, 'blur', BX.delegate(this.maskPhone, false));
             //   BX.bind(mainPhone, 'keydown', BX.delegate(this.maskPhone, false));
            }

        },

        maskPhone: function (event) {

            var keyCode;

            if (event === undefined) {
                return
            }

            event.keyCode && (keyCode = event.keyCode);
            var pos = this.selectionStart;
            if (pos < 3) event.preventDefault();
            var matrix = "+7 (___) ___ ____",
                i = 0,
                def = matrix.replace(/\D/g, ""),
                val = this.value.replace(/\D/g, ""),
                new_value = matrix.replace(/[_\d]/g, function (a) {
                    return i < val.length ? val.charAt(i++) : a
                });
            i = new_value.indexOf("_");
            if (i != -1) {
                i < 5 && (i = 3);
                new_value = new_value.slice(0, i)
            }
            var reg = matrix.substr(0, this.value.length).replace(/_+/g,
                function (a) {
                    return "\\d{1," + a.length + "}"
                }).replace(/[+()]/g, "\\$&");
            reg = new RegExp("^" + reg + "$");
            if (!reg.test(this.value) || this.value.length < 5 || keyCode > 47 && keyCode < 58) {
                this.value = new_value;
            }
            if (event.type == "blur" && this.value.length < 5) {
                this.value = "";
            }
        },

        addExtraUser: function () {

            var flag = true,
                data = {},
                count = parseInt(this.userCount.value) + 1;

            //Массив объектов новых полей
            const extrFields = [
                {name: "FIO_NEW", threat: "FIO"},
                {name: "USER_JOB_TITLE_NEW", threat: "USER_JOB_TITLE"},
                {name: "USER_PHONE_NEW", threat: "USER_PHONE"},
                {name: "USER_EMAIL_NEW", threat: "USER_EMAIL"},
            ];
            const namesExtrFields = extrFields.map(el => el.name);
            //Список всех полей формы (что-то будет всегда)
            var inputs = this.form.querySelectorAll('input');
            //Проверка на обязательность
            for (var i = 0; i < inputs.length; i++) {
                //проверяем есть ли новые поля в нашем списке
                if (namesExtrFields.includes(inputs[i].name)) {
                    //находим его аналог в инфоблоке
                    var needFieldName = extrFields.find(el => el.name == inputs[i].name);
                    //проверка на обязательность полей у которых в инфоблоке стоит каглка
                    if (this.checkMainFields(needFieldName.threat)) {
                        if (inputs[i].value == "") {
                            flag = false;
                            this.showError(inputs[i]);
                        } else {
                            this.hiddenError(inputs[i]);
                        }
                    }
                    data[inputs[i].name] = inputs[i].value;
                }
            }

            if (flag) {


                var wrapItem = BX.create({
                    tag: "div",
                    props: {className: "form__fields"},
                    children: [
                        BX.create({
                            tag: 'div',
                            props: {
                                className: 'form__field'
                            },
                            text: data["FIO_NEW"],

                        }),
                        BX.create({
                            tag: 'span',
                            props: {
                                className: 'js-btn-new-user-delete'
                            },
                            html: "X",
                            events: {
                                click: BX.proxy(this.deleteNewUser, this)
                            },
                        }),
                        BX.create('input', {
                            attrs: {
                                name: "FIO_" + count,
                                value: data["FIO_NEW"],
                                type: "hidden"
                            },

                        }),
                        BX.create('input', {
                            attrs: {
                                name: "USER_JOB_TITLE_" + count,
                                value: data["USER_JOB_TITLE_NEW"],
                                type: "hidden"
                            },

                        }),
                        BX.create('input', {
                            attrs: {
                                name: "USER_PHONE_" + count,
                                value: data["USER_PHONE_NEW"],
                                type: "hidden"
                            },

                        }),
                        BX.create('input', {
                            attrs: {
                                name: "USER_EMAIL_" + count,
                                value: data["USER_EMAIL_NEW"],
                                type: "hidden"
                            },

                        }),

                    ]
                });


                this.userCount.value++;

                BX.append(wrapItem, this.wrapNewUsers);

                this.clearFieldsNew();

                this.form.classList.add('hide-group');
                this.group.children[0].remove();
                this.wrapBtnAddCancel.classList.add('hide-group-extra');
                this.wrapbuttonSubmit.classList.remove('hide-group-extra');

            }
        },
        deleteNewUser: function (e) {

            var parentNode = e.target.parentNode;

            if (parentNode.classList.contains("form__fields")) {
                parentNode.parentNode.removeChild(parentNode);
                this.userCount.value = parseInt(this.userCount.value) - 1;

                var test = document.getElementsByName("FIO_NEW");

                this.clearFieldsNew();

            }

        },

        clearFieldsNew: function () {
            document.querySelector("input[name='FIO_NEW']").value = "";
            document.querySelector("input[name='USER_JOB_TITLE_NEW']").value = "";
            document.querySelector("input[name='USER_PHONE_NEW']").value = "";
            document.querySelector("input[name='USER_EMAIL_NEW']").value = "";
        },

        sendRequest: function (e) {


            //отменяем submit
            e.preventDefault();

            var data = {},
                flag = true;

            var maskOptions = {
                mask: '+7(000)000-00-00',
                lazy: false
            }


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
                //галка политика
                if (inputs[i].name == "CHBX_PRIVACY_POLICY") {
                    if (!inputs[i].checked) {
                        this.showError(this.errorPP);
                        flag = false;
                    } else {
                        this.hiddenError(this.errorPP);
                    }
                    data[inputs[i].name] = inputs[i].checked;
                } else {

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
                        this.container.innerHTML = processed.HTML;
                        //перемещаемся вверх
                        const scrollTarget = this.form;
                        const topOffset =  this.buttonSubmit.offsetHeight;
                        const elementPosition = scrollTarget.getBoundingClientRect().top;
                        const offsetPosition = elementPosition - topOffset;
                        window.scrollBy({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });

                        setTimeout(function () {
                            location.reload();
                        }, 4000);
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
                BX.addClass(parentNode, 'error');
                BX.append(BX.create('div', {
                    props: {
                        className: 'field__error'
                    },
                    html: BX.message('FORMS_ALERT_ERROR')
                }), parentNode);
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