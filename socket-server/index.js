const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');

const app = express();
app.use(cors());

const server = http.createServer(app);

const io = new Server(server, {
  cors: {
    origin: "http://localhost:5173", // React dev server
    methods: ["GET", "POST"]
  }
});

// Store active users and rooms
const users = new Map(); // socket.id -> { userId, username }
const typingUsers = new Map(); // roomId -> Set of users typing

io.on('connection', (socket) => {
  console.log(`User connected: ${socket.id}`);

  // === JOIN ROOM ===
  socket.on('join_room', ({ roomId, userId, username }) => {
    socket.join(roomId);
    users.set(socket.id, { userId, username, roomId });
    
    // Notify others in room
    socket.to(roomId).emit('user_joined', { 
      userId, 
      username,
      message: `${username} joined the chat`
    });
    
    console.log(`User ${username} joined room ${roomId}`);
  });

  // === SEND MESSAGE ===
  socket.on('send_message', ({ roomId, message, senderId, senderName }) => {
    const messageData = {
      id: Date.now().toString(),
      roomId,
      message,
      senderId,
      senderName,
      timestamp: new Date().toISOString()
    };
    
    // Send to everyone in room including sender
    io.to(roomId).emit('receive_message', messageData);
    
    console.log(`Message sent in room ${roomId}:`, message);
  });

  // === TYPING EVENTS ===
  socket.on('typing', ({ roomId, username }) => {
    // Track who is typing
    if (!typingUsers.has(roomId)) {
      typingUsers.set(roomId, new Set());
    }
    typingUsers.get(roomId).add(username);
    
    // Broadcast to others in room
    socket.to(roomId).emit('typing', { 
      username,
      isTyping: true 
    });
  });

  socket.on('stop_typing', ({ roomId, username }) => {
    // Remove from typing users
    if (typingUsers.has(roomId)) {
      typingUsers.get(roomId).delete(username);
    }
    
    // Broadcast to others
    socket.to(roomId).emit('typing', { 
      username,
      isTyping: false 
    });
  });

  // === LEAVE ROOM ===
  socket.on('leave_room', ({ roomId }) => {
    const user = users.get(socket.id);
    if (user) {
      socket.to(roomId).emit('user_left', { 
        username: user.username,
        message: `${user.username} left the chat`
      });
    }
    socket.leave(roomId);
  });

  // === DISCONNECT ===
  socket.on('disconnect', () => {
    const user = users.get(socket.id);
    if (user) {
      // Notify others in their room
      if (user.roomId) {
        socket.to(user.roomId).emit('user_left', { 
          username: user.username,
          message: `${user.username} disconnected`
        });
        
        // Remove from typing
        if (typingUsers.has(user.roomId)) {
          typingUsers.get(user.roomId).delete(user.username);
        }
      }
      users.delete(socket.id);
    }
    console.log(`User disconnected: ${socket.id}`);
  });
});

const PORT = process.env.PORT || 3001;
server.listen(PORT, () => {
  console.log(`Socket.io server running on port ${PORT}`);
});
