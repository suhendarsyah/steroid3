document.addEventListener("DOMContentLoaded", function () {
    const defaultCenter = [-7.3, 107.9];
    const defaultZoom = 10;

    const map = L.map("map").setView(defaultCenter, defaultZoom);

    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 18,
    }).addTo(map);

    let selectedLayer = null;
    let geojsonLayer;

    const styleDefault = {
        color: "#0f766e",
        weight: 1,
        fillColor: "#14b8a6",
        fillOpacity: 0.6,
    };

    const styleHover = {
        color: "#0f766e",
        weight: 2,
        fillColor: "#22d3ee",
        fillOpacity: 0.8,
    };

    const styleSelected = {
        color: "#111827",
        weight: 2,
        fillColor: "#f59e0b",
        fillOpacity: 0.9,
    };

    fetch("/map/kecamatan.geojson")
        .then((res) => res.json())
        .then((data) => {
            geojsonLayer = L.geoJSON(data, {
                style: styleDefault,

                onEachFeature: function (feature, layer) {
                    const nama = feature.properties.nm_kecamatan;
                    const kode = feature.properties.kd_kecamatan;

                    layer.bindTooltip(nama);

                    layer.on("mouseover", () => {
                        if (selectedLayer !== layer) layer.setStyle(styleHover);
                    });

                    layer.on("mouseout", () => {
                        if (selectedLayer !== layer)
                            geojsonLayer.resetStyle(layer);
                    });

                    layer.on("click", function () {
                        if (selectedLayer) {
                            geojsonLayer.resetStyle(selectedLayer);
                        }

                        selectedLayer = layer;
                        layer.setStyle(styleSelected);

                        map.fitBounds(layer.getBounds(), {
                            padding: [40, 40],
                            maxZoom: 13,
                        });

                        document.getElementById("infoPanel").innerHTML =
                            `<h3>${nama}</h3><p>Memuat potensi wilayah...</p>`;

                        fetch("/admin/map-data?kode=" + kode)
                            .then((res) => res.json())
                            .then((rows) => {
                                let html = `<h3>${nama}</h3><hr>`;

                                if (!rows.length) {
                                    html += "<p>Tidak ada potensi tercatat</p>";
                                } else {
                                    let uptWilayah = "";
                                    let uptTematik = {};

                                    rows.forEach((row) => {
                                        // simpan UPT wilayah
                                        if (row.jenis_upt === "wilayah") {
                                            uptWilayah = row.upt;
                                        }

                                        // kelompokkan tematik
                                        if (row.jenis_upt === "tematis") {
                                            if (!uptTematik[row.upt]) {
                                                uptTematik[row.upt] = [];
                                            }
                                            uptTematik[row.upt].push(row);
                                        }
                                    });

                                    // HEADER WILAYAH
                                    if (uptWilayah) {
                                        html += `<p><b>UPT Wilayah :</b> ${uptWilayah}</p>`;
                                    }

                                    html += `<hr><b>Potensi Wilayah</b>`;

                                    // LOOP TEMATIK
                                    Object.keys(uptTematik).forEach((upt) => {
                                        html += `<div style="margin-top:10px;">
                                            <b>${upt}</b>
                                        </div>`;

                                        uptTematik[upt].forEach((row) => {
                                            html += `
                                                <div style="margin-left:10px;margin-bottom:8px;">
                                                    ${row.komoditas ?? "-"} :
                                                    <b>${row.total ?? 0}</b>
                                                    ${row.satuan ?? ""}
                                                </div>
                                            `;
                                        });
                                    });
                                }

                                document.getElementById("infoPanel").innerHTML =
                                    html;
                            });
                    });
                },
            }).addTo(map);
        });

    document.getElementById("resetMap").addEventListener("click", function () {
        map.setView(defaultCenter, defaultZoom);

        if (selectedLayer) {
            geojsonLayer.resetStyle(selectedLayer);
            selectedLayer = null;
        }

        document.getElementById("infoPanel").innerHTML =
            "<h3>Klik Kecamatan</h3>";
    });
});
