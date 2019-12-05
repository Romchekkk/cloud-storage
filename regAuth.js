function setAuth(){
    document.getElementById("reg").style.borderTop = "1px solid black";
    document.getElementById("auth").style.borderTop = "none";
    document.getElementById("regAuth").value = "Войти";
    document.getElementsByName("username")[0].type = "hidden";
    document.getElementById("error").innerHTML = "";
}

function setReg(){
    document.getElementById("reg").style.borderTop = "none";
    document.getElementById("auth").style.borderTop = "1px solid black";
    document.getElementById("regAuth").value = "Зарегистрироваться";
    document.getElementsByName("username")[0].type = "text";
    document.getElementById("error").innerHTML = "";
}