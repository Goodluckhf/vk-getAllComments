;sbug.FormController = function() {
    'use strict';

    var self = this,
        inputSelector = 'input.link-album',
        submitSelector = 'button.getComments',
        listSelector = 'div.comments-list';



    var getDataByLink = function(str) {
        var match = /album(.+)/.exec(str);

        var data;
        if(match) {
            var index = match[1].indexOf('?');
            if(index !== -1) {
                match[1] = match[1].substring(0, index);
            }
            data = match[1].split('_');
            return {
                owner: data[0],
                album: data[1]
            };
        }
        return false;
    }

    var delay = function(ms) {
        var def = $.Deferred();
        setTimeout(function() {
            def.resolve();
        }, ms);
        return def.promise();
    }

    var renderItem = function(comment) {
        return comment.from_id + '<br>';
    }

    $('body').off('click', submitSelector);
    $('body').on('click', submitSelector, function() {
        var link = $(inputSelector).val().trim();
        var options = getDataByLink(link);
        if(!options) {
            alert('ошибочный формат ссылки');
            return;
        }

        console.time('load');
        var loader = new sbug.Loader();
        loader.render();
        var $container = $(listSelector);
        $container.html("<br><br>");

        sbug.CommentsProvider.load({
            owner_id: options.owner,
            album_id: options.album
        }).then(function() {
            console.timeEnd('load');
            var comments = sbug.CommentsProvider.get();
            var len = sbug.CommentsProvider.getCount();

            var def = $.Deferred();
            def.resolve();
            for(var i = 0; i < len; i++) {
                if(i % 50 === 0) {
                    (function(i) {
                        def = def.then(function() {
                            return delay(30);
                        }).then(function() {
                            $container.append(renderItem(comments[i]));
                            return true;
                        });
                    })(i);
                } else {
                    (function(i) {
                        def = def.then(function() {
                            $container.append(renderItem(comments[i]));
                            return true;
                        });
                    })(i);
                }
            }
        }).fail(function(er) {
            loader.remove();
            alert(JSON.stringify(er));
        });
    });
}