
function createDirectory(){
    JsHttpRequest.query(
        'userFunctions.php',
        {
            "action": 'createDirectory',
            "dirName": document.getElementById("dirName").value
        },
        function(result, error){
            document.getElementById("window-bottom").innerHTML = result.window;
        },
        true
    );
}

function deleteDirectory(dirName){
    JsHttpRequest.query(
        'userFunctions.php',
        {
            "action": 'deleteDirectory',
            "dirName": dirName
        },
        function(result, error){
            document.getElementById("window-bottom").innerHTML = result.window;
        },
        true
    );
}

function changeDirectory(dirName){
    JsHttpRequest.query(
        'userFunctions.php',
        {
            "action": 'changeDirectory',
            "dirName": dirName
        },
        function(result, error){
            document.getElementById("window-bottom").innerHTML = result.window;
        },
        true
    );
}