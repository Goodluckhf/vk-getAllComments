<html>
    <head>
        <meta charset='UTF-8'>
        <script src="/jquery.min.js"></script>

    </head>
    <body>
        <input style="width: 200px;" type='text' class='link-album' placeholder='Вставьте ссылку на альбом'>
        <button class='getComments'>Выгрузить</button>
       
    </body>
</html>


<script>
    var Event = function(name) {
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
    var EventsContainer = function() {
        var events = {};

        var has = function(name) {
            return events[name];
        }

        this.register = function(name) {
            if(has(name)) {
                throw new Error('event is already exist');
            }
            events[name] = new Event(name);
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
    var Cookie = (new function() {
        var self = this;
        
        this.set = function(name, value, options) {
            options = options || {};

            var expires = options.expires;

            if (typeof expires == "number" && expires) {
              var d = new Date();
              d.setTime(d.getTime() + expires * 1000);
              expires = options.expires = d;
            }
            if (expires && expires.toUTCString) {
              options.expires = expires.toUTCString();
            }

            value = encodeURIComponent(value);

            var updatedCookie = name + "=" + value;

            for (var propName in options) {
              updatedCookie += "; " + propName;
              var propValue = options[propName];
              if (propValue !== true) {
                updatedCookie += "=" + propValue;
              }
            }

            document.cookie = updatedCookie;
        }
        this.remove = function(name) {
            self.set(name, "", {
                expires: -1
            })
        }
        
        this.get = function(name) {
            var matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            return matches ? decodeURIComponent(matches[1]) : undefined;
        }
        
    });
    
    var Request = (new function() {
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

        this.api = function(method, data) {
            var newData = {};
            newData.url = 'https://api.vk.com/method/' + method;
            newData.data = data;
            return sendXhr(newData);
        }
        
        this.send = function(data) {
            return sendXhr(data);
        }
        var sendXhr = function(data) {
            res = addExtraData(data.data);
            return $.ajax({
                url: data.url,
                method: 'post',
                dataType: 'jsonp',
                data: res
            });
        }
    });
    var AuthService = (new function() {
        var self = this,
            user,
            isAuth = false,
            cookieNameToken = 'vk-auth-token',
            cookieNameExpire = 'vk-auth-expire',
            cookieNameId = 'vk-auth-id';

        
        var authorize = function(data) {
            Cookie.set(cookieNameToken, data.token, {expires: data.expire});
            Cookie.set(cookieNameExpire, data.expire, {expires: data.expire});
            Cookie.set(cookieNameId, data.id, {expires: data.expire});
            Request.setExtra({
                v: 5.45,
                access_token: data.token
            });
            user = data;
            isAuth = true;
        }

        var hasHash = function() {
            console.log(window.location.hash);
            return window.location.hash !== '';
        }

        var getTokenFromUrl = function() {
            var hash = window.location.hash;
            var arToken = hash.substr(1).split('&');
            console.log(arToken);
            return {
                token: arToken[0].split('=')[1],
                id: arToken[1].split('=')[1],
                expire: arToken[2].split('=')[1]
            };
        }

        this.auth = function() {
            var cookieToken = Cookie.get(cookieNameToken);
            var cookieExpire = Cookie.get(cookieNameToken);
            var cookieId = Cookie.get(cookieNameToken);
            if(cookieToken) {
                authorize({
                    id: cookieId,
                    expire: cookieExpire,
                    token: cookieToken
                });
                return true;

            }
            if(hasHash()) {
                var newToken = getTokenFromUrl();
                authorize(newToken);
                window.location.href = '/';
                return true;
            }
            window.location.href = 'https://oauth.vk.com/authorize?client_id=5180832&display=popup&redirect_uri=http://theme-id.dev&scope=photos&response_type=token&v=5.45';
        }
        self.auth();
    });
    var CommentsProvider = (new function() {
        var self = this,
            comments = [],
            count = 100,
            hasNext = false,
            events = new EventsContainer(),
            offset = 0;

        events.register('load');
        events.register('complete');

        this.on = function(event, callback) {
            events.on(event, callback);
        }

        this.off = function(event) {
            events.off(event);
        }

        var checkHasNext = function(cnt) {
            console.log(cnt);
            console.log(count + offset);
            if(cnt > count + offset) {
                hasNext = true;
            } else {
                hasNext = false;
            }
        }
        var add = function(data) {
            for(var i in data) {
                if(data.hasOwnProperty(i)) {
                    comments.push(data[i]);
                }
            }
        }
        this.get = function() {
            return comments;
        }
        this.vkApiGetComments = function(data) {
            return Request.api('photos.getAllComments', {
                owner_id: data.owner_id,
                album_id: data.album_id,
                offset: offset,
                count: count
            }).done(function(cmts) {
                events.trigger('load', {count: offset + cmts.response.items.length, all:cmts.response.count});
                add(cmts.response.items);
                offset += 100;
                return cmts;
            });
        }
        var getRequestCount = function(cnt) {
            return Math.ceil(cnt / count);
        }
        this.load = function(data) {
            var def = new $.Deferred();
            return self.vkApiGetComments(data).then(function(req) {
                def.resolve(true);
                var reqCount = getRequestCount(req.response.count) - 1;
                for(var i = 0; i < reqCount; ++i) {
                    def = def.then(function() {
                        var def1 = new $.Deferred();
                        setTimeout(function() {
                            def1.then(function() {
                                return self.vkApiGetComments(data);
                            });
                            def1.resolve();
                        }, 500, data);
                        return def1.promise();
                    });
                }
                def.done(function() {
                    events.trigger('complete', comments);
                });
                return def.promise();
            });
        }
    });
    
    var Loader = function() {
        var template = ' <div class="loader" style="position:absolute; top: 40%; left: 40%; padding:10px; width: 200px; border: 1px solid gray;">' +
            '<span style=" font-size: 30px; color: tomato"></span>' +
            '<img style="float:right; height:30px; width:auto;" src="ajax-loader.gif">' +
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
        
        CommentsProvider.on('load', function(data) {
            console.log(data);
            //console.log(all);

            populate({
                count: data.count,
                all: data.all
            });
        });
        CommentsProvider.on('complete', function() {
            $view.remove();
        });
    }

    $(function() {
        $('body').on('click', '.getComments', function() {
            console.time('load');
            var loader = new Loader();
            loader.render();

            CommentsProvider.load({
                owner_id: -20629724,
                album_id: 196682859
            }).done(function() {
                console.timeEnd('load');
                console.log(CommentsProvider.get());
            }).fail(function(er) {
                alert(JSON.stringify(er));
            });
        });

        
        //Request.api('')
    });
</script>