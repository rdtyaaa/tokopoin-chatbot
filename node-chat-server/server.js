const express = require("express");
const http = require("http");
const socketIo = require("socket.io");
const Redis = require("ioredis");
const cors = require("cors");
const axios = require("axios");

class ChatServer {
  constructor() {
    this.config = {
      port: process.env.PORT || 3000,
      apiUrl: process.env.API_URL || "http://localhost:8000",
      corsOrigin: process.env.CORS_ORIGIN || "http://localhost:8000",
      redisHost: process.env.REDIS_HOST || "127.0.0.1",
      redisPort: parseInt(process.env.REDIS_PORT) || 6379,
      disconnectDelay: 5000,
    };

    this.app = express();
    this.server = http.createServer(this.app);
    this.io = this.initializeSocketIO();
    this.redis = this.initializeRedis();

    // Track user connections - Map of globalUserId -> Set of socketIds
    this.userConnections = new Map();
    this.disconnectTimers = new Map();

    this.setupRedisSubscription();
    this.setupSocketHandlers();
  }

  initializeSocketIO() {
    return socketIo(this.server, {
      cors: {
        origin: this.config?.corsOrigin || "http://localhost:8000",
        methods: ["GET", "POST"],
        allowedHeaders: ["Content-Type"],
        credentials: true,
      },
      pingTimeout: 60000,
      pingInterval: 25000,
      transports: ["websocket", "polling"],
      upgradeTimeout: 10000,
      connectionStateRecovery: {
        maxDisconnectionDuration: 2 * 60 * 1000,
        skipMiddlewares: true,
      },
    });
  }

  initializeRedis() {
    const redisConfig = {
      host: this.config.redisHost,
      port: this.config.redisPort,
      retryDelayOnFailover: 100,
      maxRetriesPerRequest: 3,
      keepAlive: true,
      lazyConnect: true,
    };

    return {
      subscriber: new Redis(redisConfig),
      publisher: new Redis(redisConfig),
    };
  }

  setupRedisSubscription() {
    const { subscriber } = this.redis;

    subscriber.psubscribe("chat-channel.*", (err, count) => {
      if (err) {
        console.error("Failed to psubscribe:", err.message);
      } else {
        console.log(`Subscribed to ${count} channel(s).`);
      }
    });

    subscriber.on("pmessage", (pattern, channel, message) => {
      this.handleRedisMessage(pattern, channel, message);
    });

    subscriber.on("error", (err) => {
      console.error("Redis subscriber error:", err);
    });
  }

  handleRedisMessage(pattern, channel, message) {
    try {
      const parsed = JSON.parse(message);
      const payload = parsed.data;
      const room = channel.replace("laravel_database:", "");
      const { seller_id, customer_id, message: msg } = payload;

      this.io.to(room).emit("new-message", payload, (ack) => {
        if (ack && ack.length > 0) {
          console.log(
            `Message delivered to ${ack.length} clients in room ${room}`
          );
        }
      });

      console.log("Message to room:", room, payload);

      this.io.to(`notify.seller.${seller_id}`).emit("notify-new-chat", {
        customer_id,
        message: msg,
      });
    } catch (error) {
      console.error("Error handling Redis message:", error);
    }
  }

  async saveLastSeenToDB(userId, role, timestamp) {
    try {
      await axios.post(
        `${this.config.apiUrl}/api/save-last-seen`,
        {
          user_id: userId,
          role: role,
          last_seen: timestamp,
        },
        {
          timeout: 5000,
        }
      );
    } catch (error) {
      console.error(
        "Failed to save last seen:",
        error.response?.data || error.message
      );
    }
  }

  // Improved connection tracking
  addSocketConnection(globalUserId, socketId) {
    if (!this.userConnections.has(globalUserId)) {
      this.userConnections.set(globalUserId, new Set());
    }
    this.userConnections.get(globalUserId).add(socketId);
  }

  removeSocketConnection(globalUserId, socketId) {
    if (this.userConnections.has(globalUserId)) {
      this.userConnections.get(globalUserId).delete(socketId);
      if (this.userConnections.get(globalUserId).size === 0) {
        this.userConnections.delete(globalUserId);
        return true; // User has no more connections
      }
    }
    return false; // User still has connections
  }

