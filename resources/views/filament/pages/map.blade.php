<x-filament-panels::page>

<div style="position:relative;">

    <div id="map" style="height:600px;border-radius:12px;"></div>

    <div id="infoPanel"
        style="
            position:absolute;
            top:20px;
            right:20px;
            width:320px;
            background:#0f172a;
            color:white;
            padding:20px;
            border-radius:12px;
            z-index:999;
            box-shadow:0 10px 30px rgba(0,0,0,0.4);
        ">
        <h3>Klik Kecamatan</h3>
    </div>

    <button id="resetMap"
        style="
            position:absolute;
            bottom:20px;
            left:20px;
            z-index:999;
            background:#020617;
            color:white;
            padding:10px 14px;
            border-radius:8px;
        ">
        Reset Map
    </button>

</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="/map/js/map.js"></script>

</x-filament-panels::page>
