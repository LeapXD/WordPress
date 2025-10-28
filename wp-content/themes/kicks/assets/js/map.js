/* Leap google map pins plugin */
function leapMap(mapId, options) {
  const $ = jQuery;
  options = $.extend({
    styles: [],
    locations: [],
    lat: null,
    lng: null,
    zoom: 0,
  }, options);

  return $.extend({
    mapId,
    markers: [],
    positions: [],
    center: null,
    addCentrePin() {
      if (this.centre_pin) {
        this.addMarkerWithTimeout(this.centre_pin, 0, 14, false);
        const pinLatLng = new google.maps.LatLng(this.centre_pin.location);
        this.positions.push(pinLatLng);
      }
    },

    clearAllMarkers() {
      this.markers.forEach((marker) => {
        marker.setMap(null);
      });
      this.positions = [];
      this.markers = [];
    },

    addMarkers(term = null) {
      if (this.locations.length) {
        let { locations } = this;
        if (term) {
          locations = locations.filter((location) => location.category.term_id * 1 === term * 1);
        }
        let timeout = 0;
        locations.forEach((location) => {
          this.addMarkerWithTimeout(location, timeout);
          timeout += 5;
        });
      }
    },

    addMarkerWithTimeout(location, timeout, scale = 7, centerPin = true) {
      const pinLatLng = new google.maps.LatLng(location.location);
      if (centerPin) {
        this.positions.push(pinLatLng);
      }
      setTimeout(() => {
        let icon = null;
        if (typeof location.styles !== 'undefined') {
          if (location.styles.icon) {
            icon = {
              url: location.styles.icon,
              size: new google.maps.Size(location.styles.width, location.styles.height),
              scaledSize: new google.maps.Size(location.styles.width, location.styles.height),
              origin: new google.maps.Point(0, 0),
              anchor: new google.maps.Point(location.styles.width / 2, location.styles.height),
            };
          } else if (location.styles.colour) {
            icon = {
              path: google.maps.SymbolPath.CIRCLE,
              fillColor: location.styles.colour,
              fillOpacity: 0.9,
              strokeColor: location.styles.colour,
              scale,
            };
          }
        }
        const infoWindow = new google.maps.InfoWindow({
          content: location.content_string,
        });
        infoWindow.addListener('closeclick', this.centerMap.bind(this));
        const marker = this.addMarker(location, pinLatLng, icon, infoWindow);
        if (centerPin) {
          this.markers.push(marker);
        }
      }, timeout);
    },

    addMarker(location, latLng, icon, infoWindow) {
      const marker = new google.maps.Marker({
        title: location.display_name,
        ID: location.ID,
        position: latLng,
        map: this.map,
        icon,
        animation: google.maps.Animation.DROP,
      });
      marker.infoWindow = infoWindow;
      google.maps.event.addListener(marker, 'click', ((marker) => () => {
        this.closeInfoWindows();
        infoWindow.open(this.map, marker);
      })(marker));
      return marker;
    },

    openInfoWindow(post) {
      this.markers.forEach((marker) => {
        marker.infoWindow.close();
        if (marker.ID * 1 === post * 1) {
          marker.infoWindow.open(this.map, marker);
        }
      });
    },

    closeInfoWindows() {
      this.markers.forEach((marker) => {
        marker.infoWindow.close();
      });
    },

    centerMap() {
      const bounds = new google.maps.LatLngBounds();
      this.positions.forEach((position) => {
        bounds.extend(position);
      });
      this.map.fitBounds(bounds);
    },

    saveCenter() {
      this.center = this.map.getCenter();
    },

    doMap() {
      const mapCanvas = $(`[data-map="${mapId}"]`).get(0);
      if (typeof mapCanvas === 'undefined') {
        return;
      }
      this.map = new google.maps.Map(mapCanvas, {
        styles: this.map_styles,
        mapTypeControl: false,
        streetViewControl: false,
        maxZoom: this.max_zoom,
        minZoom: this.min_zoom,
      });
      this.saveCenter();
      this.clearAllMarkers();
      this.addMarkers();
      this.addCentrePin();
      this.centerMap();
      google.maps.event.addDomListener(window, 'resize', this.centerMap.bind(this));
    },
  }, options);
}

const initMaps = (LeapMap) => {
  const $ = jQuery;
  const { LeapMapPins } = window;
  const $mapCanvas = $('[data-map]');
  if (!$mapCanvas.length) return;
  const LeapMaps = [];

  $mapCanvas.each((i, map) => {
    const $map = $(map);
    LeapMaps.push(new LeapMap($map.data('map'), $map.data('options')));
  });
  if (window.google && window.google.maps) {
    LeapMaps.forEach((map) => map.doMap());
  } else {
    const script = document.createElement('script');
    script.onload = () => {
      LeapMaps.forEach((map) => map.doMap());
    };
    script.src = `https://maps.googleapis.com/maps/api/js?key=${LeapMapPins.google_maps_api_key}`;
    document.getElementsByTagName('head')[0].appendChild(script);
  }

  $('[data-mapid]').on('click change', (e) => {
    e.preventDefault();
    const $this = $(e.currentTarget);
    if ($this.data('select') && e.type === 'click') { return; }
    const mapId = $this.data('mapid');
    const term = $this.data('term') ?? $this.val();
    if (term === -1) { return; }
    const leapMap = LeapMaps.find((map) => map.mapId === mapId);
    leapMap.term = term ?? '';
    leapMap.clearAllMarkers();
    leapMap.addMarkers(term, true);
    if (!term) {
      leapMap.addCentrePin();
    }
    leapMap.centerMap(term);
  });
};

export {
  leapMap,
  initMaps,
};
