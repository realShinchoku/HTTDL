$(function () {
  var format = "image/png";
  var map;
  var mapLat = 21.006423;
  var mapLng = 105.841394;
  var mapDefaultZoom = 12;
  initialize_map();
  function initialize_map() {
    layerBG = new ol.layer.Tile({
      className : 'osm-layer',
      source: new ol.source.OSM({}),
    });
    var layer_bg = new ol.layer.Image({
      className : 'bg-layer',
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
      className : 'point-layer',
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
      layers: [layerBG,layer_bg,layer_ic],
      view: viewMap,
    });
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