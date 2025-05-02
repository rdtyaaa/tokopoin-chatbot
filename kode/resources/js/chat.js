import Echo from "laravel-echo";
window.io = require("socket.io-client");

window.Echo = new Echo({
    broadcaster: "pusher",
    key: "local",
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
    authEndpoint: "/broadcasting/auth",
    auth: {
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    },
});

// Listen for messages on the private channel for this chat
window.Echo.private("chat." + receiver_id).listen(".new.chat.message", (e) => {
    console.log("New message:", e.message);
    updateChatMessages(e.message);
});

function updateChatMessages(message) {
    // Create a new message element and append it to the messages container
    let messageContainer = document.querySelector(".messages");
    let positionClass =
        message.sender_role === "seller" ? "message-left" : "message-right";

    let messageHTML = `
        <div class="message-single ${positionClass} d-flex flex-column">
            <div class="user-area d-inline-flex ${
                message.sender_role !== "seller" ? "justify-content-end" : ""
            } align-items-center gap-3 mb-2">
                <div class="image">
                    <img src="${message.sender_image}" alt="profile.jpg">
                </div>
                <div class="meta">
                    <h6>${message.sender_name}</h6>
                </div>
            </div>
            <div class="message-body">
                <p>${message.message}</p>
                ${
                    message.files
                        ? `<div class="message-file gap-3">${message.files
                              .map(
                                  (file) =>
                                      `<a target="_blank" href="${file.url}" class="m-2"><i class="bi bi-file-pdf"></i> ${file.name}</a>`
                              )
                              .join("")}</div>`
                        : ""
                }
                <div class="message-time"><span>${message.time}</span></div>
            </div>
        </div>
    `;

    messageContainer.innerHTML += messageHTML;
}
