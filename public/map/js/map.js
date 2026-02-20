document.addEventListener("DOMContentLoaded", function () {

    const defaultCenter = [-7.3,107.9];
    const defaultZoom = 10;

    const map = L.map("map").setView(defaultCenter, defaultZoom);

    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom:18,
    }).addTo(map);

    let selectedLayer = null;
    let geojsonLayer;

    const styleDefault = {
        color:"#0f766e",
        weight:1,
        fillColor:"#14b8a6",
        fillOpacity:0.6,
    };

    const styleHover = {
        color:"#0f766e",
        weight:2,
        fillColor:"#22d3ee",
        fillOpacity:0.8,
    };

    const styleSelected = {
        color:"#111827",
        weight:2,
        fillColor:"#f59e0b",
        fillOpacity:0.9,
    };

    fetch("/map/kecamatan.geojson")
        .then(res=>res.json())
        .then(data=>{

            geojsonLayer = L.geoJSON(data,{
                style:styleDefault,

                onEachFeature:function(feature,layer){

                    const kode = feature.properties.nm_kecamatan; // 🔥 FINAL SAFE
                    const nama = feature.properties.nm_kecamatan;

                    layer.bindTooltip(nama);

                    layer.on("mouseover",()=>{
                        if(selectedLayer!==layer) layer.setStyle(styleHover);
                    });

                    layer.on("mouseout",()=>{
                        if(selectedLayer!==layer) geojsonLayer.resetStyle(layer);
                    });

                    layer.on("click",function(){

                        if(selectedLayer){
                            geojsonLayer.resetStyle(selectedLayer);
                        }

                        selectedLayer = layer;
                        layer.setStyle(styleSelected);

                        map.fitBounds(layer.getBounds(),{
                            padding:[40,40],
                            maxZoom:13,
                        });

                        document.getElementById("infoPanel").innerHTML =
                            `<h3>${nama}</h3><p>Memuat potensi...</p>`;

                        fetch("/admin/map-data?kode="+encodeURIComponent(kode))
                            .then(res=>res.json())
                            .then(data=>{

                                let html = `<h3>${nama}</h3><hr>`;

                                /*
                                |--------------------------------------------------------------------------
                                | HEADER WILAYAH
                                |--------------------------------------------------------------------------
                                */
                                if(data.wilayah){
                                    html += `
                                        <p><b>UPT Wilayah :</b> ${data.wilayah.upt_wilayah ?? '-'}</p>
                                        <p>Unit Usaha : ${data.wilayah.total_objek ?? 0}</p>
                                        <p>Pemilik : ${data.wilayah.total_pemilik ?? 0}</p>
                                        <hr>
                                    `;
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | KOMODITAS
                                |--------------------------------------------------------------------------
                                */
                                html += `<b>Potensi Komoditas</b>`;

                                if(data.komoditas && data.komoditas.length){
                                    data.komoditas.forEach(row=>{
                                        html += `
                                            <div style="margin-left:10px;">
                                                ${row.nama ?? '-'} :
                                                <b>${parseFloat(row.total ?? 0)}</b>
                                                ${row.satuan ?? ''}
                                            </div>
                                        `;
                                    });
                                }else{
                                    html += `<p>- Tidak ada data</p>`;
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | UPT TEMATIK
                                |--------------------------------------------------------------------------
                                */
                                if(data.tematik && data.tematik.length){
                                    html += `<hr><b>UPT Tematik</b>`;

                                    data.tematik.forEach(t=>{
                                        html += `
                                            <div style="margin-left:10px;">
                                                ${t.upt} :
                                                ${t.jumlah_objek} unit usaha
                                            </div>
                                        `;
                                    });
                                }

                                document.getElementById("infoPanel").innerHTML = html;
                            });

                    });
                }
            }).addTo(map);
        });

    document.getElementById("resetMap").addEventListener("click",function(){

        map.setView(defaultCenter,defaultZoom);

        if(selectedLayer){
            geojsonLayer.resetStyle(selectedLayer);
            selectedLayer=null;
        }

        document.getElementById("infoPanel").innerHTML =
            "<h3>Klik Kecamatan</h3>";
    });

});