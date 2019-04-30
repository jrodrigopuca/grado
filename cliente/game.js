// Capturando elementos del DOM (querySelector, querySelectorAll)

var sceneEl = document.querySelector('a-scene');
//console.log("[INFO] Un Elemento",sceneEl.querySelector('#redBox'));
//console.log("[INFO] Muchos Elementos", sceneEl.querySelectorAll('a-box'));

//var caja= document.querySelector(".fruta");
var txtPuntos = document.querySelector("#hPuntos");
var txtVida = document.querySelector("#hVida");

let audioClick = new Audio('assets/sounds/ButtonClick.mp3');
let explosionClick = new Audio('assets/sounds/explosion.ogg');


/*
caja.addEventListener("click",function(){
    valor=valor+1;
    texto.setAttribute("value",valor)
    console.log("hola");
})
*/

function actualizarVida(valorV) {
    let emoji="";
    if (valorV>=70 && valorV<100){emoji="😮"}
    if (valorV>=40 && valorV<70){emoji="🤕"}
    if (valorV>=20 && valorV<40){emoji="😵"}
    if (valorV>=0 && valorV<20){emoji="💀"}
    explosionClick.play();
    txtVida.textContent = emoji+valorV;
}

//var cursor= document.querySelector("a-cursor");

document.addEventListener('click', function (event) {
    if (event.target.classList.contains('fruta')) {
        /*
        anime({
            targets: cursor,
            color:["#FF0000","#FFF"],
            duration: 100,
        });
        */

        audioClick.play();

        valor = valor + 1;
        //texto.setAttribute("value",valor)
        txtPuntos.textContent = "🥗"+ valor;
        //console.log("hola");
    }
}, false);

var contador = document.getElementById('hTiempo');

function incrementarSeg() {
    segundos += 1;
    contador.innerText = "⌚" + segundos;
}

setInterval(incrementarSeg, 1000);
