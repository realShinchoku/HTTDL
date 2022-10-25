$(function () {
  var format = "image/png";
  var map;
  var mapLat = 21.006423;
  var mapLng = 105.841394;
  var mapDefaultZoom = 15;
  initialize_map();
  function initialize_map() {
    layerBG = new ol.layer.Tile({
      source: new ol.source.OSM({}),
    });
    var layer_ic = new ol.layer.Image({
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
      layers: [layerBG,layer_ic],
      view: viewMap,
    });

    var styles = {
      MultiPolygon: new ol.style.Style({
        stroke: new ol.style.Stroke({
          color: "yellow",
          width: 2,
        }),
      }),
    };
    var styleFunction = function (feature) {
      return styles[feature.getGeometry().getType()];
    };
    var vectorLayer = new ol.layer.Vector({
      //source: vectorSource,
      style: styleFunction,
    });
    map.addLayer(vectorLayer);
  }
});
