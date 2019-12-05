function show(filename, isRoot){
    var firstVar = document.getElementById("firstVar");
    var secondVar = document.getElementById("secondVar");
    JsHttpRequest.query(
        'changeAccessRights.php',
        {
            "filename": filename,
            "isRoot": isRoot
        },
        function(result){
            if (result.error != true){
                document.getElementById("fileAccessMod").innerHTML = result.mod_active;
                firstVar.value = result.mod_firstVar;
                secondVar.value = result.mod_secondVar;
                if (isRoot){
                    firstVar.setAttribute('onclick', 'changeMod(this.value, true)');
                    secondVar.setAttribute('onclick', 'changeMod(this.value, true)');
                }
                else{
                    firstVar.setAttribute('onclick', 'changeMod(this.value, false)');
                    secondVar.setAttribute('onclick', 'changeMod(this.value, false)');
                }
                document.getElementById("filename").innerHTML = filename;
                document.getElementById("closingWindow").style.opacity = "75%";
                document.getElementById("closingWindow").style.zIndex = "1";
                document.getElementById("changeAccessRights-hide").id = "changeAccessRights-visible";
            }
        },
        true
    );
}

function hide(){
    document.getElementById("filename").innerHTML = "";
    document.getElementById("changeAccessRights-visible").id = "changeAccessRights-hide";
    document.getElementById("closingWindow").style.opacity = "0%";
    document.getElementById("closingWindow").style.zIndex = "-1";
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
            }
        },
        true
    );
}