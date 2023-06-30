class App {
    constructor() {
        this.mediaLibrary = null;
    }

    form(elem, callback = function () {}) {
        const object = this;

        elem.submit(function (e) {
            e.preventDefault();
            var form = $(this);

            var action = form.attr("action");
            var method = form.attr("method");
            var data = form.serialize();

            $.ajax({
                url: action,
                type: method,
                data: data,
                dataType: "json",
                beforeSend: function () {
                    object.load("open");
                },
                success: function(response) {
                    elem.find(".is-invalid").toggleClass("is-invalid");
                    elem.find("[data-error]").html(``);
                    elem.find(".invalid-feedback").html(``);

                    if(response.message) {
                        object.showMessage(response.message[1], response.message[0]);
                    }

                    if(callback) {
                        callback(response);
                    }
                },
                error: function (response) {
                    if(response.responseJSON) {
                        if(response.responseJSON.message) {
                            object.showMessage(response.responseJSON.message[1], response.responseJSON.message[0]);
                        }
    
                        if(response.responseJSON.errors) {
                            object.showFormErrors(elem, response.responseJSON.errors, elem.data('errors') ?? 'name');
                        }
                    } else {
                        console.log('Ocorreu algum erro de servidor!');
                    }
                },
                complete: function () {
                    object.load("close");
                }
            });
        });
    }

    table(elem, urlBase) {
        return new DataTable(elem, urlBase);
    }

    showMessage(message = '', type = 'success', timeOut = 5000, 
        fadeIn = 5000, fadeOut = 5000, positionClass = 'toast-bottom-right') {
        toastr.options.timeOut = timeOut;
        toastr.options.fadeIn = fadeIn;
        toastr.options.fadeOut = fadeOut;
        toastr.options.positionClass = positionClass;

        if(type == 'success') {
            toastr.success(message);
        } else if(type == 'error') {
            toastr.error(message);
        } else if(type == 'info') {
            toastr.info(message);
        }
    }

    showFormErrors(form, errors = null, attr = 'id') {
        form.find(".is-invalid").toggleClass("is-invalid");
        form.find("[data-error]").html(``);
        form.find(".invalid-feedback").html(``);

        if(errors) {
            for(const [key, value] of Object.entries(errors)) {
                var input = form.find(`[${attr}="${key}"]`);
                input.toggleClass('is-invalid');
                input.parent().children('.invalid-feedback').html(value);
                form.find(`[data-error="${key}"]`).html(value);
            }
        }
    }

    createMask(jQueryElem, mask) {
        if(jQueryElem && mask) {
            jQueryElem.mask(mask).focusout(function (event) {  
                var target, data, element;  
                target = (event.currentTarget) ? event.currentTarget : event.srcElement;  
                data = target.value.replace(/\D/g, '');
                
                element = $(target);  
                element.unmask();  
                element.mask(mask);
            });
        }
    }

    callAjax(data = {}) {
        const object = this;

        $.ajax({
            url: data.url,
            type: data.type ? data.type : "get",
            data: data.data ? data.data : {},
            dataType: data.dataType ? data.dataType : "json",
            beforeSend: function () {
                if(!data.noLoad) {
                    object.load("open");
                }
            }, 
            success: function (response) {
                if(response.message) {
                    object.showMessage(response.message[1], response.message[0]);
                }

                if(data.success) {
                    data.success(response);
                }
            }, 
            error: function (response) {
                if(response.responseJSON) {
                    if(response.responseJSON.message) {
                        object.showMessage(response.responseJSON.message[1], response.responseJSON.message[0]);
                    }

                    if(data.error) {
                        data.error(response.responseJSON);
                    }
                } else {
                    console.log('Ocorreu algum erro de servidor!');
                }
            },
            complete: function () {
                if(!data.noLoad) {
                    object.load("close");
                }
            }
        })
    }

    load(action) {
        var load_div = $(".ajax_load");
        if(action === "open") {
            load_div.fadeIn().css("display", "flex");
        } else {
            load_div.fadeOut();
        }
    }
    
    objectifyForm(form) {
        var returnArray = {};
        var formArray = form.serializeArray();

        if(formArray) {
            for(var i = 0; i < formArray.length; i++){
                returnArray[formArray[i]["name"]] = formArray[i]["value"];
            }
        }

        return returnArray;
    }

    setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));

        let expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');

        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        
        return "";
    }

    getAddressByCEP(cep, inputs = {}, awaitText = "...") {
        let url = `https://viacep.com.br/ws/${cep}/json/`;
        let xmlHttp = new XMLHttpRequest();
    
        var endereco = document.getElementById(inputs.endereco);
        var bairro = document.getElementById(inputs.bairro);
        var cidade = document.getElementById(inputs.cidade);
        var estado = document.getElementById(inputs.estado);
    
        endereco.value = awaitText;
        bairro.value = awaitText;
        cidade.value = awaitText;
        estado.value = awaitText;
    
        xmlHttp.open('GET', url);
        xmlHttp.onreadystatechange = () => {
            if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
                let dadosJSONText = xmlHttp.responseText;
                let dadosJSONObj = JSON.parse(dadosJSONText);
    
                endereco.value = dadosJSONObj.logradouro ? dadosJSONObj.logradouro : '';
                bairro.value = dadosJSONObj.bairro ? dadosJSONObj.bairro : '';
                cidade.value = dadosJSONObj.localidade ? dadosJSONObj.localidade : '';
                estado.value = dadosJSONObj.uf ? dadosJSONObj.uf : '';
            }
        }
    
        xmlHttp.send();
    }

    copyText(sel, text) {
        var copyElem = document.querySelector(sel);
        copyElem.select();
        document.execCommand("Copy");
        
        this.showMessage(text + copyElem.value, "success");
    }

    addTextAtSelectionPosition(elem, text = "") {
        var curPos = elem.selectionStart;
        var textArea = $(elem).val();
        $(elem).val(textArea.slice(0, curPos) + text + textArea.slice(curPos));
    }

    setModal(modal, data = {}) {
        const modal_header = modal.find(".modal-header");
        const modal_title = modal_header.find(".modal-title");

        if(data) {
            if(data.effect) {
                modal.attr("class", `modal ${data.effect}`);
            }

            if(!data.noBackdrop) {
                modal.attr("data-bs-backdrop", "static");
            }

            if(!data.keyboard) {
                modal.attr("data-bs-keyboard", "false");
            }

            if(data.size) {
                modal.find(".modal-dialog").attr("class", `modal-dialog modal-${data.size}`)
            }

            if(data.title) {
                modal_title.text(data.title);
            }
        }
    }

    cleanForm(elem) {
        elem.find("input, textarea, select").each(function () {
            if($(this).attr("type") !== "submit") {
                $(this).val(``);
            }
        });
    }

    populateForm(elem, content = {}, attr = 'id') {
        elem.find("input, textarea, select").each(function () {
            if($(this).attr("type") !== "submit") {
                if(content[$(this).attr(attr)] != '') {
                    $(this).val(content[$(this).attr(attr)]);
                } else {
                    $(this).val(``);
                }
            }
        });
    }
}