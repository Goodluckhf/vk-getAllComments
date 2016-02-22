;sbug.Loader = function() {
    'use strict';

    var template = ' <div class="loader" style="position:absolute; top: 40%; left: 40%; padding:10px; width: 300px; border: 1px solid gray;">' +
        '<span style=" font-size: 30px; color: tomato"></span>' +
        '<img style="float:right; height:35px; width:auto;" src="ajax-loader.gif">' +
    '</div>';
    var $view;
    var populate = function(data) {
        var text = '';
        if(data) {
            text = data.count + ' из ' + data.all;
        } else {
            text = '0 из ...';
        }
        $view.find('span').text(text);
    }
    
    this.render = function() {
        $view = $(template);
        populate();
        $('body').append($view);
    }

    this.remove = function() {
        $view.remove();
    }

    sbug.CommentsProvider.on('load', function(data) {
        populate({
            count: data.count,
            all: data.all
        });
    });

    sbug.CommentsProvider.on('complete', function() {
        $view.remove();
    });
}