<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Next.Room</title>
    <link rel="stylesheet" type="text/css" href="styless.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #mapcontainer {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 50vh;
            margin-bottom: 20px;
        }

        #mapButton {
            display: block;
            margin: 10px auto;
            font-size: 16px;
            cursor: pointer;
        }

        #map {
            height: 400px;
            width: 100%;
            max-width: 90%;
            margin: 20px auto;
            border-radius: 20px;
            border: 1px solid #b7b3b3;
            border-width: 4px;
        }
    </style>
</head>
<body>

    <header>
        <h1>Welcome to Next.Room</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="search.php">Search</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </nav>
    </header>

    <div id="mapcontainer">
        <button id="mapButton">Show Map</button>
        <div id="map"></div>
    </div>

    <div class="container">
        <form id="searchForm" action="searches.php" method="POST">
            <label for="location">Location:</label>
            <select id="location" name="location" required>
                <option value="">Select Location</option>
            </select>

            <label for="subLocation">Sub-Location:</label>
            <select id="subLocation" name="subLocation" disabled>
                <option value="">Select Sub-Location</option>
            </select>

            <label for="check_in">Check-in Date:</label>
            <input type="date" id="check_in" name="check_in" required>

            <label for="check_out">Check-out Date:</label>
            <input type="date" id="check_out" name="check_out" required>

            <input type="text" id="search" placeholder="Search: hotel, room, resort" name="search" required>
            <button type="submit">Search</button>
            <div id="microphone"></div>
        </form>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Define subLocations with coordinates
        const subLocations = {
            Agra: {'Civil Lines': { lat: 27.2150, lon: 78.0052 },'Dayal Bagh': { lat: 27.2020, lon: 78.0220 },'Fatehabad Road': { lat: 27.1648, lon: 78.0422 },'Kamla Nagar': { lat: 27.1990, lon: 78.0015 },'MG Road': { lat: 27.1945, lon: 78.0091 },'Sikandra': { lat: 27.2284, lon: 77.9865 },'Tajganj': { lat: 27.1742, lon: 78.0422 }},
            Ahemdabad: {'Bodakdev': { lat: 23.0426, lon: 72.5286 },'Bopal': { lat: 23.0396, lon: 72.4663 },'Maninagar': { lat: 22.9795, lon: 72.6071 },'Navrangpura': { lat: 23.0352, lon: 72.5596 },'Prahlad Nagar': { lat: 23.0093, lon: 72.5100 },'Satellite': { lat: 23.0305, lon: 72.5089 },'Vastrapur': { lat: 23.0379, lon: 72.5260 }},
            Ajmer: {'Civil Lines': { lat: 26.4673, lon: 74.6371 },'Dargah Bazar': { lat: 26.4524, lon: 74.6379 },'Ganj': { lat: 26.4674, lon: 74.6303 },'Kutchery Road': { lat: 26.4631, lon: 74.6264 },'Madar Gate': { lat: 26.4609, lon: 74.6410 },'Mayo': { lat: 26.4857, lon: 74.6317 },'Vaishali Nagar': { lat: 26.4724, lon: 74.6360 }},
            Amritsar: {'Chheharta': { lat: 31.6336, lon: 74.8205 },'Golden Temple Area': { lat: 31.6199, lon: 74.8765 },'GT Road': { lat: 31.6361, lon: 74.8722 },'Lawrence Road': { lat: 31.6310, lon: 74.8601 },'Mall Road': { lat: 31.6348, lon: 74.8745 },'Putlighar': { lat: 31.6318, lon: 74.8554 },'Ranjit Avenue': { lat: 31.6461, lon: 74.8590 }},
            Banglore: {'Electronic City': { lat: 12.8395, lon: 77.6771 },'HSR Layout': { lat: 12.9116, lon: 77.6413 },'Indiranagar': { lat: 12.9716, lon: 77.6412 },'Jayanagar': { lat: 12.9260, lon: 77.5833 },'Koramangala': { lat: 12.9352, lon: 77.6245 },'MG Road': { lat: 12.9719, lon: 77.5963 },'Whitefield': { lat: 12.9698, lon: 77.7499 }},
            Bhopal: {'Arera Colony': { lat: 23.2067, lon: 77.4285 },'Bairagarh': { lat: 23.2886, lon: 77.3616 },'Hoshangabad Road': { lat: 23.1936, lon: 77.4339 },'Kolar Road': { lat: 23.1593, lon: 77.4556 },'MP Nagar': { lat: 23.2350, lon: 77.4329 },'New Market': { lat: 23.2345, lon: 77.4045 },'Shahpura': { lat: 23.2009, lon: 77.4533 }},
            Bhuvneshwar: {'Chandrasekharpur': { lat: 20.3553, lon: 85.8186 },'Jaydev Vihar': { lat: 20.2967, lon: 85.8192 },'Kharavel Nagar': { lat: 20.2924, lon: 85.8410 },'Old Town': { lat: 20.2349, lon: 85.8333 },'Patia': { lat: 20.3588, lon: 85.8191 },'Saheed Nagar': { lat: 20.2990, lon: 85.8441 },'Unit 1 to 9': { lat: 20.2914, lon: 85.8175 }},
            Chandigarh: {'Sector 10': { lat: 30.7533, lon: 76.7833 },'Sector 11': { lat: 30.7536, lon: 76.7796 },'Sector 17': { lat: 30.7410, lon: 76.7824 },'Sector 22': { lat: 30.7419, lon: 76.7711 },'Sector 35': { lat: 30.7305, lon: 76.7699 },'Sector 8': { lat: 30.7537, lon: 76.7857 },'Sector 9': { lat: 30.7533, lon: 76.7803 }},
            Chennai: {'Adyar': { lat: 13.0067, lon: 80.2572 },'Anna Nagar': { lat: 13.0846, lon: 80.2182 },'Besant Nagar': { lat: 12.9981, lon: 80.2654 },'Kodambakkam': { lat: 13.0496, lon: 80.2312 },'Mylapore': { lat: 13.0336, lon: 80.2691 },'T. Nagar': { lat: 13.0418, lon: 80.2337 },'Velachery': { lat: 12.9784, lon: 80.2197 }},
            Coimbatore: {'Avinashi Road': { lat: 11.0200, lon: 76.9802 },'Gandhipuram': { lat: 11.0177, lon: 76.9675 },'Peelamedu': { lat: 11.0191, lon: 77.0284 },'Race Course': { lat: 10.9986, lon: 76.9714 },'RS Puram': { lat: 11.0085, lon: 76.9463 },'Saibaba Colony': { lat: 11.0172, lon: 76.9427 },'Vadavalli': { lat: 10.9980, lon: 76.9147 }},
            Dehradun: {'Clock Tower': { lat: 30.3180, lon: 78.0290 },'Dalanwala': { lat: 30.3244, lon: 78.0548 },'Mussoorie Road': { lat: 30.4406, lon: 78.0506 },'Patel Nagar': { lat: 30.2980, lon: 78.0224 },'Rajpur Road': { lat: 30.3566, lon: 78.0664 },'Sahastradhara Road': { lat: 30.3630, lon: 78.0864 },'Vasant Vihar': { lat: 30.3350, lon: 78.0437 }},
            Guwahati: {'Beltola': { lat: 26.1221, lon: 91.7898 },'Chandmari': { lat: 26.1854, lon: 91.7651 },'Dispur': { lat: 26.1433, lon: 91.7898 },'Ganeshguri': { lat: 26.1446, lon: 91.8027 },'Kahilipara': { lat: 26.1344, lon: 91.7531 },'Paltan Bazaar': { lat: 26.1810, lon: 91.7480 },'Pan Bazaar': { lat: 26.1823, lon: 91.7492 }},
            Gwalior: {'City Centre': { lat: 26.2235, lon: 78.1839 },'Lashkar': { lat: 26.2037, lon: 78.1719 },'Morar': { lat: 26.2323, lon: 78.2331 },'Phool Bagh': { lat: 26.2106, lon: 78.1632 },'Ravi Nagar': { lat: 26.2359, lon: 78.2032 },'Sikandar Kampoo': { lat: 26.2062, lon: 78.1782 },'Thatipur': { lat: 26.2267, lon: 78.2226 }},
            Hyderabad: {'Banjara Hills': { lat: 17.4116, lon: 78.4487 },'Gachibowli': { lat: 17.4401, lon: 78.3489 },'Hitech City': { lat: 17.4504, lon: 78.3802 },'Jubilee Hills': { lat: 17.4334, lon: 78.4072 },'Kondapur': { lat: 17.4799, lon: 78.3528 },'Madhapur': { lat: 17.4483, lon: 78.3915 },'Secunderabad': { lat: 17.4399, lon: 78.4983 }},
            Indore: {'Annapurna': { lat: 22.6982, lon: 75.8529 },'MG Road': { lat: 22.7182, lon: 75.8577 },'Nipania': { lat: 22.7626, lon: 75.8930 },'Palasia': { lat: 22.7173, lon: 75.8867 },'Rau': { lat: 22.6457, lon: 75.8122 },'Saket Nagar': { lat: 22.7006, lon: 75.8767 },'Vijay Nagar': { lat: 22.7506, lon: 75.8936 }},
            Jaipur: {'Ajmer Road': { lat: 26.9124, lon: 75.7553 },'C-Scheme': { lat: 26.9157, lon: 75.7930 },'Jagatpura': { lat: 26.8467, lon: 75.8361 },'Malviya Nagar': { lat: 26.8511, lon: 75.8197 },'Mansarovar': { lat: 26.8623, lon: 75.7495 },'Vaishali Nagar': { lat: 26.9140, lon: 75.7345 },'Vidhyadhar Nagar': { lat: 26.9744, lon: 75.7834 }},
            Jodhpur: {'Basni': { lat: 26.2350, lon: 73.0091 },'Paota': { lat: 26.2962, lon: 73.0311 },'Ratanada': { lat: 26.2830, lon: 73.0206 },'Shastri Nagar': { lat: 26.2684, lon: 73.0245 },'Sardarpura': { lat: 26.2806, lon: 73.0225 },'Umaid Heritage': { lat: 26.2879, lon: 73.0321 },'Vijay Nagar': { lat: 26.2823, lon: 73.0469 }},
            Kanpur: {'Arya Nagar': { lat: 26.4624, lon: 80.3445 },'Civil Lines': { lat: 26.4657, lon: 80.3490 },'Kakadeo': { lat: 26.4702, lon: 80.3055 },'Kalyanpur': { lat: 26.5152, lon: 80.2912 },'Kidwai Nagar': { lat: 26.4254, lon: 80.3554 },'Swaroop Nagar': { lat: 26.4812, lon: 80.3436 },'Tilak Nagar': { lat: 26.4516, lon: 80.3306 }},
            Kochi: {'Edapally': { lat: 10.0365, lon: 76.3161 },'Kadavanthra': { lat: 9.9670, lon: 76.3034 },'Kaloor': { lat: 10.0010, lon: 76.2987 },'Kakkanad': { lat: 10.0159, lon: 76.3344 },'Marine Drive': { lat: 9.9816, lon: 76.2757 },'Panampilly Nagar': { lat: 9.9660, lon: 76.2997 },'Vyttila': { lat: 9.9609, lon: 76.3142 }},
            Kolkata: {'Alipore': { lat: 22.5381, lon: 88.3315 },'Ballygunge': { lat: 22.5286, lon: 88.3654 },'Garia': { lat: 22.4664, lon: 88.3790 },'Howrah': { lat: 22.5958, lon: 88.2636 },'New Town': { lat: 22.5958, lon: 88.4790 },'Salt Lake City': { lat: 22.5867, lon: 88.4171 },'Tollygunge': { lat: 22.4984, lon: 88.3476 }},
            Lucknow: {'Aliganj': { lat: 26.8774, lon: 80.9369 },'Aminabad': { lat: 26.8533, lon: 80.9376 },'Gomti Nagar': { lat: 26.8467, lon: 81.0067 },'Hazratganj': { lat: 26.8537, lon: 80.9424 },'Indira Nagar': { lat: 26.8761, lon: 81.0005 },'Jankipuram': { lat: 26.9051, lon: 80.9398 },'Rajajipuram': { lat: 26.8353, lon: 80.8940 }},
            Ludhiana: {'Civil Lines': { lat: 30.9101, lon: 75.8402 },'Dugri': { lat: 30.8724, lon: 75.8447 },'Ghumar Mandi': { lat: 30.9076, lon: 75.8524 },'Model Town': { lat: 30.9109, lon: 75.8500 },'Pakhowal Road': { lat: 30.8903, lon: 75.8242 },'Sarabha Nagar': { lat: 30.8955, lon: 75.8330 },'Vikas Nagar': { lat: 30.9176, lon: 75.8356 }},
            Mysore: {'Gokulam': { lat: 12.3223, lon: 76.6319 },'JP Nagar': { lat: 12.2915, lon: 76.6394 },'Kuvempu Nagar': { lat: 12.2958, lon: 76.6385 },'Saraswathipuram': { lat: 12.2993, lon: 76.6386 },'Vijayanagar': { lat: 12.3301, lon: 76.6122 },'Hebbal': { lat: 12.3563, lon: 76.6112 },'Lakshmipuram': { lat: 12.3087, lon: 76.6442 }},
            NewDelhi: {'Connaught Place': { lat: 28.6315, lon: 77.2167 },'Chandni Chowk': { lat: 28.6565, lon: 77.2303 },'Karol Bagh': { lat: 28.6511, lon: 77.1915 },'Lajpat Nagar': { lat: 28.5674, lon: 77.2434 },'Saket': { lat: 28.5272, lon: 77.2187 },'Hauz Khas': { lat: 28.5495, lon: 77.2011 },'Dwarka': { lat: 28.5921, lon: 77.0460 }},
            Raipur: {'Shankar Nagar': { lat: 21.2497, lon: 81.6586 },'Telibandha': { lat: 21.2444, lon: 81.6579 },'Amanaka': { lat: 21.2515, lon: 81.6120 },'Pandri': { lat: 21.2393, lon: 81.6329 },'Tatibandh': { lat: 21.2414, lon: 81.5945 },'Samta Colony': { lat: 21.2489, lon: 81.6442 },'Saraswati Nagar': { lat:21.2544, lon:81.6013 }},
            Thiruvananthapuram: {'Kowdiar': { lat: 8.5300, lon: 76.9485 },'Pattom': { lat: 8.5222, lon: 76.9415 },'Kesavadasapuram': { lat: 8.5261, lon: 76.9440 },'Vazhuthacaud': { lat: 8.5091, lon: 76.9585 },'Ulloor': { lat: 8.5370, lon: 76.9369 },'Palayam': { lat: 8.5068, lon: 76.9474 },'Vattiyoorkavu': { lat: 8.5087, lon: 76.9735 }}
        };

        const locations = Object.keys(subLocations);

        const locationSelect = document.getElementById('location');
        const subLocationSelect = document.getElementById('subLocation');

        let map, marker;

        // Populate Location Dropdown
        locations.forEach(location => {
            const option = document.createElement('option');
            option.value = location;
            option.textContent = location;
            locationSelect.appendChild(option);
        });

        // Handle Location Change
        locationSelect.addEventListener('change', function () {
            const selectedLocation = this.value;
            subLocationSelect.disabled = !selectedLocation;
            subLocationSelect.innerHTML = '<option value="">Select Sub-Location</option>';

            if (selectedLocation && subLocations[selectedLocation]) {
                Object.keys(subLocations[selectedLocation]).forEach(function(subLocation) {
                    const option = document.createElement('option');
                    option.value = subLocation;
                    option.textContent = subLocation;
                    subLocationSelect.appendChild(option);
                });
            }
        });

        // Map initialization
        document.getElementById('mapButton').addEventListener('click', function () {
            const mapDiv = document.getElementById('map');
            mapDiv.style.display = 'block';

            if (!mapDiv._leaflet) {
                map = L.map('map').setView([21.2514, 81.6296], 13);  // Default location (Raipur)
                mapDiv._leaflet = true;

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
            }
        });

        // Handle Sub-Location Selection and Update Map
        subLocationSelect.addEventListener('change', function () {
            const selectedLocation = locationSelect.value;
            const selectedSubLocation = this.value;

            if (selectedLocation && selectedSubLocation) {
                const { lat, lon } = subLocations[selectedLocation][selectedSubLocation];

                if (map && lat && lon) {
                    if (marker) {
                        map.removeLayer(marker);
                    }

                    map.setView([lat, lon], 15);
                    marker = L.marker([lat, lon]).addTo(map)
                        .bindPopup(`<b>${selectedSubLocation}</b><br>${selectedLocation}`).openPopup();
                }
            }
        });
    </script>
</body>
</html>
