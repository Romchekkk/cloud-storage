function userFunctions(action, newMod, dirName){
    JsHttpRequest.query(
        'userFunctions.php',
        {
            "action": action,
            "newMod": newMod,
            "dirName": dirName
        },
        function(result, error){
            document.getElementById("window").innerHTML = result.window;
        },
        true
    );
}