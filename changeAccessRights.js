function show(filename, isRoot){
    JsHttpRequest.query(
        'changeAccessRights.php',
        {
            "filename": filename,
            "isRoot": isRoot
        },
        function(result){
            if (result.error != true){
                var firstVar = document.getElementById("firstVar");
                var secondVar = document.getElementById("secondVar");
                var closingWindow = document.getElementById("closingWindow");
                document.getElementById("fileAccessMod").innerHTML = result.mod_active;
                firstVar.value = result.mod_firstVar;
                secondVar.value = result.mod_secondVar;
                document.getElementById("filename").innerHTML = filename;
                if (result.mod_active == "Разделяемый(Доступ есть у выделенной группы пользователей)"){
                    document.getElementById("usersList-accessRights-hide").id = "usersList-accessRights-visible";
                    searchAccessRights(isRoot);
                }
                else if(document.getElementById("usersList-accessRights-visible")){
                    document.getElementById("usersList-accessRights-visible").id = "usersList-accessRights-hide";
                }
                if (isRoot){
                    firstVar.setAttribute('onclick', 'changeMod(this.value, true)');
                    secondVar.setAttribute('onclick', 'changeMod(this.value, true)');
                    if (result.mod_active == "Разделяемый(Доступ есть у выделенной группы пользователей)"){
                        document.getElementById("search-accessRights").setAttribute('onkeyup', 'searchAccessRights(true)');
                    }
                }
                else{
                    firstVar.setAttribute('onclick', 'changeMod(this.value, false)');
                    secondVar.setAttribute('onclick', 'changeMod(this.value, false)');
                    if (result.mod_active == "Разделяемый(Доступ есть у выделенной группы пользователей)"){
                        document.getElementById("search-accessRights").setAttribute('onkeyup', 'searchAccessRights(false)');
                    }
                }
                closingWindow.style.opacity = "75%";
                closingWindow.style.zIndex = "1";
                document.getElementById("changeAccessRights-hide").id = "changeAccessRights-visible";
                
            }
        },
        true
    );
}

function hide(){
    document.getElementById("filename").innerHTML = "";
    document.getElementById("closingWindow").style.opacity = "0%";
    document.getElementById("closingWindow").style.zIndex = "-1";
    document.getElementById("changeAccessRights-visible").id = "changeAccessRights-hide";
    document.getElementById("search-accessRights").value = "";
    document.getElementById("usersList-accessRights").innerHTML = "";
    if (document.getElementById("usersList-accessRights-visible")){
        document.getElementById("usersList-accessRights-visible").id = "usersList-accessRights-hide";
    }
}

function changeMod(newMod, isRoot){
    JsHttpRequest.query(
        'changeAccessRights.php',
        {
            "action": 'changeAccessRights',
            "filename": document.getElementById("filename").innerHTML,
            "newMod": newMod,
            "isRoot": isRoot
        },
        function(result){
            if (result.error != true){
                document.getElementById("fileAccessMod").innerHTML = result.mod_active;
                document.getElementById("firstVar").value = result.mod_firstVar;
                document.getElementById("secondVar").value = result.mod_secondVar;
                if (isRoot){
                    document.getElementById("accessRootMod").innerHTML = result.mod_active;
                }
                if (result.mod_active == "Разделяемый(Доступ есть у выделенной группы пользователей)"){
                    document.getElementById("usersList-accessRights-hide").id = "usersList-accessRights-visible";
                    searchAccessRights(isRoot);
                }
                else if(document.getElementById("usersList-accessRights-visible")){
                    document.getElementById("usersList-accessRights-visible").id = "usersList-accessRights-hide";
                }
            }
        },
        true
    );
}

function searchAccessRights(isRoot){
    JsHttpRequest.query(
        'searchAccessRights.php',
        {
            "filename": document.getElementById("filename").innerHTML,
            "isRoot": isRoot,
            "forSearch": document.getElementById("search-accessRights").value
        },
        function(result){
            if (result.error != true){
                document.getElementById("usersList-accessRights").innerHTML = result.usersList;
            }
        },
        true
    );
}

function check(username, isRoot){
    JsHttpRequest.query(
        'changeAccessRights.php',
        {
            "action": 'addToSharedAccess',
            "filename": document.getElementById("filename").innerHTML,
            "isRoot": isRoot,
            "username": username
        },
        function(){},
        true
    );
}