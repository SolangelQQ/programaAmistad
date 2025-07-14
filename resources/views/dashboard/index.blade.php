@extends('layouts.app')

@section('content')
<div class="mx-auto" style="max-width: 95%">
    <h1 class="text-2xl font-bold py-6">Panel Principal</h1>

    @include('dashboard.partials.welcome-card')
    
    <!-- Stats Cards - Ahora con datos dinámicos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @component('dashboard.partials.stats-cards')
            @slot('title', 'Total de todas las Actividades')
            @slot('value', $stats['total_activities']['value'])
            @slot('percentage', abs($stats['total_activities']['percentage']))
            @slot('trend', $stats['total_activities']['trend'])
            @slot('color', 'blue-700')
            @slot('icon')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            @endslot
        @endcomponent
        
        @component('dashboard.partials.stats-cards')
            @slot('title', 'Total PeerBuddies')
            @slot('value', $stats['total_peer_buddies']['value'])
            @slot('percentage', abs($stats['total_peer_buddies']['percentage']))
            @slot('trend', $stats['total_peer_buddies']['trend'])
            @slot('color', 'purple-500')
            @slot('icon')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            @endslot
        @endcomponent
        
        @component('dashboard.partials.stats-cards')
            @slot('title', 'Amistades Activas')
            @slot('value', $stats['active_friendships']['value'])
            @slot('percentage', abs($stats['active_friendships']['percentage']))
            @slot('trend', $stats['active_friendships']['trend'])
            @slot('color', 'blue-400')
            @slot('icon')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            @endslot
        @endcomponent
        
        @component('dashboard.partials.stats-cards')
            @slot('title', 'Actividades este mes')
            @slot('value', $stats['activities_this_month']['value'])
            @slot('percentage', abs($stats['activities_this_month']['percentage']))
            @slot('trend', $stats['activities_this_month']['trend'])
            @slot('color', 'blue-700')
            @slot('icon')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            @endslot
        @endcomponent
    </div>
    
    @include('dashboard.partials.calendar')
    
    @include('dashboard.partials.modals.event-modal')
    @include('dashboard.partials.modals.auth-modal')
</div>

@push('scripts')
    <script src="https://apis.google.com/js/api.js"></script>
    <!-- <script src="{{ asset('js/calendar.js') }}"></script> -->
     <script>
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
     </script>
@endpush

@endsection