  getActiveConnectionCount(globalUserId) {
    return this.userConnections.has(globalUserId)
      ? this.userConnections.get(globalUserId).size
      : 0;
  }

  // Improved method to check if user is still connected
  isUserStillConnected(globalUserId) {
    if (!this.userConnections.has(globalUserId)) {
      return false;
    }

    const socketIds = this.userConnections.get(globalUserId);
    let activeConnections = 0;

    // Check each socket ID to see if it's still connected
    for (const socketId of socketIds) {
      const socket = this.io.sockets.sockets.get(socketId);
      if (socket && socket.connected) {
        activeConnections++;
      } else {
        // Remove dead socket from tracking
        socketIds.delete(socketId);
      }
    }

    // Clean up if no active connections
    if (activeConnections === 0) {
      this.userConnections.delete(globalUserId);
      return false;
    }

    console.log(
      `User ${globalUserId} has ${activeConnections} active connections`
    );
    return true;
  }

  async addUserOnline(globalUserId, socketId) {
    try {
      const wasOffline = !this.userConnections.has(globalUserId);

      // Add socket to tracking
      this.addSocketConnection(globalUserId, socketId);

      // Clear any pending disconnect timer
      if (this.disconnectTimers.has(globalUserId)) {
        clearTimeout(this.disconnectTimers.get(globalUserId));
        this.disconnectTimers.delete(globalUserId);
        console.log(`Cleared disconnect timer for ${globalUserId}`);
      }

      // Only broadcast if user was previously offline
      if (wasOffline) {
        await this.redis.publisher.sadd("online_users", globalUserId);

        this.io.emit("user-online-status", {
          user_id: globalUserId,
          online: true,
          last_seen: null,
        });

        console.log(`User ${globalUserId} added to online list`);
      } else {
        console.log(
          `User ${globalUserId} additional connection (${this.getActiveConnectionCount(
            globalUserId
          )})`
        );
      }
    } catch (error) {
      console.error("Error adding user online:", error);
    }
  }

  async removeUserOffline(globalUserId, userId, role, socketId) {
    try {
      // Remove this specific socket connection
      const isLastConnection = this.removeSocketConnection(
        globalUserId,
        socketId
      );

      console.log(
        `User ${globalUserId} connection removed. Remaining: ${this.getActiveConnectionCount(
          globalUserId
        )}`
      );

      if (isLastConnection) {
        // This was the last connection, mark user as offline
        await this.redis.publisher.srem("online_users", globalUserId);

        // Save last seen to database
        const timestamp = new Date().toISOString();
        await this.saveLastSeenToDB(userId, role, timestamp);

        // Broadcast status change to all users
        this.io.emit("user-online-status", {
          user_id: globalUserId,
          online: false,
          last_seen: timestamp,
        });

        console.log(`User ${globalUserId} removed from online list`);
      }
    } catch (error) {
      console.error("Error removing user offline:", error);
    }
  }

  async getOnlineUsers() {
    try {
      return await this.redis.publisher.smembers("online_users");
    } catch (error) {
      console.error("Error getting online users:", error);
      return [];
    }
  }

  async handleImmediateDisconnect(globalUserId, userId, role, socketId) {
    try {
      // Cancel any existing timer
      if (this.disconnectTimers.has(globalUserId)) {
        clearTimeout(this.disconnectTimers.get(globalUserId));
        this.disconnectTimers.delete(globalUserId);
      }

      // Remove the socket connection and check if user should be offline
      await this.removeUserOffline(globalUserId, userId, role, socketId);
    } catch (error) {
      console.error("Error in immediate disconnect:", error);
    }
  }

