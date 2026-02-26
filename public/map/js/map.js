document.addEventListener("DOMContentLoaded", function () {

    const defaultCenter = [-7.3,107.9];
    const defaultZoom = 10;

    const map = L.map("map").setView(defaultCenter, defaultZoom);

    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom:18,
    }).addTo(map);

    let selectedLayer = null;
    let selectedKode  = null;
    let selectedNama  = null;
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

    /*
    |--------------------------------------------------------------------------
    | INIT FILTER TAHUN (FILAMENT SAFE)
    |--------------------------------------------------------------------------
    */
    function initFilterTahun(){

        const select = document.querySelector("#filterTahun");
        if(!select) return;
        if(select.dataset.loaded === "1") return;

        fetch(window.location.origin + "/admin/map-tahun")
            .then(res => res.json())
            .then(list => {

                list.forEach(thn => {
                    const opt = document.createElement("option");
                    opt.value = thn;
                    opt.textContent = thn;
                    select.appendChild(opt);
                });

                select.dataset.loaded = "1";
            });
    }

    setInterval(initFilterTahun,500);

    /*
    |--------------------------------------------------------------------------
    | FUNCTION LOAD DATA MAP
    |--------------------------------------------------------------------------
    */
    function loadData(kode, nama){

        selectedKode = kode;
        selectedNama = nama;

        document.getElementById("infoPanel").innerHTML =
            `<h3>${nama}</h3><p>Memuat potensi...</p>`;

        const select = document.querySelector("#filterTahun");
        const tahun  = select ? select.value : null;

        let url = "/admin/map-data?kode="+encodeURIComponent(kode);

        if(tahun){
            url += "&tahun="+tahun;
        }

        fetch(url)
            .then(res=>res.json())
            .then(data=>{


                /*
                |--------------------------------------------------------------------------
                | FOTO BIDANG (VERSI MINIMAL - TANPA UBAH LOGIKA DATA)
                |--------------------------------------------------------------------------
                */

                /*
                |--------------------------------------------------------------------------
                | DETEKSI FOTO BERDASARKAN UPT TEMATIK (VERSI STABIL STEROID)
                |--------------------------------------------------------------------------
                */

                /*
|--------------------------------------------------------------------------
| DETEKSI FOTO BERDASARKAN STRUKTUR UPT (VERSI FINAL STEROID)
|--------------------------------------------------------------------------
*/

                /*
|--------------------------------------------------------------------------
| FOTO BERDASARKAN UPT WILAYAH (VERSI STABIL STEROID)
|--------------------------------------------------------------------------
*/

                /*
|--------------------------------------------------------------------------
| FOTO BERDASARKAN JENIS UPT (VERSI PALING STABIL)
|--------------------------------------------------------------------------
*/

                let bidangFoto = "/map/bidang/peternakan-bg.jpg"; 
                // 🔥 default semua wilayah pakai visual netral peternakan

                // kalau ada tematik → override
                if(data.tematik && data.tematik.length){

                    const namaTematik = (data.tematik[0].upt ?? "").toLowerCase();

                    if(namaTematik.includes("tangkap")){
                        bidangFoto = "/map/bidang/perikanan-tangkap-bg.jpg";
                    }
                    else if(namaTematik.includes("budidaya")){
                        bidangFoto = "/map/bidang/perikanan-budidaya-bg.jpg";
                    }
                    else if(namaTematik.includes("kesehatan")){
                        bidangFoto = "/map/bidang/kesehatan-hewan-bg.jpg";
                    }
                }
                /*
                |--------------------------------------------------------------------------
                | FOTO BERDASARKAN KOMODITAS DOMINAN (LOCAL SAFE)
                |--------------------------------------------------------------------------
                */

                let fotoKomoditas = "/map/komoditas/default.jpg";

                if(data.komoditas && data.komoditas.length){

                    const dominan = [...data.komoditas]
                        .sort((a,b)=>parseFloat(b.total) - parseFloat(a.total))[0];

                    if(dominan && dominan.nama){

                        const slug = dominan.nama
                            .toLowerCase()
                            .replace(/\s+/g,'-');

                        fotoKomoditas = "/map/komoditas/"+slug+".jpg";
                    }
                }

                let html = `<h3>${nama}</h3>`;

                if(bidangFoto){
                    html += `
                        <img 
                            src="${bidangFoto}"
                            style="width:100%;height:140px;object-fit:cover;border-radius:10px;margin-bottom:10px;"
                        >
                    `;
                }

                html += `<hr>`;

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
    }

    /*
    |--------------------------------------------------------------------------
    | LOAD GEOJSON
    |--------------------------------------------------------------------------
    */
    fetch("/map/kecamatan.geojson")
        .then(res=>res.json())
        .then(data=>{

            geojsonLayer = L.geoJSON(data,{
                style:styleDefault,

                onEachFeature:function(feature,layer){

                    const kode = feature.properties.nm_kecamatan;
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

                        loadData(kode,nama);
                    });
                }
            }).addTo(map);
        });

    /*
    |--------------------------------------------------------------------------
    | AUTO REFRESH SAAT FILTER BERUBAH
    |--------------------------------------------------------------------------
    */
    document.addEventListener("change",function(e){

        if(e.target && e.target.id === "filterTahun"){
            if(selectedKode){
                loadData(selectedKode,selectedNama);
            }
        }

    });

    /*
    |--------------------------------------------------------------------------
    | RESET MAP
    |--------------------------------------------------------------------------
    */
    document.getElementById("resetMap").addEventListener("click",function(){

        map.setView(defaultCenter,defaultZoom);

        if(selectedLayer){
            geojsonLayer.resetStyle(selectedLayer);
            selectedLayer=null;
        }

        selectedKode = null;
        selectedNama = null;

        document.getElementById("infoPanel").innerHTML =
            "<h3>Klik Kecamatan</h3>";
    });

});