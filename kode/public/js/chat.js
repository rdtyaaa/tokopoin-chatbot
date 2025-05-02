/******/ (() => { // webpackBootstrap
/*!******************************!*\
  !*** ./resources/js/chat.js ***!
  \******************************/
// Wait for DOM to be fully loaded
window.addEventListener('DOMContentLoaded', function () {
  // Initialize Echo
  window.Echo = new Echo({
    broadcaster: "pusher",
    key: "local",
    wsHost: window.location.hostname,
    wsPort: 6001,
    wssPort: 6001,
    forceTLS: false,
    encrypted: false,
    disableStats: true,
    enabledTransports: ["ws", "wss"],
    authEndpoint: "/broadcasting/auth",
    auth: {
      headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
      }
    }
  });

  // Initialize chat variables
  var currentChatId = null;
  var messagesContainer = document.querySelector(".messages");
  var messageInput = document.querySelector(".message-input");
  var sendButton = document.querySelector(".message-submit");

  // Event listener for message input
  if (messageInput) {
    messageInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter" && !e.shiftKey) {
        sendMessage();
      }
    });
  }

  // Event listener for send button
  if (sendButton) {
    sendButton.addEventListener("click", sendMessage);
  }
});

// Initialize chat variables
var currentChatId = null;
var messagesContainer = document.querySelector(".messages");
var messageInput = document.querySelector(".message-input");
var sendButton = document.querySelector(".message-submit");

// Event listener for message input
messageInput.addEventListener("keypress", function (e) {
  if (e.key === "Enter" && !e.shiftKey) {
    sendMessage();
  }
});

// Event listener for send button
if (sendButton) {
  sendButton.addEventListener("click", sendMessage);
}

// Function to set up chat for a specific user
function setupChat(chatId) {
  currentChatId = chatId;

  // Listen for new messages
  window.Echo["private"]("chat.".concat(chatId)).listen(".new.chat.message", function (e) {
    updateChatMessages(e.message);
    scrollToBottom();
  });

  // Mark messages as read when chat is opened
  markMessagesAsRead(chatId);
}

// Function to send message
function sendMessage() {
  var currentUserId = parseInt(document.querySelector('meta[name="user-id"]').getAttribute('content'));
  var currentUserRole = document.querySelector('meta[name="user-role"]').getAttribute('content');
  if (!currentChatId || !messageInput.value.trim()) return;
  var message = {
    message: messageInput.value.trim(),
    sender_role: currentUserRole,
    sender_id: currentUserId,
    receiver_id: currentChatId
  };

  // Send message via API
  fetch("/api/chat/send", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify(message)
  }).then(function (response) {
    return response.json();
  }).then(function (data) {
    if (data.success) {
      messageInput.value = '';
      updateChatMessages(data.message);
      scrollToBottom();
    }
  })["catch"](function (error) {
    return console.error('Error:', error);
  });
}

// Function to update chat messages
function updateChatMessages(message) {
  var messageContainer = document.querySelector(".messages");
  var positionClass = message.sender_role === 'seller' ? 'message-left' : 'message-right';
  var messageHTML = "\n        <div class=\"message-single ".concat(positionClass, " d-flex flex-column\">\n            <div class=\"user-area d-inline-flex ").concat(message.sender_role !== 'seller' ? 'justify-content-end' : '', " align-items-center gap-3 mb-2\">\n                <div class=\"image\">\n                    <img src=\"").concat(message.sender_image, "\" alt=\"profile.jpg\">\n                </div>\n                <div class=\"meta\">\n                    <h6>").concat(message.sender_name, "</h6>\n                </div>\n            </div>\n            <div class=\"message-body\">\n                <p>").concat(message.message, "</p>\n                ").concat(message.files ? "\n                    <div class=\"message-file gap-3\">\n                        ".concat(message.files.map(function (file) {
    return "\n                            <a target=\"_blank\" href=\"".concat(file.url, "\" class=\"m-2\">\n                                <i class=\"bi bi-file-pdf\"></i> ").concat(file.name, "\n                            </a>\n                        ");
  }).join(''), "\n                    </div>\n                ") : '', "\n                <div class=\"message-time\"><span>").concat(message.time, "</span></div>\n            </div>\n        </div>\n    ");
  messageContainer.innerHTML += messageHTML;
  messageContainer.scrollTop = messageContainer.scrollHeight;
}

// Function to mark messages as read
function markMessagesAsRead(chatId) {
  fetch("/api/chat/mark-as-read/".concat(chatId), {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  })["catch"](function (error) {
    return console.error('Error marking messages as read:', error);
  });
}

// Function to scroll to bottom of chat
function scrollToBottom() {
  if (messagesContainer) {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }
}

// Event listener for chat selection
document.querySelectorAll('.get-chat').forEach(function (element) {
  element.addEventListener('click', function () {
    var chatId = this.id;
    setupChat(chatId);

    // Load chat history
    fetch("/api/chat/history/".concat(chatId)).then(function (response) {
      return response.json();
    }).then(function (data) {
      messagesContainer.innerHTML = '';
      data.messages.forEach(function (message) {
        updateChatMessages(message);
      });
      scrollToBottom();
    });
  });
});
/******/ })()
;