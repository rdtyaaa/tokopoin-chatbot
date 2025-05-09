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

// Redis client untuk SUBSCRIBE
const subscriber = new Redis({ host: "127.0.0.1", port: 6379 });

// Redis client untuk COMMAND
const publisher = new Redis({ host: "127.0.0.1", port: 6379 });

// Subscribe to channels from Laravel (match the pattern)
subscriber.psubscribe("chat-channel.*.*", (err, count) => {
  if (err) {
    console.error("Failed to psubscribe:", err.message);
  } else {
    console.log(`Subscribed to ${count} channel(s).`);
  }
});

// Listen to Redis messages and forward them to WebSocket clients
subscriber.on("pmessage", (pattern, channel, message) => {
  const parsed = JSON.parse(message);
  const payload = parsed.data;

  const room = channel.replace("laravel_database:", "");
  console.log("Message to room:", room, payload);

  io.to(room).emit("new-message", payload);
});

const axios = require("axios");

async function saveLastSeenToDB(userId, role, timestamp) {
  try {
    await axios.post("http://localhost:8000/api/save-last-seen", {
      user_id: userId,
      role: role,
      last_seen: timestamp,
    });
  } catch (error) {
    console.error(
      "Failed to save last seen:",
      error.response?.data || error.message
    );
  }
}

// Socket.io connection
io.on("connection", async (socket) => {
  const role = socket.handshake.query.role;
  const userId = socket.handshake.query.user_id;
  const globalUserId = `${role}-${userId}`;

  // Tambahkan user ke Redis Set online
  await publisher.sadd("online_users", globalUserId);

  // Emit status online user ini ke semua client
  io.emit("user-online-status", { user_id: globalUserId, online: true });

  // Ambil semua user yang online dari Redis dan kirim ke client yang baru konek
  const allOnline = await publisher.smembers("online_users");
  console.log("Emitting all-users-online with:", allOnline);
  socket.emit("all-users-online", allOnline);

  // Tangani join ke room chat
  socket.on("join", (room) => {
    socket.join(room);
    console.log(`User ${globalUserId} joined room ${room}`);
  });

  socket.on("request-online-users", async () => {
    const onlineUsers = await publisher.smembers("online_users");
    socket.emit("all-users-online", onlineUsers);
  });

  // Tangani disconnect
  socket.on("disconnect", async () => {
    await publisher.srem("online_users", globalUserId);
    await saveLastSeenToDB(userId, role, new Date());
    console.log("All online users:", allOnline);
    io.emit("user-online-status", { user_id: globalUserId, online: false });
  });
});

// Start the server
server.listen(3000, () => {
  console.log("Server running on http://localhost:3000");
});
