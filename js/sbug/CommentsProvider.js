;sbug.CommentsProvider = (new function() {
    'use strict';

    var self = this,
        comments = [],
        count = 100,
        allCount = null,
        events = new sbug.EventsContainer(),
        offset = 0;

    events.register('load');
    events.register('complete');

    this.on = function(event, callback) {
        events.on(event, callback);
    }

    this.off = function(event) {
        events.off(event);
    }

    this.getCount = function() {
        return allCount;
    }

    var add = function(data) {
        for(var i in data) {
            if(data.hasOwnProperty(i)) {
                comments.push(data[i]);
            }
        }
    }
    
    var init = function() {
        allCount = null;
        offset = 0;
        comments = [];
    }

    this.get = function() {
        return comments;
    }

    this.vkApiGetComments = function(data) {
        return sbug.Request.api('photos.getAllComments', {
            owner_id: data.owner_id,
            album_id: data.album_id,
            offset: offset,
            count: count
        }).then(function(cmts) {
            if(cmts.error) {
                var errDef = new $.Deferred();
                errDef.reject(cmts.error);
                return errDef.promise();
            }
            add(cmts.response.items);
            if(!allCount) {
                allCount = cmts.response.count;
            }
            events.trigger('load', {count: offset + cmts.response.items.length, all:cmts.response.count});
            offset += 100;
            return cmts;
        });
    }

    var getRequestCount = function(cnt) {
        return Math.ceil(cnt / count);
    }
    
    this.load = function(data) {
        init();
        var def = new $.Deferred();
        return self.vkApiGetComments(data).then(function(req) {
            def.resolve(true);

            var reqCount = getRequestCount(req.response.count) - 1;
            for(var i = 0; i < reqCount; ++i) {
                def = def.then(function() {
                    var def1 = new $.Deferred();
                    setTimeout(function() {
                        def1.resolve();
                    }, 500, data);
                    return def1.promise();
                }).then(function() {
                    return self.vkApiGetComments(data);
                });
            }
            def.done(function() {
                events.trigger('complete', comments);
            });
            return def.promise();
        });
    }
});