const assetsUrl = "./assets/";
const cardsSrcs = ["0.png", "1.png", "2.png", "3.png", "4.png", "5.png", "6.png", "7.png", "8.png", "9.png", "10.png", "11.png", "12.png", "13.png", "14.png", "15.png", "16.png", "17.png"]
let cards = [];

for(let i = 0; i < cardsSrcs.length; i++) {
    let a = new Image();
    a.src = assetsUrl + cardsSrcs[i];
    cards.push(a);
}