window.onload = function(){ 
    if(!location.href.includes("index.html"))
     document.getElementById("navButtonOff").addEventListener("click",function(){switchNav(this);})
    document.getElementById("toTopBtn").addEventListener("click",function(){toTop(this);})

    var on = 0;
    var navBar = document.getElementsByTagName('nav')[0];
    var wrapper = document.getElementById('navWrapper');
    function switchNav(item){
        if(on == 0){
            navBar.style.visibility = "visible";
            on = 1;
            item.id = "navButtonOn";
            item.innerHTML = "✕";
            wrapper.style.border = "1px solid black";    
            wrapper.style.backgroundColor = "#CFCFCF";    
        }
        else if(on == 1){
            navBar.style.visibility = "hidden";
            on = 0;
            item.id = "navButtonOff";
            item.innerHTML = "\u00A0≡\u00A0";
            wrapper.style.border = "none";
            wrapper.style.backgroundColor = "";
        }
    }
    //registracija

    function validirajLozinku(){
        var lozinka = document.getElementById("lozinka");
        var potvrdaLozinke = document.getElementById("potvrdalozinke");
        if(potvrdaLozinke){
            if(lozinka.value != potvrdaLozinke.value) {
                potvrdaLozinke.setCustomValidity("Lozinke se ne podudaraju");
            } else {
                potvrdaLozinke.setCustomValidity('');
            }
        }
    }
    
    //obrazac
    if(location.href.includes("obrazac.html")){
        var url = document.getElementById("url");
        var tel = document.getElementById("tel");
        url.addEventListener("input", function(){urlVerifikacija(this, this.value);});
        tel.addEventListener("input", function(){telVerifikacija(this, this.value);});
        document.getElementById("jacinaMotora").addEventListener("input", function(){vrijednost(this.value);});
        document.getElementById("favcolor").addEventListener("click", function(){this.value="#666666";});
        document.getElementById("favcolor").addEventListener("input", function(){changeColor(this.value);});
        document.getElementById("crvena").addEventListener("click", function(){changeColor("crvena");});
        document.getElementById("zelena").addEventListener("click", function(){changeColor("zelena");});
        document.getElementById("plava").addEventListener("click", function(){changeColor("plava");});
        document.getElementsByClassName("formWrapper")[0].addEventListener("input", function(){
            if(urlVerifikacija(url, url.value) && telVerifikacija(tel, tel.value)){
                document.getElementById("submitBtn").removeAttribute("disabled")
            }
        })
        document.getElementById("referrer").addEventListener("click",function(){window.history.back();})
        var userLang = navigator.language || navigator.userLanguage; 
        document.getElementById("lang").value = userLang;
        var h2 = document.getElementById("referrer");
        var splitreferrer = document.referrer.split("/");
        var duljina = splitreferrer.length;
        h2.innerHTML = splitreferrer[duljina-1];
        
        var submit = document.getElementById("submitBtn");
        submit.disabled = true;
    }

    function vrijednost(vrijednost){
        document.getElementById("rangelbl").innerHTML = vrijednost + " kW";
    }
    function toTop(button){
        document.body.scrollTop = 0;//Safari
        document.documentElement.scrollTop = 0;
    }
    function changeColor(vrijednost){
        if(vrijednost.startsWith("#")){
            document.getElementById("favcolor").value = vrijednost;
            document.body.style.backgroundColor = vrijednost;
        }
        else{
            var crvena = document.getElementById("crvena");
            var zelena = document.getElementById("zelena");
            var plava = document.getElementById("plava");
            var colorSelector = document.getElementById("favcolor");
            if(crvena.checked || zelena.checked || plava.checked)
                colorSelector.disabled = true;
            else{
                colorSelector.removeAttribute("disabled");
                document.body.style.backgroundColor = "white";
                document.getElementById("favcolor").value= "#FFFFFF";
            }
            if(crvena.checked){
                colorSelector.value = "#FF0000";
                document.body.style.backgroundColor = "red";
            }
            if(zelena.checked){
                colorSelector.value = "#008000";
                document.body.style.backgroundColor = "green";
            }
            if(plava.checked){
                colorSelector.value = "#0000ff";
                document.body.style.backgroundColor = "blue";
            }
            if(crvena.checked && zelena.checked){
                colorSelector.value = "#a52a2a";
                document.body.style.backgroundColor = "brown";
            }
            if(crvena.checked && plava.checked){
                document.body.style.backgroundColor = "purple"
                colorSelector.value = "#800080";
            }
            if(zelena.checked && plava.checked){
                document.body.style.backgroundColor = "yellow";
                colorSelector.value = "#ffff00";
            }
            if(crvena.checked && zelena.checked && plava.checked){
                document.body.style.backgroundColor = "black";
                colorSelector.value = "#000000";
            }         
        }   
    }

    function telVerifikacija(objekt, vrijednost){
        document.getElementById("submitBtn").disabled = true;
        if(vrijednost.length > 14)
            objekt.setCustomValidity("Broj je predugačak");
        else if(vrijednost.length == 0)
            objekt.setCustomValidity("Niste unijeli telefon");
        else if(!vrijednost.startsWith("+") && !vrijednost.startsWith("00") )
            objekt.setCustomValidity("Broj ne počinje sa +/00");
        else if(isNaN(vrijednost.substr(1)) || vrijednost.includes(".")){
            objekt.setCustomValidity("Broj sadrži znamenke");
            console.log(vrijednost.substr(1));
        }
        else{
            objekt.setCustomValidity('')
            return true;
        }
    }

    function urlVerifikacija(objekt, vrijednost){
        document.getElementById("submitBtn").disabled = true;
        if(!vrijednost.startsWith("http://") && !vrijednost.startsWith("https://") || vrijednost.startsWith("http://.") || vrijednost.startsWith("https://."))
            objekt.setCustomValidity("Url ima krivi početak");
        else if(vrijednost.length == 0)
            objekt.setCustomValidity("Niste unijeli url");
        else if(vrijednost.includes(".."))
            objekt.setCustomValidity("U urlu imate \"..\"");
        else if(!vrijednost.endsWith(".hr"))
            objekt.setCustomValidity("Url ne završava sa .hr");
        else{
            objekt.setCustomValidity("");
            return true;
        }
    }

    var on = 0;
    var navBar = document.getElementsByTagName('nav')[0];
    var wrapper = document.getElementById('navWrapper');
    function switchNav(item){
        if(on == 0){
            navBar.style.visibility = "visible";
            on = 1;
            item.id = "navButtonOn";
            item.innerHTML = "✕";
            wrapper.style.border = "1px solid black";    
            wrapper.style.backgroundColor = "#CFCFCF";    
        }
        else if(on == 1){
            navBar.style.visibility = "hidden";
            on = 0;
            item.id = "navButtonOff";
            item.innerHTML = "\u00A0≡\u00A0";
            wrapper.style.border = "none";
            wrapper.style.backgroundColor = "";
        }
    }
    //registracija
    function validirajLozinku(){
        var lozinka = document.getElementById("lozinka");
        var potvrdaLozinke = document.getElementById("potvrdalozinke");
        if(potvrdaLozinke){
            if(lozinka.value != potvrdaLozinke.value) {
                potvrdaLozinke.setCustomValidity("Lozinke se ne podudaraju");
            } else {
                potvrdaLozinke.setCustomValidity('');
            }
        }
    }
}