  async handleDelayedDisconnect(globalUserId, userId, role, socketId) {
    try {
      // Remove socket connection first
      const isLastConnection = this.removeSocketConnection(
        globalUserId,
        socketId
      );

      if (isLastConnection) {
        // Set a timer to mark user as offline after delay
        const timerId = setTimeout(async () => {
          // Double-check user is still disconnected
          if (!this.isUserStillConnected(globalUserId)) {
            await this.redis.publisher.srem("online_users", globalUserId);

            const timestamp = new Date().toISOString();
            await this.saveLastSeenToDB(userId, role, timestamp);

            this.io.emit("user-online-status", {
              user_id: globalUserId,
              online: false,
              last_seen: timestamp,
            });

            console.log(`User ${globalUserId} marked as offline after timeout`);
          }
          this.disconnectTimers.delete(globalUserId);
        }, this.config.disconnectDelay);

        this.disconnectTimers.set(globalUserId, timerId);
      }
    } catch (error) {
      console.error("Error in delayed disconnect:", error);
    }
  }

  setupSocketHandlers() {
    this.io.on("connection", async (socket) => {
      const { role, user_id: userId } = socket.handshake.query;
      const globalUserId = `${role}-${userId}`;

      console.log(`User ${globalUserId} connected with socket ${socket.id}`);

      // Add user to online list
      await this.addUserOnline(globalUserId, socket.id);

      // Send current online users to the newly connected user
      const allOnline = await this.getOnlineUsers();
      socket.emit("all-users-online", allOnline);

      // Join seller notification room
      if (role === "seller") {
        const notifRoom = `notify.seller.${userId}`;
        socket.join(notifRoom);
        console.log(`Seller ${userId} joined notif room ${notifRoom}`);
      }

      // Handle room joining
      socket.on("join", (room) => {
        if (typeof room === "string" && room.trim()) {
          socket.join(room);
          console.log(`User ${globalUserId} joined room ${room}`);
        }
      });

      // Handle online users request
      socket.on("request-online-users", async () => {
        try {
          const onlineUsers = await this.getOnlineUsers();
          socket.emit("all-users-online", onlineUsers);
          console.log(
            `Sent online users to ${globalUserId}:`,
            onlineUsers.length,
            "users"
          );
        } catch (error) {
          console.error(
            `Error sending online users to ${globalUserId}:`,
            error
          );
        }
      });

      // Add heartbeat/ping handler
      socket.on("ping", () => {
        socket.emit("pong");
      });

      // Handle disconnect with improved logic
      socket.on("disconnect", (reason) => {
        console.log(
          `User ${globalUserId} disconnected: ${reason} at ${new Date().toISOString()}`
        );

        // Handle different disconnect reasons
        if (
          reason === "transport close" ||
          reason === "client namespace disconnect"
        ) {
          // Client explicitly closed connection, mark offline immediately
          this.handleImmediateDisconnect(globalUserId, userId, role, socket.id);
        } else {
          // Network issue or server disconnect, use delay
          this.handleDelayedDisconnect(globalUserId, userId, role, socket.id);
        }
      });

      // Handle connection errors
      socket.on("error", (error) => {
        console.error(`Socket error for user ${globalUserId}:`, error);
      });

      // Handle reconnection
      socket.on("reconnect", () => {
        console.log(`User ${globalUserId} reconnected`);

        // Cancel any pending offline timer
        if (this.disconnectTimers.has(globalUserId)) {
          clearTimeout(this.disconnectTimers.get(globalUserId));
          this.disconnectTimers.delete(globalUserId);
        }
      });
    });
  }

  start() {
    this.server.listen(this.config.port, () => {
      console.log(`Server running on http://localhost:${this.config.port}`);
    });
  }

  // Graceful shutdown
  async shutdown() {
    console.log("Shutting down server...");

    // Clear all timers
    for (const timer of this.disconnectTimers.values()) {
      clearTimeout(timer);
    }
    this.disconnectTimers.clear();

    this.redis.subscriber.disconnect();
    this.redis.publisher.disconnect();

    this.server.close(() => {
      console.log("Server closed");
      process.exit(0);
    });
  }
}

// Initialize and start the server
const chatServer = new ChatServer();
chatServer.start();

// Handle graceful shutdown
process.on("SIGTERM", () => chatServer.shutdown());
process.on("SIGINT", () => chatServer.shutdown());
