;sbug.Request = (new function() {
    'use strict';

    var self = this,
        extraData = {};

    var addExtraData = function(data) {
        for(var i in extraData) {
            if(extraData.hasOwnProperty(i)) {
                data[i] = extraData[i];
            }
        }
        return data;
    }

    this.setExtra = function(data) {
        extraData = data;
    }

    var sendXhr = function(data) {
        var res = addExtraData(data.data);
        return $.ajax({
            url: data.url,
            method: 'post',
            dataType: 'jsonp',
            data: res
        });
    }

    this.api = function(method, data) {
        var newData = {};
        newData.url = 'https://api.vk.com/method/' + method;
        newData.data = data;
        return sendXhr(newData);
    }

    this.send = function(data) {
        return sendXhr(data);
    }
});