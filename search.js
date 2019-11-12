function search(){
    JsHttpRequest.query(
        'search.php',
        {
            "forSearch": document.getElementById("search").value
        },
        function(result){
            document.getElementById("usersList").innerHTML = result.usersList;
        },
        true
    );
}