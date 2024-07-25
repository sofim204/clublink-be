const selectContact = (payload) => {
    var elements = document.getElementsByClassName("menu-item");
    for (var i = 0; i < elements.length; i++) {
        elements[i].className = elements[i].className.replace(" active", "");
    }
    payload.className += " active";
}
const getCurrentDate = (payload) => {
    const monthNames = [
        "January", "February", "March", "April", 
        "May", "June", "July", "August", 
        "September", "October", "November", "December"
    ];
    if (payload) {
        const dateObj = new Date(payload);
    } else {
        const dateObj = new Date();
    }
    const dateObj = new Date();
    const month = monthNames[dateObj.getMonth()];
    const day = String(dateObj.getDate()).padStart(2, '0');
    const year = dateObj.getFullYear();
    const hour = dateObj.getHours();
    const minute = dateObj.getMinutes();
    const output = year + ', ' + month + ' ' + day + ' ' + hour + ':' + minute;
    return output;
}
const chat = document.getElementById("chat_room");
const sendButton = document.getElementById("send_button");
sendButton.addEventListener("click", function(){
    const timestampValue = getCurrentDate();
    const timestamp = document.createElement("i");
    timestamp.className = "align-flex-end";
    timestamp.appendChild(document.createTextNode(timestampValue));
    
    const messageText = document.getElementById("message").value;
    const message = document.createElement("div");
    message.className = "chats align-flex-end shadow";

    const header = document.createElement("h1");
    header.appendChild(document.createTextNode("Admin"));
    message.appendChild(header);
    message.appendChild(document.createTextNode(messageText));
    
    chat.prepend(timestamp);
    chat.prepend(message);
    document.getElementById("message").value = "";
    document.getElementById("message").focus();
}); 