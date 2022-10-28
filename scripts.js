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
      const positionFeature = new ol.Feature({
        geometry: new ol.geom.Point(ol.proj.transform([mapLng, mapLat], 'EPSG:4326',     
        'EPSG:3857')),
      });
      positionFeature.setStyle(
        new ol.style.Style({
          image: new ol.style.Circle({
            radius: 6,
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

      new ol.layer.Vector({
        map: map,
        source: new ol.source.Vector({
          features: [positionFeature],
        }),
      });
    }

    map.on('click', function (evt) {
      var zoom = map.getView().getZoom();
      var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
      var lon = lonlat[0];
      var lat = lonlat[1];
      var myPoint = 'POINT(' + lon + ' ' + lat + ')';
      var item = {
        name: 'abc',
        geom : myPoint
      }
      $.ajax({
          type: "POST",
          url: "api.php",
          //dataType: 'json',
          data: {function: 'add', item: item, distance: zoom},
          success : function (result, status, error) {
              console.log(result);
              layer_ic.getSource().changed();
          },
          error: function (req, status, error) {
              alert(req + " " + status + " " + error);
          }
      });
  });
  }
  
  /* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
});

function createJsonObj(result) {
  return {
    type: "FeatureCollection",
    crs: {
      type: "name",
      properties: {
        name: "EPSG:4326",
      },
    },
    features: [
      {
        type: "Feature",
        geometry: result,
      },
    ],
  };
}

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
