// import postData from 'chat.js'
// const url = "https://dev-cms-sgmaster.sellon.net/api_admin/crm/chat/";
const url = "http://localhost/backend-sellon/api_admin/crm/chat/";

async function postData(url = '', data = {}) {
    // Default options are marked with *
    const response = await fetch(url, {
        method: 'POST', // *GET, POST, PUT, DELETE, etc.
        mode: 'cors', // no-cors, *cors, same-origin
        cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
        credentials: 'same-origin', // include, *same-origin, omit
        headers: {
            'Content-Type': 'application/json'
            // 'Content-Type': 'application/x-www-form-urlencoded',
        },
        redirect: 'follow', // manual, *follow, error
        referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
        body: JSON.stringify(data) // body data type must match "Content-Type" header
    });
    return response.json(); // parses JSON response into native JavaScript objects
}

const getDate = (payload) => {
    const monthNames = [
        "January", "February", "March", "April", 
        "May", "June", "July", "August", 
        "September", "October", "November", "December"
    ];
    
    let dateObj;
    if (payload) {
        dateObj = new Date(payload);
    } else {
        dateObj = new Date();
    }
    
    const month = monthNames[dateObj.getMonth()];
    const day = String(dateObj.getDate()).padStart(2, '0');
    const year = dateObj.getFullYear();
    const hour = dateObj.getHours();
    const minute = dateObj.getMinutes();
    const output = month + ' ' + day + ', ' + year + ' ' + hour + ':' + minute;
    return output;
}

const loadMessage = () => {
    const chat = document.getElementById("chat_room");
    const room_id = document.getElementById("chat_room_id").value
    fetch(url+'detail/'+room_id)
        .then(response => response.json())
        .then(response => {
            chat.innerHTML= "";
            response.data.map((data)=>{
                const header = document.createElement("h1");
                const message = document.createElement("div");
                const timestamp = document.createElement("i");

                if(data[8]=='announcement'){
                    timestamp.className = "align-center";
                    timestamp.appendChild(document.createTextNode(getDate(data[2])));
                    message.className = "chats align-center shadow";
                    message.style = "background:#80ADCB";
                    message.align = "center";
                    message.appendChild(document.createTextNode(data[9]));
                }
                else if(data[6]=="Admin"){
                    timestamp.className = "align-flex-end";
                    timestamp.appendChild(document.createTextNode(getDate(data[2])));
                    message.className = "chats align-flex-end shadow";
                    header.appendChild(document.createTextNode(data[6]));
                    message.appendChild(header);
                    message.appendChild(document.createTextNode(data[9]));
                }
                else {
                    timestamp.className = "align-flex-start";
                    timestamp.appendChild(document.createTextNode(getDate(data[2])));
                    message.className = "chats align-flex-start shadow";
                    header.appendChild(document.createTextNode(data[6]));
                    message.appendChild(header);
                    message.appendChild(document.createTextNode(data[9]));
                }
                
                chat.prepend(timestamp);
                chat.prepend(message);
            })
            chat.scrollTo(0, 0);
            document.getElementById("message").value = "";
            document.getElementById("message").focus();
        });
}

const selectContact = (payload) => {
    for (var i = 0; i < elements.length; i++) {
        elements[i].className = elements[i].className.replace(" active", "");
    }
    payload.className += " active";
}

document.addEventListener("DOMContentLoaded", function(){
    loadMessage();    
});

const sendButton = document.getElementById("send_button");
sendButton.addEventListener("click", function(){
    const room_id = document.getElementById("chat_room_id").value
    const message = document.getElementById("message").value
    // loadMessage();    
    postData(url+'send_chat_admin/'+room_id, { 
        textMessage: message,
    }).then(data => loadMessage() );
}); 