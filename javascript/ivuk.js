$(()=>{
    console.log('ready');
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
        $.ajax({
            url: "api.php?fetch_postanskiUred=1",
        }).done(function(data) {
            poljeSPodatcima = data;
        });

        $vrijednost = $("#select").on('change', function(){
            $("tbody").empty();
            $htmlObject = $(poljeSPodatcima);
            $val = $vrijednost.val();
            console.log($val);
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
                $("tbody").append("<tr height=30 style='font-size: 28px;'><td colspan=3>Poštanski uredi za trenutno odabranu državu trenutno ne postoje.</td></tr>")
            }
        });
    }
    
    if(window.location.href.includes("drzave")){
        $("#submitBtn").click(()=>{
            console.log($("#naziv").val(), $("#skraceniOblik").val(), $("#produzeniOblik").val(), $("#clanEU").val())
            $.ajax({
                method: "POST",
                url: "api.php?insert_drzava",
                data: {naziv: $("#naziv").val(), skraceniOblik: $("#skraceniOblik").val(), produzeniOblik: $("#produzeniOblik").val(), clanEU:  $("#clanEU").val() },
            }).done(function(data) {
                if(data == "Uspjeh"){
                    $("tbody > tr:last").before(`<tr><td>${$("#naziv").val()}</td><td>${$("#skraceniOblik").val()}</td><td>${$("#produzeniOblik").val()}</td><td>${$("#clanEU").val()}</td></tr>`);
           
                }
            });
    
        })
    }
})