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
            console.log($htmlObject);
            $val = $vrijednost.val();
            $array = $($htmlObject).children();
            $count = 0;
            $array.each(item => {
                $item = $array[item];
                if($($item).children()[3].innerHTML == $val){
                    $("tbody").append($item);
                    $count++;
                }
                else{
                    console.log($($item).children()[3].innerHTML, $val)
                }
            });
            if($count == 0){
                $("tbody").append("<tr height='24'><td colspan=3>Podatci ne postoje</td></tr>")
            }
        });
    }
})