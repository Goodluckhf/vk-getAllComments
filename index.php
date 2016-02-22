<!DOCTYPE html>
<html>
    <head>
        <meta charset='UTF-8'>
        <link rel="stylesheet" href="css/bootstrap.min.css">
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
        <div class="container">
            <input class="form-control link-album" style="width: 500px; float:left;" type='text' placeholder='Вставьте ссылку на альбом'>
            <button class='getComments btn btn-primary'>Выгрузить</button>
            <div class="comments-list">
                <br>
                <br>

            </div>
        </div>
    </body>
</html>


<script>
    $(function() {
        var form = new sbug.FormController();
    });
</script>