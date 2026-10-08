@extends('layouts.main')
@section('title', 'Location Tracking Dashboard')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-8">
                    <h4 class="page-title">Location Tracking</h4>
                    <p class="text-muted mb-0">Monitor field team location in real-time and review historical routes.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar / Filters -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">Filters</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Employee / User</label>
                            <select id="userFilter" class="form-select select2">
                                <option value="">Select Employee</option>
                                <!-- Populated dynamically -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" id="dateFilter" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <button class="btn btn-primary w-100" id="loadRouteBtn">Load Route</button>
                    </div>
                </div>
                
                <!-- My Tracking Card -->
                <div class="card mt-3">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">My Tracking</h5>
                    </div>
                    <div class="card-body text-center">
                        <p class="text-muted text-sm mb-3">Manually start or stop your daily route tracking.</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <button class="btn btn-success flex-fill" id="startTrackingBtn"><i class="fa fa-play me-1"></i> Start</button>
                            <button class="btn btn-danger flex-fill" id="stopTrackingBtn"><i class="fa fa-stop me-1"></i> Stop</button>
                        </div>
                    </div>
                </div>
                
                <!-- Tracking Stats Card -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Tracking Summary</h5>
                        <div class="mt-3">
                            <p class="mb-1 text-muted text-sm">Total Distance Travelled</p>
                            <h4 id="totalDistance">0.00 km</h4>
                        </div>
                        <div class="mt-3">
                            <p class="mb-1 text-muted text-sm">Last Ping</p>
                            <h5 id="lastPingTime" class="text-primary">-</h5>
                        </div>
                        <div class="mt-3">
                            <p class="mb-1 text-muted text-sm">GPS Accuracy</p>
                            <h5 id="gpsAccuracy">-</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map View -->
            <div class="col-md-9">
                <div class="card h-100">
                    <div class="card-body p-0" style="min-height: 600px; position: relative;">
                        <!-- Loading Overlay -->
                        <div id="mapLoading" class="position-absolute w-100 h-100 d-flex justify-content-center align-items-center bg-white" style="z-index: 10; opacity: 0.8;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div id="trackingMap" style="width: 100%; height: 100%; min-height: 600px; border-radius: 4px;"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var map;
    var markers = [];
    var userMarkers = {}; // Store live markers by user ID
    var polyline = null;
    var boxSlug = '{{ request()->route("box_slug") }}';
    var currentUserId = '{{ Auth::id() }}';
    
    // Initialize Map
    function initMap() {
        map = new google.maps.Map(document.getElementById('trackingMap'), {
            center: { lat: 28.6139, lng: 77.2090 }, // Default to India (Delhi)
            zoom: 5,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        $('#mapLoading').addClass('d-none');
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                map.setCenter({ lat: position.coords.latitude, lng: position.coords.longitude });
                map.setZoom(14);
            }, function() {}, { enableHighAccuracy: true, timeout: 10000 });
        }

        loadCurrentLocations();
        if (typeof startLiveLocalTracking === 'function') startLiveLocalTracking();
    }
    
    // Load active users dropdown
    function loadUsers() {
        $.ajax({
            url: "{{ route('employee.get.box.users', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'GET',
            success: function(res) {
                if (res && res.data) {
                    res.data.forEach(user => {
                        $('#userFilter').append(`<option value="${user.id}">${user.name}</option>`);
                    });
                    
                    if ($('#userFilter').hasClass('select2-hidden-accessible')) {
                        $('#userFilter').trigger('change');
                    } else if ($.fn.select2) {
                        $('#userFilter').select2();
                    }
                }
            }
        });
    }

    // Load current locations for all users to show on map initially
    function loadCurrentLocations() {
        $.ajax({
            url: "{{ route('fieldstracking.location.current', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'GET',
            success: function(res) {
                if(res.success && res.data.length > 0) {
                    let bounds = new google.maps.LatLngBounds();
                    res.data.forEach(loc => {
                        let position = new google.maps.LatLng(parseFloat(loc.latitude), parseFloat(loc.longitude));
                        let userId = loc.user_id;
                        
                        let infoContent = `<b>${loc.user ? loc.user.name : 'Unknown User'}</b><br>Accuracy: ${loc.accuracy}m<br>Updated: ${new Date(loc.recorded_at).toLocaleTimeString()}`;

                        if (userMarkers[userId]) {
                            // Update existing marker position to simulate live movement
                            userMarkers[userId].marker.setPosition(position);
                            userMarkers[userId].infoWindow.setContent(infoContent);
                        } else {
                            // Create new marker
                            let infoWindow = new google.maps.InfoWindow({ content: infoContent });
                            let marker = new google.maps.Marker({
                                position: position,
                                map: map,
                                title: loc.user ? loc.user.name : 'User',
                                icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                            });

                            marker.addListener('click', () => {
                                infoWindow.open(map, marker);
                            });

                            userMarkers[userId] = { marker: marker, infoWindow: infoWindow };
                            markers.push(marker);
                        }
                        
                        bounds.extend(position);
                    });
                    
                    // Only fit bounds on the first load if we don't have many markers yet, so we don't constantly snap the admin's view
                    if (markers.length <= res.data.length && !window.hasFittedInitialBounds) {
                        map.fitBounds(bounds);
                        window.hasFittedInitialBounds = true;
                    }
                }
            }
        });
    }

    // Auto-refresh the dashboard map with live locations every 15 seconds
    setInterval(loadCurrentLocations, 15000);

    var polyline = null;
    var directionsRenderer = null;
    var directionsService = null;

    var routeRefreshInterval = null;

    // Clear existing map elements
    function clearMap() {
        markers.forEach(marker => marker.setMap(null));
        markers = [];
        userMarkers = {}; // Clear stored live markers
        if (polyline) polyline.setMap(null);
        if (directionsRenderer) directionsRenderer.setMap(null);
    }

    // Load user's route for a specific day
    $('#loadRouteBtn').click(function() {
        let userId = $('#userFilter').val();
        let date = $('#dateFilter').val();

        if (!userId) {
            toastr.error('Please select an employee first.');
            return;
        }

        // Set up real-time route refresh every 1 minute
        if (routeRefreshInterval) clearInterval(routeRefreshInterval);
        routeRefreshInterval = setInterval(() => {
            fetchRouteData(userId, date, false);
        }, 60000);

        $('#mapLoading').removeClass('d-none');
        fetchRouteData(userId, date, true);
    });

    function fetchRouteData(userId, date, fitBounds) {
        $.ajax({
            url: "{{ route('fieldstracking.location.route', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'GET',
            data: { boxuser_id: userId, date: date },
            success: function(res) {
                if (fitBounds) $('#mapLoading').addClass('d-none');
                clearMap();

                if (res.success && res.data.points.length > 0) {
                    let points = res.data.points;
                    let routeCoords = [];
                    let bounds = new google.maps.LatLngBounds();

                    points.forEach((point, index) => {
                        let position = { lat: parseFloat(point.latitude), lng: parseFloat(point.longitude) };
                        routeCoords.push(position);
                        bounds.extend(position);

                        // Add marker for Start and End points
                        if (index === 0 || index === points.length - 1) {
                            let label = index === 0 ? 'Start' : 'End';
                            let iconColor = index === 0 ? 'green-dot.png' : 'red-dot.png';
                            
                            let marker = new google.maps.Marker({
                                position: position,
                                map: map,
                                title: label,
                                icon: `http://maps.google.com/mapfiles/ms/icons/${iconColor}`
                            });

                            let infoWindow = new google.maps.InfoWindow({
                                content: `<b>${label}</b><br>${new Date(point.recorded_at).toLocaleTimeString()}`
                            });
                            
                            marker.addListener('click', () => { infoWindow.open(map, marker); });
                            markers.push(marker);
                        }
                    });

                    // Draw the raw route line connecting the exact recorded points
                    if (points.length > 1) {
                        polyline = new google.maps.Polyline({ path: routeCoords, geodesic: true, strokeColor: '#0d6efd', strokeOpacity: 1.0, strokeWeight: 4 });
                        polyline.setMap(map);
                    }

                    if (fitBounds) map.fitBounds(bounds);

                    // Update UI stats
                    $('#totalDistance').text(res.data.total_distance_km + ' km');
                    $('#lastPingTime').text(new Date(points[points.length-1].recorded_at).toLocaleTimeString());
                    $('#gpsAccuracy').text('Live Updating...'); 
                } else {
                    if (fitBounds) toastr.info('No location history found for this user on the selected date.');
                    $('#totalDistance').text('0.00 km');
                    $('#lastPingTime').text('-');
                }
            },
            error: function() {
                if (fitBounds) $('#mapLoading').addClass('d-none');
                toastr.error('Failed to load route data.');
            }
        });
    }

    // Handle Start/Stop Tracking Actions
    async function sendTrackingAction(actionStr, btn) {
        if (!navigator.geolocation) {
            toastr.error('Geolocation is not supported by this browser.');
            return;
        }

        let originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        navigator.geolocation.getCurrentPosition(
            async function(position) {
                let batteryInfo = null;
                let networkInfo = "Unknown";
                
                try {
                    if ('getBattery' in navigator) {
                        const battery = await navigator.getBattery();
                        batteryInfo = Math.round(battery.level * 100);
                    }
                } catch(e) {}
                
                try {
                    if ('connection' in navigator) {
                        networkInfo = navigator.connection.effectiveType || "Unknown";
                    }
                } catch(e) {}

                $.ajax({
                    url: "{{ route('fieldstracking.location.store', ['box_slug' => request()->route('box_slug')]) }}",
                    type: 'POST',
                    data: {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy || 10,
                        timestamp: new Date().toISOString(),
                        action: actionStr,
                        battery_level: batteryInfo,
                        network_status: 'online',
                        network_type: networkInfo !== "Unknown" ? networkInfo : null,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        btn.prop('disabled', false).html(originalHtml);
                        if (res.success) {
                            toastr.success(`Tracking ${actionStr}ed successfully!`);
                            if (actionStr === 'start') {
                                localStorage.setItem('bizzcrm_is_tracking', 'true');
                                updateTrackingUI();
                            } else if (actionStr === 'end') {
                                localStorage.setItem('bizzcrm_is_tracking', 'false');
                                updateTrackingUI();
                            }
                            
                            // Auto-load route for this user for immediate visual feedback
                            if ($('#userFilter').find(`option[value="${currentUserId}"]`).length) {
                                $('#userFilter').val(currentUserId).trigger('change');
                                $('#loadRouteBtn').click();
                            } else {
                                loadCurrentLocations();
                            }
                        } else {
                            toastr.error(res.message || `Failed to ${actionStr} tracking`);
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).html(originalHtml);
                        toastr.error(`Failed to ${actionStr} tracking. Please try again.`);
                    }
                });
            },
            function(error) {
                console.warn('Geolocation Error: ', error.message, ' Code: ', error.code);
                // Fallback to IPAPI Geolocation API
                $.ajax({
                    url: 'https://ipapi.co/json/',
                    type: 'GET',
                    success: function(data) {
                        if(data && data.latitude && data.longitude) {
                            $.ajax({
                                url: "{{ route('fieldstracking.location.store', ['box_slug' => request()->route('box_slug')]) }}",
                                type: 'POST',
                                data: {
                                    latitude: data.latitude,
                                    longitude: data.longitude,
                                    accuracy: 50,
                                    timestamp: new Date().toISOString(),
                                    action: actionStr,
                                    battery_level: null,
                                    network_status: 'online',
                                    network_type: null,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(res) {
                                    btn.prop('disabled', false).html(originalHtml);
                                    if (res.success) {
                                        toastr.success(`Tracking ${actionStr}ed successfully via fallback!`);
                                        if (actionStr === 'start') {
                                            localStorage.setItem('bizzcrm_is_tracking', 'true');
                                            updateTrackingUI();
                                        } else if (actionStr === 'end') {
                                            localStorage.setItem('bizzcrm_is_tracking', 'false');
                                            updateTrackingUI();
                                        }
                                        
                                        // Auto-load route for this user for immediate visual feedback
                                        if ($('#userFilter').find(`option[value="${currentUserId}"]`).length) {
                                            $('#userFilter').val(currentUserId).trigger('change');
                                            $('#loadRouteBtn').click();
                                        } else {
                                            loadCurrentLocations();
                                        }
                                    } else {
                                        toastr.error(res.message || `Failed to ${actionStr} tracking`);
                                    }
                                },
                                error: function() {
                                    btn.prop('disabled', false).html(originalHtml);
                                    toastr.error(`Failed to ${actionStr} tracking. Please try again.`);
                                }
                            });
                        } else {
                            btn.prop('disabled', false).html(originalHtml);
                            toastr.warning('Location access denied and fallback failed. Cannot ' + actionStr + ' tracking.');
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).html(originalHtml);
                        toastr.warning('Location access denied or unavailable. Cannot ' + actionStr + ' tracking.');
                    }
                });
            },
            { enableHighAccuracy: true, timeout: 30000, maximumAge: 30000 }
        );
    }

    $('#startTrackingBtn').click(function() { sendTrackingAction('start', $(this)); });
    $('#stopTrackingBtn').click(function() { sendTrackingAction('end', $(this)); });

    function updateTrackingUI() {
        if (localStorage.getItem('bizzcrm_is_tracking') === 'true') {
            $('#startTrackingBtn').removeClass('btn-success').addClass('btn-outline-success').prop('disabled', true);
            $('#stopTrackingBtn').removeClass('btn-outline-danger').addClass('btn-danger').prop('disabled', false);
        } else {
            $('#startTrackingBtn').removeClass('btn-outline-success').addClass('btn-success').prop('disabled', false);
            $('#stopTrackingBtn').removeClass('btn-danger').addClass('btn-outline-danger').prop('disabled', true);
        }
    }

    // Auto-ping location every 5 minutes if tracking is active
    function autoPingLocation() {
        if (localStorage.getItem('bizzcrm_is_tracking') !== 'true') return;

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) { sendSilentPing(position.coords.latitude, position.coords.longitude, position.coords.accuracy); },
                function() {
                    // Fallback ping
                    $.ajax({
                        url: 'https://ipapi.co/json/', type: 'GET',
                        success: function(data) { if(data && data.latitude) { sendSilentPing(data.latitude, data.longitude, 50); } }
                    });
                },
                { enableHighAccuracy: true, timeout: 30000, maximumAge: 30000 }
            );
        }
    }

    async function sendSilentPing(lat, lng, accuracy) {
        let batteryInfo = null;
        try { if ('getBattery' in navigator) { const battery = await navigator.getBattery(); batteryInfo = Math.round(battery.level * 100); } } catch(e) {}
        $.ajax({
            url: "{{ route('fieldstracking.location.store', ['box_slug' => request()->route('box_slug')]) }}",
            type: 'POST',
            data: {
                latitude: lat, longitude: lng, accuracy: accuracy || 50,
                timestamp: new Date().toISOString(), action: 'ping',
                battery_level: batteryInfo, network_status: navigator.onLine ? 'online' : 'offline', network_type: null,
                _token: $('meta[name="csrf-token"]').attr('content')
            }
        });
    }

    var liveUserMarker = null;
    var liveUserPolyline = null;
    var liveWatchId = null;

    function startLiveLocalTracking() {
        if (navigator.geolocation) {
            liveWatchId = navigator.geolocation.watchPosition(function(position) {
                var pos = { lat: position.coords.latitude, lng: position.coords.longitude };
                
                if (localStorage.getItem('bizzcrm_is_tracking') === 'true') {
                    if (!liveUserMarker) {
                        liveUserMarker = new google.maps.Marker({
                            position: pos, map: map, icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png', title: 'You are here'
                        });
                        map.setCenter(pos);
                    } else {
                        liveUserMarker.setPosition(pos);
                    }

                    if (!liveUserPolyline) {
                        liveUserPolyline = new google.maps.Polyline({
                            path: [pos], geodesic: true, strokeColor: '#FF0000', strokeOpacity: 1.0, strokeWeight: 4, map: map
                        });
                    } else {
                        liveUserPolyline.getPath().push(new google.maps.LatLng(pos.lat, pos.lng));
                    }
                } else {
                    if (liveUserMarker) { liveUserMarker.setMap(null); liveUserMarker = null; }
                    if (liveUserPolyline) { liveUserPolyline.setMap(null); liveUserPolyline = null; }
                }
            }, function(error) {}, { enableHighAccuracy: true, maximumAge: 10000, timeout: 30000 });
        }
    }

    updateTrackingUI();
    // 1 Minute = 60,000 milliseconds (for real-time granular routes)
    setInterval(autoPingLocation, 60000);

    // Ensure users are loaded even if map takes time
    loadUsers();

    // Dynamically load Google Maps API if not present
    if (typeof google === 'object' && typeof google.maps === 'object') {
        initMap();
    } else {
        var apiKey = 'AIzaSyCix326VJYzGXyMzF7_yTD_vbn5DBMHZL4';
        var script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + apiKey + '&callback=initMapGoogle';
        script.async = true;
        script.defer = true;
        window.initMapGoogle = function() {
            initMap();
        };
        document.head.appendChild(script);
    }
});
</script>
@endsection
