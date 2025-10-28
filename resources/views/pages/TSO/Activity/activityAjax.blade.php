<style>
    .info-window {
    width: 300px;
    overflow: hidden;
    font-size: 14px;
    line-height: 2;
    margin: 10px;
}
</style>
<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"></h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">


                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Date Time</th>
                            </tr>
                            </thead>
                            <tbody>
                                @php
                                 $location = [];
                                @endphp
                                @foreach ($tsoActivities as $key => $tsoActivity)
                                @php
                                    $activity_type = $tsoActivity->activity_type ?? '--';
                                    $name = $tsoActivity->location->company_name ?? $tsoActivity->location->shop->company_name ?? $tsoActivity->location->invoice_no ?? '--';
                                    $shop_name =  $tsoActivity->location->company_name ?? $tsoActivity->location->shop->company_name ?? '--';
                                    $distributor_name = htmlspecialchars($tsoActivity->location->distributor->distributor_name ?? $tsoActivity->location->shop->distributor->distributor_name ?? '--' , ENT_QUOTES);
                                    $tso_name = $tsoActivity->location->tso->name ?? $tsoActivity->location->shop->tso->name ?? '--';
                                    $location []= [
                                        'lat'=>$tsoActivity->latitude,
                                        'long'=>$tsoActivity->longitude,
                                        'title'=>$tsoActivity->location_title ,
                                        'heading' => $activity_type ,
                                        'name' => $name ,
                                        'shop_name' => $shop_name ,
                                        'distributor_name' => $distributor_name ,
                                        'tso_name' => $tso_name ,
                                        'date' => $tsoActivity->created_at
                                    ];
                                    // dump($location);
                                @endphp
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$tsoActivity->location_title}}</td>
                                        <td>{{$tsoActivity->table_name}} </td>
                                        <td>{{$activity_type ? '('.$activity_type.')':''}} {{$name}}</td>
                                        <td>{{date('d-m-Y h:i:s', strtotime($tsoActivity->created_at))}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    // dump($location);
    $myJson = json_encode($location);
    ?>
<script>
    $(document).ready(function(){
        initMap();
    });
var data  = '<?php echo $myJson ; ?>';

function initMap() {
    var loopdata = JSON.parse(data);
    console.log(loopdata );

    var location1 = {
        lat: parseFloat(loopdata[0].lat),
        lng: parseFloat(loopdata[0].long)
    };
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: location1
    });

    var locations = []; // an array to store all the locations
    var geocoder = new google.maps.Geocoder();
    var image = "{{ asset('/public/assets/images/icons/location.png') }}";
    console.log(image);

    $.each(loopdata, function(key, value) {
        var location = {
            lat: parseFloat(value.lat),
            lng: parseFloat(value.long)
        };
        var marker = new google.maps.Marker({
            position: location,
            map: map,
            // icon: {
            //     // url: image,
            //     // scaledSize: new google.maps.Size(30, 30),
            //     path: google.maps.SymbolPath.PARKING,
            //     fillColor: '#dfe234', // Change to your desired color
            //     strokeColor: '#dfe234',
            //     fillOpacity: 1.0,
            //     strokeWeight: 1,
            //     scale: 10, // Adjust the size of the marker
            // },
        });

        locations.push(location); // add the location to the array

        geocoder.geocode({
            location: location
        }, function(results, status) {

              // Format the date
            var date = new Date(value.date); // Assuming 'date' is in a format recognized by Date
            var options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true }; // Specify the desired date format
            var formattedDate = date.toLocaleDateString('en-US', options); // Change 'en-US' to your preferred locale

              // Create content for the InfoWindow
            var contentString = '<div class="info-window">' +
            '<h3>' + value.heading + '</h3>' + // Title or Heading
            '<p><strong>Date : </strong>' + formattedDate + '</p>' + // Additional details
            '<p><strong>Shop Name : </strong>' + value.shop_name + '</p>' + // Additional details
            '<p><strong>Distributor Name : </strong>' + value.distributor_name + '</p>' + // Additional details
            '<p><strong>TSO Name : </strong>' + value.tso_name + '</p>' + // Additional details
            '<p><strong>Location : </strong>' + value.title + '</p>' + // Additional details
            '</div>';

            // create an info window with the location's address
            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });
            // attach the info window to the marker
            marker.addListener("click", function() {
                infowindow.open(map, marker);
            });

        });
    });

    var lineSymbol = {
        path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW
    };

    var line = new google.maps.Polyline({
        path: locations,
        geodesic: true,
        strokeColor: '#FF0000',
        strokeOpacity: 1.0,
        strokeWeight: 2,
        icons: [{
            icon: lineSymbol,
            offset: '100%'
        }],
        map: map
    });

    animateMarker(line, map);
}

function animateMarker(line, map) {
    var count = 0;
    var interval = setInterval(function() {
        count = (count + 1) % 200;

        var icons = line.get('icons');
        icons[0].offset = (count / 2) + '%';
        line.set('icons', icons);

        if (count == 0) {
            clearInterval(interval);
        }
    }, 200);
}

// function initMap() {
// var loopdata = JSON.parse(data);

// var location1 = {lat:parseFloat(loopdata[0].lat),lng:parseFloat(loopdata[0].long)};
//         var map = new google.maps.Map(document.getElementById('map'), {
//             zoom: 15,
//             center: location1
//         });

//         var locations = []; // an array to store all the locations
//         var geocoder = new google.maps.Geocoder();

// $.each(loopdata,function(key,value){
//     var location = {lat: parseFloat(value.lat), lng: parseFloat(value.long)};
//         var marker = new google.maps.Marker({
//             position: location,
//             map: map
//         });

//         locations.push(location); // add the location to the array

//          geocoder.geocode({location: location}, function(results, status) {

//             // create an info window with the location's address
//             var infowindow = new google.maps.InfoWindow({
//                 content: value.title
//             });
//             // attach the info window to the marker
//             marker.addListener("click", function() {
//                 infowindow.open(map, marker);
//             });

//     });
// });


// for (var i = 0; i < locations.length - 1; i++) {
//         var line = new google.maps.Polyline({
//             path: [locations[i], locations[i+1]],
//             geodesic: true,
//             strokeColor: '#FF0000',
//             strokeOpacity: 1.0,
//             strokeWeight: 2
//         });

//         line.setMap(map); // show the polyline on the map
//     }
// }

</script>
