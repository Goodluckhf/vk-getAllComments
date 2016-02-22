<html>
    <head>
        <meta charset='UTF-8'>
        <script src="/js/vendor/jquery.min.js"></script>
        
        <script src="/js/sbug/sbug.js"></script>
        <script src="/js/sbug/Event.js"></script>
        <script src="/js/sbug/EventsContainer.js"></script>
        <script src="/js/sbug/Cookie.js"></script>
        <script src="/js/sbug/Request.js"></script>
        <script src="/js/sbug/AuthService.js"></script>
        <script src="/js/sbug/CommentsProvider.js"></script>
        <script src="/js/sbug/Loader.js"></script>
        <script src="/js/sbug/FormController.js"></script>

    </head>
    <body>
        <input style="width: 200px;" type='text' class='link-album' placeholder='Вставьте ссылку на альбом'>
        <button class='getComments'>Выгрузить</button>
        <div class="comments-list">
            <br>
            <br>
            
        </div>
       
    </body>
</html>


<script>
    $(function() {
        var delay = function(ms) {
            var def = $.Deferred();
            setTimeout(function() {
                def.resolve();
            }, ms);
            return def.promise();
        }
        
        $('body').on('click', '.getComments', function() {
            console.time('load');
            var loader = new sbug.Loader();
            loader.render();
            var $container = $('.comments-list');
            $container.html("<br><br>");

            sbug.CommentsProvider.load({
                owner_id: -20629724,
                album_id: 196682859
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
                                $container.append(comments[i].from_id + ' : ' + i +  '<br>');
                                return true;
                            });
                        })(i);
                    } else {
                        (function(i) {
                            def = def.then(function() {
                                $container.append(comments[i].from_id + ' : ' + i +  '<br>');
                                return true;
                            });
                        })(i);
                    }
                }
            }).fail(function(er) {
                console.log(er);
                loader.remove();
            });
        });
    });
</script>