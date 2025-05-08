const express = require("express");
const http = require("http");
const socketIo = require("socket.io");
const Redis = require("ioredis");
const cors = require("cors");

const app = express();
const server = http.createServer(app);

// Setup CORS for WebSocket
const io = socketIo(server, {
  cors: {
    origin: "http://localhost:8000",
    methods: ["GET", "POST"],
    allowedHeaders: ["Content-Type"],
    credentials: true,
  },
});

// Setup Redis client
const redis = new Redis({
  host: "127.0.0.1",
  port: 6379,
});

// Subscribe to channels from Laravel (match the pattern)
redis.psubscribe("chat-channel.*.*", (err, count) => {
  if (err) {
    console.error("Failed to psubscribe:", err.message);
  } else {
    console.log(`Subscribed to ${count} channel(s).`);
  }
});

// Listen to Redis messages and forward them to WebSocket clients
redis.on("pmessage", (pattern, channel, message) => {
  const parsed = JSON.parse(message);
  const payload = parsed.data;

  const room = channel.replace("laravel_database:", "");
  console.log("Message to room:", room, payload);

  io.to(room).emit("new-message", payload);
});

// WebSocket connection handling
// io.on("connection", (socket) => {
//   console.log("A user connected");

//   socket.on("join", (room) => {
//     socket.join(room);
//     console.log("User joined room:", room);
//   });

//   socket.on("disconnect", () => {
//     console.log("A user disconnected");
//   });
// });

io.on("connection", (socket) => {
  const userId = socket.handshake.query.user_id;

  if (userId) {
    redis.sadd("online_users", userId);
    console.log(`User ${userId} connected`);

    // Emit event ke semua klien tentang user online
    io.emit("user-online-status", { user_id: userId, online: true });
  }

  socket.on("disconnect", async () => {
    if (userId) {
      await redis.srem("online_users", userId);
      console.log(`User ${userId} disconnected`);

      io.emit("user-online-status", { user_id: userId, online: false });
    }
  });
});

// Start the server
server.listen(3000, () => {
  console.log("Server running on http://localhost:3000");
});
