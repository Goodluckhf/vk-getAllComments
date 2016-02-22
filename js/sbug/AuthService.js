;sbug.AuthService = (new function() {
    'use strict';

    var self = this,
        user,
        isAuth = false,
        cookieNameToken = 'vk-auth-token';

    var authorize = function(data) {
        sbug.Cookie.set(cookieNameToken, data.token);

        sbug.Request.setExtra({
            v: 5.45,
            access_token: data.token
        });
        user = data;
        isAuth = true;
    }

    var hasHash = function() {
        return window.location.hash !== '';
    }

    var getTokenFromUrl = function() {
        var hash = window.location.hash;
        var arToken = hash.substr(1).split('&');

        return {
            token: arToken[0].split('=')[1],
            id: arToken[1].split('=')[1],
            expire: arToken[2].split('=')[1]
        };
    }

    this.auth = function() {
        var cookieToken = sbug.Cookie.get(cookieNameToken);

        if(cookieToken) {
            authorize({
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
        window.location.href = 'https://oauth.vk.com/authorize?client_id=5180832&display=popup&redirect_uri=http://old.sb-ug.ru/comments&scope=photos&response_type=token&v=5.45';
    }
    self.auth();
});