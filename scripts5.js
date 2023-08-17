const api_url = "php/";

const canvas = document.getElementById('myCanvas');

let hoverableCards = [];
let drawn = -1;
let action = "before";
let active = -1;
let activeStep = -1;
let drawThrow = false;
let subCard = "";
let isThereCard = true;
let card = "";

let screw = -1;
let screwColor = "#34495e";

let mode = "play";

let requestSent = false;

let me = {}

let players = [];

/* let connection = "loading";

function changeConnection(conn) {
    connection =  conn;

    switch(conn) {
        case "disconnected":

    }
} */

function debounce(func, delay) {
    let timeoutId;
    
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            func.apply(this, args);
        }, delay);
    };
}

if(localStorage.getItem('token')) {
    if(localStorage.getItem('roomPass')) {
        const url = api_url + 'checkRoom.php'; 
    
        const data = new FormData();
        data.append('token', localStorage.getItem('token'));
        data.append('roomPass', localStorage.getItem('roomPass')); 
        
        fetch(url, {
            method: "POST",
            body: data
        })
        .then(response => {
            if (!response.ok) {
            throw new Error('Network response was not ok.');
            }
            return response.json();
        })
        .then(async data => {
            if(data.msg == "done") {
                me = data.player;
                document.getElementById('main').classList.add('hide');
                game();
            } else if (data.msg == "noroom") {
                localStorage.removeItem("roomPass");
                document.querySelector('.main .connect').classList.add('hide');
                document.querySelector('.main .enter').classList.remove('hide');
                initEnter();
            } else if (data.msg == "nouser") {
                localStorage.removeItem("roomPass");
                localStorage.removeItem("token");
                initMain();
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
    } else {
        const url = api_url + 'getPlayer.php'; 
    
        const data = new FormData();
        data.append('token', localStorage.getItem('token'));
    
        fetch(url, {
            method: "POST",
            body: data
        })
        .then(response => {
            if (!response.ok) {
            throw new Error('Network response was not ok.');
            }
            return response.json();
        })
        .then(async data => {
            if(data.msg == "done") {
                me = data.player;
                document.querySelector('.main .connect').classList.add('hide');
                document.querySelector('.main .enter').classList.remove('hide');
                initEnter();
            } else if (data.msg == "not") {
                localStorage.removeItem("token");
                initMain();
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
    }
} else {
    initMain();
}

function initMain() {
    canvas.classList.add('hide');
    document.getElementById('main').classList.remove('hide');
    document.querySelector('.main .enter').classList.add('hide');
    document.querySelector('.main .connect').classList.remove('hide');

    let input = document.querySelector('.main .connect #input');
    let errMsg = document.querySelector('.main .connect #error');
    let button = document.querySelector('.main .connect #btn');

    button.onclick = () => {
        
        if(input.value == "") {
            input.classList.add('danger');
            errMsg.classList.add('show');

        } else {

            const url = api_url + 'connectToServer.php'; 

            const data = new FormData();
            data.append('name', input.value);

            fetch(url, {
                method: "POST",
                body: data
            })
            .then(response => {
                if (!response.ok) {
                throw new Error('Network response was not ok.');
                }
                return response.json();
            })
            .then(async data => {
                if(data.msg == "done") {
                    localStorage.setItem('token', data.token);
                    me = {
                        name: input.value,
                        token: data.token
                    }
                    document.querySelector('.main .connect').classList.add('hide');
                    document.querySelector('.main .enter').classList.remove('hide');
                    initEnter();
                } else if (data.msg == "not") {
                    alert("There is a problem please try again");
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
        }
    }
}

function initEnter() {  
    canvas.classList.add('hide');
    document.getElementById('main').classList.remove('hide');
    document.querySelector('.main .enter').classList.remove('hide');
    document.querySelector('.main .connect').classList.add('hide');

    let createInput = document.querySelector('.main .enter .create #input');
    let createButton = document.querySelector('.main .enter .create #btn');
    let joinInput = document.querySelector('.main .enter .join #input');
    let joinButton = document.querySelector('.main .enter .join #btn');

    createButton.onclick = () => {
        
        if(createInput.value == "") {
            createInput.classList.add('danger');
        } else {

            const url = api_url + 'createRoom.php'; 

            const data = new FormData();
            data.append('roomPass', createInput.value);
            data.append('token', localStorage.getItem("token"));

            fetch(url, {
                method: "POST",
                body: data
            })
            .then(response => {
                if (!response.ok) {
                throw new Error('Network response was not ok.');
                }
                return response.json();
            })
            .then(async data => {
                if(data.msg == "done") {
                    localStorage.setItem('roomPass', data.roomPass);
                    document.getElementById('main').classList.add('hide');
                    game();
                } else if (data.msg == "nouser") {
                    localStorage.removeItem("token");
                    initMain();
                } else if (data.msg == "not") {
                    alert("There is a problem please try again");
                } else if (data.msg == "alreadyExists") {
                    alert("There is already a room with this pass try another one");
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
        }
    }
    
    joinButton.onclick = () => {
        
        if(joinInput.value == "") {
            joinInput.classList.add('danger');
        } else {

            const url = api_url + 'joinRoom.php'; 

            const data = new FormData();
            data.append('roomPass', joinInput.value);
            data.append('token', localStorage.getItem("token"));

            fetch(url, {
                method: "POST",
                body: data
            })
            .then(response => {
                if (!response.ok) {
                throw new Error('Network response was not ok.');
                }
                return response.json();
            })
            .then(async data => {
                if(data.msg == "done") {
                    localStorage.setItem('roomPass', data.roomPass);
                    document.getElementById('main').classList.add('hide');
                    game();
                } else if (data.msg == "not") {
                    alert("There is a problem while joining this room please try again");
                } else if (data.msg == "nouser") {
                    localStorage.removeItem("token");
                    initMain();
                } else if (data.msg == "noroom") {
                    alert("There is no room with this password");
                } else if(data.msg == "full") {
                    alert("This room is full right now");
                } else if (data.msg == "ended") {
                    alert("This room has already ended");
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
        }
    }
}

async function game() {
    let turn = 0;
    let lastThrown = -1;

    canvas.classList.remove('hide');
    const context = canvas.getContext('2d');
    
    // Set canvas size
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    let centerX = canvas.width / 2;
    let centerY = canvas.height / 2;
    window.addEventListener('resize', (event) => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        centerX = canvas.width / 2;
        centerY = canvas.height / 2;    
    });
    
    canvas.addEventListener('mousemove', (event) => {
        const rect = canvas.getBoundingClientRect();
        const mouseX = event.clientX - rect.left;
        const mouseY = event.clientY - rect.top;

        let h = false;

        hoverableCards.forEach(card => {
            const rectX = card.x; // Adjust the rectangle X position as needed
            const rectY = card.y; // Adjust the rectangle Y position as needed
            const rectWidth = card.w; // Adjust the rectangle width as needed
            const rectHeight = card.h; // Adjust the rectangle height as needed

            if (
                mouseX >= rectX && mouseX <= rectX + rectWidth &&
                mouseY >= rectY && mouseY <= rectY + rectHeight
            ) {
                h = true;
            }
        });
        if(h && canvas.style.cursor != "pointer") {
            canvas.style.cursor = 'pointer';
        } else if (!h && canvas.style.cursor == "pointer") {
            canvas.style.cursor = 'default';
        }
    });

    const clickHandler = (event) => {
        console.log("a");
        if (!requestSent) {

            requestSent = true;
            const rect = canvas.getBoundingClientRect();
            const mouseX = event.clientX - rect.left;
            const mouseY = event.clientY - rect.top;

            hoverableCards.forEach((card, index) => {
                const rectX = card.x; // Adjust the rectangle Y position as needed
                const rectY = card.y; // Adjust the rectangle Y position as needed
                const rectWidth = card.w; // Adjust the rectangle width as needed
                const rectHeight = card.h; // Adjust the rectangle height as needed


                if (
                    mouseX >= rectX && mouseX <= rectX + rectWidth &&
                    mouseY >= rectY && mouseY <= rectY + rectHeight
                    ) {
                        let f = false;
                        if (action == "before") {
                            if(card.name == "draw") {
                                let url = api_url + 'draw.php'; 
                                const data = new FormData();    
                                data.append('roomPass', localStorage.getItem('roomPass')); 
                                data.append('token', localStorage.getItem('token')); 
                                fetch(url, {
                                    method: "POST",
                                    body: data
                                })
                                .then(response => {
                                    requestSent = false;
                                    if (!response.ok) {
                                    throw new Error('Network response was not ok.');
                                    }
                                    console.log("b");
                                    return response.json();
                                })
                                .then(async data => {
                                    if(data.msg == "done") {
                                        update();
                                    }
                                })
                                .catch(error => {
                                    console.error('Fetch error:', error);
                                });

                            } else if (card.name == "throw") {
                                let url = api_url + 'drawThrow.php'; 
                                let data  = new FormData();
                                data.append('roomPass', localStorage.getItem('roomPass')); 
                                data.append('token', localStorage.getItem('token')); 
                                fetch(url, {
                                    method: "POST",
                                    body: data
                                })
                                .then(response => {
                                    requestSent = false;
                                    if (!response.ok) {
                                    throw new Error('Network response was not ok.');
                                    }
                                    return response.json();
                                })
                                .then(async data => {
                                    if(data.msg == "done") {
                                        update();
                                    }
                                })
                                .catch(error => {
                                    console.error('Fetch error:', error);
                                });
                            } else if (card.name.split('-')[0] == me.id) {
                                let url = api_url + 'setTryThrowCard.php'; 
                                let data  = new FormData();
                                data.append('roomPass', localStorage.getItem('roomPass')); 
                                data.append('token', localStorage.getItem('token')); 
                                data.append("card", card.name);
                                fetch(url, {
                                    method: "POST",
                                    body: data
                                })
                                .then(response => {
                                    requestSent = false;
                                    if (!response.ok) {
                                    throw new Error('Network response was not ok.');
                                    }
                                    return response.json();
                                })
                                .then(async data => {
                                    if(data.msg == "done") {
                                        update();
                                        setTimeout(() => {
                                            let url = api_url + 'tryThrow.php'; 
                                            let data  = new FormData();
                                            data.append('roomPass', localStorage.getItem('roomPass')); 
                                            data.append('token', localStorage.getItem('token')); 
                                            data.append("card", card.name);
                                            fetch(url, {
                                                method: "POST",
                                                body: data
                                            })
                                            .then(response => {
                                                requestSent = false;
                                                if (!response.ok) {
                                                throw new Error('Network response was not ok.');
                                                }
                                                return response.json();
                                            })
                                            .then(async data => {
                                                if(data.msg == "done") {
                                                    update();
                                                    nextTurn();
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Fetch error:', error);
                                            });
                                        }, 3000);
                                    }
                                })
                                .catch(error => {
                                    console.error('Fetch error:', error);
                                });
                            } else if (card.name == "screw") {
                                let url = api_url + 'screw.php'; 
                                let data  = new FormData();
                                data.append('roomPass', localStorage.getItem('roomPass')); 
                                data.append('token', localStorage.getItem('token')); 
                                fetch(url, {
                                    method: "POST",
                                    body: data
                                })
                                .then(response => {
                                    requestSent = false;
                                    if (!response.ok) {
                                    throw new Error('Network response was not ok.');
                                    }
                                    return response.json();
                                })
                                .then(async data => {
                                    if(data.msg == "done") {
                                        update();
                                        nextTurn();
                                    }
                                })
                                .catch(error => {
                                    console.error('Fetch error:', error);
                                });
                            } else {
                                requestSent = false;
                            }
                        } else if(action == "after") {
                            if (card.name == "throw") {
                                let url = api_url + 'throw.php'; 
                                let data = new FormData();
                                data.append("card", "drawn");
                                data.append('roomPass', localStorage.getItem('roomPass')); 
                                data.append('token', localStorage.getItem('token'));
                                fetch(url, {
                                    method: "POST",
                                    body: data
                                })
                                .then(response => {
                                    requestSent = false;
                                    if (!response.ok) {
                                    throw new Error('Network response was not ok.');
                                    }
                                    return response.json();
                                })
                                .then(async data => {
                                    if(data.msg == "done") {
                                        update();
                                    }
                                })
                                .catch(error => {
                                    console.error('Fetch error:', error);
                                });
                            } else if ((card.name.split('-')[0] == me.id)) {
                                if(drawThrow) {
                                    let url = api_url + 'substitute.php'; 
                                    let data  = new FormData();
                                    data.append("token", localStorage.getItem("token"));
                                    data.append("roomPass", localStorage.getItem("roomPass"));
                                    data.append("card", card.name);
                                    fetch(url, {
                                        method: "POST",
                                        body: data
                                    })
                                    .then(response => {
                                        requestSent = false;
                                        if (!response.ok) {
                                        throw new Error('Network response was not ok.');
                                        }
                                        return response.json();
                                    })
                                    .then(async data => {
                                        if(data.msg == "done") {
                                            update();
                                            setTimeout(() => {
                                                nextTurn();
                                            }, 3000);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Fetch error:', error);
                                    });
                                } else {
                                    let url = api_url + 'substitute.php'; 
                                    let data  = new FormData();
                                    data.append("token", localStorage.getItem("token"));
                                    data.append("roomPass", localStorage.getItem("roomPass"));
                                    data.append("card", card.name);
                                    fetch(url, {
                                        method: "POST",
                                        body: data
                                    })
                                    .then(response => {
                                        requestSent = false; 
                                        if (!response.ok) {
                                        throw new Error('Network response was not ok.');
                                        }
                                        return response.json();
                                    })
                                    .then(async data => {
                                        if(data.msg == "done") {
                                            update();
                                            setTimeout(() => {
                                                nextTurn();
                                            }, 3000);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Fetch error:', error);
                                    });
                                }
                            } else {
                                requestSent = false;
                            }
                        } else if (action == "post") {
                            if(active >= 7 && active <= 10 || active == 12) {
                                let url = api_url + 'reveal.php'; 
                                let data  = new FormData();
                                data.append('roomPass', localStorage.getItem('roomPass')); 
                                data.append('token', localStorage.getItem('token')); 
                                data.append("card", card.name);
                                fetch(url, {
                                    method: "POST",
                                    body: data
                                })
                                .then(response => {
                                    requestSent = false;
                                    if (!response.ok) {
                                    throw new Error('Network response was not ok.');
                                    }
                                    return response.json();
                                })
                                .then(async data => {
                                    if(data.msg == "done") {
                                        update();
                                        setTimeout(() => {
                                            if(data.pastActive >= 7 && data.pastActive <= 10) {
                                                if(activeStep == -1) nextTurn();
                                            } else if(data.pastActive == 12) {
                                                if(activeStep == -1) nextTurn();
                                            }
                                        }, 5000);
                                    }
                                })
                                .catch(error => {
                                    console.error('Fetch error:', error);
                                });
                            } else if (active == 11 && subCard != "") {
                                console.log("b");
                                let url = api_url + 'substitute.php'; 
                                let data  = new FormData();
                                data.append('roomPass', localStorage.getItem('roomPass')); 
                                data.append('token', localStorage.getItem('token')); 
                                data.append("card", card.name);
                                fetch(url, {
                                    method: "POST",
                                    body: data
                                })
                                .then(response => {
                                    requestSent = false;
                                    if (!response.ok) {
                                    throw new Error('Network response was not ok.');
                                    }
                                    return response.json();
                                })
                                .then(async data => {
                                    if(data.msg == "done") {
                                        update();
                                        setTimeout(() => {
                                            nextTurn();
                                        }, 3000);
                                    }
                                })
                                .catch(error => {
                                    console.error('Fetch error:', error);
                                });
                            } else if (active == 11 && subCard == "") {
                                let url = api_url + 'setSubCard.php'; 
                                let data  = new FormData();
                                data.append('roomPass', localStorage.getItem('roomPass')); 
                                data.append('token', localStorage.getItem('token')); 
                                data.append("card", card.name);
                                fetch(url, {
                                    method: "POST",
                                    body: data
                                })
                                .then(response => {
                                    requestSent = false;
                                    if (!response.ok) {
                                    throw new Error('Network response was not ok.');
                                    }
                                    return response.json();
                                })
                                .then(async data => {
                                    if(data.msg == "done") {
                                        update();
                                    }
                                })
                                .catch(error => {
                                    console.error('Fetch error:', error);
                                });
                            } else if (active == 13) {
                                let url = api_url + 'setTryThrowCard.php'; 
                                let data  = new FormData();
                                data.append('roomPass', localStorage.getItem('roomPass')); 
                                data.append('token', localStorage.getItem('token')); 
                                data.append("card", card.name);
                                fetch(url, {
                                    method: "POST",
                                    body: data
                                })
                                .then(response => {
                                    requestSent = false;
                                    if (!response.ok) {
                                    throw new Error('Network response was not ok.');
                                    }
                                    return response.json();
                                })
                                .then(async data => {
                                    if(data.msg == "done") {
                                        update();
                                        setTimeout(() => {
                                            let url = api_url + 'forceThrow.php'; 
                                            let data  = new FormData();
                                            data.append('roomPass', localStorage.getItem('roomPass')); 
                                            data.append('token', localStorage.getItem('token')); 
                                            data.append("card", card.name);
                                            fetch(url, {
                                                method: "POST",
                                                body: data
                                            })
                                            .then(response => {
                                                requestSent = false;
                                                if (!response.ok) {
                                                throw new Error('Network response was not ok.');
                                                }
                                                return response.json();
                                            })
                                            .then(async data => {
                                                if(data.msg == "done") {
                                                    update();
                                                    nextTurn();
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Fetch error:', error);
                                            });
                                        }, 3000);
                                    }
                                });
                            } else {
                                requestSent = false;
                            }
                        } else {
                            f = true;
                        }

                        if(card.name == "next") {
                            f = false;
                            let url = api_url + 'newRound.php'; 
                            const data = new FormData();    
                            data.append('roomPass', localStorage.getItem('roomPass'));
                            fetch(url, {
                                method: "POST",
                                body: data
                            })
                            .then(response => {
                                requestSent = false;
                                if (!response.ok) {
                                throw new Error('Network response was not ok.');
                                }
                                return response.json();
                            })
                            .then(async data => {
                                if(data.msg == "done") {
                                    update();
                                }
                            })
                            .catch(error => {
                                console.error('Fetch error:', error);
                            });
                        } else if (card.name == "end") {
                            f = false;
                            let url = api_url + 'endGame.php';
                            data = new FormData(); 
                            data.append('roomPass', localStorage.getItem('roomPass'));
                            fetch(url, {
                                method: "POST",
                                body: data
                            })
                            .then(response => {
                                requestSent = false;
                                if (!response.ok) {
                                throw new Error('Network response was not ok.');
                                }
                                return response.json();
                            })
                            .then(async data => {
                                if(data.msg == "done") {
                                    localStorage.removeItem('roomPass');
                                    initEnter();
                                }
                            })
                            .catch(error => {
                                console.error('Fetch error:', error);
                            });
                        } else if (card.name == "exit") {
                            let exitDiv = document.querySelector(".exit");
                            exitDiv.classList.remove("hide");
                        } else {
                            f = true;
                        }

                        if(f) 
                            requestSent = false;
                    }
            });
            if(hoverableCards.length == 0)
                requestSent = false;
        }
    }
    
    const debouncedClickHandler = debounce(clickHandler, 300);
    canvas.addEventListener('mouseup',debouncedClickHandler);
    
    await load();
    await draw();
    
    let updateI = setInterval(async () => {await update()}, 2000);
    

    
    async function update() {
        requestSent = false;
        hoverableCards = [];

        /* if(mode == "show") {
            action = "wait";
        } else if (mode == "wait") {
            localStorage.removeItem("token");
            clearInterval(updateI);
            initMain();
        } */

        if(localStorage.getItem("roomPass") && localStorage.getItem("token")) {
            await load();
            await draw();
        }

    }
    async function nextTurn() {
        url = api_url + 'nextTurn.php'; 

        data = new FormData();
        data.append('token', localStorage.getItem('token'));
        data.append('roomPass', localStorage.getItem('roomPass'));

        await fetch(url, {
            method: "POST",
            body: data
        })
        .then(response => {
            if (!response.ok) {
            throw new Error('Network response was not ok.');
            }
            return response.json();
        })
        .then(data => {
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
    }
    async function draw() {
        clearCanvas();

        if(action == "before" && screw == -1) {
            const text = "SCREW";
            const textSize = 50;
            context.font = `${textSize}px Arial`;
            context.textAlign = 'center';
            
            const boxWidth = context.measureText(text).width + 30;
            const boxHeight = 80;
            let boxX = canvas.width - boxWidth / 2 - 200;
            let boxY = canvas.height - boxHeight / 2 - 120;
            
            context.fillStyle = screwColor;
            context.beginPath();
            context.roundRect(boxX, boxY, boxWidth, boxHeight, 30);
            context.fill();
    
            context.fillStyle = 'white';
            context.fillText(text, canvas.width - 200, canvas.height - 100);
            hoverableCards.push({x: canvas.width - boxWidth / 2 - 200, y: canvas.height - boxHeight / 2 - 120, w: boxWidth, h: boxHeight, name: "screw"});
        }

        if(players.length < 3) {
            let text = "Waiting for other players...";
            let textSize = 50;
            context.font = `${textSize}px Arial`;
            context.textAlign = 'center';

            context.fillStyle = '#fff';
            context.fillText(text, centerX, centerY);
            
            text = "Room Pass: " + localStorage.getItem("roomPass");
            textSize = 25;
            context.font = `${textSize}px Arial`;

            const boxAlpha = 0.7; 
            context.globalAlpha = boxAlpha;
            
            const boxWidth = context.measureText(text).width + 30;
            const boxHeight = 80;
            let boxX = canvas.width - 180 - boxWidth / 2;
            let boxY = 30;
            context.fillStyle = 'black';
            context.fillRect(boxX, boxY, boxWidth, boxHeight);
            
            context.fillStyle = '#fff';
            context.globalAlpha = 1 ;   
            context.fillText(text, canvas.width - 180, 80);

        }

        if(mode == "show") {
            let bw = 5 * 50 + 80;
            let bh = 5 * 40;

            context.beginPath();
            context.moveTo(centerX - bw / 2, centerY - bh / 2);
            context.lineTo(centerX - bw / 2, centerY - bh / 2 + bh);
            for (var x = 80; x <= bw; x += 50) {
                context.moveTo(centerX - bw / 2 + x, centerY - bh / 2);
                context.lineTo(centerX - bw / 2 + x, centerY - bh / 2 + bh);
            }
            for (var x = 0; x <= bh; x += 40) {
                context.moveTo(centerX - bw / 2, centerY - bh / 2 + x);
                context.lineTo(centerX - bw / 2 + bw, centerY - bh / 2 + x);
            }
            context.strokeStyle = "black";
            context.stroke();

            let data = [
                ['players', '1', '2', '3', '4', 'total']
            ];

            let row = [me.name];
            let total = 0;
            for(let i = 0; i < 4; i++) {
                if(i < me.scores.length) {
                    row.push(me.scores[i]);
                    total += parseInt(me.scores[i]);
                } else {
                    row.push(0);
                }
            }
            row.push(total);
            data.push(row);

            for(let j = 0; j < 3; j++) {
                row = [players[j].name];
                total = 0;
                for(let i = 0; i < 4; i++) {
                    if(i < players[j].scores.length) {
                        row.push(players[j].scores[i]);
                        total += parseInt(players[j].scores[i]);
                    } else {
                        row.push(0);
                    }
                }
                row.push(total);
                data.push(row);
            }

            context.font = "bold 16px Verdana";
            context.fillStyle ="black";
            for (let y = centerY - bh / 2, count = 0; count < 5; y += 40) {
                    context.fillText(data[count][0], centerX - bw / 2 + 40, y + 25);
                    for (let x = centerX - bw / 2 + 70, keyCount = 1; x < centerX + bw / 2 - 60; x += 50) {
                        context.fillText(data[count][keyCount], x + 30, y + 25);
                        ++keyCount;
                    }
                    context.fillText(data[count][5], centerX + bw / 2 - 24, y + 25);
                ++count;
            }
            context.closePath();

            if (me.scores.length != 4) {
                const text = "Next Round";
                const textSize = 50;
                context.font = `${textSize}px Arial`;
                context.textAlign = 'center';
                
                const boxWidth = context.measureText(text).width + 30;
                const boxHeight = 80;
                let boxX = canvas.width - boxWidth / 2 - 200;
                let boxY = canvas.height - boxHeight / 2 - 120;
                
                context.fillStyle = screwColor;
                context.beginPath();
                context.roundRect(boxX, boxY, boxWidth, boxHeight, 30);
                context.fill();
        
                context.fillStyle = 'white';
                context.fillText(text, canvas.width - 200, canvas.height - 100);
                hoverableCards.push({x: canvas.width - boxWidth / 2 - 200, y: canvas.height - boxHeight / 2 - 120, w: boxWidth, h: boxHeight, name: "next"});
            } else {
                const text = "End Game";
                const textSize = 50;
                context.font = `${textSize}px Arial`;
                context.textAlign = 'center';
                
                const boxWidth = context.measureText(text).width + 30;
                const boxHeight = 80;
                let boxX = canvas.width - boxWidth / 2 - 200;
                let boxY = canvas.height - boxHeight / 2 - 120;
                
                context.fillStyle = screwColor;
                context.beginPath();
                context.roundRect(boxX, boxY, boxWidth, boxHeight, 30);
                context.fill();
        
                context.fillStyle = 'white';
                context.fillText(text, canvas.width - 200, canvas.height - 100);
                hoverableCards.push({x: canvas.width - boxWidth / 2 - 200, y: canvas.height - boxHeight / 2 - 120, w: boxWidth, h: boxHeight, name: "end"});
            }
        }

        write(me, 0);
        
        for(let i = 0; i < players.length; i++) {
            write(players[i], i + 1);
        }

        if(mode == "play") {
            if(lastThrown != -1) {
                await drawCard(centerX - 60, centerY + (drawThrow?20:0), 0, lastThrown, turn == me.id && (action == "before" || (action == "after" && !drawThrow )), "throw");
    
            } else {
                let width = 80;
                let height = 100;
                
                if(turn == me.id && ((action == "after" && !drawThrow ))) {
                    context.strokeStyle = '#27ae60'; // Border color
                    context.lineWidth = 3;
                    context.strokeRect(centerX - 60 - width / 2, centerY-height/2, width, height);
    
                    hoverableCards.push({x: centerX - 60-width / 2, y: centerY - height / 2, w: width, h: height, name : "throw"});
                }
    
            }
    
            if(isThereCard) {
                if(drawn == -1)
                    await drawCard(centerX + 60, centerY, 0, 0, turn == me.id && action == "before", "draw");
                else {
                    await drawCard(centerX + 60, centerY, 0, 0);
                    await drawCard(centerX + 60, centerY + 20, 0, drawn);
                }
            }
        }

        
        let text = "exit";
        let textSize = 20;
        context.textAlign = 'center';

        const boxWidth = context.measureText(text).width + 30;
        const boxHeight = 44;
        let boxX = 70 - boxWidth / 2;
        let boxY = 29;
        context.fillStyle = '#c0392b';
        context.fillRect(boxX, boxY, boxWidth, boxHeight);
        
        hoverableCards.push({x: boxX, y: boxY, w: boxWidth, h: boxHeight, name: "exit"});
        
        context.strokeStyle = '#fff';
        context.lineWidth = 2;
        context.strokeRect(boxX, boxY, boxWidth, boxHeight);

        context.fillStyle = '#fff';       
        context.fillText(text, 70, 60);

    
        if(me != {})
            await drawDeck(me, 0, turn == me.id && (action == "before" || action == "after" || (action == "post" && (active == 7 || active == 8 || (active == 12 && activeStep == 0) || (active == 11 && subCard == "") || active == 13))));
        for(let i = 0; i < players.length; i++) {
            await(drawDeck(players[i], i + 1, turn == me.id && action == "post" && (active == 9 || active == 10 || (active == 11 && subCard != "") || (active == 12 && activeStep == i + 1))))
        }
    
    }
    function clearCanvas() {
        context.clearRect(0, 0, canvas.width, canvas.height);
    }
    async function load() {
        
        url = api_url + 'getPlayers.php'; 

        let data = new FormData();
        data.append('token', localStorage.getItem('token'));
        data.append('roomPass', localStorage.getItem('roomPass'));

        await fetch(url, {
            method: "POST",
            body: data
        })
        .then(response => {
            if (!response.ok) {
            throw new Error('Network response was not ok.');
            }
            return response.json();
        })
        .then(data => {
            if(data.msg == "done") {
                players = data.players;
                me = data.me;
            } else if (data.msg == "not") {
                localStorage.removeItem("token");
                initMain();
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });

        url = api_url + 'getData.php'; 
        data = new FormData();
        data.append("token", localStorage.getItem("token"));
        data.append("roomPass", localStorage.getItem("roomPass"));

        await fetch(url, {
            method: "POST",
            body: data
        })
        .then(response => {
            if (!response.ok) {
            throw new Error('Network response was not ok.');
            }
            return response.json();
        })
        .then(data => {
            if(data.msg == "done") {
                lastThrown = data.lastThrown;
                if(data.turn != turn) {
                    if(data.turn == me.id) {
                        action = "before";
                    } else {
                        action = "wait";
                    }
                }
                turn = data.turn; 
                isThereCard = data.isThereCard;
                screw = data.screw;
                mode = data.mode;
                drawn = data.drawn;
                active = data.active;
                activeStep = data.activeStep;
                drawThrow = data.drawThrow;
                subCard = data.subCard;
                action = data.action;
                card = data.card;
                if(data.mode == "ended") {
                    localStorage.removeItem('roomPass');
                    initEnter();
                }
            } else if (data.msg == "not") {
                localStorage.removeItem("token");
                initMain();
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
    }

    function write(player, order) {
        const text = player.name;
        const textSize = 35;
        context.font = `${textSize}px Arial`;
        context.textAlign = 'center';
        
        const boxAlpha = 0.7; 
        context.globalAlpha = boxAlpha;
        
        const boxWidth = context.measureText(text).width + 30;
        const boxHeight = 80;
        let boxX = 0;
        let boxY = 0;
        const borderWidth = 3;
        if(order == 0) {
            boxX = centerX - boxWidth / 2;
            boxY = canvas.height - 60 - boxHeight / 2;
        } else if (order == 1) { 
            boxX = canvas.width - boxWidth / 2 - 240;
            boxY = centerY - boxHeight / 2 - 10;
        } else if(order == 2) {
            boxX = centerX - boxWidth / 2;
            boxY = 70 - boxHeight / 2;
        } else if(order == 3) {
            boxX = 250 - boxWidth / 2;
            boxY = centerY - boxHeight / 2 - 10;
        }
        if(screw == player.id) {
            context.fillStyle = '#2980b9';
        } else {
            context.fillStyle = 'black';
        }
        context.fillRect(boxX, boxY, boxWidth, boxHeight);
        
        context.globalAlpha = 1.0;

        if(turn == player.id) {
            context.strokeStyle = '#27ae60'; // Border color
            context.lineWidth = borderWidth;
            context.strokeRect(boxX, boxY, boxWidth, boxHeight);
        }

        
        context.fillStyle = 'white';

        if(order == 0) {
            context.fillText(text, centerX, canvas.height - 50);
        } else if(order == 1) {
            context.fillText(text, canvas.width - 240, centerY);
        } else if (order == 2) {
            context.fillText(text, centerX, 80);
        } else if (order == 3) {
            context.fillText(text, 250, centerY);
        }
    }

    async function drawDeck(player, order, hover) {
        let angle = - order * 22 / 14;
        let x = 0;
        let y = 0;
        for(let i = 0; i < player.cards.length; i++) {

            let shown = (subCard == player['id'] + '-' + i) ||
                        (card == player['id'] + '-' + i);

            switch(order) {
                case 0:
                    x = centerX + 140 - (90 * i);
                    y = canvas.height - 160 - shown * 20;
                    break;
                case 1:
                    x = canvas.width - 410 - shown * 20;
                    y = centerY - 140 + (90 * i);
                    break;
                case 2:
                    x = centerX - 140 + (90 * i);
                    y = 170 + shown * 20;
                    break;
                case 3:
                    x = 370 + shown * 20;
                    y = centerY + 140 - (90 * i);
                    break;
            }

            await drawCard(x, y, angle, player.cards[i], hover, `${player.id}-${i}`);
        }
    }

    function drawCard(x, y, angle, card = 0, hover, name) {
        let width = 80;
        let height = 100;
        
        context.translate(x, y);
        context.rotate(angle);
        context.drawImage(cards[card], -width / 2, -height/2, width, height);
        if(hover) {
            context.strokeStyle = '#27ae60'; // Border color
            context.lineWidth = 3;
            context.strokeRect(-width / 2, -height/2, width, height);

            hoverableCards.push({x: x-width / 2, y: y - height / 2, w: width, h: height, name});
        }

        context.rotate(-angle);
        context.translate(-x, -y);
    }

}

document.querySelector(".exit .no").addEventListener("click", () => {
    document.querySelector(".exit").classList.add("hide");
});

document.querySelector(".exit .yes").addEventListener("click", () => {
    let url = api_url + 'exitRoom.php';
    data = new FormData(); 
    data.append('token', localStorage.getItem('token'));
    data.append('roomPass', localStorage.getItem('roomPass'));
    fetch(url, {
        method: "POST",
        body: data
    })
    .then(response => {
        requestSent = false;
        if (!response.ok) {
        throw new Error('Network response was not ok.');
        }
        return response.json();
    })
    .then(async data => {
        if(data.msg == "done") {
            document.querySelector(".exit").classList.add("hide");
            localStorage.removeItem("roomPass");
            initEnter();
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
    });
});