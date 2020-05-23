$(()=>{
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
        console.log("login");
        $("#forgottenUsername").click(function(){
            forgottenPassword($(this));
        })
        let loggingInWithUsername = true;
        function forgottenPassword(objekt){
            if(loggingInWithUsername){
                loggingInWithUsername = false;
                $(objekt).html("Zaboravili ste e-mail? Prijavite se pomoću korisničkog imena")
                $("#kor_imeTextBox").hide();
                $("#e_mailTextBox").show();
            }
            else{
                loggingInWithUsername = true;
                $(objekt).html("Zaboravili ste korisničko ime? Prijavite se pomoću e-maila")
                $("#kor_imeTextBox").show();
                $("#e_mailTextBox").hide();
            }
        }
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
            
            $array = search($("#search").val());

            $array.each(function(index){
                $("tbody").append($(this)[0])
            });
            $("tbody").append(zadnji);
                
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
            $("tbody").append(zadnji);
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
            $("tbody").append(zadnji);
        })

        function sortArray(sort, sorting){
            let length =  $("tbody").children(":not(:last-child)").length-1;
            $array =  $("tbody").children(":not(:last-child)");
            $("tbody").empty();
            $("#broj_poslanih").html("Broj poslanih");
            $("#broj_primljenih").html("Broj primljenih");

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
                $("tbody").append("<tr height=30 style='font-size: 28px;'><td colspan=5>Poštanski uredi za trenutno odabranu državu trenutno ne postoje.</td></tr>")
            }
            $("tbody").append(zadnji);
        });

        function search(val){
            let length =  $("tbody").children(":not(:last-child)").length;
            $array =  $("tbody").children(":not(:last-child)");
            $("tbody").empty();
            for(var i = 0; i < length; i++){
                if($array[i].children[0].innerHTML.includes(val) == false && $array[i].children[1].innerHTML.includes(val) == false && $array[i].children[2].innerHTML.includes(val) == false){
                    console.log("izbacujem:", $array[i]);
                    $($array[i]).css('display','none');
                }
                else{
                    $($array[i]).css('display','table-row');
                }
            }
            return $array;
        }
    }
    
    if(window.location.href.includes("drzave")){
        $("#submitBtn").click(()=>{
            console.log($("#naziv").val(), $("#skraceniOblik").val(), $("#produzeniOblik").val(), $("#clanEU").val())
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
        let showing = 0; //0 - left, 1 - right
        $("#showingLeft").click(()=>{switchShowing(0)});
        $("#showingRight").click(()=>{switchShowing(1)});

        function switchShowing(switchTo){
            if(switchTo == 0){
                $("#showingLeft").addClass('activeShow');
                $("#showingRight").removeClass('activeShow');
                $(".my").each(function(){$(this).css('display', 'table-row')});
                $(".all").each(function(){$(this).css('display', 'none')});
                showing = 0;
            }
            else if(switchTo == 1){
                $("#showingLeft").removeClass('activeShow');
                $("#showingRight").addClass('activeShow');
                $(".my").each(function(){$(this).css('display', 'none ')});
                $(".all").each(function(){$(this).css('display', 'table-row')});
                showing = 1;
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
                $("#greska").html('Niste popunili sva polja');
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
                    $("#greska").html('Korisnik uspješno blokiran');
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
                $("#greska").html('Niste popunili sva polja');
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
    }

    if(window.location.href.includes("posiljke")){
        let showing = 0; //0 - left, 1 - right
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
            $("#greska").html('');
            $("#showingLeft").removeClass('activeShow');
            $("#showingMiddle1").removeClass('activeShow');
            $("#showingMiddle2").removeClass('activeShow');
            $("#showingRight").removeClass('activeShow');


            $("#novaPosiljkaWrapper").hide();
            $("#saljemPrimamWrapper").hide();
            $("#statistikaWrapper").hide();
        }

        function switchShowing(switchTo){
            if(switchTo == 0){
                showing = 0;
                removeActiveClass();
                $("#showingLeft").addClass('activeShow');
                $("#novaPosiljkaWrapper").show();
                
            }
            else if(switchTo == 1){
                showing = 1;
                removeActiveClass();
                $("#showingMiddle1").addClass('activeShow');
                $("#saljemPrimamWrapper").show();
            }
            else if(switchTo == 2){
                showing = 2;
                removeActiveClass();
                $("#showingMiddle2").addClass('activeShow');
                $("#statistikaWrapper").show();
            }
            else if(switchTo == 3){
                showing = 3;
                removeActiveClass();
                $("#showingRight").addClass('activeShow');
            }
        }

        $("#posaljiPosiljkuBtn").click(()=>{
            $.ajax({
                method: "POST",
                url: "api.php?insert_posiljka",
                data: {id_primatelja: $("#ime_primatelja").val(), masa: $("#masa").val()},
            }).done(function(data) {
                console.log(data)
                if(data == "Uspjeh"){
                    $("#greska").html("Pošiljka uspješno poslana");
                }
                else{
                    $("#greska").html("Došlo je do pogreške, molimo pokušajte opet");
                }
            });    
        });
        
        $(".spremanZaIsporuku").unbind().click(function(){
            $("#overlay").show();
            $($(".modal")[0]).show();   

            $id_posiljka = $(this).children()[5].innerHTML;
            $ime_posiljatelja = $(this).children()[0].innerHTML;
            $cijenaPoKg = $(this).children()[2].innerHTML;
            $masa = $(this).children()[3].innerHTML;

            $("#id_posiljka").val($id_posiljka);
            $("#ime_posiljatelja").val($ime_posiljatelja);
            $("#cijenaPoKg").val($cijenaPoKg);
            $("#masa").val($masa);
        })

        $("#zatražiRačunBtn").click(function(){
            $cijenaPoKg = $("#cijenaPoKg").val();
            $masa =  $("#masa").val();
            $iznos = $cijenaPoKg*$masa;
            $id_posiljka = $("#id_posiljka").val();
        
            $.ajax({
                method: "POST",
                url: "api.php?insert_racun",
                data: {id_posiljka: $id_posiljka, iznos: $iznos},
            }).done(function(data) {
                console.log(data);
            });    
        })
    }

    function filtrirajPoDatumu(){
        console.log($("#od").val(),$("#do").val())
        if($("#od").val() != '' && $("#do").val() != ''){
            let poljeSPodatcima = '';
            $.ajax({
                url: "api.php?fetch_drzaveStatistika",
                data: {od: $("#od").val(), do: $("#do").val() },
            }).done(function(data) {
                poljeSPodatcima = data;
                console.log(data);
            });

            $("#statistikaTbody").empty();
            
            $array = $($(poljeSPodatcima)).children();
            $count = 0;
            $array.each(item => {               
                $("tbody").append($array[item]);
                $count++;
            });

            if($count == 0){
                $("tbody").append("<tr height=30 style='font-size: 28px;'><td colspan=3>Podatci ne postoje.</td></tr>")
            }
        }
    }
})
