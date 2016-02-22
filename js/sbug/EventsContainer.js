;sbug.EventsContainer = function() {
    'use strict';

    var events = {};

    var has = function(name) {
        return events[name];
    }

    this.register = function(name) {
        if(has(name)) {
            throw new Error('event is already exist');
        }
        events[name] = new sbug.Event(name);
    }

    this.on = function(event, callback) {
        if(!has(event)) {
            throw new Error('there is no event');
        }
        events[event].on(callback);
    }

    this.off = function(event) {
        if(!has(event)) {
            throw new Error('there is no event');
        }
        events[event].off();
    }

    this.trigger = function(event, data) {
        if(!has(event)) {
            throw new Error('there is no event');
        }
        events[event].trigger(data);
    }
}

