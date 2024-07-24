const WebSocket = require('ws');
const axios = require('axios');
const express = require('express');
const session = require('express-session');
const http = require('http');

const app = express();

// Use the session middleware
const sessionMiddleware = session({
    secret: 'your-secret-key',
    resave: false,
    saveUninitialized: true,
    cookie: { secure: false } // Set secure to true if using HTTPS
});

app.use(sessionMiddleware);

const server = http.createServer(app);

const wss = new WebSocket.Server({ noServer: true });

server.on('upgrade', (request, socket, head) => {
    sessionMiddleware(request, {}, () => {
        wss.handleUpgrade(request, socket, head, (ws) => {
            wss.emit('connection', ws, request);
        });
    });
});
wss.on('connection', (socket, request) => {
    console.log('Client connected');
    // Ensure CSRF token is set in the session
    request.session.csrfToken = request.session.csrfToken || Math.random().toString(36).slice(2);

    socket.on('message', async (message) => {
        try {
            const data = JSON.parse(message);
            const csrfToken = data._token; // Extract CSRF token from the WebSocket message
            console.log('CSRF Token:', csrfToken);

            // Verify CSRF token

            console.log(`token req ${request.session.csrfToken}`);
            console.log(`token: ${csrfToken}`);



            // if (csrfToken !== request.session.csrfToken) {
            //     console.error('CSRF Token mismatch. Disconnecting client.');
            //     socket.terminate();
            //     return;
            // }



            // Include CSRF token in the HTTP request headers
            const headers = {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': request.session.csrfToken,
            };

            // Send HTTP request to update lesson progress
            await axios.post('http://tutoland_app.test/update-lesson-progress', data, { headers });

            console.log(`Received and stored data: ${message}`);
        } catch (error) {
            console.error('Error processing message:', error);
        }
    });

    socket.on('close', () => {
        console.log('Client disconnected');
    });
});

server.listen(3000, () => {
    console.log('Server running on port 3000');
});
