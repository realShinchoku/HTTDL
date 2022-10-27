$(function () {
  var format = "image/png";
  var map;
  var mapLat = 21.006423;
  var mapLng = 105.841394;
  var mapDefaultZoom = 12;
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        mapLat = pos.coords.latitude;
        mapLng = pos.coords.longitude;
        initialize_map(true);
      },
      () => {
        initialize_map(false);
      }
    );
  }

  function initialize_map(isPos) {
    layerBG = new ol.layer.Tile({
      className: "osm-layer",
      source: new ol.source.OSM({}),
    });
    var layer_bg = new ol.layer.Image({
      className: "bg-layer",
      source: new ol.source.ImageWMS({
        ratio: 1,
        url: "http://localhost:8080/geoserver/internetcafe/wms",
        params: {
          FORMAT: format,
          VERSION: "1.1.1",
          STYLES: "",
          LAYERS: "internetcafe:bg",
        },
      }),
    });
    var layer_ic = new ol.layer.Image({
      className: "point-layer",
      source: new ol.source.ImageWMS({
        ratio: 1,
        url: "http://localhost:8080/geoserver/internetcafe/wms",
        params: {
          FORMAT: format,
          VERSION: "1.1.1",
          STYLES: "",
          LAYERS: "internetcafe:loc",
        },
      }),
    });
    var viewMap = new ol.View({
      center: ol.proj.fromLonLat([mapLng, mapLat]),
      zoom: mapDefaultZoom,
    });
    map = new ol.Map({
      target: "map",
      layers: [layerBG, layer_bg, layer_ic],
      view: viewMap,
    });

    if (isPos) {
      const geolocation = new ol.Geolocation({
        tracking: true,
        trackingOptions: {
          enableHighAccuracy: true,
        },
        projection: viewMap.getProjection(),
      });
      geolocation.on("error", function (error) {
        alert("Vui lòng gps");
      });
      const accuracyFeature = new ol.Feature({
        geometry:geolocation.getAccuracyGeometry()
      });
      geolocation.on("change:accuracyGeometry", function () {
        accuracyFeature.setGeometry(geolocation.getAccuracyGeometry());
      });

      const positionFeature = new ol.Feature();
      positionFeature.setStyle(
        new ol.style.Style({
          image: new ol.style.Circle({
            radius: 5,
            fill: new ol.style.Fill({
              color: "#3399CC",
            }),
            stroke: new ol.style.Stroke({
              color: "#fff",
              width: 2,
            }),
          }),
        })
      );

      geolocation.on("change:position", function () {
        const coordinates = geolocation.getPosition();
        positionFeature.setGeometry(
          coordinates ? new ol.geom.Point(coordinates) : null
        );
      });
      new ol.layer.Vector({
        map: map,
        source: new ol.source.Vector({
          features: [accuracyFeature, positionFeature],
        }),
      });
    }
    map.render();
  }

  /* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
});

function filterFunction() {
  var input, filter, ul, li, a, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  div = document.getElementById("myDropdown");
  a = div.getElementsByTagName("a");
  for (i = 0; i < a.length; i++) {
    txtValue = a[i].textContent || a[i].innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      a[i].style.display = "";
    } else {
      a[i].style.display = "none";
    }
  }
}

function toggleDropdown() {
  document.getElementById("myDropdown").classList.toggle("show");
}
