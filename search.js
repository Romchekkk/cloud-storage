function search(){
    JsHttpRequest.query(
        'search.php',
        {
            "forSearch": document.getElementById("search").value
        },
        function(result){
            if (result.error == false){
                document.getElementById("usersList").innerHTML = result.usersList;
            }
        },
        true
    );
}