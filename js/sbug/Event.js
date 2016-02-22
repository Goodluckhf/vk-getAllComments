;sbug.Event = function(name) {
    'use strict';

    var callbacks = [];

    this.name = name;

    this.on = function(callback) {
        callbacks.push(callback);
    }

    this.trigger = function(data) {
        for(var i in callbacks) {
            callbacks[i](data);
        }
    }
    
    this.off = function() {
        callbacks = [];
    }
}
