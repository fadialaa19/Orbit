import { useState, useEffect, useRef } from 'react';
import io from 'socket.io-client';

// Connect to Socket.io server
const socket = io.connect('http://localhost:3001');

// Current user info (replace with actual auth)
const currentUser = {
  id: 'user_123',
  username: 'Ahmed'
};

function ChatComponent({ roomId = 'support_room_1' }) {
  const [messages, setMessages] = useState([]);
  const [newMessage, setNewMessage] = useState('');
  const [isTyping, setIsTyping] = useState(false);
  const [typingUsers, setTypingUsers] = useState([]);
  const [connected, setConnected] = useState(false);
  
  const messagesEndRef = useRef(null);
  const typingTimeoutRef = useRef(null);

  // === SOCKET CONNECTION & EVENT LISTENERS ===
  useEffect(() => {
    // Join room on connect
    socket.emit('join_room', {
      roomId,
      userId: currentUser.id,
      username: currentUser.username
    });

    setConnected(true);

    // Listen for incoming messages
    socket.on('receive_message', (messageData) => {
      setMessages((prev) => [...prev, messageData]);
      scrollToBottom();
    });

    // Listen for typing events
    socket.on('typing', ({ username, isTyping: typing }) => {
      setTypingUsers((prev) => {
        if (typing && !prev.includes(username)) {
          return [...prev, username];
        } else if (!typing) {
          return prev.filter((u) => u !== username);
        }
        return prev;
      });
    });

    // Listen for user join/leave (optional notifications)
    socket.on('user_joined', ({ username }) => {
      console.log(`${username} joined`);
    });

    socket.on('user_left', ({ username }) => {
      console.log(`${username} left`);
    });

    // Cleanup on unmount
    return () => {
      socket.emit('leave_room', { roomId });
      socket.off('receive_message');
      socket.off('typing');
      socket.off('user_joined');
      socket.off('user_left');
    };
  }, [roomId]);

  // Auto-scroll to bottom when messages change
  useEffect(() => {
    scrollToBottom();
  }, [messages]);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  // === TYPING HANDLER ===
  const handleInputChange = (e) => {
    setNewMessage(e.target.value);

    // Emit typing event
    if (!isTyping) {
      setIsTyping(true);
      socket.emit('typing', { roomId, username: currentUser.username });
    }

    // Clear previous timeout
    if (typingTimeoutRef.current) {
      clearTimeout(typingTimeoutRef.current);
    }

    // Stop typing after 1 second of inactivity
    typingTimeoutRef.current = setTimeout(() => {
      setIsTyping(false);
      socket.emit('stop_typing', { roomId, username: currentUser.username });
    }, 1000);
  };

  // === SEND MESSAGE ===
  const sendMessage = async () => {
    if (newMessage.trim() === '') return;

    const messageData = {
      roomId,
      message: newMessage,
      senderId: currentUser.id,
      senderName: currentUser.username
    };

    // Emit to socket server
    socket.emit('send_message', messageData);

    // Clear typing status
    if (isTyping) {
      setIsTyping(false);
      socket.emit('stop_typing', { roomId, username: currentUser.username });
    }

    setNewMessage('');
  };

  // === HANDLE KEY PRESS ===
  const handleKeyPress = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  };

  // === RENDER ===
  return (
    <div className="chat-container" style={styles.container}>
      {/* Header */}
      <div style={styles.header}>
        <h3>💬 Chat</h3>
        <span style={styles.status}>
          {connected ? '🟢 متصل' : '🔴 غير متصل'}
        </span>
      </div>

      {/* Messages Area */}
      <div style={styles.messagesArea}>
        {messages.map((msg, index) => {
          const isOwnMessage = msg.senderId === currentUser.id;
          return (
            <div
              key={index}
              style={{
                ...styles.messageBubble,
                ...(isOwnMessage ? styles.myMessage : styles.otherMessage)
              }}
            >
              {!isOwnMessage && (
                <div style={styles.senderName}>{msg.senderName}</div>
              )}
              <div style={styles.messageText}>{msg.message}</div>
              <div style={styles.timestamp}>
                {new Date(msg.timestamp).toLocaleTimeString([], {
                  hour: '2-digit',
                  minute: '2-digit'
                })}
              </div>
            </div>
          );
        })}
        
        {/* Typing Indicator */}
        {typingUsers.length > 0 && (
          <div style={styles.typingIndicator}>
            {typingUsers.join(', ')} {typingUsers.length === 1 ? 'يكتب...' : 'يكتبون...'}
          </div>
        )}
        
        <div ref={messagesEndRef} />
      </div>

      {/* Input Area */}
      <div style={styles.inputArea}>
        <textarea
          value={newMessage}
          onChange={handleInputChange}
          onKeyDown={handleKeyPress}
          placeholder="اكتب رسالتك..."
          style={styles.input}
          rows={1}
        />
        <button onClick={sendMessage} style={styles.sendButton}>
          إرسال
        </button>
      </div>
    </div>
  );
}

// === STYLES ===
const styles = {
  container: {
    width: '100%',
    maxWidth: '500px',
    margin: '0 auto',
    border: '1px solid #e5e7eb',
    borderRadius: '12px',
    overflow: 'hidden',
    fontFamily: 'Cairo, sans-serif'
  },
  header: {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: '12px 16px',
    backgroundColor: '#4f46e5',
    color: 'white'
  },
  status: {
    fontSize: '12px'
  },
  messagesArea: {
    height: '400px',
    overflowY: 'auto',
    padding: '16px',
    backgroundColor: '#f9fafb',
    display: 'flex',
    flexDirection: 'column',
    gap: '8px'
  },
  messageBubble: {
    maxWidth: '75%',
    padding: '10px 14px',
    borderRadius: '16px',
    position: 'relative'
  },
  myMessage: {
    alignSelf: 'flex-end',
    backgroundColor: '#4f46e5',
    color: 'white',
    borderBottomRightRadius: '4px'
  },
  otherMessage: {
    alignSelf: 'flex-start',
    backgroundColor: 'white',
    color: '#1f2937',
    borderBottomLeftRadius: '4px',
    boxShadow: '0 1px 2px rgba(0,0,0,0.05)'
  },
  senderName: {
    fontSize: '11px',
    fontWeight: 'bold',
    color: '#6b7280',
    marginBottom: '4px'
  },
  messageText: {
    fontSize: '14px',
    lineHeight: '1.4',
    whiteSpace: 'pre-wrap'
  },
  timestamp: {
    fontSize: '10px',
    opacity: 0.7,
    marginTop: '4px',
    textAlign: 'right'
  },
  typingIndicator: {
    fontSize: '12px',
    color: '#6b7280',
    fontStyle: 'italic',
    padding: '8px'
  },
  inputArea: {
    display: 'flex',
    gap: '8px',
    padding: '12px',
    backgroundColor: 'white',
    borderTop: '1px solid #e5e7eb'
  },
  input: {
    flex: 1,
    padding: '10px 14px',
    borderRadius: '20px',
    border: '1px solid #e5e7eb',
    outline: 'none',
    resize: 'none',
    fontFamily: 'inherit',
    fontSize: '14px'
  },
  sendButton: {
    padding: '10px 20px',
    backgroundColor: '#4f46e5',
    color: 'white',
    border: 'none',
    borderRadius: '20px',
    cursor: 'pointer',
    fontWeight: 'bold',
    transition: 'background 0.2s'
  }
};

export default ChatComponent;
