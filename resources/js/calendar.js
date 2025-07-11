// Google Calendar API configuration
const CLIENT_ID = '722105965023-gfb5s3nu6fplfug3oh9li381l7ol9nh3.apps.googleusercontent.com';
const API_KEY = 'AIzaSyD_9ZQZQZQZQZQZQZQZQZQZQZQZQZQZQZQ';
const SCOPES = 'https://www.googleapis.com/auth/calendar';
let gapiInited = false;
let gisInited = false;
let tokenClient;

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar las APIs de Google
    gapiLoad();
    gisLoad();
});

function gapiLoad() {
    gapi.load('client', initializeGapiClient);
}

async function initializeGapiClient() {
    await gapi.client.init({
        apiKey: API_KEY,
        discoveryDocs: ['https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest'],
    });
    gapiInited = true;
}

function gisLoad() {
    tokenClient = google.accounts.oauth2.initTokenClient({
        client_id: CLIENT_ID,
        scope: SCOPES,
        callback: '', 
    });
    gisInited = true;
}

function handleAuthClick() {
    tokenClient.callback = async (resp) => {
        if (resp.error !== undefined) {
            throw resp;
        }
        // Cerrar modal de autenticación si está abierto
        window.dispatchEvent(new CustomEvent('auth-success'));
    };

    if (gapi.client.getToken() === null) {
        tokenClient.requestAccessToken({prompt: 'consent'});
    } else {
        tokenClient.requestAccessToken({prompt: ''});
    }
}

async function createCalendarEvent(title, startTime, description) {
    const endTime = new Date(startTime);
    endTime.setHours(endTime.getHours() + 1);

    const event = {
        summary: title,
        description: description,
        start: {
            dateTime: startTime.toISOString(),
            timeZone: 'America/La_Paz',
        },
        end: {
            dateTime: endTime.toISOString(),
            timeZone: 'America/La_Paz',
        },
    };

    const request = gapi.client.calendar.events.insert({
        calendarId: 'primary',
        resource: event,
    });

    const response = await request;
    return response.result;
}