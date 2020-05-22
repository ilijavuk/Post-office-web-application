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
        let sort = -1; //1 = desc ↓ | 2 = asc ↑

        $.ajax({
            url: "api.php?fetch_postanskiUred=1",
        }).done(function(data) {
            poljeSPodatcima = data;
        });
        
        $("#broj_posiljki").click(()=>{
            sort = ((sort+1)%2);
            if(sort == -1){
                $("#broj_posiljki").html("Broj pošiljki");
            }
            else if(sort == 0){
                $("#broj_posiljki").html("Broj pošiljki ▼");
            }
            else if(sort == 1){
                $("#broj_posiljki").html("Broj pošiljki ▲");
            }

            
            var zadnji = document.getElementsByTagName("tbody")[0].lastElementChild;
            $array = sortArray(sort);

            $array.each(function(index){
                $("tbody").append($(this)[0])
            });
            $("tbody").append(zadnji);
        })

        function sortArray(){
            let length =  $("tbody").children(":not(:last-child)").length-1;
            $array =  $("tbody").children(":not(:last-child)");
            $("tbody").empty();
            $("#broj_posiljki").html("Broj pošiljki");

            for(var i = 0; i < length; length--){
                var ekstrem = $array[i].children[4].innerHTML;
                var indeks = i;
                for(var j = i; j < length; j++){
                    if(sort == 0){
                        if($array[j].children[4].innerHTML < ekstrem){
                            ekstrem = $array[j].children[4].innerHTML;
                            indeks = j;
                        }
                    }
                    else if(sort == 1){
                        if($array[j].children[4].innerHTML > ekstrem){
                            ekstrem = $array[j].children[4].innerHTML;
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

        $(".neplacen").unbind().click(function(){
            $("#overlay").show();
            $racun_id = $(this).children()[5].innerHTML;
            $vrijemeIzdavanja = $(this).children()[0].innerHTML;
            $placen = $(this).children()[1].innerHTML;
            $placen = ($placen === "0") ? 'False' : 'True';
            $iznos = $(this).children()[2].innerHTML;
            $puniIznos = $(this).children()[3].innerHTML;
            $slika = $(this).children()[4].innerHTML;
            console.log($vrijemeIzdavanja, $placen, $iznos, $puniIznos, $slika);  
            let date = new Date($vrijemeIzdavanja);
            date.setDate(date.getDate() + 7);

            $("#racun_id").val($racun_id);
            $("#rokPlacanja").val(`${date.getFullYear()}-${date.getMonth()+1}-${date.getDate()} ${date.getHours()}:${date.getMinutes()}:${date.getSeconds()}`);
            $("#vrijemeIzdavanja").val($vrijemeIzdavanja);
            $("#placen").val($placen);
            $("#iznos").val($iznos);
            $("#puniIznos").val($puniIznos);   
            $($(".modal")[0]).show();         
        })

        $("#submitBtn").click(function(){
            $racun_id = $("#racun_id").val();
            $slika = $("#slika").val();
            if($slika != null && $racun_id != null){
                $.ajax({
                    method: "POST",
                    url: "api.php?update_racun",
                    data: {racun_id: $racun_id, slika: $slika},
                }).done(function(data) {
                    location.reload();
                });    
            }
        })

        $("#overlay").click(this,()=>{
            $($(".modal")[0]).hide();
            $("#overlay").hide(); 
        })
    }

    if(window.location.href.includes("posiljke")){
        let showing = 0; //0 - left, 1 - right
        $("#showingLeft").click(()=>{switchShowing(0)});
        $("#showingMiddle1").click(()=>{switchShowing(1)});
        $("#showingMiddle2").click(()=>{switchShowing(2)});
        $("#showingRight").click(()=>{switchShowing(3)});

        function removeActiveClass(){
            $("#showingLeft").removeClass('activeShow');
            $("#showingMiddle1").removeClass('activeShow');
            $("#showingMiddle2").removeClass('activeShow');
            $("#showingRight").removeClass('activeShow');

            $(".gallery").hide();
            $("#saljemPrimamWrapper").hide();
        }

        function switchShowing(switchTo){
            if(switchTo == 0){
                showing = 0;
                removeActiveClass();
                $("#showingLeft").addClass('activeShow');
                $(".gallery").show();
                
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
            }
            else if(switchTo == 3){
                showing = 3;
                removeActiveClass();
                $("#showingRight").addClass('activeShow');
            }
        }
    }
})
