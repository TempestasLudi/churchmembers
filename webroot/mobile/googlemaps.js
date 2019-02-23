var centerLat = 52.1610660;
var centerLng = 4.4715440;
var centerZoom = 14;
var jsonmarkers;
var mapinitialized = false;

function initializeMap(cLat, cLng) {

  if (cLat!=null && cLng!=null){
    centerLat = cLat;
    centerLng = cLng;
    centerZoom = 18;
  }

  $("#GoogleMap").gmap3(
  {
    action: 'init',
    options:{
      center:[centerLat, centerLng],
      zoom:centerZoom,
      minZoom: 2,
      maxZoom: 18,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      mapTypeControl: true,
      mapTypeControlOptions: {
        style: google.maps.MapTypeControlStyle.DEFAULT
      },
      navigationControl: true,
      scrollwheel: true,
      streetViewControl: true
    },
    callback: getMarkers(),
    events:{
      click: function(){
        $(this).gmap3({
          action:'clear',
          name:'overlay'
        });
      }
    }
  })

  mapinitialized = true;
}

function getMarkers(){
  $.getJSON("../model/classes/ProcessRequest.php",
    "action=getdata&type=markers",
    function(jsonmarkers) {

      $("#GoogleMap").gmap3(
      {
        action: 'addMarkers',
        radius:35,
        markers: jsonmarkers,
        clusters:{
          // This style will be used for clusters with more than 0 markers
          0: {
            content: '<div class="cluster cluster-1">CLUSTER_COUNT</div>',
            width: 53,
            height: 52
          },
          // This style will be used for clusters with more than 15 markers
          15: {
            content: '<div class="cluster cluster-2">CLUSTER_COUNT</div>',
            width: 56,
            height: 55
          },
          // This style will be used for clusters with more than 30 markers
          30: {
            content: '<div class="cluster cluster-3">CLUSTER_COUNT</div>',
            width: 66,
            height: 65
          }
        },
        cluster:{
          events: {
            mouseover: function(marker, event, data){
              $("#markersdata").html(createInfoWindow(data, true, ''));
            },
            click:function(cluster, event, data) {
              cluster.map.panTo(data.latLng);
              cluster.map.setZoom(cluster.map.getZoom()+2);
            }
          }
        },
        marker: {
          events:{
            click: function (marker, event, data){
              $(this).gmap3({
                action:'panTo',
                args:[marker.position]
              });
            },
            mouseover: function(marker, event, data){
              $("#markersdata").html(createInfoWindow(data, false, ''));
            }
          }
        }
      })
    });
}

function createInfoWindow(data, cluster, infowindow){
  if (cluster == true){
    $.each(data.markers,function(index, marker){
      infowindow = createInfoWindow(marker.data, false, infowindow);
    })
  } else {

      infowindow += '<table border="0" cellspacing="0" cellpadding="0" width="90%" class="ui-widget-content addressbox">';
      infowindow += '<tbody>';
      var photo = "";
      var membertext = "";
      var curmember = "";
      var count = 0;
      var address = data.address;
      $.each(address.members,function(index, member){
        curmember = member
        photo = member.photo
        membertext += '<tr onclick="destroyMap();getAddress('+address.id+','+member.id+')"><td  width="70">'+photo+'</td><td><h2>' + member.name + '</h2></td></tr>';
        count++;
      });

      if (count == 1){
        infowindow += '<tr onclick="destroyMap();getAddress('+address.id+','+curmember.id+')"><td  width="70">'+photo+'</td><td ><span class="address">' + curmember.name + '</span>';
        infowindow += '<br/><i>' + address.street +' ' + address.number + '<br/>' + address.zip + ' ' + address.city + '<br/>' + address.phone+'</i></td></tr>';
      } else {
        infowindow += '<tr onclick="destroyMap();getAddress('+address.id+')"><td   width="70"><img src="includes/phpThumb/phpThumb.php?w=70&h=70&far=1&zc=1&src=../../css/images/icons/home.png" height="70"/></td><td ><span class="address">' + address.name + '</span>';
        infowindow += '<br/><i>' + address.street +' ' + address.number + '<br/>' + address.zip + ' ' + address.city + '<br/>' + address.phone+'</i></td></tr>';
        infowindow += membertext;
      }
      infowindow += "</tbody></table>";

  }

  return infowindow;

}

function destroyMap(){
  $("#GoogleMap").gmap3({action: 'destroy'})
  mapinitialized = false;
}