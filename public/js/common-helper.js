var common = {
    sendAjax: function(params, callback, isPassArg) {
        var self = this;
        var sendUrl = this.checkTypeOf(params, 'url', null);
        if (!sendUrl) {
            return true;
        }
        $.ajax({
            method: this.checkTypeOf(params, 'method', "GET"),
            dataType: this.checkTypeOf(params, 'dataType', "json"),
            data: this.checkTypeOf(params, 'data', []),
            url: this.checkTypeOf(params, 'url', "/"),
            beforeSend: function() {
                //$(".loading").show();
            },
            complete: function() {
                //$(".loading").hide();
            }
        }).done(function(result) {
            if (callback) {
                if (isPassArg) {
                    self[callback](isPassArg)
                } else {
                    self[callback](result);
                }
            }
        }).fail(function(jqXhr) {});
    },
    sendFormDataAjax: function(params, callback, fallback) {
        var self = this;
        var sendUrl = this.checkTypeOf(params, 'url', null);
        if (!sendUrl) {
            return true;
        }
        $.ajax({
            method: this.checkTypeOf(params, 'method', "GET"),
            data: this.checkTypeOf(params, 'data', []),
            url: this.checkTypeOf(params, 'url', "/"),
            contentType: false,
            processData: false,
            beforeSend: function() {
                //$(".loading").show();
            },
            complete: function() {
                //$(".loading").hide();
            }
        }).done(function(result) {
            self[callback](result);
        }).fail(function(jqXhr) {});
    },
    checkTypeOf: function(params, key, defaultVal) {
	     return (params && typeof params[key] != "undefined") ? params[key] : defaultVal;
	}
};