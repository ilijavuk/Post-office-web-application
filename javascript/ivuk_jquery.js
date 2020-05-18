
//popis
if(location.href.includes("popis.html")){
    $(document).ready(function() {
        var searchTags = new Array();
        $.ajax({
            method: "POST",
            url: "../json/search.json",
            success: function(data){
                $.each(data,function(key, val){
                    searchTags.push(val);
                })
            }
        })
        $(".searchField").autocomplete({
            source: searchTags
        });
    });
    $.ajax({
        url: "https://barka.foi.hr/WebDiP/2019/materijali/zadace/dz3/userNameSurname.php?all",
        success: function(data){
            var redci = $(data).find("user");
            for(i = 0; i < redci.length; i++){
                var redak = "<tr><td>"+
                $(redci).find("name")[i].textContent + "</td><td>" + 
                $(redci).find("surname")[i].textContent + "</td><td class='email'>" + 
                $(redci).find("email")[i].textContent + "</td><td>" + 
                "<img src='../"+$(redci).find("image")[i].textContent+"' alt = 'Slika ne postoji!' height='50' width='50' >" + "</td></tr>";
                
                $("#tbody").append(redak);
            }
	    $(".email").unbind().click(function(){checkUserExists(this)});
            $('#tablica').DataTable();
        }
  
    });
    function checkUserExists(objekt){        
        var roditelj = $(objekt).parent();
        var redci = roditelj.find("td");
        var ime = redci[0].textContent;
        var prezime = redci[1].textContent;
        var email = redci[2].textContent;
        var slika = redci[3].textContent;
        $.ajax({
            url: "https://barka.foi.hr/WebDiP/2019/materijali/zadace/dz3/userNameSurname.php?name="+ime+"&surname="+prezime+"",
            success: function(data){
                var username = $(data).find("username")[0].textContent;
                var aktivacijskiKod = $(data).find("code")[0].textContent;
                if(username == 0)
                    alert("Taj korisnik ne postoji");
                else if(username != 0 && aktivacijskiKod == "")
                    alert(`Aktivacijski kod za korisnika ${ime} ne postoji`);
                else{
                    
                    clearCookie();
                    document.cookie = `ime=${ime};path=/`;
                    document.cookie = `prezime=${prezime};path=/`;
                    document.cookie = `email=${email};path=/`;
                    document.cookie = `slika=${slika};path=/`;
                    document.cookie = `korisnickoIme=${username};path=/`;
                    document.cookie = `code=${aktivacijskiKod};path=/`;
                    document.location.href="../obrasci/registracija.html";
                }
            }
        })
    }
    function clearCookie(){
        var kolacic = document.cookie.split('; ');
        for (var i = 0; i < kolacic.length; i++) {
            naziv = kolacic[i].split("=")[0];
            document.cookie = naziv + '=;path=/;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        }
    }
}
if(location.href.includes("index.html")||location.href.includes("era.html")||location.href.includes("navigacija.html")){
    $(document).ready(function() {
        var searchTags = new Array();
        $.ajax({
            method: "post",
            url: "json/search.json",
            success: function(data){
                $.each(data,function(key, val){
                    searchTags.push(val);
                })
            }
        })
        $(".searchField").autocomplete({
            source: searchTags
        });
    });
}
else if(location.href.includes("obrazac.html")||location.href.includes("prijava.html")||location.href.includes("multimedija.html")){
    $(document).ready(function() {
        var searchTags = new Array();
        $.ajax({
            method: "POST",
            url: "../json/search.json",
            success: function(data){
                $.each(data,function(key, val){
                    searchTags.push(val);
                })
            }
        })
        $(".searchField").autocomplete({
            source: searchTags
        });
    });
}
//registracija
if(location.href.includes("registracija.html")){
    $(document).ready(function() {
        var searchTags = new Array();
        $.getJSON("../json/search.json", 
            function(data){
                $.each(data, function(key, val){
                    searchTags.push(val);
                });
            });
        $(".searchField").autocomplete({
            source: searchTags
        });
    });
    $(document).ready(function() {
        $("#lozinka").on("keyup",function(){pronadjiUsera();});
        $("#url").on("keyup", function(){urlVerifikacija(this.value);});
        $("#tel").on("keyup", function(){telVerifikacija(this.value);});
        $("#submitBtn").click(function(e){
            if( pronadjiUsera() && urlVerifikacija($("#url").val()) && telVerifikacija($("#tel").val()) && lozinkaVerifikacija()){
                return true;
            }
            else{
                return false;
            }
        });
    });

    var kolacic = document.cookie.split("; ");
    for(var i = 0; i < kolacic.length; i++){
        var naziv = kolacic[i].split("=")[0];
        var vrijednost = kolacic[i].split("=")[1]
        switch(naziv){
            case "ime": $("#ime").val(vrijednost);break;
            case "prezime": $("#prezime").val(vrijednost);break;
            case "email": $("#email").val(vrijednost);break;
            case "korisnickoIme": $("#korisnickoIme").val(vrijednost);break;
        }
    } 
    //dohvacanje jsona
    var json;
    $.ajax({
        type: "Get",
        url: "../json/users.json",
        dataType: "json",
        success: function(data) {
            json = data;
        }
    });
    function pronadjiUsera(){
        var ime = $("#ime").val();
        var prezime = $("#prezime").val();
        var email = $("#email").val();
        var lozinka = $("#lozinka").val();
        $("#lozinka").parent().get(0).classList.add("error");
        for(var i = 0; i < json.length; i++){
            if(json[i]["name"] == ime && json[i]["surname"] == prezime && json[i]["email"] == email && json[i]["password"] == lozinka){
                $("#lozinka").parent().get(0).classList.remove("error");
                return true;
            }
        }
        return false;
    }
    function urlVerifikacija(vrijednost){
        const regex = RegExp("(((http(s)?(\\:\\/\\/))+(www\\.)?([\\w\\-\\/])+(\\.[a-zA-Z]{2,3}\\/?))[^\\s]*[^.,;:\\?\\!\@\\^\\$-])(.\\.hr)$");
        var test = regex.test(vrijednost);
        if(test == false){
            $("#url").parent().get(0).classList.add("error");
            return false;
        }
        else{
            $("#url").parent().get(0).classList.remove("error");
            return true;
        }
            
    }
    function telVerifikacija(vrijednost){
        const regex = RegExp("^(((00)\\d{8,12}|\\+\\d{8,13}))$");
        var test = regex.test(vrijednost);
        if(test == false){
            $("#tel").parent().get(0).classList.add("error");
            return false;
        }
        else{
            $("#tel").parent().get(0).classList.remove("error");
            return true;
        }
    }
    function lozinkaVerifikacija(){
        var vrijednost1 = $("#lozinka").val();
        var vrijednost2 = $("#potvrdalozinke").val();
        if(vrijednost1 != vrijednost2){
            $("#potvrdalozinke").parent().get(0).classList.add("error");
            return false;
        }
        else{
            $("#potvrdalozinke").parent().get(0).classList.remove("error");
            return true;
        }
    }
}
