$(()=>{
    var kolacic = document.cookie.split("; ");
    let prihvatioUvjete = false;
    for(var i = 0; i < kolacic.length; i++){
        var naziv = kolacic[i].split("=")[0];
        var vrijednost = kolacic[i].split("=")[1]
        if(naziv == 'uvjetiKoristenja' && vrijednost == '1'){
            prihvatioUvjete = true;
        }
    } 
    if(!prihvatioUvjete){
        let cookiePopup = '<div id="cookiePopup"><p>Ova stranica koristi kolačiće. Korištenjem ove stranice prihvaćate naše uvjete korištenja <input type="button" id="acceptCookies" value="Slažem se"></p></div>';
        $("body").append(cookiePopup);
        $('#acceptCookies').parents('div').fadeIn( 1000 );
        $("#acceptCookies").click(()=>{
            $('#acceptCookies').parents('div').fadeOut( 1000 );
            $.ajax({
                url: "api.php?fetch_trajanjeKolacica",
            }).done(function(data) {
                document.cookie = `uvjetiKoristenja=1;path=/;Max-Age=${data}`;
                $('#acceptCookies').parents('div').fadeOut( 1000 );

                $.ajax({
                    url: "api.php?update_cookiesAccept",
                }).done(function(data) {
                    $("#snackbar").html('Uvjeti prihvaćeni');
                    showSnackbar();
                });

            });
        });
    }

    let on = false;
    $("#navButton").click(() => {
        if(on){
            on = false;
            $("#navButton").removeClass('On');
            $("#wrapper").removeClass('rotateOut')
            $("#wrapper").addClass('rotateIn')
            $($(".navBar")[0]).addClass('slide-out');
            $($(".navBar")[0]).removeClass('slide-in');
        }
        else{
            on = true;
            $("#navButton").addClass('On');
            $("#wrapper").addClass('rotateOut')
            $("#wrapper").removeClass('rotateIn')
            $($(".navBar")[0]).addClass('slide-in');
            $($(".navBar")[0]).removeClass('slide-out');
        }
    });

    if(window.location.href.includes("login")){
        $("#forgottenUsername").click(function(){
            forgottenPassword($(this));
        })
        let loggingInWithUsername = true;
        function forgottenPassword(objekt){
            if(loggingInWithUsername){
                loggingInWithUsername = false;
                $(objekt).html("Zaboravili ste e-mail? Prijavite se pomoću korisničkog imena")
                $("#korisnicko_imeTextBox").hide();
                $("#emailTextBox").show();
            }
            else{
                loggingInWithUsername = true;
                $(objekt).html("Zaboravili ste korisničko ime? Prijavite se pomoću e-maila")
                $("#korisnicko_imeTextBox").show();
                $("#emailTextBox").hide();
            }
        }

        $('#submitBtn').click(()=>{
            let provjeraProsla = true;
            
            if($("#lozinka").val() == ''){
                $("#lozinka").css('outline', 'solid 1px red');
            }

            switch(loggingInWithUsername){
                case true: 
                    if($("#korisnicko_ime").val() == ''){
                        $("#korisnicko_ime").css('outline', 'solid 1px red');
                    }
                    else if($("#korisnicko_ime").val() != ''){
                        $.ajax({
                            method: 'POST',
                            url: "api.php?login",
                            data:{korisnicko_ime: $("#korisnicko_ime").val(), lozinka: $("#lozinka").val() }
                        }).done(function(data) {
                            console.log(data);
                            prikaziOdgovor(data);
                        });
                    }
                ;break;
                case false: 
                    if($("#email").val() == ''){
                        $("#email").css('outline', 'solid 1px red');
                    }
                    else if($("#email").val() != ''){
                        $.ajax({
                            method: 'POST',
                            url: "api.php?login",
                            data:{email: $("#email").val(), lozinka: $("#lozinka").val() }
                        }).done(function(data) {
                            prikaziOdgovor(data);
                        });
                    }
                ;break;
            }
            
        })

        function prikaziOdgovor(odgovor){
            switch(odgovor){
                case "1": location.href='index.php';break;
                case "0": $("#snackbar").html('Prijava nije uspjela');break;
                case "Zaključani ste!":  $("#snackbar").html('Zaključani ste!');break;
            }
            showSnackbar();
        }
    }

    if(window.location.href.includes("register")){
        $(".textbox").each(function(){
            if($($(this).children()[1]).val() == ''){
                $($(this).children()[1]).on('input',function(){$(this).css('outline','none');})
            }
        })

        $("#submitBtn").click(()=>{
            let provjeraProsla = true;
            $(".textbox").each(function(){
                if($($(this).children()[1]).val() == ''){
                    $($(this).children()[1]).css('outline','solid 1px red');
                    provjeraProsla = false;
                    $("#snackbar").html('Niste popunili sva polja');
                    showSnackbar();
                }
            })

            if($("#lozinka").val() != $("#potvrda_lozinke").val()){
                $("#potvrda_lozinke").css('outline', 'solid 1px red');
                $("#snackbar").html('Lozinke se ne podudaraju');
                showSnackbar();
                provjeraProsla = false;
            }

            if($("#korisnicko_ime").val().length < 3){
                $("#snackbar").html('Korisničko ime je prekratko (min 3 znamenke)');
                showSnackbar();
                provjeraProslaProsla = false;
            }
            
            $.ajax({
                method: 'POST',
                url: "api.php?fetch_korisnickoIme",
                data:{korisnicko_ime: $("#korisnicko_ime").val()}
            }).done(function(data) {
                if(data != '0'){
                    provjeraProsla = false;
                    $("#snackbar").html('To korisničko ime je već zauzeto');
                    showSnackbar();
                }
            });

            if(provjeraProsla){
                $.ajax({
                    method: 'POST',
                    url: "api.php?insert_korisnik",
                    data:{ime: $("#ime").val(), prezime: $("#prezime").val(), korisnicko_ime: $("#korisnicko_ime").val(), email: $("#email").val(), lozinka: $("#lozinka").val() }
                }).done(function(data) {
                    if(data=="uspjeh"){
                        window.location.href="login.php";
                    }
                });
            }
        })
    }

    if(window.location.href.includes("uredi")){
        let poljeSPodatcima = '';
        let sort1 = -1; //1 = desc ↓ | 2 = asc ↑
        let sort2 = -1; //1 = desc ↓ | 2 = asc ↑
      
        $.ajax({
            url: "api.php?fetch_postanskiUred",
        }).done(function(data) {
            poljeSPodatcima = data;
        });

        $("#search").on('input', ()=>{
            var zadnji = document.getElementsByTagName("tbody")[0].lastElementChild;
            $array = search($("#search").val(), poljeSPodatcima);
            $("tbody").append($array)

            if(zadnji != null && zadnji.id == 'elementZaDodavanje'){
                $("tbody").append(zadnji);
            }
                
        });

        $("#broj_poslanih").click(()=>{
            sort1 = ((sort1+1)%2);
            if(sort1 == -1){
                $("#broj_poslanih").html("Broj poslanih");
            }
            else if(sort1 == 0){
                $("#broj_poslanih").html("Broj poslanih ▼");
                $("#broj_primljenih").html("Broj primljenih");
            }
            else if(sort1 == 1){
                $("#broj_poslanih").html("Broj poslanih ▲");
                $("#broj_primljenih").html("Broj primljenih");
            }
            
            var zadnji = document.getElementsByTagName("tbody")[0].lastElementChild;
            $array = sortArray(sort1,4);

            $array.each(function(index){
                $("tbody").append($(this)[0])
            });
            
            if(zadnji != null && zadnji.id == 'elementZaDodavanje'){
                $("tbody").append(zadnji);
            }
        })

        $("#broj_primljenih").click(()=>{
            sort2 = ((sort2+1)%2);
            if(sort2 == -1){
                $("#broj_primljenih").html("Broj primljenih");
            }
            else if(sort2 == 0){
                $("#broj_primljenih").html("Broj primljenih ▼");
                $("#broj_poslanih").html("Broj poslanih");
            }
            else if(sort2 == 1){
                $("#broj_primljenih").html("Broj primljenih ▲");
                $("#broj_poslanih").html("Broj poslanih");
            }
            
            var zadnji = document.getElementsByTagName("tbody")[0].lastElementChild;
            $array = sortArray(sort2,5);

            $array.each(function(index){
                $("tbody").append($(this)[0])
            });
            if(zadnji != null && zadnji.id == 'elementZaDodavanje'){
                $("tbody").append(zadnji);
            }
        })

        $(".postanskiUred").click(function(){
            $.ajax({
                method: "POST",
                url: "api.php?fetch_galerija",
                data: {postanskiUred_id: $(this).children()[6].innerHTML },
            }).done(function(data) {
                polje=(data.split(' '));
                if(polje.length > 1){
                    $('.modal').show();
                    $('#overlay').show();
                    polje.forEach(element => {
                        if(element != ''){
                            let e = `<figure class="galleryFigure"><img src=${element} style="width:100%;"/></figure>`;
                            $("#gallery").append(e);
                        }}
                    ); 
                }
                else{
                    $("#snackbar").html('Za taj ured ne postoje slike');
                    showSnackbar();
                }
            });
        })

        $("#overlay").click(()=>{
            $('.modal').hide();
            $('#overlay').hide();
            $($("#gallery").children()).each(function(){
                $(this).remove()
            });
        })

        function sortArray(sort, sorting){
            let length =  $("tbody").children(":not(:last-child)").length-1;
            $array =  $("tbody").children(":not(:last-child)");
            $("tbody").empty();

            for(var i = 0; i < length; length--){
                var ekstrem = $array[i].children[sorting].innerHTML;
                var indeks = i;
                for(var j = i; j < length; j++){
                    if(sort == 0){
                        if($array[j].children[sorting].innerHTML < ekstrem){
                            ekstrem = $array[j].children[sorting].innerHTML;
                            indeks = j;
                        }
                    }
                    else if(sort == 1){
                        if($array[j].children[sorting].innerHTML > ekstrem){
                            ekstrem = $array[j].children[sorting].innerHTML;
                            indeks = j;
                        }
                    }
                }
                $tmp = $array[length-1];
                $array[length-1] = $array[indeks];
                $array[indeks] = $tmp;
            }
            return $array;
        }

        $vrijednost = $("#select").on('change', function(){
            var zadnji = document.getElementsByTagName("tbody")[0].lastElementChild;
            $("tbody").empty();
            $htmlObject = $(poljeSPodatcima);
            $val = $vrijednost.val();
            $array = $($htmlObject).children();
            $count = 0;
            $array.each(item => {
                $item = $array[item];
                if($($item).children()[3].innerHTML == $val || $val == "-1"){
                    $("tbody").append($item);
                    $count++;
                }
            });

            if($count == 0){
                $("tbody").append("<tr height=30 style='font-size: 28px;'><td colspan=5>Poštanski uredi za odabranu državu trenutno ne postoje.</td></tr>")
            }
            if(zadnji != null && zadnji.id == 'elementZaDodavanje'){
                $("tbody").append(zadnji);
            }
        });

        function search(val, array){
            $array = $(array).children();
            let length = $array.length; 
            let copy = [];
            $("tbody").empty();
            for(var i = 0; i < length; i++){
                if($array[i].children[0].innerHTML.includes(val) == true || $array[i].children[1].innerHTML.includes(val) == true || $array[i].children[2].innerHTML.includes(val) == true){
                    copy.push($array[i]);
                }
            }
            return copy;
        }

        $("#insertPostanskiBtn").click(function(){
            if($("#naziv").val() == '' || $("#adresa").val() == '' || $("#poštanskiBroj").val() == ''){
                $("#snackbar").html('Niste ispunili sva polja');
                showSnackbar();
            }
            else{
                $.ajax({
                    method: 'POST',
                    url: './api.php?insert_postanskiUred',
                    data: { id_moderatora: $("#moderator").val(), id_drzave: $("#drzava").val(), naziv: $("#naziv").val(), adresa: $("#adresa").val(), postanskiBroj: $("#poštanskiBroj").val() }
                }).done(function(data){
                    if(data == 'Uspjeh'){
                        $("#snackbar").html('Ured uspješno dodan');
                        showSnackbar();
                    }
                    else{
                        $("#snackbar").html('Ured nije dodan');
                        showSnackbar();
                    }
                })
            }
        });
    }
    
    if(window.location.href.includes("drzave")){
        $("#submitBtn").click(()=>{
            if($("#naziv").val() == "" || $("#skraceniOblik").val() == "" || $("produzeniOblik").val() == "")
            {
                alert("ne valja");
            }
            else{
                $.ajax({
                    method: "POST",
                    url: "api.php?insert_drzava",
                    data: {naziv: $("#naziv").val(), skraceniOblik: $("#skraceniOblik").val(), produzeniOblik: $("#produzeniOblik").val(), clanEU:  $("#clanEU").val() },
                }).done(function(data) {
                    if(data == "Uspjeh"){
                        $("tbody > tr:last").before(`<tr><td>${$("#naziv").val()}</td><td>${$("#skraceniOblik").val()}</td><td>${$("#produzeniOblik").val()}</td><td>${$("#clanEU").val()}</td></tr>`);
               
                    }
                });
            }
        })
    }

    if(window.location.href.includes("racuni")){
        $("#showingLeft").click(()=>{switchShowing(0)});
        $("#showingMiddle").click(()=>{switchShowing(1)});
        $("#showingRight").click(()=>{switchShowing(2)});

        function removeActiveClass(){
            $("#showingLeft").removeClass('activeShow');
            $("#showingMiddle").removeClass('activeShow');
            $("#showingRight").removeClass('activeShow');
            $("#my").hide();
            $("#izdani").hide();
            $("#zahtjevi").hide();
        }

        function switchShowing(switchTo){
            removeActiveClass();
            if(switchTo == 0){
                $("#showingLeft").addClass('activeShow');
                $("#my").show();
            }
            else if(switchTo == 1){
                $("#showingMiddle").addClass('activeShow');
                $("#izdani").show();
            }
            else if(switchTo == 2){
                $("#showingRight").addClass('activeShow');
                $("#zahtjevi").show();
            }
        }

        $(".neplacen").click(function(){
            $("#overlay").show();
            $racun_id = $(this).children()[5].innerHTML;
            $vrijemeIzdavanja = $(this).children()[0].innerHTML;
            $placen = $(this).children()[1].innerHTML;
            $placen = ($placen === "0") ? 'False' : 'True';
            $iznos = $(this).children()[2].innerHTML;
            $puniIznos = $(this).children()[3].innerHTML;
            $slika = $(this).children()[4].innerHTML; 
            let date = new Date($vrijemeIzdavanja);
            date.setDate(date.getDate() + 7);

            $("#racun_id").val($racun_id);
            $("#rokPlacanja").val(`${date.getFullYear()}-${date.getMonth()+1}-${date.getDate()} ${date.getHours()}:${date.getMinutes()}:${date.getSeconds()}`);
            $("#vrijemeIzdavanja").val($vrijemeIzdavanja);
            $("#placen").val($placen);
            $("#iznos").val($iznos);
            $("#puniIznos").val($puniIznos);   
            $($(".modal")[0]).show();       
            $("#updateRacun").show();  
        })

        $(".neplacenModerator").click(function(){
            $.ajax({
                method: "POST",
                url: "api.php?fetch_korisnikFromRacun",
                data: {racun_id: $(this).children()[5].innerHTML }
            }).done(function(data) {
                $korisnik_id = $(data).children()[0].innerHTML;
                $korisnik_ime = $(data).children()[1].innerHTML;
                $("#ime_korisnika").val($korisnik_ime);
                $("#korisnik_id").val($korisnik_id);
            });

            $("#overlay").show();
            $($(".modal")[0]).show();
            $("#blokirajKorisnika").show();
            $("#rok").val(new Date($(this).children()[0].innerHTML));
            let preostaloVremena = pretvoriVrijeme(new Date($(this).children()[6].innerHTML)-new Date());
            $("#rok_za_placanje").val(preostaloVremena == '-1' ? "Rok prošao, možete blokirati korisnika" : preostaloVremena);
            if(preostaloVremena == '-1'){
                $("#buttonWrapperBlock").show();
            }
        })

        $("#blockBtn").click(()=>{
            if($("#blokirajNa").val() == "" || isNaN($("#blokirajNa").val())){
                $("#snackbar").html('Niste popunili sva polja');
                showSnackbar();
                closeModal();
            }
            else{
                let d = new Date();
                d = d.addHours($("#blokirajNa").val());

                $.ajax({
                    method: "POST",
                    url: "api.php?update_korisnikBlock",
                    data: {korisnik_id: $("#korisnik_id").val(), blokiranDo: d},
                }).done(function(data) {
                    $("#snackbar").html('Korisnik uspješno blokiran');
                    showSnackbar();
                    closeModal();
                });    
            }
        });

        Date.prototype.addHours = function(h) {
            this.setTime(this.getTime() + (h*60*60*1000));
            return this;
        }

        $("#submitBtn").click(function(){
            $racun_id = $("#racun_id").val();
            $slika = $("#slika").val();
            if($slika != "" && $racun_id != null){
                $.ajax({
                    method: "POST",
                    url: "api.php?update_racun",
                    data: {racun_id: $racun_id, slika: $slika},
                }).done(function(data) {
                    location.reload();
                });    
            }
            else{
                $("#snackbar").html('Niste popunili sva polja');
                closeModal();
            }
        })

        $("#overlay").click(this,()=>{
            closeModal();
        })

        function closeModal(){
            $($(".modal")[0]).hide();
            $("#overlay").hide(); 
            $("#buttonWrapperBlock").hide();    
            $("#updateRacun").hide();  
            $("#blokirajKorisnika").hide();
        }

        function pretvoriVrijeme(milisec) {
            var sekunde = (milisec / 1000).toFixed(1);    
            var minute = (milisec / (1000 * 60)).toFixed(1);    
            var sati = (milisec / (1000 * 60 * 60)).toFixed(1);    
            var dani = (milisec / (1000 * 60 * 60 * 24)).toFixed(1);    
            if (milisec < 0){
                return "-1";
            } else if(sekunde < 60) {
                return sekunde + " Sec";
            } else if (minute < 60) {
                return minute + " Min";
            } else if (sati < 24) {
                return sati + " Sati";
            } else {
                return dani + " Dana"
            }
        }

        $("#azurirajRacuneBtn").click(()=>{
            $("#zahtjeviZaRacune").children().each(function(){
                if($($(this).children()[2]).children()[0].value == "" ){
                    $(this).css('outline','3px solid red');
                }
                else{
                    $(this).css('outline','3px solid green');
                    $iznos_obrade = $("#obrada").val();
                    $racun_id = $("#racun_id").html();

                    let success = 0;
                    $.ajax({
                        method: "POST",
                        url: "api.php?update_racunDodajIznos",
                        data: {iznos_obrade: $iznos_obrade, racun_id:$racun_id },
                    }).done(function(data) {
                        if(data == "Uspjeh"){
                            success = 1;
                        }
                    });

                    if(success){
                        $(this).remove();
                    }
                    
                }
            });
        })
    }

    if(window.location.href.includes("posiljke")){
        if($("#saljemTable"))
        $('#saljemTable').DataTable({
            "pageLength": 7,
            responsive: true,
            "dom": 'f<"top">rt<"bottom"p><"clear">',
            "language": {
              "emptyTable": "Trenutno ne šaljete nijednu pošiljku",
              "sZeroRecords": "Ne postoje pošiljke s traženim pojmom"
            }
        });
        if($("#primamTable"))
        $('#primamTable').DataTable( {
            "columnDefs": [
                {
                    "targets": [ 5 ],
                    "visible": false,
                    "searchable": false
                }
            ],
            "dom": 'f<"top">rt<"bottom"p><"clear">',
            "language": {
              "emptyTable": "Trenutno ne primate nijednu pošiljku",
              "sZeroRecords": "Ne postoje pošiljke s traženim pojmom"
            }
        } );
        if($("#primamModerator"))
        $('#primamModerator').DataTable( {
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                }
            ],
            "dom": 'f<"top">rt<"bottom"p><"clear">',
            "language": {
              "emptyTable": "Trenutno nema novih pošiljki u vašem uredu",
              "sZeroRecords": "Ne postoje pošiljke s traženim pojmom"
            }
        } );
        if($("#statistikaTable"))
            $('#statistikaTable').DataTable({
                "dom": 'f<"top">rt<"bottom"p><"clear">',
                "language": {
                "emptyTable": "U ovom uredu nema postojećih pošiljki",
                "sZeroRecords": "Ne postoje pošiljke s traženim pojmom"
                }
            });

        if($("#zahtjeviTable"))
        $('#zahtjeviTable').DataTable( {
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                },
            ],
            "dom": 'f<"top">rt<"bottom"p><"clear">',
            "language": {
              "emptyTable": "Trenutno nemate zahtjeva",
              "sZeroRecords": "Ne postoje zahtjevi s traženim pojmom"
            }
        } );

        $("#showingLeft").click(()=>{switchShowing(0)});
        $("#showingMiddle1").click(()=>{switchShowing(1)});
        $("#showingMiddle2").click(()=>{switchShowing(2)});
        $("#showingRight").click(()=>{switchShowing(3)});
        $("#filtrirajBtn").click(()=>{filtrirajPoDatumu()});
        $("#overlay").click(this,()=>{
            $($(".modal")[0]).hide();
            $("#overlay").hide(); 
        })

        function removeActiveClass(){
            $("#showingLeft").removeClass('activeShow');
            $("#showingMiddle1").removeClass('activeShow');
            $("#showingMiddle2").removeClass('activeShow');
            $("#showingRight").removeClass('activeShow');


            $("#novaPosiljkaWrapper").hide();
            $("#saljemPrimamWrapper").hide();
            $("#statistikaWrapper").hide();
            $("#zahtjeviZaPosiljkamaWrapper").hide();
        }

        function switchShowing(switchTo){
            if(switchTo == 0){
                removeActiveClass();
                $("#showingLeft").addClass('activeShow');
                $("#novaPosiljkaWrapper").show();
                
            }
            else if(switchTo == 1){
                removeActiveClass();
                $("#showingMiddle1").addClass('activeShow');
                $("#saljemPrimamWrapper").show();
            }
            else if(switchTo == 2){
                removeActiveClass();
                $("#showingMiddle2").addClass('activeShow');
                $("#statistikaWrapper").show();
            }
            else if(switchTo == 3){
                removeActiveClass();
                $("#showingRight").addClass('activeShow');
                $("#zahtjeviZaPosiljkamaWrapper").show();
            }
        }

        $("#posaljiPosiljkuBtn").click(()=>{
            if($("#masa").val() == ''){
                $("#snackbar").html("Niste popunili sva polja");
                showSnackbar();
            }
            $.ajax({
                method: "POST",
                url: "api.php?insert_posiljka",
                data: {id_primatelja: $("#ime_primatelja").val(), masa: $("#masa").val()},
            }).done(function(data) {
                if(data == "Uspjeh"){
                    $("#snackbar").html("Pošiljka uspješno poslana");
                    showSnackbar();
                }
                else{
                    $("#snackbar").html("Došlo je do pogreške, molimo pokušajte opet");
                    showSnackbar();
                }
            });    
        });
        
        $(".spremanZaIsporuku").unbind().click(function(){
            $("#overlay").show();
            $($(".modal")[0]).show(); 
            $("#zatraziRacunWrapper").show();  

            $id_posiljka = $(this).children()[5].innerHTML;
            $ime_posiljatelja = $(this).children()[0].innerHTML;
            $cijenaPoKg = $(this).children()[2].innerHTML;
            $masa = $(this).children()[3].innerHTML;

            $("#id_posiljka").val($id_posiljka);
            $("#ime_posiljatelja").val($ime_posiljatelja);
            $("#cijenaPoKgModal").val($cijenaPoKg);
            $("#masaModal").val($masa);
        })

        $("#zatražiRačunBtn").click(function(){
            $cijenaPoKg = $("#cijenaPoKgModal").val();
            $masa =  $("#masaModal").val();
            $iznos = $cijenaPoKg*$masa;
            $id_posiljka = $("#id_posiljka").val();
        
            $.ajax({
                method: "POST",
                url: "api.php?insert_racun",
                data: {id_posiljka: $id_posiljka, iznos: $iznos},
            }).done(function(data) {
                closeModal();
            });    
        })

        function filtrirajPoDatumu(){
            if($("#od").val() != '' && $("#do").val() != ''){
                let poljeSPodatcima = '';
                $.ajax({
                    method: "POST",
                    url: "api.php?fetch_drzaveStatistika",
                    data: {od: $("#od").val(), do: $("#do").val() },
                }).done(function(data) {
                    poljeSPodatcima = data;
                    $("#statistikaTbody").empty();
                           
                    $("tbody").append(poljeSPodatcima);
                     
                    if(poljeSPodatcima.length == 0){
                        $("tbody").append("<tr height=30 style='font-size: 28px;'><td colspan=3>Za odabrano razdoblje u njegovim poštanskim uredima nije bilo pošiljki</td></tr>")
                    }
                });
            }
        }

        $("#azurirajPosiljkeBtn").click(()=>{
            $("#prihvacanjeZahtjeva").children().each(function(){
                if($($(this).children()[4]).children()[0].value == "" || $($(this).children()[5]).children()[0].value == "-1" || $($(this).children()[6]).children()[0].value == "-1" ){
                    $(this).css('outline','3px solid red');
                }
                else{
                    $(this).css('outline','3px solid green');
                    $id_pocetniUred = $($(this).children()[5]).children()[0].value == "-1" || $($(this).children()[6]).children()[0].value; 
                    $id_konacniUred = $($(this).children()[6]).children()[0].value == "-1" || $($(this).children()[6]).children()[0].value; 
                    $cijenaPoKg = $($(this).children()[4]).children()[0].value;
                    $posiljka_id = $(this).children()[0].innerHTML;
                    
                    let success = 0;
                    $.ajax({
                        method: "POST",
                        url: "api.php?update_posiljkaAktiviraj",
                        data: {id_pocetniUred: $id_pocetniUred, id_konacniUred:$id_konacniUred, cijenaPoKg:$cijenaPoKg, posiljka_id:$posiljka_id  },
                    }).done(function(data) {
                        if(data == "Uspjeh"){
                            success = 1;
                        }
                    });
                    if(success){
                        $(this).remove();
                    }
                }
            });
        })

        $("#primamModerator").children().not('.dataTables_empty').click(function(){
            $("#overlay").show();
            $($(".modal")[0]).show(); 
            $("#proslijediPosiljkuWrapper").show();  
            $("#posiljka_id").val($(this).children()[0].innerHTML);
            $("#konacni_ured").val($(this).children()[2].innerHTML)
            $stigaoNaOdrediste = $(this).children()[3].innerHTML;

            switch($stigaoNaOdrediste){
                case 'Da': 
                    $("#proslijediPosiljkuBtn").val("Isporuči");
                    $("#sljedeci_ured_txtBox").hide();
                    $("#proslijediPosiljkuBtn").click(function(){
                        $.ajax({
                            method: "POST",
                            url: "api.php?update_posiljkaProslijedi=1",
                            data: { posiljka_id:$("#posiljka_id").val() },
                        }).done(function(data) {
                            location.reload();
                        });
                    }); 
                ;break;
                case 'Ne': 
                    $("#proslijediPosiljkuBtn").val("Proslijedi"); 
                    $("#sljedeci_ured_txtBox").show();
                    $("#proslijediPosiljkuBtn").click(()=>{
                        if($("#sljedeci_ured").val() == "-1"){
                            $("#sljedeci_ured").css('outline','solid 3px red');
                        }
                        else{
                            $("#sljedeci_ured").css('outline','none');
                            $.ajax({
                                method: "POST",
                                url: "api.php?update_posiljkaProslijedi=2",
                                data: {id_trenutniUred: $("#sljedeci_ured").val(), posiljka_id:$("#posiljka_id").val() },
                            }).done(function(data) {
                                location.reload();
                            });
                        }
                    }); 
                break;
            }
        })

        drawCanvas([[12,'Poslane pošiljke'],[2,'Broj plaćenih']]);

        function drawCanvas(values){
            $canvas = $("#canvas");
            let canvasContext = canvas.getContext("2d");
            canvasContext.clearRect(0, 0, canvas.width, canvas.height);
    
            let drawOn = 10;
            let height = canvas.height;
            let width = canvas.width;
            let columnWidth = (width-10-10*values.length)/values.length;
            canvasContext.fillRect(0, height-40, width, 3);

            let mjernaJedinica = (height-50-40)/Math.max(Math.max(...values));
            for(var i = 0; i < values.length; i++){
                canvasContext.fillStyle = ("#"+(Math.floor( Math.random() * parseInt('0xFFFFFF') )+1).toString(16)); 
                canvasContext.fillRect(drawOn, height-40, columnWidth, -1*values[i][0]*mjernaJedinica);
                canvasContext.fillStyle = "#000000"
                canvasContext.font = "24px Segoe UI";
                canvasContext.fillText(values[i][0], drawOn+(columnWidth)/2-canvasContext.measureText(values[i][0]).width/2, height-values[i]*mjernaJedinica-15-40);
                canvasContext.font = "16px Segoe UI";
                canvasContext.fillText(values[i][1], drawOn+(columnWidth)/2-canvasContext.measureText(values[i][1]).width/2, height-20+8);
                drawOn += columnWidth+10;
            }
        }

        function drawValue(value){
            
        }

        drawValue(1);
        drawValue(2);
        drawValue(5);

        function closeModal(){
            $($(".modal")[0]).hide();
            $("#zatraziRacunWrappper").hide(); 
            $("#proslijediPosiljkuWrapper").hide(); 
        }
    } 
    
    if(window.location.href.includes('postavke')){
        let poljeSPodatcima = $("#dnevnikTbody").children();
        let nocniNacinRada = false;
        for(var i = 0; i < kolacic.length; i++){
            var naziv = kolacic[i].split("=")[0];
            var vrijednost = kolacic[i].split("=")[1]
            if(naziv == 'nocniNacinRada'){
                if( vrijednost == "true" ){
                    nocniNacinRada = true;
                    $("#nocniNacinRada").prop('checked', true);
                }
                else if( vrijednost == "false"){
                    nocniNacinRada = false;
                    $("#nocniNacinRada").prop('checked', false);
                }
            }
        } 
        $("#showingLeft").click(()=>{switchShowing(0)});
        $("#showingRight").click(()=>{switchShowing(1)});

        function removeActiveClass(){
            $("#showingLeft").removeClass('activeShow');
            $("#showingRight").removeClass('activeShow');
            $("#everyUser").hide();
            $("#adminOnly").hide();
        }

        function switchShowing(switchTo){
            removeActiveClass();
            if(switchTo == 0){
                $("#showingLeft").addClass('activeShow');
                $("#everyUser").show();
            }
            else if(switchTo == 1){
                $("#showingRight").addClass('activeShow');
                $("#adminOnly").show();
            }
        }

        $(".odblokiraj").click(function(){
            $.ajax({
                method: "POST",
                url: "api.php?update_korisnikUnblock=2",
                data: {korisnik_id: $(this).siblings()[1].innerHTML },
            }).done(function(data) {
                $(this).parent().remove();
                $("#snackbar").html('Korisnik uspješno odblokiran');
                showSnackbar();
            });
        })
        
        $("#resetirajUvjeteBtn").click(function(){
            $.ajax({
                url: "api.php?update_cookiesReset",
            }).done(function(data) {
                $("#snackbar").html('Uvjeti korištenja uspješno resetirani');
                showSnackbar();
            });
        })

        $("#nocniNacinRada").click(function(){
            document.cookie = nocniNacinRada + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            document.cookie = `nocniNacinRada=${$("#nocniNacinRada").prop('checked')};path=/`;
        })

        $("#postaviTrajanjeKolacica").click(function(){
            if($("#trajanjeKolacica").val() == '' || isNaN($("#trajanjeKolacica").val())){
                $("#snackbar").html('Niste popunili polje s vrijednosti');
                showSnackbar();
            }
            else{
                $.ajax({
                    method: 'POST',
                    url: './api.php?update_postavkeSet',
                    data: { trajanjeKolacica: $("#trajanjeKolacica").val() },
                }).done(function(data){
                    if(data == 'Uspjeh'){
                        $("#snackbar").html('Trajanje kolačića uspješno postavljeno');
                        showSnackbar();
                    }
                    else{
                        $("#snackbar").html('Postavljanje trajanja kolačića nije uspjelo');
                        showSnackbar();
                    }
                })
            }
        })

        $("#postaviTrajanjeSesije").click(function(){
            if($("#trajanjeSesije").val() == '' || isNaN($("#trajanjeSesije").val())){
                $("#snackbar").html('Niste popunili polje s vrijednosti');
                showSnackbar();
            }
            else{
                $.ajax({
                    method: 'POST',
                    url: './api.php?update_postavkeSet',
                    data: { trajanjeSesije: $("#trajanjeSesije").val() },
                }).done(function(data){
                    console.log(data);
                    if(data == 'Uspjeh'){
                        $("#snackbar").html('Trajanje sesije uspješno postavljeno');
                        showSnackbar();
                    }
                    else{
                        $("#snackbar").html('Postavljanje trajanja sesije nije uspjelo');
                        showSnackbar();
                    }
                })
            }
        })

        $("#postaviStranicenje").click(function(){
            if($("#stranicenje").val() == '' || isNaN($("#stranicenje").val())){
                $("#snackbar").html('Niste popunili polje s vrijednosti');
                showSnackbar();
            }
            else{
                $.ajax({
                    method: 'POST',
                    url: './api.php?update_postavkeSet',
                    data: { stranicenje: $("#stranicenje").val() },
                }).done(function(data){
                    console.log(data)
                    if(data == 'Uspjeh'){
                        $("#snackbar").html('Straničenje uspješno postavljeno');
                        showSnackbar();
                    }
                    else{
                        $("#snackbar").html('Postavljanje straničenja nije uspjelo');
                        showSnackbar();
                    }
                })
            }
        })

        $(".dnevnikRedak").click(function(){
            $("#snackbar").html( $(this).children()[3].innerHTML == '' ? 'Za ovu radnju ne postoji upit' : $(this).children()[3].innerHTML );
            showSnackbar();
        });

        $("#filtrirajBtn").click(function(){
            if($("#do").val() != '' && $("#od").val() != ''){
                $.ajax({
                    method: "POST",
                    url: "./api.php?fetch_dnevnikRada",
                    data: { od: $("#od").val(), do: $("#do").val() }
                }).done(function(data){
                    $("#dnevnikTbody").empty();
                    $("#dnevnikTbody").append(data);
                })
            }
            else{
                $("#snackbar").html('Niste popunili sva polja');
                showSnackbar();
            }
        })

        $("#search").on('input',function(){
            let array = filterDnevnik($("#search").val(),poljeSPodatcima);
            if(array.length > 0){
                $("#dnevnikTbody").append(array);
            }
            else{
                $("#dnevnikTbody").append("<tr><td colspan=3>Za upisani pojam ne postoje podatci</td></tr>");
            }
        })

        function filterDnevnik(val, array){
            $array = $(array);
            let length = $array.length; 
            let copy = [];
            $("tbody").empty();
            for(var i = 0; i < length; i++){
                if($array[i].children[0].innerHTML.includes(val) == true || $array[i].children[1].innerHTML.includes(val) == true || $array[i].children[2].innerHTML.includes(val) == true){
                    copy.push($array[i]);
                }
            }
            return copy;
        }
    }

    function showSnackbar() {
        var x = document.getElementById("snackbar");
        x.className = "show";
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    }
})
