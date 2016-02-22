<html>
    <head>
        <script>
//            var letters = "АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
//            var lettersCount = 32;
//            var getKey = function(chr) {
//                return chr.charCodeAt(0) % 1040;
//            }
//            var crypt = function(str, key) {
//                var encodedStr = '';
//                for(var i = 0; i < str.length; i++) {
//                    var char = str[i].toUpperCase();
//                    var current = getKey(char);
//                    encodedStr += letters[(current + key) % lettersCount];
//                }
//                return encodedStr;
            //}

            var crypt = function(str, key) {
                var decodedString = '';
                for(var i = 0; i < str.length; i++) {
                    console.log(String.fromCharCode(str.charCodeAt(i) + key));
                    decodedString += String.fromCharCode(str.charCodeAt(i) + key);
                }
                return decodedString;
            }

            console.log(crypt("a костян", 2));

        </script>

    </head>
    <body>
        
    </body>
</html>